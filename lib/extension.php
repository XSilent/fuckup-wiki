<?php
/* FUCKUP-Wiki - A free Wiki Software
 * 
 * Copyricht (C) 2011 		Michael Neuhaus
 * Copyright (C) 2002-2004	The FUCKUP-Wiki Project
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

require_once('lib/interfaces/extension.php');

/**
 * FWikiExtension
 * 
 * Base class for all wiki extensions
 * 
 * @author Michael Neuhaus 
 */
abstract class FWikiExtension implements IFWikiExtension
{
	/**
	 * User object
	 * 
	 * @var FWikiUser
	 */
	protected $_user;
	
	/**
	 * Page object
	 * 
	 * @var FWikiPage
	 */
	protected $_page;

	/**
	 * 
	 * @param FWikiUser $user
	 * @param FWikiPage $page
	 */
	public function __construct(FWikiUser $user=null, FWikiPage $page=null)
	{
		$this->_user = $user;
		
		$this->_page = $page;
	}
	
	/**
	 * User
	 * 
	 * @param FWikiUser $user
	 */
	public function setUser(FWikiUser $user)
	{
		$this->_user = $user;	
	}
	
	/**
	 * Page
	 * 
	 * @param FWikiPage $page
	 */
	public function setPage(FWikiPage $page)
	{
		$this->_page = $page;	
	}
	
	/**
	 * @return FWikiUser
	 */
	public function getUser()
	{
		return $this->_user;
	}
	
	/**
	 * @return FWikiPage
	 */
	public function getPage()
	{
		return $this->_page;
	}
}