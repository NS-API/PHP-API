<?php
/*
 * Copyright 2011 Jurrie Overgoor <jurrie@narrowxpres.nl>
 *
 * This file is part of phpNS.
 *
 * phpNS is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * phpNS is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * phpNS. If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * This is a Cache implementation that stores the responses in a MySQL database table.
 */
class MySQLCache extends Cache
{
	private $table;
	private $dbConnection;

	// ID's that are used in the cache table
	const ID_STATIONS = 0;
	const ID_PRICES = 1;
	const ID_ACTUELDEPARTURETIMES = 2;
	const ID_TRAINSCHEDULER = 3;
	const ID_OUTAGES = 4;

	public function __construct($retriever, $server, $username, $password, $database, $table)
	{
		$this->table = $table;

		// Connect to the database
		$this->dbConnection = mysql_connect($server, $username, $password);
		if ($this->dbConnection === FALSE)
		{
			throw new Exception("Cannot connect to MySQL database: ".mysql_error(), mysql_errno());
		}
		if (mysql_select_db($database, $this->dbConnection) === FALSE)
		{
			throw new Exception("Cannot select database: ".mysql_error($this->dbConnection), mysql_errno($this->dbConnection));
		}
		 
		// Check if our cache table exists
		$result = mysql_query("SHOW TABLES LIKE '".mysql_real_escape_string($table)."'", $this->dbConnection);
		if ($result === FALSE)
		{
			throw new Exception("Cannot query for existance of table: ".mysql_error($this->dbConnection), mysql_errno($this->dbConnection));
		}
		if (mysql_num_rows($result) !== 1)
		{
			self::createCacheTable($this->dbConnection, $table);
		}

		parent::__construct($retriever);
	}

	public static function createCacheTable($link, $table)
	{
		$sql  = "CREATE TABLE `".mysql_real_escape_string($table)."` (";
		$sql .= "  `method` tinyint(3) unsigned NOT NULL,";
		$sql .= "  `parameters` varchar(115) COLLATE utf8_bin NOT NULL,";
		$sql .= "  `result` longtext COLLATE utf8_bin NOT NULL,";
		$sql .= "  `timestamp` int(11) NOT NULL,";
		$sql .= "PRIMARY KEY (`method`,`parameters`)";
		$sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";

		$result = mysql_query($sql, $link);
		if ($result === FALSE)
		{
			throw new Exception("Could not create cache table: ".mysql_error($link), mysql_errno($link));
		}
	}

	private function tryFromCache($id, $timeToCache)
	{
		$parameters = array();
		$arguments = func_get_args();
		array_shift($arguments); // Remove $id
		array_shift($arguments); // Remove $timeToCache
		foreach ($arguments as $arg)
		{
			if ($arg instanceof Station)
			{
				$parameters[] = $arg->getCode();
			}
			else
			{
				$parameters[] = $arg;
			}
		}
		$parameters = serialize($parameters);
		$result = mysql_query("SELECT `result` FROM `".mysql_real_escape_string($this->table)."` WHERE `method` = ".mysql_real_escape_string($id)." AND `parameters` = '".mysql_real_escape_string($parameters)."' AND `timestamp` > ".(time()-$timeToCache), $this->dbConnection);
		if ($result === FALSE)
		{
			return NULL;
		}
		if (mysql_num_rows($result) !== 1)
		{
			return NULL;
		}
		return mysql_result($result, 0, 0);
	}

	private function putInCache($id, $xmlResult)
	{
		$parameters = array();
		$arguments = func_get_args();
		array_shift($arguments); // Remove $id
		array_shift($arguments); // Remove $xmlResult
		foreach ($arguments as $arg)
		{
			if ($arg instanceof Station)
			{
				$parameters[] = $arg->getCode();
			}
			else
			{
				$parameters[] = $arg;
			}
		}
		$parameters = serialize($parameters);
		$result = mysql_query("UPDATE `".mysql_real_escape_string($this->table)."` SET `result` = '".mysql_real_escape_string($xmlResult)."', `timestamp` = ".time()." WHERE `method` = ".mysql_real_escape_string($id)." AND `parameters` = '".mysql_real_escape_string($parameters)."'", $this->dbConnection);
		if (mysql_affected_rows($this->dbConnection) < 1)
		{
			mysql_query("INSERT INTO `".mysql_real_escape_string($this->table)."` (`method`, `parameters`, `result`, `timestamp`) VALUES (".mysql_real_escape_string($id).", '".mysql_real_escape_string($parameters)."', '".mysql_real_escape_string($xmlResult)."', ".time().")", $this->dbConnection);
		}
	}

	public function getStations()
	{
		$xml = $this->tryFromCache(self::ID_STATIONS, $this->getTimeToCacheStations());
		if ($xml === NULL)
		{
			$xml = $this->getRetriever()->getStations();
			$this->putInCache(self::ID_STATIONS, $xml);
		}
		return $xml;
	}

	public function getRates($fromStation, $toStation, $viaStation = null, $dateTime = null)
	{
		$xml = $this->tryFromCache(self::ID_PRICES, $this->getTimeToCacheRates(), $fromStation, $toStation, $viaStation, $dateTime);
		if ($xml === NULL)
		{
			$xml = $this->getRetriever()->getRates($fromStation, $toStation, $viaStation, $dateTime);
			$this->putInCache(self::ID_PRICES, $xml, $fromStation, $toStation, $viaStation, $dateTime);
		}
		return $xml;
	}

	public function getActuelDepartureTimes($station)
	{
		$xml = $this->tryFromCache(self::ID_ACTUELDEPARTURETIMES, $this->getTimeToCacheActuelDepartureTimes(), $station);
		if ($xml === NULL)
		{
			$xml = $this->getRetriever()->getActuelDepartureTimes($station);
			$this->putInCache(self::ID_ACTUELDEPARTURETIMES, $xml, $station);
		}
		return $xml;
	}

	public function getTrainscheduler($fromStation, $toStation, $viaStation = null, $previousAdvices = null, $nextAdvices = null, $dateTime = null, $departure = null, $hslAllowed = null, $yearCard = null)
	{
		$xml = $this->tryFromCache(self::ID_TRAINSCHEDULER, $this->getTimeToCacheTrainscheduler(), $fromStation, $toStation, $viaStation, $previousAdvices, $nextAdvices, $dateTime, $departure, $hslAllowed, $yearCard);
		if ($xml === NULL)
		{
			$xml = $this->getRetriever()->getTrainscheduler($fromStation, $toStation, $viaStation, $previousAdvices, $nextAdvices, $dateTime, $departure, $hslAllowed, $yearCard);
			$this->putInCache(self::ID_TRAINSCHEDULER, $xml, $fromStation, $toStation, $viaStation, $previousAdvices, $nextAdvices, $dateTime, $departure, $hslAllowed, $yearCard);
		}
		return $xml;
	}

	public function getOutages($station, $actual = null, $unplanned = null)
	{
		$xml = $this->tryFromCache(self::ID_OUTAGES, $this->getTimeToCacheOutages(), $station, $actual, $unplanned);
		if ($xml === NULL)
		{
			$xml = $this->getRetriever()->getOutages($station, $actual, $unplanned);
			$this->putInCache(self::ID_OUTAGES, $xml, $station, $actual, $unplanned);
		}
		return $xml;
	}
}