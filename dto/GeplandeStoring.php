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
require_once(dirname(__file__).'/Storing.php');

class GeplandeStoring extends Storing
{
	private $periode;
	private $advies;

	public function __construct($id, $traject, $bericht, $reden, $periode, $advies)
	{
		parent::__construct($id, $traject, $bericht, $reden);
		$this->periode = $periode;
		$this->advies = $advies;
	}

	public function getPeriode()
	{
		return $this->periode;
	}
	
	public function getAdvies()
	{
		return $this->advies;
	}
}
?>