<?php
/* FUCKUP-Wiki - A free Wiki Software
 * 
 * Copyright (C) 2011		Michael Neuhaus
 * Copyright (C) 2002-2004 	The FUCKUP-Wiki Project
 * Copyright (C) 2002-2004 	Mutwin Kraus and Lukas Bombach
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

require_once('config/settings.php');

/**
 * FWikiPassword
 * 
 * 
 * @author Michael Neuhaus
 */
class FWikiPassword
{
	
	/**
	 * Tests if the given password in plain 
	 * macht with the given hash.
	 * 
	 * @param string $plain Plain user password
	 * @param string $hash Hash vom user password
	 * @return bool
	 */
	public static function isEqual($plain, $hash)
	{
		$result = false;
		
		$test = hash( _PASSWORD_HASH_ALGO, $plain );
	
		if ($hash === $test) $result = true;

		return $result;
	}
	
	/**
	 * Returns hash for the given plain string.
	 * Hash algo depends on settings.
	 * 
	 * @param string $plain
	 * @return string Hash 
	 */
	public static function getHash($plain)
	{
		return hash( _PASSWORD_HASH_ALGO, $plain );
	}
}