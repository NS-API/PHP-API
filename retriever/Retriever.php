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
 * A Retriever is the object that does the actual fetching of data.
 * In its basic form, it models a html GET.
 *
 * We have abstracted this, so people can implement their own ways to retrieve information. One example is support for proxies.
 * We only include the cURLRetriever in phpNS, so another example is sites that don't have cURL installed.
 */
abstract class Retriever
{
	private $username;
	private $password;

	const URL_STATIONS = "http://webservices.ns.nl/ns-api-stations";
	const URL_RATES = "http://webservices.ns.nl/ns-api-prijzen-v2";
	const URL_REALDEPARTURETIMES = "http://webservices.ns.nl/ns-api-avt";
	const URL_TRAINSCHEDULER = "http://webservices.ns.nl/ns-api-trainscheduler";
	const URL_OUTAGES = "http://webservices.ns.nl/ns-api-outages";

	public function __construct($username, $password)
	{
		$this->username = $username;
		$this->password = $password;
	}

	protected function getUsername()
	{
		return $this->username;
	}

	protected function getPassword()
	{
		return $this->password;
	}

	public abstract function getStations();
	public abstract function getRates($fromStation, $toStation, $viaStation = null, $dateTime = null);
	public abstract function getRealDepartureTimes($station);
	public abstract function getTrainScheduler($fromStation, $toStation, $viaStation = null, $previousAdvices = null, $nextAdvices = null, $dateTime = null, $departure = null, $hslAllowed = null, $yearCard = null);
	public abstract function getOutages($station = null, $actual = null, $unplanned = null);
}