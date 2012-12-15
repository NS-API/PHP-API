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
require_once(dirname(__FILE__).'/../retriever/Retriever.php');

/**
 * A cache is an object that keeps track of requests made, and responses retrieved.
 * When the same request is made again within the 'cache treshold', the stored response is returned, and no call to the server is done.
 * This keeps the number of requests to the server to a minimum.
 */
abstract class Cache
{
	private $retriever;

	// Seconds to cache a previous result
	private $timeToCacheStations = 86400; // 60 * 60 * 24
	private $timeToCachePrijzen = 86400; // 60 * 60 * 24
	private $timeToCacheActuelevertrektijden = 30;
	private $timeToCacheTreinplanner = 60;
	private $timeToCacheStoringen = 120; // 60 * 2

	public function __construct($retriever)
	{
		$this->retriever = $retriever;
	}

	protected function getRetriever()
	{
		return $this->retriever;
	}

	public function getTimeToCacheStations()
	{
		return $this->timeToCacheStations;
	}

	public function setTimeToCacheStations($timeToCacheStations)
	{
		$this->timeToCacheStations = $timeToCacheStations;
	}
	public function getTimeToCachePrijzen()
	{
		return $this->timeToCachePrijzen;
	}

	public function setTimeToCachePrijzen($timeToCachePrijzen)
	{
		$this->timeToCachePrijzen = $timeToCachePrijzen;
	}

	public function getTimeToCacheActuelevertrektijden()
	{
		return $this->timeToCacheActuelevertrektijden;
	}

	public function setTimeToCacheActuelevertrektijden($timeToCacheActuelevertrektijden)
	{
		$this->timeToCacheActuelevertrektijden = $timeToCacheActuelevertrektijden;
	}

	public function getTimeToCacheTreinplanner()
	{
		return $this->timeToCacheTreinplanner;
	}

	public function setTimeToCacheTreinplanner($timeToCacheTreinplanner)
	{
		$this->timeToCacheTreinplanner = $timeToCacheTreinplanner;
	}

	public function getTimeToCacheStoringen()
	{
		return $this->timeToCacheStoringen;
	}

	public function setTimeToCacheStoringen($timeToCacheStoringen)
	{
		$this->timeToCacheStoringen = $timeToCacheStoringen;
	}

	public abstract function getStations();
	public abstract function getPrijzen($fromStation, $toStation, $viaStation = null, $dateTime = null);
	public abstract function getActueleVertrektijden($station);
	public abstract function getTreinplanner($fromStation, $toStation, $viaStation = null, $previousAdvices = null, $nextAdvices = null, $dateTime = null, $departure = null, $hslAllowed = null, $yearCard = null);
	public abstract function getStoringen($station, $actual = null, $unplanned = null);
}
?>