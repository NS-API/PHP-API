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
class VertrekkendeTrein
{
	private $ritNummer;
	private $vertrekTijd;
	private $vertrekVertraging;
	private $vertrekVertragingTekst;
	private $eindBestemming;
	private $treinSoort;
	private $vertrekSpoor;
	private $vertrekSpoorGewijzigd;
	private $opmerkingen;

	public function __construct($ritNummer, $vertrekTijd, $vertrekVertraging, $vertrekVertragingTekst, $eindBestemming, $treinSoort, $vertrekSpoor, $vertrekSpoorGewijzigd, $opmerkingen)
	{
		$this->ritNummer = $ritNummer;
		$this->vertrekTijd = $vertrekTijd;
		$this->vertrekVertraging = $vertrekVertraging;
		$this->vertrekVertragingTekst = $vertrekVertragingTekst;
		$this->eindBestemming = $eindBestemming;
		$this->treinSoort = $treinSoort;
		$this->vertrekSpoor = $vertrekSpoor;
		$this->vertrekSpoorGewijzigd = $vertrekSpoorGewijzigd;
		$this->opmerkingen = $opmerkingen;
	}

	public function getRitNummer()
	{
		return $this->ritNummer;
	}

	public function getVertrekTijd()
	{
		return $this->vertrekTijd;
	}

	public function getVertrekVertraging()
	{
		return $this->vertrekVertraging;
	}

	public function getVertrekVertragingTekst()
	{
		return $this->vertrekVertragingTekst;
	}
	 
	public function getEindBestemming()
	{
		return $this->eindBestemming;
	}
	 
	public function getTreinSoort()
	{
		return $this->treinSoort;
	}
	 
	public function getVertrekSpoor()
	{
		return $this->vertrekSpoor;
	}

	public function isVertrekSpoorGewijzigd()
	{
		return $this->vertrekSpoorGewijzigd;
	}

	public function opmerkingen()
	{
		return $this->opmerkingen;
	}
}
?>