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
class TravelStop
{
	private $name;
	private $time;
	private $track;
	private $trackChange;

	public function __construct($name, $time, $track, $trackChange)
	{
		$this->name = $name;
		$this->time = $time;
		$this->track = $track;
		$this->trackChange = $trackChange;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getTime()
	{
		return $this->time;
	}

	public function getTrack()
	{
		return $this->track;
	}

	public function hasTrackChange()
	{
		return $this->trackChange;
	}
}