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
 * FWikiParser
 * 
 * @author Michael Neuhaus / Mutwin Kraus and Lukas Bombach
 */
class FWikiParser
{

	/**
	 * 
	 * @var array
	 */
	protected $nowiki;
	
	/**
	 * 
	 * @var int
	 */
	protected $i;
	
	/**
	 * Page
	 * 
	 * @var FWikiPage
	 */
	protected $_page;
	
	/**
	 * FWikiUser
	 * 
	 * @var user
	 */
	protected $_user;


	public function __construct($user=null, $page=null)
  	{
  		$this->_user = $user;
  		$this->_page = $page;
  		
    	$this->nowiki = array();
		$this->i = 0;
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
  	 * Page
  	 * 
  	 * @param FWikiPage $page
  	 */
  	public function setPage(FWikiPage $page)
  	{
  		$this->_page = $page;
  	}
  	
  	public function parse($string)
  	{
	    $string = $this->extract_no_wiki($string);
	
		$string = $this->prepare_returns($string);
		$string = $this->work_html($string);
		$string = $this->underline($string);
		$string = $this->italic($string);
		$string = $this->bold($string);
		$string = $this->find_list($string);
		$string = $this->fix_html($string);
		$string = $this->find_table($string);
		$string = $this->find_newsletter_quote($string);
	    $string = $this->headline($string);
		$string = $this->center($string);
		$string = $this->code($string);
		$string = $this->hr($string);
		$string = $this->link_wiki($string);
		$string = $this->link_labeled_wiki($string);
		$string = $this->auto_link_url($string);
		$string = $this->auto_link_email($string);
		$string = $this->link_labeled_url($string);
	    $string = $this->link_labeled_email($string);
		$string = $this->image($string);
		$string = $this->fix_returns($string);
		$string = $this->show_page($string);
	
		$string = $this->insert_no_wiki($string);

		return $string;
	}
  
  	private function prepare_returns($string)
  	{
    	$string = preg_replace("/\r\n/", "\n", $string);
   		return preg_replace("/\r/", "\n", $string);  
  	}

  	private function bold($string, $sep="\"\"")
  	{
		return preg_replace("/".$sep."(.+)".$sep."/U", "<b>\\1</b>", $string);
  	}

  	private function italic($string, $sep="\"\"\"")
  	{
    	return preg_replace("/".$sep."(.+)".$sep."/U", "<i>\\1</i>", $string);
  	}

  	private function underline($string, $sep="\"\"\"\"")
  	{
    	return preg_replace("/".$sep."(.+)".$sep."/U", "<u>\\1</u>", $string);
 	}
  
  	private function show_page($string)
  	{
     	return preg_replace("/\[show: ([^\]]+)\]/ie", "\$this->view_page('\\1')", $string);
  	}
  
    private function view_page($string)
    {      
      	$implemented .= ",".$this->work_html(rawurldecode( $page->getName() ));
      
      	if (preg_match("/" . $string . "/", $implemented)) return "[show: " . $string . "]";
      
      	$implemented .= "," . $string;
      	
      	return show(rawurlencode($this->undo_html($string)));
    }

	private function find_list($string)
	{	
      	return preg_replace("/\n(\*.*)\n([^\*])/Use", "\$this->do_list('\\1', '\\2')", $string);
	}
    
	private function do_list($string, $s)
	{
		$string = preg_replace("/\*([^\n]*)\n?/", "<li>\\1</li>", $string);
		return "\n<ul class=\"WikiUL\">".$string."</ul>".$s;
	}
      
	private function find_table($string)
	{	
		return preg_replace("/\n(\|.*)\n([^\|])/Use", "\$this->do_table('\\1', '\\2')", $string);
	}
	
	private function do_table($string, $s)
    {        
		$out  = "<table class=\"WikiTable\" border=\"1\">";
        
        $string = preg_replace("/^\|/", "<tr><td>", $string);
        $string = preg_replace("/\|\n\|/", "</td></tr><tr><td>", $string);
        $string = preg_replace("/\|$/", "</td></tr>", $string);
        $string = preg_replace("/\|/", "</td><td>", $string);
		
		$out .= $string;
		$out .= "</table>";
		
		return $out.$s;
      }
    
    private function find_newsletter_quote($string)
    { 
	  	$string = preg_replace("/\n\s{0,1}&gt;/", "\n>", $string);
		
	  	return preg_replace("/(\n>.*\n)([^>])/Use", "\$this->do_newsletter_quote('\\1', '\\2')", $string);
    }
    
	private function do_newsletter_quote($string, $s)
	{        
        $out  = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
        $out .= "<tr><td width=\"25\" style=\"background-image:url('upload/wikifiles/newsletter_quote.gif'); background-repeat:repeat-y;\">";
		$out .= "<img src=\"upload/wikifiles/newsletter_quote.gif\" width=\"25\" height=\"1\"></td>";
		$string = preg_replace("/\n>/", "\n", $string);
		$out .= "<td>".ltrim($this->find_newsletter_quote($string))."</td></tr></table>";
		
		return $out.$s;
	}

	private function center($string)
	{
		return preg_replace("/\[center\](.*)\[\/center\]/iU", "<center>\\1</center>", $string);
	}
  
	private function code($string)
	{
		return preg_replace("/\[code\](.*)\[\/code\]/iUs", "<pre class=\"code\">\\1</pre>", $string);
	}

	private function headline($string)
	{
    	$string = preg_replace("/={4}(.+)={4}\n/U", "<h4 class=\"WikiH4\">\\1</h4>\n", $string);
    	$string = preg_replace("/={3}(.+)={3}\n/U", "<h3 class=\"WikiH3\">\\1</h3>\n", $string);
    	$string = preg_replace("/={2}(.+)={2}\n/U", "<h2 class=\"WikiH2\">\\1</h2>\n", $string);
    	$string = preg_replace("/={1}(.+)={1}\n/U", "<h1 class=\"WikiH1\">\\1</h1>\n", $string);
    	
		return $string;
  	}

  	private function hr($string)
  	{
		return preg_replace("/\[hr\]/", "<hr>", $string);
  	}

  	private function image($string)
  	{                             
    	$string = preg_replace("/\[img src=<a href=\"([^\]]+)]\" target=\"_blank\">\\1\]<\/a>/i", "<img src=\"\\1\">", $string);

    	return    preg_replace("/\[img src=([^\]]+)\]/i", "<img src=\"upload/" . $this->_page->getName() . "_\\1\">", $string);
  	}

  	private function link_wiki($string)
  	{ 	    
    	if ($this->_user->isAuthorized()){
	  		$string = preg_replace("/\[\[FWikiExtUser\|([^\|\]]*)\|([^\]]*)\]\]/i", "<a href=\"index.php?FWikiExtUser\">\\1</a>", $string);
	  		$string = preg_replace("/\[\[FWikiExtUser\]\]/i", "<a href=\"index.php?FWikiExtUser\">Benutzer-Einstellungen</a>", $string);
    	} else {
	  		$string = preg_replace("/\[\[FWikiExtUser\|([^\|\]]*)\|([^\]]*)\]\]/i", "<a href=\"index.php?FWikiExtUser\">\\2</a>", $string);
	  		$string = preg_replace("/\[\[FWikiExtUser\]\]/i", "<a href=\"index.php?FWikiExtUser\">Anmelden</a>", $string);
    	}
    	
    	$ver = '';
    	if (!empty($_GET["v"])) $ver = "&v=".$_GET["v"];
    	$string = preg_replace("/\[\[FWikiEdit\]\]/i", "<a href=\"index.php?" . $this->_page->getName() . "&edit=1" . $ver . "\">&Auml;ndern</a>", $string);
    
    	return preg_replace("/\[\[([^\]\|]*)\]\]/", "<a href=\"index.php?\\1\">\\1</a>", $string);
	}

	function link_labeled_wiki($string)
	{	    
		if(!empty($_GET["v"])) $ver = "&v=".$_GET["v"];
	    $string = preg_replace("/\[\[FWikiEdit\|([^\]]*)\]\]/i", "<a href=\"index.php?". $this->_page->getName() ."&edit=1" . $ver . "\">\\1</a>", $string);
	    
	    return preg_replace("/\[\[([^\|@]*)\|([^\]]*)\]\]/", "<a href=\"index.php?\\1\">\\2</a>", $string);
	}
  
  	private function link_labeled_url($string)
  	{
		return preg_replace("/\[\[\@<a href=\"([^\"]*)\" target=\"_blank\">\\1<\/a>\|([^\]]*)\]\]/", "<a href=\"\\1\" target=\"_blank\">\\2</a>", $string);
  	}

  	private function link_labeled_email($string)
  	{
    	return preg_replace("/\[\[<a href=\"mailto:([^\"]+)\">\\1<\/a>\|([^\]]*)\]\]/", "<a href=\"mailto:\\1\">\\2</a>", $string);
  	}

  	private function auto_link_url($string)
  	{
    	return preg_replace("/(\w+):\/\/([^\s\|]+)/", "<a href=\"\\1://\\2\" target=\"_blank\">\\1://\\2</a>", $string);
  	}

  	private function auto_link_email($string)
  	{
    	return preg_replace("/([a-zA-Z0-9-_\.]+@[a-zA-Z0-9]*\.[a-zA-Z0-9]{1,5})/", "<a href=\"mailto:\\1\">\\1</a>", $string);
  	}

  	private function fix_html($string)
  	{
    	$search = Array("/\"/");

    	$replace = Array("&quot;");
    
    	return preg_replace($search, $replace, $string);
  	}

  	private function fix_returns($string)
  	{
    	return preg_replace("/\n/", "<br>\n", $string);
  	}

  	private function extract_no_wiki($string)
  	{
		preg_match_all("/∞(.*)∞/Us", $string, $this->nowiki);
	
		return preg_replace("/∞(.*)∞/Us", "[nowiki-placemarker]", $string);
  	}

  	private function insert_no_wiki($string)
  	{
    	return preg_replace("/\[nowiki-placemarker\]/e", "\$this->get_nowiki()", $string);
  	}

	private function get_nowiki()
	{
		$string = $this->nowiki[1][$this->i];
		$string = $this->work_html($string);
		$string = $this->fix_returns($string);
		$string = $this->fix_html($string);
		$this->i = $this->i + 1;
		
		return $string;
	}

  	private function work_html($string)
  	{
	     $search = array(
              "/&/",
              "/°/",
              "/¢/",
              "/£/",
              "/§/",
              "/•/",
              "/¶/",
              "/ß/",
              "/®/",
              "/©/",
              "/™/",
              "/´/",
              "/¨/",
              "/≠/",
              "/Æ/",
              "/Ø/",
              "/∞/",
              "/±/",
              "/≤/",
              "/≥/",
              "/¥/",
              "/µ/",
              "/∂/",
              "/∑/",
              "/∏/",
              "/π/",
              "/∫/",
              "/ª/",
              "/º/",
              "/Ω/",
              "/æ/",
              "/ø/",
              "/¿/",
              "/¡/",
              "/¬/",
              "/√/",
              "/ƒ/",
              "/≈/",
              "/∆/",
              "/«/",
              "/»/",
              "/…/",
              "/ /",
              "/À/",
              "/Ã/",
              "/Õ/",
              "/Œ/",
              "/œ/",
              "/–/",
              "/—/",
              "/“/",
              "/”/",
              "/‘/",
              "/’/",
              "/÷/",
              "/◊/",
              "/ÿ/",
              "/Ÿ/",
              "/⁄/",
              "/€/",
              "/‹/",
              "/›/",
              "/ﬁ/",
              "/ﬂ/",
              "/‡/",
              "/·/",
              "/‚/",
              "/„/",
              "/‰/",
              "/Â/",
              "/Ê/",
              "/Á/",
              "/Ë/",
              "/È/",
              "/Í/",
              "/Î/",
              "/Ï/",
              "/Ì/",
              "/Ó/",
              "/Ô/",
              "//",
              "/Ò/",
              "/Ú/",
              "/Û/",
              "/Ù/",
              "/ı/",
              "/ˆ/",
              "/˜/",
              "/¯/",
              "/˘/",
              "/˙/",
              "/˚/",
              "/¸/",
              "/˝/",
              "/˛/",
			  "/</",
	          "/>/");

    $replace = array(
              "&amp;",
              "&iexcl;",
              "&cent;",
              "&pound;",
              "&curren;",
              "&yen;",
              "&brvbar;",
              "&sect;",
              "&uml;",
              "&copy;",
              "&ordf;",
              "&laquo;",
              "&not;",
              "&shy;",
              "&reg;",
              "&macr;",
              "&deg;",
              "&plusmn;",
              "&sup2;",
              "&sup3;",
              "&acute;",
              "&micro;",
              "&para;",
              "&middot;",
              "&cedil;",
              "&sup1;",
              "&ordm;",
              "&raquo;",
              "&frac14;",
              "&frac12;",
              "&frac34;",
              "&iquest;",
              "&Agrave;",
              "&Aacute;",
              "&Acirc;",
              "&Atilde;",
              "&Auml;",
              "&Aring;",
              "&AElig;",
              "&Ccedil;",
              "&Egrave;",
              "&Eacute;",
              "&Ecirc;",
              "&Euml;",
              "&Igrave;",
              "&Iacute;",
              "&Icirc;",
              "&Iuml;",
              "&ETH;",
              "&Ntilde;",
              "&Ograve;",
              "&Oacute;",
              "&Ocirc;",
              "&Otilde;",
              "&Ouml;",
              "&times;",
              "&Oslash;",
              "&Ugrave;",
              "&Uacute;",
              "&Ucirc;",
              "&Uuml;",
              "&Yacute;",
              "&THORN;",
              "&szlig;",
              "&agrave;",
              "&aacute;",
              "&acirc;",
              "&atilde;",
              "&auml;",
              "&aring;",
              "&aelig;",
              "&ccedil;",
              "&egrave;",
              "&eacute;",
              "&ecirc;",
              "&euml;",
              "&igrave;",
              "&iacute;",
              "&icirc;",
              "&iuml;",
              "&eth;",
              "&ntilde;",
              "&ograve;",
              "&oacute;",
              "&ocirc;",
              "&otilde;",
              "&ouml;",
              "&divide;",
              "&oslash;",
              "&ugrave;",
              "&uacute;",
              "&ucirc;",
              "&uuml;",
              "&yacute;",
              "&thorn;",
			  "&lt;",
              "&gt;");	                     
	
	    return preg_replace($search, $replace, $string);   
  }

  private function undo_html($string)
  {
	     $search = array("/&nbsp;/",
              "/&iexcl;/",
              "/&cent;/",
              "/&pound;/",
              "/&curren;/",
              "/&yen;/",
              "/&brvbar;/",
              "/&sect;/",
              "/&uml;/",
              "/&copy;/",
              "/&ordf;/",
              "/&laquo;/",
              "/&not;/",
              "/&shy;/",
              "/&reg;/",
              "/&macr;/",
              "/&deg;/",
              "/&plusmn;/",
              "/&sup2;/",
              "/&sup3;/",
              "/&acute;/",
              "/&micro;/",
              "/&para;/",
              "/&middot;/",
              "/&cedil;/",
              "/&sup1;/",
              "/&ordm;/",
              "/&raquo;/",
              "/&frac14;/",
              "/&frac12;/",
              "/&frac34;/",
              "/&iquest;/",
              "/&Agrave;/",
              "/&Aacute;/",
              "/&Acirc;/",
              "/&Atilde;/",
              "/&Auml;/",
              "/&Aring;/",
              "/&AElig;/",
              "/&Ccedil;/",
              "/&Egrave;/",
              "/&Eacute;/",
              "/&Ecirc;/",
              "/&Euml;/",
              "/&Igrave;/",
              "/&Iacute;/",
              "/&Icirc;/",
              "/&Iuml;/",
              "/&ETH;/",
              "/&Ntilde;/",
              "/&Ograve;/",
              "/&Oacute;/",
              "/&Ocirc;/",
              "/&Otilde;/",
              "/&Ouml;/",
              "/&times;/",
              "/&Oslash;/",
              "/&Ugrave;/",
              "/&Uacute;/",
              "/&Ucirc;/",
              "/&Uuml;/",
              "/&Yacute;/",
              "/&THORN;/",
              "/&szlig;/",
              "/&agrave;/",
              "/&aacute;/",
              "/&acirc;/",
              "/&atilde;/",
              "/&auml;/",
              "/&aring;/",
              "/&aelig;/",
              "/&ccedil;/",
              "/&egrave;/",
              "/&eacute;/",
              "/&ecirc;/",
              "/&euml;/",
              "/&igrave;/",
              "/&iacute;/",
              "/&icirc;/",
              "/&iuml;/",
              "/&eth;/",
              "/&ntilde;/",
              "/&ograve;/",
              "/&oacute;/",
              "/&ocirc;/",
              "/&otilde;/",
              "/&ouml;/",
              "/&divide;/",
              "/&oslash;/",
              "/&ugrave;/",
              "/&uacute;/",
              "/&ucirc;/",
              "/&uuml;/",
              "/&yacute;/",
              "/&thorn;/",
              "/&amp;/",
              "/&quot;/",
              "/&lt;/",
              "/&gt;/");
              
    $replace = array(" ",
              "°",
              "¢",
              "£",
              "§",
              "•",
              "¶",
              "ß",
              "®",
              "©",
              "™",
              "´",
              "¨",
              "≠",
              "Æ",
              "Ø",
              "∞",
              "±",
              "≤",
              "≥",
              "¥",
              "µ",
              "∂",
              "∑",
              "∏",
              "π",
              "∫",
              "ª",
              "º",
              "Ω",
              "æ",
              "ø",
              "¿",
              "¡",
              "¬",
              "√",
              "ƒ",
              "≈",
              "∆",
              "«",
              "»",
              "…",
              " ",
              "À",
              "Ã",
              "Õ",
              "Œ",
              "œ",
              "–",
              "—",
              "“",
              "”",
              "‘",
              "’",
              "÷",
              "◊",
              "ÿ",
              "Ÿ",
              "⁄",
              "€",
              "‹",
              "›",
              "ﬁ",
              "ﬂ",
              "‡",
              "·",
              "‚",
              "„",
              "‰",
              "Â",
              "Ê",
              "Á",
              "Ë",
              "È",
              "Í",
              "Î",
              "Ï",
              "Ì",
              "Ó",
              "Ô",
              "",
              "Ò",
              "Ú",
              "Û",
              "Ù",
              "ı",
              "ˆ",
              "˜",
              "¯",
              "˘",
              "˙",
              "˚",
              "¸",
              "˝",
              "˛",
              "&",
              "\"",
              "<",
              ">");
	                     
	    return preg_replace($search, $replace, $string);   
  }
}

?>
