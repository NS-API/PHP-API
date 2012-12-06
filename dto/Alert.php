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
class Alert
{
	private $id;
	private $serious;
	private $text;

	public function __construct($id, $serious, $text)
	{
		$this->id = $id;
		$this->serious = $serious;
		$this->text = $text;
	}

	public function getId()
	{
		return $this->id;
	}

	public function isSerious()
	{
		return $this->serious;
	}

	public function getText()
	{
		return $this->text;
	}
}