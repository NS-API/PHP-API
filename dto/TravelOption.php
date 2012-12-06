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
require_once(dirname(__FILE__).'/Alert.php');
require_once(dirname(__FILE__) . '/TravelPart.php');

class TravelOption
{
	private $numberOfChanges;
	private $scheduledTravelTime;
	private $actualTravelTime;
	private $optimal;
	private $scheduledDepartureTime;
	private $actualDepartureTime;
	private $scheduledArrivalTime;
	private $actualArrivalTime;
	private $alert;
	private $travelParts;

	public function __construct($numberOfChanges, $scheduledTravelTime, $actualTravelTime, $optimal, $scheduledDepartureTime, $actualDepartureTime, $scheduledArrivalTime, $actualArrivalTime, $alert, $travelParts)
	{
		$this->numberOfChanges = $numberOfChanges;
		$this->scheduledTravelTime = $scheduledTravelTime;
		$this->actualTravelTime = $actualTravelTime;
		$this->optimal = $optimal;
		$this->scheduledDepartureTime = $scheduledDepartureTime;
		$this->actualDepartureTime = $actualDepartureTime;
		$this->scheduledArrivalTime = $scheduledArrivalTime;
		$this->actualArrivalTime = $actualArrivalTime;
		$this->alert = $alert;
		$this->travelParts = $travelParts;
	}

	public function getNumberOfChanges()
	{
		return $this->numberOfChanges;
	}

	public function getScheduledTravelTime()
	{
		return $this->scheduledTravelTime;
	}

	public function getActualTravelTime()
	{
		return $this->actualTravelTime;
	}

	public function isOptimaal()
	{
		return $this->optimal;
	}

	public function getScheduledDepartureTime()
	{
		return $this->scheduledDepartureTime;
	}

	public function getActualDepartureTime()
	{
		return $this->actualDepartureTime;
	}

	public function getScheduledArrivalTime()
	{
		return $this->scheduledArrivalTime;
	}

	public function getActualArrivalTime()
	{
		return $this->actualArrivalTime;
	}

	public function getAlert()
	{
		return $this->alert;
	}

	public function getTravelParts()
	{
		return $this->travelParts;
	}
}