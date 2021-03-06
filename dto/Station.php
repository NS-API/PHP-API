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
class Station
{
	private $naam;
	private $code;
	private $land;
	private $latitude;
	private $longitude;
	private $alias;

	public function __construct($naam, $code, $land, $latitude, $longitude, $alias)
	{
		$this->naam = $naam;
		$this->code = $code;
		$this->land = $land;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->alias = $alias;
	}

	public function getNaam()
	{
		return $this->naam;
	}

	public function getCode()
	{
		return $this->code;
	}

	public function getLand()
	{
		return $this->land;
	}

	public function getLatitude()
	{
		return $this->latitude;
	}

	public function getLongitude()
	{
		return $this->longitude;
	}

	public function isAlias()
	{
		return $this->alias;
	}
}
?>