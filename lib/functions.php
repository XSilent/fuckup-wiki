<?php
/* FUCKUP-Wiki - A free Wiki Software
 * 
 * Copyright (C) 2011		Michael Neuhaus
 * Copyright (C) 2002-2004	The FUCKUP-Wiki Project
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
class FWikiFunctions
{
	/**
	 * Parser 
	 * 
	 * @var FWikiParser
	 */
	private $_parser;
	
	/**
	 * User
	 * 
	 * @var FWikiUser
	 */
	private $_user;
	
	/**
	 * 
	 * @param FWikiParser $parser
	 * @param FWikiUser $user
	 */
	public function __construct($parser=null, $user=null)
	{
		$this->_parser = $parser;
		
		$this->_user = $user;
	}
	
	/**
	 * Parser
	 * 
	 * @param FWikiParser $parser
	 */
	public function setParser(FWikiParser $parser)
	{
		$this->_parser = $parser;
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
	 * Show wiki page
	 * 
	 * @param FWikiPage $page
	 * @param bool $isMain
	 */
	public function show(FWikiPage $page, $isMain=false)
	{	
		$v = '';
		if (isset($_GET["v"])) $v = $_GET["v"];
	  
		if ($isMain){
			$revision = FWikiIO::get_revision($page, $v);
		} else {
	    	$revision = FWikiIO::get_revision($page);
		}
	    
		if ($revision == null){
			return "Diese Seite existiert bisher noch nicht... Aber das wird sich jetzt vielleicht <a href=\"index.php?" . $page->getName() . "&edit=1\">&auml;ndern</a> :)!";
		} else {
	    	return $this->_parser->parse( $revision->getText() );
		}
	}
	
	/**
	 * 
	 * @param FWikiPage $page
	 * @param FWikiUser $user
	 */
	public function show_user_page(FWikiPage $page, FWikiUser $user)
	{		  
		if ($this->_user->isAuthorized()){
			if (file_exists("wikis/" . $user->getName() . $page->getName() )) return show($this->_user->getName() . $page->getName() );
		}
		
		return $this->show($page);
	}
	
	/**
	 * 
	 * @param FWikiPage $page
	 * @param unknown_type $v
	 */
	public function show_edit(FWikiPage $page, $v)
	{	
		$revision = FWikiIO::get_revision($page, $v);
		
		$text = '';
		if ($revision != null) $text = $revision->getText();
		
	  	$out  = "<form enctype=\"multipart/form-data\" name=\"wiki_edit\" method=\"POST\" action=\"index.php?" . $page->getName() . "\">";
	  	$out .= "<textarea name=\"WikiCode\" rows=\"25\" cols=\"100\">" . $text . "</textarea>";
	  
	  	if ($revision == null){
	    	$out .= "<input type=\"hidden\" name=\"revision\" value=\"1\">";
	  	} else {
	    	$out .= "<input type=\"hidden\" name=\"revision\" value=\"" . $revision->getRevision() . "\">";
	  	}
	  
	  	$out .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"" . _MAX_UPLOAD_SIZE . "\">";  
	  	$out .= "<br>Upload: <input name=\"userfile\" type=\"file\"> ";
	  	$out .= "<input type=\"submit\" name=\"edited\" value=\"&Auml;ndern\">";
	  	$out .= "</form>";
	  
	  	return $out;
	}
	
	/**
	 * 
	 * @param string $string
	 */
	private function _correct_name($string)
	{
		return preg_replace("/([^\.]+)\.(.*)$/", "\\1." . $this->_getRandom(5) . ".\\2", $string);
	}
	
	/**
	 * Get random number 
	 * @param int $laenge
	 */
	private function _getRandom($laenge)
	{
	   	$abc = "a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9";
	   	$abcarray = explode(", ", $abc);
	   	mt_srand((double)microtime() * 1000000);
	   	$daspasswort = '';
	   	for ($i=1;$i<=$laenge;$i++){
			$zufall = mt_rand(0, 35);
			$daspasswort .= $abcarray[$zufall];
		}
		
		return $daspasswort;
	}
	
	/**
	 * Prevent calls to unknown methodes
	 * 
	 * @param string $name
	 * @param array $parameterArray
	 */
	public function __call($name, $parameterArray) 
	{
		throw new Exception('Method ' . $name . ' is not available!');
	}
	
}

?>