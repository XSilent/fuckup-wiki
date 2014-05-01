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
 * FWikiChanges
 * 
 * Displays all changed wiki pages
 * (ordered by modification date)
 * 
 * @author Michael Neuhaus
 */
class FWikiChanges extends FWikiExtension
{
	
	/**
	 * 
	 */
	public function getOutput()
	{
	  $out = "<ul>";
	  
	  // Read out Wikidirectory
	  $dh = opendir("wikis");
	  while ($file = readdir($dh)){
		if ($file != "." && $file != ".." && !preg_match("/^\./", $file)){
			$files[] = preg_replace("/(.*)\.xml$/", "\\1", $file);
		}
	  }
	      
	  closedir($dh);
	  
	  // Sort files by date modified
	  usort($files, array('FWikiChanges', '_cmpDateModified'));
	  
	  // Get first "_DEFAULT_MAX_CHANGES" files
	  foreach($files as $i => $page){
		if($i>=_DEFAULT_MAX_CHANGES) break;
		
		$header = FWikiIO::get_latest_revision_header( new FWikiPage($files[$i], $this->_user) );        
		$files[$i] = htmlentities(rawurldecode($files[$i]));
		
		$out .= "<li><a href=\"index.php?" . $files[$i] . "\">" . $files[$i] . "</a>, ge&auml;ndert am " . date("d.m.y H:i:s", $header[3]) . " von <a href=\"index.php?" . $header[2] . "\">" . $header[2] . "</a></li>";  
	  }
	  
	  $out .= "</ul>";
	  
	  return $out;
	}
	
	/**
	 * Compares the timestamp of the 
	 * two files given by the xml name. 
	 * 
	 * @param string $a wiki xml filename 
	 * @param string $b wiki xml filename
	 * @return int
	 */
	private function _cmpDateModified($a, $b)
	{
	  $ta = filemtime("wikis/" . $a . ".xml");
	  $tb = filemtime("wikis/" . $b . ".xml");
	  
	  if ($ta == $tb) return 0;
	  return ($ta < $tb) ? 1 : -1;
	}

}

?>
