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

require_once('lib/extension.php');

/**
 * FWikiTest - Extension
 * 
 * This is just a test/example extension.
 * 
 * Every extension must be a subclass 
 * from FWikiExtension
 * 
 * @author Michael Neuhaus
 */
class FWikiExample extends FWikiExtension
{
	
	/**
	 * Every extension must implement
	 * this method and return the 
	 * extensions output. 
	 */
	public function getOutput()
	{
		return 'Output from a FWiki extension';
	}	
}

?>
