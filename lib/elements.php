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

/**
 * FWikiElements
 * 
 * Multiple parts of a wiki page
 *
 * @todo think about to split the private methods
 * 		 down to own classes an move FWikiElements 
 * 		 to a factory...
 * 
 * @author Michael Neuhaus / Mutwin Kraus and Lukas Bombach
 */
class FWikiElements
{
	/**
	 * User
	 * 
	 * @var FWikiUser
	 */
	private $_user;
	
	/**
	 * Page
	 * 
	 * @var FWikiPage
	 */
	private $_page;
	
	/**
	 * 
	 * @var bool
	 */
	private $revision_warning;
	
	
	/**
	 * 
	 * @param FWikiUser $use
	 * @param FWikiPage $page
	 * @param bool $revision_warning
	 */
	public function __construct(FWikiUser $user=null, FWikiPage $page=null)
	{
		$this->_user = $user;

		$this->_page = $page;
		
		$this->_revision_warning = $page->getRevisionWarning();
	}
	
	/**
	 * Choos element
	 * 
	 * @param string $name
	 */
	public function WikiElements($name)
	{
		// Call extension?	
		if (substr($name, 0, 5) == 'FWiki' ) return $this->_extension_call($name);
		
	  	switch($name)
	  	{	   	  
	    	case "Login-menu":
	      		return $this->_login_menu();
	      		break;
	      
	    	case "Revision-chooser":
	      		return $this->_revision_chooser();
	      		break;
	      
	    	case "Revision-warning":
	      		return $this->_revision_warning();
	      		break;	     	    
	      
	    	default:
	      		return "[Wikielement: \"" . $name . "\"]";
	  }
	}

	/**
	 * Wikielement makes use of wiki extension
	 */
	private function _extension_call($name)
	{
		$classfile = substr($name, 5, strlen($name) - 4);
		require_once('FWiki/' . $classfile . '.php');
		$wikiClass = new $name();

		return $wikiClass->getOutput();
	}
	
	/**
	 * 
	 */
	private function _login_menu()
	{	 	
	  if (!$this->_user->isAuthorized()) {
		$out  = "<form name=\"login\" method=\"POST\" action=\"index.php?" . $this->_page->getName() . "\">";
		
		if ($this->_user->isName_err()) {
			$out .= "<span class=\"error\">Login: </span>";
		} else {
			$out .= "Login: ";
		}
		$loginName = '';
		if (isset($_POST["username"])) $loginName = $_POST["username"];
		
		$out .= "<input name=\"username\" value=\"". $loginName ."\"> ";
		
		if ($this->_user->isPass_err()) {
			$out .= "<span class=\"error\">Pass: </span>";
		} else { 
			$out .= "Pass: ";  
		}
		
		$loginPass = '';
		if (isset($_POST["login_pass"])) $loginPass = $_POST["login_pass"];
		
		$out .= "<input type=\"password\" name=\"password\" value=\"". $loginPass."\">";
		
		$out .= "<input type=\"submit\" name=\"login_sent\" value=\"Login\">";
		$out .= "</form>";
	
		return $out;
	  } else {
	    return "Eingeloggt als <a href=\"index.php?" . $this->_user->getName() . "\">" . $this->_user->getName() . "</a> - <a href=\"index.php?" . $this->_page->getName() . "&logout=1\">Ausloggen</a>";
	  }
	}
	
	/**
	 * 
	 */
	private function _revision_chooser()
	{
	  $header = FWikiIO::get_latest_revision_header($this->_page);
	  
	  $out  = "<form action=\"index.php\" method=\"GET\">";
	  $out .= "Gehe zu Revision: ";
	  $out .= "<input type=\"hidden\" name=\"" . $this->_page->getName() . "\">";
	  $out .= "<input name=\"v\" value=\"" . $header[1] . "\" size=\"3\">";
	  $out .= "<input type=\"submit\" value=\"Go!\">";
	  $out .= "</form>";
	  
	  return $out;
	}
	
	/**
	 * 
	 */
	private function _revision_warning()
	{
	  $out = '';
	  
	  if($this->_revision_warning == true) {
	    $out  = "<div class=\"warning\">Warnung: Ein anderer Benutzer hat diese ";
	    $out .= "Seite gerade eben editiert, seine Version wurde &uuml;berschrieben ";
	    $out .= "(oder Du hast 2 mal auf '&Auml;ndern' gedr&uuml;ckt)! ";
	    $out .= "Bitte nimm dir eine Minute und vergleiche seine Version ";
	    $out .= "(die Voraktuellste) mit Deiner.</div>";
	  }
	  
	  return $out;
	}
}

?>
