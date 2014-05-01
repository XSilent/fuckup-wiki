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
 * FWikiLayout
 * 
 * 
 * @author Michael Neuhaus
 */
class FWikiLayout
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
	 * Functions
	 * 
	 * @var FWikiFunctions
	 */
	protected $func;
	
	/**
	 * Elements
	 * 
	 * @var FWikiElements
	 */
	protected $elements;
	
	/**
	 * Current layout
	 * 
	 * @var string
	 */
	protected $_layout;
	
	/**
	 * 
	 * @param FWikiUser $user
	 * @param FWikiPage $page
	 */
	public function __construct(FWikiUser $user, FWikiPage $page, FWikiFunctions $func, FWikiElements $elements)
	{
		$this->_user = $user;

		$this->_page = $page;
		
		$this->_func = $func;
		
		$this->_elements = $elements;
		
		$this->_selectLayout();
	}

	/**
	 * Get current layoutname
	 * 
	 * @return string
	 */
	public function getLayoutName()
	{
		return $this->_layout;
	}
	
	/**
	 * Select layout, depends on
	 * if user is logged in.
	 */
	protected function _selectLayout()
	{ 		
		if (!$this->_user->isAuthorized()){
			$this->_layout = _DEFAULT_LAYOUT;			
		} else {
			$this->_layout = $this->_user->getLayout();
		}
	}
	
	/**
	 * Build layout  
	 */
	public function render()
	{
		if (!file_exists("layout/" . $this->_layout . "/index.htm")){
			die ("<span style=\"color:red\">Critical Error:</span> Cannot find layout-file <i>layout/" . $this->_layout . "/index.htm</i>"); // Gibt probleme wenn der User eingeloggt ist und ein customdesign benutzt welches nicht mehr existiert
		}
 

		$fp = fopen("layout/" . $this->_layout . "/index.htm", "r");
		$out = '';
		while(!feof($fp)){
		  $out .= fgets($fp);
		}
		fclose($fp);
		
		$out = preg_replace("/\[Wikipage: \"([^\"]*)\"\]/ie", '$this->_func->show( new FWikiPage(\'\\1\') )', $out);
		$out = preg_replace("/\[Wikiuserpage: \"([^\"]*)\"\]/ie", '$this->_func->show_user_page( new FWikiPage(\'\\1\', $this->_user), $this->_user )', $out); 
		$out = preg_replace("/\[Wikielement: \"([^\"]*)\"\]/ie", '$this->_elements->WikiElements(\'\\1\')', $out);
		
		
		if (!preg_match("/^FWiki(.*)/i", $this->_page->getName(), $FWikiOut)){
			# Display wiki (content) page
			$v = '';
			if (isset($_GET["v"])) $v = $_GET["v"];
			if (isset($_GET["edit"]) && $_GET["edit"] == 1) {
				$out = preg_replace("/\[Wikimainpage\]/ie", '$this->_func->show_edit($this->_page, \''. $v . '\')', $out);
			} else {
				$out = preg_replace("/\[Wikimainpage\]/ie", '$this->_func->show($this->_page, true)', $out);	
			}
		    
		} else {
			# Display special wiki functionality
			if (file_exists("FWiki/" . $FWikiOut[1] . ".php")) {
		    	require_once("FWiki/" . $FWikiOut[1] . ".php");
		    	
		    	$fwikiName = 'FWiki' . $FWikiOut[1];
		    	$fwikiClass = new $fwikiName($this->_user, $this->_page);
		    	
		    	$out = preg_replace("/\[Wikimainpage\]/ie", '$fwikiClass->getOutput()', $out);
		  	} else {
		    	$out = preg_replace("/\[Wikimainpage\]/i", "<i>FWiki".$FWikiOut[1]."</i> konnte nicht gefunden werden.", $out);
		  	}
		}
		
		return $out;
	}
}