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
require_once(dirname(__FILE__).'/dto/DepartingTrain.php');
require_once(dirname(__FILE__).'/dto/TravelOption.php');
require_once(dirname(__FILE__).'/dto/PlannedOutage.php');
require_once(dirname(__FILE__).'/dto/UnplannedOutage.php');

require_once(dirname(__FILE__).'/cache/Cache.php');
require_once(dirname(__FILE__).'/cache/NoCache.php');
require_once(dirname(__FILE__).'/cache/FileCache.php');
require_once(dirname(__FILE__).'/cache/MySQLCache.php');

require_once(dirname(__FILE__).'/retriever/Retriever.php');
require_once(dirname(__FILE__).'/retriever/cURLRetriever.php');

class NS {
	private $cache;

	public function __construct($cache) {
		$this->cache = $cache;
	}
	
	public function getCache() {
		return $this->cache;
	}

	public function getStationByCode($code) {
		$stations = $this->getStations();
		foreach ($stations as $station) {
			if ($station->isAlias()) {
				continue;
			}

			if ($station->getCode() === $code) {
				return $station;
			}
		}
	}

    public function getStationsByCoordinates($longitude, $latitude, $maxDiff) {
        $stations = $this->getStations();
        $result = array();
        foreach ($stations as $station) {
            if ($station->isAlias()) {
                continue;
            }

            $diff = Utils::distanceBetweenPoints($latitude, $longitude, $station->getLatitude(), $station->getLongitude(), 'Km');
            if (intval($diff) < intval($maxDiff)) {
                $result[] = $station;
            }
        }
        return $result;
    }

    public function getStationNamesByCoordinates($longitude, $latitude, $maxDiff) {
        $stations = $this->getStationsByCoordinates($longitude, $latitude, $maxDiff);
        $result = array();
        foreach ($stations as $station) {
            $result[] = $station->getName();
        }
        return $result;
    }

    public function getStationByStationName($stationName) {
        $result = null;
        $stations = $this->getStations();
        foreach($stations as $station) {
            if ($station->getName() == $stationName) {
                $result = $station;
            }
        }
        return $result;
    }

	public function getStations() {
		$xml = $this->cache->getStations();
		$xml = new SimpleXMLElement($xml);

		$result = array();
		foreach ($xml->station as $xmlStation) {
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

	public function getRates($fromStation, $toStation, $viaStation = null, $dateTime = null) {
		$xml = $this->cache->getRates($fromStation, $toStation, $viaStation, $dateTime);
		$xml = new SimpleXMLElement($xml);

		$result = array();
		foreach ($xml->Product as $xmlProduct) {
			$name = (string)$xmlProduct['naam'];

			$rates = array();
			foreach ($xmlProduct->Prijs as $xmlRate) {
				$discount = (string)$xmlRate['korting'];
				$class = (string)$xmlRate['klasse'];
				$rate = (string)$xmlRate;
				$rate = new Rate($discount, $class, $rate);
				$rates[] = $rate;
			}
			$product = new Product($name, $rates);
			$result[] = $product;
		}
		return $result;
	}

    public function getActualDepartureTimesByStationName($stationName) {
        return $this->getActualDepartureTimes($this->getStationByStationName($stationName));
    }

	public function getActualDepartureTimes($station) {
		$xml = $this->cache->getActualDepartureTimes($station);
		$xml = new SimpleXMLElement($xml);

		$result = array();
		foreach ($xml->VertrekkendeTrein as $xmlDepartingTrain) {
			$shiftNumber = (string) $xmlDepartingTrain->RitNummer;
			$departureTime = Utils::ISO8601Date2UnixTimestamp($xmlDepartingTrain->VertrekTijd);
			$departureDelay = NULL;
			$departureDelayText = NULL;
			if ($xmlDepartingTrain->VertrekVertraging !== NULL && (string)$xmlDepartingTrain->VertrekVertraging !== "") {
				$departureDelay = Utils::ISO8601Period2UnixTimestamp($xmlDepartingTrain->VertrekVertraging, $departureTime);
				$departureDelayText = (string) $xmlDepartingTrain->VertrekVertragingTekst;
			}
			$finalDestination = (string) $xmlDepartingTrain->EindBestemming;
			$trainType = (string) $xmlDepartingTrain->TreinSoort;
			$departureTrack = (string) $xmlDepartingTrain->VertrekSpoor;
			$departureTrackChanged = Utils::string2Boolean($xmlDepartingTrain->VertrekSpoor['wijziging']);

			$remarks = array();
			if ($xmlDepartingTrain->Opmerkingen->Opmerking !== NULL) {
				foreach ($xmlDepartingTrain->Opmerkingen->Opmerking as $xmlOpmerking) {
					$remarks[] = trim((string) $xmlOpmerking);
				}
			}
			$departingTrain = new DepartingTrain(
                $shiftNumber, $departureTime, $departureDelay, $departureDelayText,
                $finalDestination, $trainType, $departureTrack, $departureTrackChanged, $remarks
            );
			$result[] = $departingTrain;
		}
		return $result;
	}

	public function getTrainscheduler($fromStation, $toStation, $viaStation = null, $previousAdvices = null, $nextAdvices = null, $dateTime = null, $departure = null, $hslAllowed = null, $yearCard = null) {
		$xml = $this->cache->getTrainscheduler($fromStation, $toStation, $viaStation, $previousAdvices, $nextAdvices, $dateTime, $departure, $hslAllowed, $yearCard);
		$xml = new SimpleXMLElement($xml);

		$result = array();
		foreach ($xml->TravelOption as $xmlTravelOption) {
			$numberOfChanges = (string)$xmlTravelOption->AantalOverstappen;
			$scheduledTravelTime = Utils::ISO8601Date2UnixTimestamp($xmlTravelOption->GeplandeReisTijd);
			$actualTravelTime = Utils::ISO8601Date2UnixTimestamp($xmlTravelOption->ActueleReisTijd);
			$optimal = Utils::string2Boolean($xmlTravelOption->Optimaal);
			$plannedDepartureTime = Utils::ISO8601Date2UnixTimestamp($xmlTravelOption->GeplandeVertrekTijd);
			$actualDepartureTime = Utils::ISO8601Date2UnixTimestamp($xmlTravelOption->ActueleVertrekTijd);
			$plannedArrivalTime = Utils::ISO8601Date2UnixTimestamp($xmlTravelOption->GeplandeAankomstTijd);
			$actualArrivalTime = Utils::ISO8601Date2UnixTimestamp($xmlTravelOption->ActueleAankomstTijd);

			$alert = NULL;
			if ($xmlTravelOption->Alert->Id !== NULL) {
				$xmlAlert = $xmlTravelOption->Alert;
				$id = (string)$xmlAlert->Id;
				$serious = Utils::string2Boolean($xmlAlert->Serious);
				$text = (string)$xmlAlert->Text;
				$alert = new Alert($id, $serious, $text);
			}

			$travelParts = array();
			foreach ($xmlTravelOption->ReisDeel as $xmlTravelPart) {
				$travelType = (string)$xmlTravelPart['travelType'];
				$transportationType = (string)$xmlTravelPart->VervoerType;
				$shiftNumber = (string)$xmlTravelPart->RitNummer;

				$travelStops = array();
				foreach ($xmlTravelPart->ReisStop as $xmlTravelStop) {
					$name = (string)$xmlTravelStop->Naam;
					$time = Utils::ISO8601Date2UnixTimestamp($xmlTravelStop->Tijd);
					$track = (string)$xmlTravelStop->Spoor;
					$trackChange = Utils::string2Boolean($xmlTravelStop->Spoor['wijziging']);
					$travelStop = new TravelStop($name, $time, $track, $trackChange);
					$travelStops[] = $travelStop;
				}
				$tavelPart = new TravelPart($travelType, $transportationType, $shiftNumber, $travelStops);
				$travelParts[] = $tavelPart;
			}
			$travelOption = new TravelOption($numberOfChanges, $scheduledTravelTime, $actualTravelTime, $optimal, $plannedDepartureTime, $actualDepartureTime, $plannedArrivalTime, $actualArrivalTime, $alert, $travelParts);
			$result[] = $travelOption;
		}
		return $result;
	}

	public function getOutages($station, $actual = null, $unplanned = null) {
		$result = array();
		$xml = $this->cache->getOutages($station, $actual, $unplanned);
		$xml = new SimpleXMLElement($xml);

		foreach ($xml->Ongepland->Storing as $xmlUnplannedOutage) {
			$id = (string)$xmlUnplannedOutage->id;
			$line = (string)$xmlUnplannedOutage->Line;
			$cause = (string)$xmlUnplannedOutage->Cause;
			$message = (string)$xmlUnplannedOutage->Message;
			$date = Utils::ISO8601Date2UnixTimestamp($xmlUnplannedOutage->Date);
			$unplannedOutage = new UnplannedOutage($id, $line, $message, $cause, $date);
			$result[] = $unplannedOutage;
		}

		foreach ($xml->Gepland->Storing as $xmlPlannedOutage) {
			$id = (string)$xmlPlannedOutage->id;
			$line = (string)$xmlPlannedOutage->Line;
			$interval = (string)$xmlPlannedOutage->Interval;
			$cause = (string)$xmlPlannedOutage->Cause;
			$advice = (string)$xmlPlannedOutage->Advice;
			$message = (string)$xmlPlannedOutage->Message;
			$plannedOutage = new PlannedOutage($id, $line, $message, $cause, $interval, $advice);
			$result[] = $plannedOutage;
		}
		return $result;
	}

}