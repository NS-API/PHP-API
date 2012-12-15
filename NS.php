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
require_once(dirname(__FILE__).'/Utils.php');
require_once(dirname(__FILE__).'/NSException.php');

require_once(dirname(__FILE__).'/dto/Station.php');
require_once(dirname(__FILE__).'/dto/Product.php');
require_once(dirname(__FILE__).'/dto/VertrekkendeTrein.php');
require_once(dirname(__FILE__).'/dto/ReisMogelijkheid.php');
require_once(dirname(__FILE__).'/dto/GeplandeStoring.php');
require_once(dirname(__FILE__).'/dto/OngeplandeStoring.php');

require_once(dirname(__FILE__).'/retriever/Retriever.php');
require_once(dirname(__FILE__).'/retriever/cURLRetriever.php');

class NS
{
	private $cache;

	public function __construct($cache)
	{
		$this->cache = $cache;
	}
	
	public function getCache()
	{
		return $this->cache;
	}

	/**
	 * This function returns the stations that are within $maxDiff kilometers from the given latitude/longitude position.
	 */
	public function getStationsByCoordinates($latitude, $longitude, $maxDiff)
	{
		$stations = $this->getStations();
		$result = array();
		foreach ($stations as $station)
		{
			if ($station->isAlias())
			{
				continue;
			}

			$diff = Utils::getDistanceBetweenPoints($latitude, $longitude, $station->getLatitude(), $station->getLongitude());
			if ($diff < $maxDiff)
			{
				$result[] = $station;
			}
		}
	return $result;
	}

	public function getStationByCode($code)
	{
		$stations = $this->getStations();
		foreach ($stations as $station)
		{
			if ($station->isAlias())
			{
				continue;
			}

			if ($station->getCode() === $code)
			{
				return $station;
			}
		}
	}

	public function getStations()
	{
		$xml = $this->cache->getStations();
		$xml = new SimpleXMLElement($xml);

		$result = array();
		foreach ($xml->station as $xmlStation)
		{
			$name = (string)$xmlStation->name;
			$code = (string)$xmlStation->code;
			$country = (string)$xmlStation->country;
			$lat = (string)$xmlStation->lat;
			$long = (string)$xmlStation->long;
			$alias = Utils::string2Boolean($xmlStation->alias);
			$station = new Station($name, $code, $country, $lat, $long, $alias);
			$result[] = $station;
		}
		return $result;
	}

	public function getPrijzen($fromStation, $toStation, $viaStation = null, $dateTime = null)
	{
		$xml = $this->cache->getPrijzen($fromStation, $toStation, $viaStation, $dateTime);
		$xml = new SimpleXMLElement($xml);

		$result = array();
		foreach ($xml->Product as $xmlProduct)
		{
			$naam = (string)$xmlProduct['naam'];

			$prijzen = array();
			foreach ($xmlProduct->Prijs as $xmlPrijs)
			{
				$korting = (string)$xmlPrijs['korting'];
				$klasse = (string)$xmlPrijs['klasse'];
				$prijs = (string)$xmlPrijs;
				$prijs = new Prijs($korting, $klasse, $prijs);
				$prijzen[] = $prijs;
			}
			$product = new Product($naam, $prijzen);
			$result[] = $product;
		}
		return $result;
	}

	public function getActueleVertrektijden($station)
	{
		$xml = $this->cache->getActueleVertrektijden($station);
		$xml = new SimpleXMLElement($xml);

		$result = array();
		foreach ($xml->VertrekkendeTrein as $xmlVertrekkendeTrein)
		{
			$ritnummer = (string)$xmlVertrekkendeTrein->RitNummer;
			$vertrekTijd = Utils::ISO8601Date2UnixTimestamp($xmlVertrekkendeTrein->VertrekTijd);
			$vertrekVertraging = NULL;
			$vertrekVertragingTekst = NULL;
			if ($xmlVertrekkendeTrein->VertrekVertraging !== NULL && (string)$xmlVertrekkendeTrein->VertrekVertraging !== "")
			{
				$vertrekVertraging = Utils::ISO8601Period2UnixTimestamp($xmlVertrekkendeTrein->VertrekVertraging, $vertrekTijd);
				$vertrekVertragingTekst = (string)$xmlVertrekkendeTrein->VertrekVertragingTekst;
			}
			$eindBestemming = (string)$xmlVertrekkendeTrein->EindBestemming;
			$treinSoort = (string)$xmlVertrekkendeTrein->TreinSoort;
			$vertrekSpoor = (string)$xmlVertrekkendeTrein->VertrekSpoor;
			$vertrekSpoorGewijzigd = Utils::string2Boolean($xmlVertrekkendeTrein->VertrekSpoor['wijziging']);

			$opmerkingen = array();
			if ($xmlVertrekkendeTrein->Opmerkingen->Opmerking !== NULL)
			{
				foreach ($xmlVertrekkendeTrein->Opmerkingen->Opmerking as $xmlOpmerking)
				{
					$opmerkingen[] = trim((string)$xmlOpmerking);
				}
			}
			$vertrekkendeTrein = new VertrekkendeTrein($ritnummer, $vertrekTijd, $vertrekVertraging, $vertrekVertragingTekst, $eindBestemming, $treinSoort, $vertrekSpoor, $vertrekSpoorGewijzigd, $opmerkingen);
			$result[] = $vertrekkendeTrein;
		}
		return $result;
	}

	public function getTreinplanner($fromStation, $toStation, $viaStation = null, $previousAdvices = null, $nextAdvices = null, $dateTime = null, $departure = null, $hslAllowed = null, $yearCard = null)
	{
		$xml = $this->cache->getTreinplanner($fromStation, $toStation, $viaStation, $previousAdvices, $nextAdvices, $dateTime, $departure, $hslAllowed, $yearCard);
		$xml = new SimpleXMLElement($xml);

		$result = array();
		foreach ($xml->ReisMogelijkheid as $xmlReisMogelijkheid)
		{
			$aantalOverstappen = (string)$xmlReisMogelijkheid->AantalOverstappen;
			$geplandeReisTijd = Utils::ISO8601Date2UnixTimestamp($xmlReisMogelijkheid->GeplandeReisTijd);
			$actueleReisTijd = Utils::ISO8601Date2UnixTimestamp($xmlReisMogelijkheid->ActueleReisTijd);
			$optimaal = Utils::string2Boolean($xmlReisMogelijkheid->Optimaal);
			$geplandeVertrekTijd = Utils::ISO8601Date2UnixTimestamp($xmlReisMogelijkheid->GeplandeVertrekTijd);
			$actueleVertrekTijd = Utils::ISO8601Date2UnixTimestamp($xmlReisMogelijkheid->ActueleVertrekTijd);
			$geplandeAankomstTijd = Utils::ISO8601Date2UnixTimestamp($xmlReisMogelijkheid->GeplandeAankomstTijd);
			$actueleAankomstTijd = Utils::ISO8601Date2UnixTimestamp($xmlReisMogelijkheid->ActueleAankomstTijd);

			$melding = NULL;
			if ($xmlReisMogelijkheid->Melding->Id !== NULL)
			{
				$xmlMelding = $xmlReisMogelijkheid->Melding;
				$id = (string)$xmlMelding->Id;
				$ernstig = Utils::string2Boolean($xmlMelding->Ernstig);
				$text = (string)$xmlMelding->Text;
				$melding = new Melding($id, $ernstig, $text);
			}

			$reisDelen = array();
			foreach ($xmlReisMogelijkheid->ReisDeel as $xmlReisDeel)
			{
				$reisSoort = (string)$xmlReisDeel['reisSoort'];
				$vervoerType = (string)$xmlReisDeel->VervoerType;
				$ritNummer = (string)$xmlReisDeel->RitNummer;

				$reisStops = array();
				foreach ($xmlReisDeel->ReisStop as $xmlReisStop)
				{
					$naam = (string)$xmlReisStop->Naam;
					$tijd = Utils::ISO8601Date2UnixTimestamp($xmlReisStop->Tijd);
					$spoor = (string)$xmlReisStop->Spoor;
					$spoorWijziging = Utils::string2Boolean($xmlReisStop->Spoor['wijziging']);
					$reisStop = new ReisStop($naam, $tijd, $spoor, $spoorWijziging);
					$reisStops[] = $reisStop;
				}
				$reisDeel = new ReisDeel($reisSoort, $vervoerType, $ritNummer, $reisStops);
				$reisDelen[] = $reisDeel;
			}
			$reisMogelijkheid = new ReisMogelijkheid($aantalOverstappen, $geplandeReisTijd, $actueleReisTijd, $optimaal, $geplandeVertrekTijd, $actueleVertrekTijd, $geplandeAankomstTijd, $actueleAankomstTijd, $melding, $reisDelen);
			$result[] = $reisMogelijkheid;
		}
		return $result;
	}

	public function getStoringen($station, $actual = null, $unplanned = null)
	{
		$result = array();
		$xml = $this->cache->getStoringen($station, $actual, $unplanned);
		$xml = new SimpleXMLElement($xml);

		foreach ($xml->Ongepland->Storing as $xmlOngeplandeStoring)
		{
			$id = (string)$xmlOngeplandeStoring->id;
			$traject = (string)$xmlOngeplandeStoring->Traject;
			$reden = (string)$xmlOngeplandeStoring->Reden;
			$bericht = (string)$xmlOngeplandeStoring->Bericht;
			$datum = Utils::ISO8601Date2UnixTimestamp($xmlOngeplandeStoring->Datum);
			$ongeplandeStoring = new OngeplandeStoring($id, $traject, $bericht, $reden, $datum);
			$result[] = $ongeplandeStoring;
		}

		foreach ($xml->Gepland->Storing as $xmlGeplandeStoring)
		{
			$id = (string)$xmlGeplandeStoring->id;
			$traject = (string)$xmlGeplandeStoring->Traject;
			$periode = (string)$xmlGeplandeStoring->Periode;
			$reden = (string)$xmlGeplandeStoring->Reden;
			$advies = (string)$xmlGeplandeStoring->Advies;
			$bericht = (string)$xmlGeplandeStoring->Bericht;
			$geplandeStoring = new GeplandeStoring($id, $traject, $bericht, $reden, $periode, $advies);
			$result[] = $geplandeStoring;
		}
		return $result;
	}
}
?>