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
 * IFWikiExtension
 * 
 * Every FWiki extension must implement
 * this interface.
 * 
 * @author Michael Neuhaus
 */
interface IFWikiExtension
{
	/**
	 * Returns the output from the
	 * implemented wiki extension
	 * 
	 * @return string
	 */
	public function getOutput();
	
	/**
	 * Construtor 
	 * 
	 * @param FWikiUser $user
	 * @param FWikiPage $page
	 */
	public function __construct(FWikiUser $user=null, FWikiPage $page=null);
	
	/**
	 * Set user object
	 * 
	 * @param FWikiUser $user
	 */
	public function setUser(FWikiUser $user);
	
	/**
	 * Set page object
	 * 
	 * @param $page
	 */
	public function setPage(FWikiPage $page);
	
	/**
	 * Get user object
	 * 
	 * @return FWikiUser
	 */
	public function getUser();
	
	/**
	 * Get page object
	 * 
	 * @return FWikiPage
	 */
	public function getPage();
	
}