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
 * FWikiUserList
 * 
 * Outputs all wiki user
 * 
 * @author Michael Neuhaus
 */
class FWikiUserList extends FWikiExtension
{
	
	/**
	 * 
	 */
	public function getOutput()
	{
		$out = "<ul>";
	  
		$dh = opendir("user");
		while ($file = readdir($dh)){
			if ($file != "." && $file != ".." && !preg_match("/^\./", $file)) {
				$name = preg_replace("/(.*)\.usr$/", "\\1", $file);
				$out .= "<li><a href=\"index.php?" . $name . "\">" . $name . "</a></li>";
			}
		}
		closedir($dh);
	  
		$out .= "</ul>";
	  
		return $out;
	}
}

?>
