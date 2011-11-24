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
require_once('dto/Melding.php');
require_once('dto/ReisDeel.php');

class ReisMogelijkheid
{
	private $aantalOverstappen;
	private $geplandeReisTijd;
	private $actueleReisTijd;
	private $optimaal;
	private $geplandeVertrekTijd;
	private $actueleVertrekTijd;
	private $geplandeAankomstTijd;
	private $actueleAankomstTijd;
	private $melding;
	private $reisDelen;

	public function __construct($aantalOverstappen, $geplandeReisTijd, $actueleReisTijd, $optimaal, $geplandeVertrekTijd, $actueleVertrekTijd, $geplandeAankomstTijd, $actueleAankomstTijd, $melding, $reisDelen)
	{
		$this->aantalOverstappen = $aantalOverstappen;
		$this->geplandeReisTijd = $geplandeReisTijd;
		$this->actueleReisTijd = $actueleReisTijd;
		$this->optimaal = $optimaal;
		$this->geplandeVertrekTijd = $geplandeVertrekTijd;
		$this->actueleVertrekTijd = $actueleVertrekTijd;
		$this->geplandeAankomstTijd = $geplandeAankomstTijd;
		$this->actueleAankomstTijd = $actueleAankomstTijd;
		$this->melding = $melding;
		$this->reisDelen = $reisDelen;
	}

	public function getAantalOverstappen()
	{
		return $this->aantalOverstappen;
	}

	public function getGeplandeReisTijd()
	{
		return $this->geplandeReisTijd;
	}

	public function getActueleReisTijd()
	{
		return $this->actueleReisTijd;
	}

	public function isOptimaal()
	{
		return $this->optimaal;
	}

	public function getGeplandeVertrekTijd()
	{
		return $this->geplandeVertrekTijd;
	}

	public function getActueleVertrekTijd()
	{
		return $this->actueleVertrekTijd;
	}

	public function getGeplandeAankomstTijd()
	{
		return $this->geplandeAankomstTijd;
	}

	public function getActueleAankomstTijd()
	{
		return $this->actueleAankomstTijd;
	}

	public function getMelding()
	{
		return $this->melding;
	}

	public function getReisDelen()
	{
		return $this->reisDelen;
	}
}
?>