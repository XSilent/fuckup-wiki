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
 * FWikiList
 * 
 * List of all existing wiki pages
 * 
 * @author Michael Neuhaus
 */
class FWikiList extends FWikiExtension
{
	
	/**
	 * 
	 */
	public function getOutput()
	{
	  $out = "<ul>";
	  
	  $dh = opendir("wikis");
	  
	  while ($file = readdir($dh)){
	  	     
	    if ($file != "." && $file != ".." && !preg_match("/^\./", $file)){
	    	 
	        $file = htmlentities(rawurldecode(preg_replace("/(.*)\.xml$/", "\\1", $file)));
	        
	        $out .= "<li> <a href=\"index.php?".$file."\">".$file."</a></li>";
	    } 
	  }
	  
	  closedir($dh);
	  
	  $out .= "</ul>";
	  
	  return $out;
	}

}

?>
