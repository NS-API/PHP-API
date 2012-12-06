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
class DepartingTrain
{
	private $shiftNumber;
	private $departureTime;
	private $departureDelay;
	private $departureDelayText;
	private $finalDestination;
	private $trainType;
	private $departureTrack;
	private $departureTrackChanged;
	private $remarks;

	public function __construct($shiftNumber, $departureTime, $departureDelay, $departureDelayText, $finalDestination, $trainType, $departureTrack, $departureTrackChanged, $remarks)
	{
		$this->shiftNumber = $shiftNumber;
		$this->departureTime = $departureTime;
		$this->departureDelay = $departureDelay;
		$this->departureDelayText = $departureDelayText;
		$this->finalDestination = $finalDestination;
		$this->trainType = $trainType;
		$this->departureTrack = $departureTrack;
		$this->departureTrackChanged = $departureTrackChanged;
		$this->remarks = $remarks;
	}

	public function getShiftNumber()
	{
		return $this->shiftNumber;
	}

	public function getDepartureTime()
	{
		return $this->departureTime;
	}

	public function getDepartureDelay()
	{
		return $this->departureDelay;
	}

	public function getDepartureDelayText()
	{
		return $this->departureDelayText;
	}
	 
	public function getFinalDestination()
	{
		return $this->finalDestination;
	}
	 
	public function getTrainType()
	{
		return $this->trainType;
	}
	 
	public function getDepartureTrack()
	{
		return $this->departureTrack;
	}

	public function hasChangedDepartureTrack()
	{
		return $this->departureTrackChanged;
	}

	public function getRemarks()
	{
		return $this->remarks;
	}
}