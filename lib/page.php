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
 * FWikiPage
 * 
 * Represents a wiki page with
 * all necessary meta data.
 * 
 * @author Michael Neuhaus
 */
class FWikiPage
{
	/**
	 * Pagename
	 * 
	 * @var string
	 */
	private $_name;
	
	/**
	 * User object
	 * 
	 * @var FWikiUser
	 */
	private $_user;
	
	/**
	 *
	 * @var string
	 */
	private $_revision_warning = false;
	
	
	/**
	 * 
	 * 
	 * @param string $name Pagename
	 */
	public function __construct($name='', FWikiUser $user)
	{
		$this->_name = $name;
		
		$this->_user = $user;
	}
	
	/**
	 * Create page name by URL
	 */
	public function createNameByURL()
	{
		$queries = explode("&", getenv("QUERY_STRING"));
		$pagename = preg_replace("/=/", "", $queries[0]);
		if (empty($pagename)) $pagename = "Home";

		$this->_name = $pagename;
	}
	
	/**
	 * Handle post (page edited)
	 */
	public function handlePOST()
	{		
		if (!empty($_POST["edited"])){
		  if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
		    move_uploaded_file($_FILES['userfile']['tmp_name'], "upload/" . $this->_name . "_" . $_FILES['userfile']['name']);
		  }
		
		  if ($_POST["revision"]+1 == FWikiIO::get_revision_number($this)) $this->_revision_warning == true;
		
		  FWikiIO::write($this, $this->_user->getName(), stripslashes($_POST["WikiCode"]));
		}
	}
	
	/**
	 * Set page name
	 * 
	 * @param string $value
	 */
	public function setName($value)
	{		
		$this->_name = $value;	
	}
	
	/**
	 * Get page name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Revision warning
	 * 
	 * @return bool
	 */
	public function getRevisionWarning()
	{
		return $this->_revision_warning;
	}
}