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
	private $name;
	private $code;
	private $country;
	private $latitude;
	private $longitude;
	private $alias;

	public function __construct($name, $code, $country, $latitude, $longitude, $alias)
	{
		$this->name = $name;
		$this->code = $code;
		$this->country = $country;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->alias = $alias;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getCode()
	{
		return $this->code;
	}

	public function getCountry()
	{
		return $this->country;
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