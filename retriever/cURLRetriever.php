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
require_once(dirname(__file__).'/Retriever.php');
require_once(dirname(__file__).'/NScURLRetrieverException.php');

/**
 * A simple Retriever implementation that uses cURL to retrieve data from the NS.
 */
class cURLRetriever extends Retriever
{
	const SOAP_FAULT = "<soap:Fault>";
	const SOAP_FAULTSTRING_START = "<faultstring>";
	const SOAP_FAULTSTRING_END = "</faultstring>";

	const XML_ERROR_INVALID_WEBSERVICE = 002; // 002:The requested webservice is not found
	const XML_ERROR_INVALID_KEY = 006; // 006:No customer found for the specified username and password
	const XML_ERROR_UNEXPECTED = 009; // 099:An unexpected exception occured
	const XML_ERROR_LIMIT_REACHED = 013; // 013:The limit for calling this webservice has been reached

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
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$xml = curl_exec($ch);
		
		if (curl_errno($ch) != 0)
		{
			throw new NScURLRetrieverException(NScURLRetrieverException::TYPE_CURL, $url, curl_error($ch), curl_errno($ch));
		}
		
		curl_close($ch);

		if (strpos($xml, self::SOAP_FAULT) > -1)
		{
			// This is an error response
			$faultstringStartPosition = strpos($xml, self::SOAP_FAULTSTRING_START);
			$faultstringEndPosition = strpos($xml, self::SOAP_FAULTSTRING_END);
			if ($faultstringStartPosition > -1 && $faultstringEndPosition > $faultstringStartPosition)
			{
				$faultstring = substr($xml, $faultstringStartPosition + strlen(self::SOAP_FAULTSTRING_START), $faultstringEndPosition- $faultstringStartPosition - strlen(self::SOAP_FAULTSTRING_START));
				if (preg_match("/^([0-9]+):(.+)$/", $faultstring, $matches) > 0)
				{
					throw new NScURLRetrieverException(NScURLRetrieverException::TYPE_XML, $url, $matches[2], $matches[1]);
				}
				else
				{
					throw new NScURLRetrieverException(NScURLRetrieverException::TYPE_XML, $url, $faultstring);
				}
			}
			else
			{
				throw new NScURLRetrieverException(NScURLRetrieverException::TYPE_XML, $url);
			}
		}

		return $xml;
	}
}
?>