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
require_once(dirname(__FILE__).'/TravelStop.php');

class TravelPart
{
	private $travelType;
	private $transportationType;
	private $shiftNumber;
	private $travelStops;

	public function __construct($travelType, $transportationType, $shiftNumber, $travelStops)
	{
		$this->travelType = $travelType;
		$this->transportationType = $transportationType;
		$this->shiftNumber = $shiftNumber;
		$this->travelStops = $travelStops;
	}

	public function getTravelType()
	{
		return $this->travelType;
	}

	public function getTransportationType()
	{
		return $this->transportationType;
	}

	public function getShiftNumber()
	{
		return $this->shiftNumber;
	}

	public function getTravelStops()
	{
		return $this->travelStops;
	}
}