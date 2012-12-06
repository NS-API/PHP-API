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
	private $timeToCachePrices = 86400; // 60 * 60 * 24
	private $timeToCacheActuelDepartureTimes = 30;
	private $timeToCacheTrainscheduler = 60;
	private $timeToCacheOutages = 120; // 60 * 2

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
	public function getTimeToCachePrices()
	{
		return $this->timeToCachePrices;
	}

	public function setTimeToCachePrices($timeToCachePrices)
	{
		$this->timeToCachePrices = $timeToCachePrices;
	}

	public function getTimeToCacheActuelDepartureTimes()
	{
		return $this->timeToCacheActuelDepartureTimes;
	}

	public function setTimeToCacheActuelDepartureTimes($timeToCacheActuelDepartureTimes)
	{
		$this->timeToCacheActuelDepartureTimes = $timeToCacheActuelDepartureTimes;
	}

	public function getTimeToCacheTrainscheduler()
	{
		return $this->timeToCacheTrainscheduler;
	}

	public function setTimeToCacheTrainscheduler($timeToCacheTrainscheduler)
	{
		$this->timeToCacheTrainscheduler = $timeToCacheTrainscheduler;
	}

	public function getTimeToCacheOutages()
	{
		return $this->timeToCacheOutages;
	}

	public function setTimeToCacheOutages($timeToCacheOutages)
	{
		$this->timeToCacheOutages = $timeToCacheOutages;
	}

	public abstract function getStations();
	public abstract function getRates($fromStation, $toStation, $viaStation = null, $dateTime = null);
	public abstract function getActuelDepartureTimes($station);
	public abstract function getTrainscheduler($fromStation, $toStation, $viaStation = null, $previousAdvices = null, $nextAdvices = null, $dateTime = null, $departure = null, $hslAllowed = null, $yearCard = null);
	public abstract function getOutages($station, $actual = null, $unplanned = null);
}