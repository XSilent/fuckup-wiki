<?php
/* FUCKUP-Wiki - A free Wiki Software
 *
 * Copyright (C) 2011		Michael Neuhaus
 * Copyright (C) 2002-2004 	The FUCKUP-Wiki Project
 * Copyright (C) 2002-2004	Mutwin Kraus and Lukas Bombach
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

/**
 * FWikiRevision
 * 
 * 
 * @author Michael Neuhaus
 */
class FWikiRevision
{
	/**
	 * Revision nr
	 * @var int
	 */
	private $_revision;
  
	/**
	 * User
	 * 
	 * @var FWikiUser
	 */
	private $_user;
  
	/**
	 * Timestamp of revision
	 * 
	 * @var int
	 */
	private $_timestamp;
  
	/**
	 * Text of current revision
	 * 
	 * @var string
	 */
	private $_text;
  
	public function __construct($revision, $user, $timestamp, $text)
  	{
    	$this->_revision	= $revision;
    	$this->_user		= $user;
    	$this->_timestamp	= $timestamp;
    	$this->_text 		= $text;
  	}
  	
  	/**
  	 * @return string
  	 */
  	public function getText()
  	{
 		return $this->_text; 		
  	}
  	
  	/**
  	 * @return int
  	 */
  	public function getRevision()
  	{
  		return $this->_revision;	
  	}
  	
  	/**
  	 * @return int
  	 */
  	public function getTimestamp()
  	{
 		return $this->_timestamp; 		
  	}
  	
}