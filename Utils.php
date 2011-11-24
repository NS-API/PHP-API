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
abstract class Utils
{
	public static function ISO8601Date2UnixTimestamp($iso8601)
	{
		return strtotime($iso8601);
	}

	public static function UnixTimestamp2ISO8601Date($unixTimestamp)
	{
		return date('c', $unixTimestamp);
	}

	public static function boolean2String($boolean)
	{
		return ($boolean ? "true" : "false");
	}

	public static function string2Boolean($string)
	{
		return strtolower($string) === "true";
	}
}
?>