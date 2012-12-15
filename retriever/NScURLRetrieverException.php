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
require_once(dirname(__FILE__).'/../NSException.php');

class NScURLRetrieverException extends NSException
{
	/**
	 * When an NScURLRetrieverException is of this type, something went wrong with cURL.
	 * getCode() will return cURL's error code.
	 * getMessage() will return cURL's error message.
	 */
	const TYPE_CURL = "curl";
	
	/**
	 * When an NScURLRetrieverException is of this type, the returned XML was a soap fault.
	 * Usually, the soap fault messages from NS follow pattern <ERRNR>:<ERRMSG>.
	 * getCode() will return the <ERRNR> part (always a in digits), or NULL if the soap fault was not in the above pattern.
	 * getMessage() will return the <ERRMSG> part, or the complete soap fault if the soap fault was not in the above pattern.
	 */
	const TYPE_XML = "xml";

	/**
	 * Should either be TYPE_CURL or TYPE_XML.
	 */
	private $type;
	
	private $url;
	private $faultstring;

	public function __construct($type, $url, $faultstring = null, $faultcode = null)
	{
		$this->type = $type;
		$this->url = $url;
		$this->message = $faultstring;
		$this->code = $faultcode;
	}
	
	/**
	 * Returns the type of error. It's either TYPE_CURL or TYPE_XML. See there an explanation. 
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * Returns the URL that was requested.
	 */
	public function getUrl()
	{
		return $this->url;
	}
}
?>