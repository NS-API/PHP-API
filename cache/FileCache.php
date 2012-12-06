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
 * This is a Cache implementation that stores the responses on disk in a directory structure.
 */
class FileCache extends Cache
{
	private $tmpDir;

	public function __construct($retriever, $tmpDir)
	{
		parent::__construct($retriever);
		$this->tmpDir = $tmpDir;
	}

	public function getStations()
	{
		$tmpFile = $this->initTmpDir("stations")."result.xml";
		if (file_exists($tmpFile) && filemtime($tmpFile) + $this->getTimeToCacheStations() > time())
		{
			return file_get_contents($tmpFile);
		}
		else
		{
			$xml = $this->getRetriever()->getStations();
			file_put_contents($tmpFile, $xml);
			return $xml;
		}
	}

	public function getRates($fromStation, $toStation, $viaStation = null, $dateTime = null)
	{
		$tmpFile = $this->initTmpDir("rates", $fromStation, $toStation, $viaStation, $dateTime)."result.xml";
		if (file_exists($tmpFile) && filemtime($tmpFile) + $this->getTimeToCachePrices() > time())
		{
			return file_get_contents($tmpFile);
		}
		else
		{
			$xml = $this->getRetriever()->getRates($fromStation, $toStation, $viaStation, $dateTime);
			file_put_contents($tmpFile, $xml);
			return $xml;
		}
	}

	public function getActuelDepartureTimes($station)
	{
		$tmpFile = $this->initTmpDir("avt", $station)."result.xml";
		if (file_exists($tmpFile) && filemtime($tmpFile) + $this->getTimeToCacheActuelDepartureTimes() > time())
		{
			return file_get_contents($tmpFile);
		}
		else
		{
			$xml = $this->getRetriever()->getActuelDepartureTimes($station);
			file_put_contents($tmpFile, $xml);
			return $xml;
		}
	}

	public function getTrainscheduler($fromStation, $toStation, $viaStation = null, $previousAdvices = null, $nextAdvices = null, $dateTime = null, $departure = null, $hslAllowed = null, $yearCard = null)
	{
		$tmpFile = $this->initTmpDir("trainscheduler", $fromStation, $toStation, $viaStation, $previousAdvices, $nextAdvices, $dateTime, $departure, $hslAllowed, $yearCard)."result.xml";
		if (file_exists($tmpFile) && filemtime($tmpFile) + $this->getTimeToCacheTrainscheduler() > time())
		{
			return file_get_contents($tmpFile);
		}
		else
		{
			$xml = $this->getRetriever()->getTrainscheduler($fromStation, $toStation, $viaStation, $previousAdvices, $nextAdvices, $dateTime, $departure, $hslAllowed, $yearCard);
			file_put_contents($tmpFile, $xml);
			return $xml;
		}
	}

	public function getOutages($station, $actual = null, $unplanned = null)
	{
		$tmpFile = $this->initTmpDir("outages", $station, $actual, $unplanned)."result.xml";
		if (file_exists($tmpFile) && filemtime($tmpFile) + $this->getTimeToCacheOutages() > time())
		{
			return file_get_contents($tmpFile);
		}
		else
		{
			$xml = $this->getRetriever()->getOutages($station, $actual, $unplanned);
			file_put_contents($tmpFile, $xml);
			return $xml;
		}
	}

	private function initTmpDir($functionName)
	{
		$arguments = func_get_args();
		$strTmpDir = $this->tmpDir . "/";
		foreach ($arguments as $arg)
		{
			if ($arg === null)
			{
				$strTmpDir .= "NULL/";
			}
			elseif ($arg instanceof Station)
			{
				$strTmpDir .= $arg->getCode() . "/";
			}
			elseif (is_bool($arg))
			{
				$strTmpDir .= ($arg ? "TRUE" : "FALSE") . "/";
			}
			elseif (is_int($arg))
			{
				$strTmpDir .= $arg . "/";
			}
			elseif (is_string($arg))
			{
				$strTmpDir .= $arg . "/";
			}
			else
			{
				trigger_error("FileCache::initTmpDir got an object of unknown type", E_USER_WARNING);
				$strTmpDir .= $arg . "/";
			}
		}
		if (!file_exists($strTmpDir))
		{
			mkdir($strTmpDir, 0700, TRUE);
		}
		return $strTmpDir;
	}
}