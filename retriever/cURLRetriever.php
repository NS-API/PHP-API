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
 * A simple Retriever implementation that uses cURL to retrieve data from the NS.
 */
require_once('Retriever.php');

class cURLRetriever extends Retriever
{
	public function __construct($username, $password)
	{
		parent::__construct($username, $password);
	}

	public function getStations()
	{
		return $this->getXML(parent::URL_STATIONS);
	}

	public function getPrijzen($fromStation, $toStation, $viaStation = null, $dateTime = null)
	{
		return $this->getXML(parent::URL_PRIJZEN."?from=".$fromStation->getCode()."&to=".$toStation->getCode().($viaStation !== NULL ? "&via=".$viaStation->getCode() : "").($dateTime !== NULL ? "&dateTime=".Utils::UnixTimestamp2ISO8601Date($dateTime) : ""));
	}

	public function getActueleVertrektijden($station)
	{
		return $this->getXML(parent::URL_ACTUELEVERTREKTIJDEN."?station=".$station->getCode());
	}

	public function getTreinplanner($fromStation, $toStation, $viaStation = null, $previousAdvices = null, $nextAdvices = null, $dateTime = null, $departure = null, $hslAllowed = null, $yearCard = null)
	{
		return $this->getXML(parent::URL_TREINPLANNER."?fromStation=".$fromStation->getCode()."&toStation=".$toStation->getCode().($viaStation !== NULL ? "&viaStation=".$viaStation->getCode() : "").($previousAdvices !== NULL ? "&previousAdvices=".$previousAdvices : "").($nextAdvices !== NULL ? "&nextAdvices=".$nextAdvices : "").($dateTime !== NULL ? "&dateTime=".Utils::UnixTimestamp2ISO8601Date($dateTime) : "").($departure !== NULL ? "&departure=".Utils::boolean2String($departure) : "").($hslAllowed !== NULL ? "&hslAllowed=".Utils::boolean2String($hslAllowed) : "").($yearCard !== NULL ? "&yearCard=".Utils::boolean2String($yearCard) : ""));
	}

	public function getStoringen($station = null, $actual = null, $unplanned = null)
	{
		return $this->getXML(parent::URL_STORINGEN.($station !== NULL ? "?station=".$station->getCode() : "?=").($actual !== NULL ? "&actual=".Utils::boolean2String($actual) : "").($unplanned !== NULL ? "&unplanned=".Utils::boolean2String($unplanned) : ""));
	}

	private function getXML($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, parent::getUsername() . ":" . parent::getPassword());
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$xml = curl_exec($ch);
		curl_close($ch);
		return $xml;
	}
}
?>