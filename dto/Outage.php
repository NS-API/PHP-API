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
abstract class Outage
{
	private $id;
	private $line;
	private $message;
	private $cause;

	public function __construct($id, $line, $message, $cause)
	{
		$this->id = $id;
		$this->line= $line;
		$this->message = $message;
		$this->cause = $cause;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getLine()
	{
		return $this->line;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function getCause()
	{
		return $this->cause;
	}
}