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

/**
 * FWikiIO
 * 
 * @author Michael Neuhaus / Mutwin Kraus and Lukas Bombach
 */
class FWikiIO
{

	public static function get_revision(FWikiPage $page, $revision=-1)
	{
		$user = '';
		$timestamp = 0;
		$text = '';
		
		if (!file_exists("wikis/" . $page->getName() . ".xml")){		
			return null;
		} else {
		    $fp = fopen("wikis/" . $page->getName() . ".xml", "r");
		    
		    // Get revision header
		    if ($revision==-1 OR empty($revision)){
		      $revinfos = preg_replace("/<REVISION-([0-9]*) user=\"([^\"]*)\" timestamp=\"([^\"]*)\">(.*)/", "\\1[:-)=:]\\2[:-)=:]\\3[:-)=:]\\4", fgets($fp));
		      $revinfo = explode("[:-)=:]", $revinfos);
		      // $revinfo = get_latest_revision_header($page->getName()); Warum geht das nicht?
		      $revision = $revinfo[0];
		      $user = $revinfo[1];
		      $timestamp = $revinfo[2];
		      $text = $revinfo[3];
		    } else {
		      do {
		        $line = fgets($fp, 4096);
		      } while(!feof($fp) && !preg_match("/<REVISION-" . $revision . " user=\"([^\"]*)\" timestamp=\"([^\"]*)\">(.*)/", $line, $out));
		        
		      if (isset($out[1])) $user = $out[1];
		      if (isset($out[2])) $timestamp = $out[2];
		      if (isset($out[3])) $text = $out[3]; 
		    }
	    
	    	// Get revision body
	    	while(!feof($fp)){
		      $line = fgets($fp);
		      if(preg_match("/<\/REVISION-".$revision.">/", $line)) break;
		      $text .= $line;
		    }
		    fclose($fp);
	    
		    // Return object containing all revision-informations
		    require_once('lib/revision.php');
		    
	    	return new FWikiRevision($revision, $user, $timestamp, preg_replace("/\n$/", "", $text));    
	  	}
	}
	
	public static function write(FWikiPage $page, $username, $text)
	{
		// Create file if not exists
		if (!file_exists("wikis/" . $page->getName() . ".xml")){
			$fp = fopen("wikis/" . $page->getName() . ".xml", "w"); fclose($fp);
		}
		  
	  	// Open file  
	  	$fp = fopen("wikis/" . $page->getName() .".xml", "r+");
	  
		// Fill variables
		$time = time();
		$revision = FWikiIO::get_revision_number( $page );
		$revision++;
		
		if(empty($username)) $user = "guest";
	  
		// Read out file
		$OldWiki = file_get_contents("wikis/". $page->getName() . ".xml");
	  
		// Write new file                 // Hier sollte ein \n zwischen header und text... mal in ner anderen Version testen ob das so einfach umzustellen ist
		fputs($fp, "<REVISION-" . $revision." user=\"" . $username . "\" timestamp=\"" . $time . "\">" . $text . "\n</REVISION-" . $revision . ">\n" . $OldWiki);
	
		// Close file
		fclose($fp);
	}
	
	
	public static function get_revision_number(FWikiPage $page)
	{
		if (file_exists("wikis/" . $page->getName() .".xml")){
			$fp = fopen("wikis/" . $page->getName() .".xml", "r");
			$rev = preg_replace("/<REVISION-([0-9]*) .*/", "\\1", trim(fgets($fp)));
			fclose($fp);
			
			return $rev;
		}
	}
	
	public static function get_latest_revision_header(FWikiPage $page)
	{
		if (!file_exists("wikis/" . $page->getName() . ".xml")){
			return null;
		} else {
			$fp = fopen("wikis/" . $page->getName() . ".xml", "r");
	    
			preg_match("/<REVISION-([0-9]*) user=\"([^\"]*)\" timestamp=\"([^\"]*)\">(.*)/", fgets($fp), $out);
	    
			fclose($fp);
		
			return $out;
	  	}    
	}
	
	
	public static function get_first_revision_header(FWikiPage $page)   /* ALPHA + Unusable */
	{
		if (!file_exists("wikis/". $page->getName() .".xml")) return null;
	  
		$fp = fopen("wikis/" . $page->getName() . ".xml", "r");
		fseek($fp, 0, SEEK_END);
	  
		while (!$header_found){
			fseek($fp, -1, SEEK_CUR);
			if (preg_match("/(\n|\r)/", fgetc($fp))) {
	      		$pos = ftell($fp);
	      		$line = fgets($fp);
	      		if (preg_match("/<REVISION-1 user=\"([^\"]*)\" timestamp=\"([^\"]*)\">(.*)/", $line, $out) OR preg_match("/<REVISION-1>/", $line))
	        	$header_found = true;
	      		fseek($fp, $pos-3);
	    	}
	  	}
	  
		return $out;
	}
}


?>
