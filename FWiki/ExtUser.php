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
 * FWikiExtUser
 * 
 * 
 * @author Michael Neuhaus
 */
class FWikiExtUser extends FWikiExtension
{
	
	/**
	 * @return string
	 */
	public function getOutput()
	{ 	  	
	  if (!isset($_POST["CreateNewUser"])){
		if (!$this->_user->isAuthorized() ) {
			return $this->_show_new_user_form();
		} else {
			if (!isset($_POST["EditUser"])){
				return $this->_show_edit_user_form();
			} else {
				if (empty($_POST["password"])) return $this->_show_new_user_form("Bitte passwort eingeben.");
	            
	          if ($_POST["password"] != $_POST["rpt_password"]) return $this->_show_new_user_form("'Passwort' und 'Passwort Wiederholung' stimmen nicht &uuml;berein!.");
	
			  $this->_new_user($this->_user->getName(), $_POST["password"], $_POST["layout"], $_POST["email"]);
			  
			  return "Einstellungen wurden &uuml;bernommen, bitte neu einloggen um die Einstellungen zu &uuml;bernehmen.";
			} 
		  }
		} else {
			if (isset($_POST["username"]) && empty($_POST["username"])) return $this->_show_new_user_form($_POST["username"], $_POST["password"], $_POST["rpt_password"], $_POST["email"], $_POST["layout"], "Kein Benutzername ist kein Benutzername!");
	      	
	
	      	if (isset($_POST["password"]) && empty($_POST["password"])) return $this->_show_new_user_form($_POST["username"], $_POST["password"], $_POST["rpt_password"], $_POST["email"], $_POST["layout"], "Bitte passwort eingeben.");
	  
	      	if (isset($_POST["username"]) && preg_match("/[^a-zA-Z0-9_ -]/", $_POST["username"])) return $this->_show_new_user_form($_POST["username"], $_POST["password"], $_POST["rpt_password"], $_POST["email"], $_POST["layout"], "Der Benutzername kann nur aus Buchstaben, Zahlen, [Leertaste], \"-\" und \"_\" bestehen.");
	  
	      	if (isset($_POST["username"]) && file_exists("user/". $_POST["username"] . ".usr")) return $this->_show_new_user_form($_POST["username"], $_POST["password"], $_POST["rpt_password"], $_POST["email"], $_POST["layout"], "Benutzername existiert bereits.");
	      
			if (isset($_POST["password"]) && isset($_POST["rpt_password"]) && $_POST["password"] != $_POST["rpt_password"]) return $this->_show_new_user_form($_POST["username"], $_POST["password"], $_POST["rpt_password"], $_POST["email"], $_POST["layout"], "'Passwort' und 'Passwort Wiederholung' stimmen nicht &uuml;berein!.");
	      
			if (isset($_POST["username"])) $this->_new_user($_POST["username"], $_POST["password"], $_POST["layout"], $_POST["email"]);
			if (isset($_POST["username"]) && !file_exists("wikis/" . $_POST["username"] . ".xml")) {
				$userpage = new FWikiPage();
				$userpage->setName( $_POST["username"] );
			
				FWikiIO::write($userpage, $this->_user->getName(), "=" . $_POST["username"] . "=\n\n");
			}
		  
			return "Anmeldung erfolgreich! Du kannst Dich jetzt einloggen.";
		}
	}
	
	/**
	 * 
	 * @param string $name
	 * @param string $pass
	 * @param string $rpt_pass
	 * @param string $email
	 * @param string $layout
	 * @param string $err
	 * @return string
	 */
	private function _show_new_user_form($name="", $pass="", $rpt_pass="", $email="", $layout="", $err="")
	{	    
		$out  = "<form action=\"index.php?" . $this->_page->getName() . "\" method=\"post\">";
		$out .= "<table class=\"UserTable\" width=\"475\">";
		$out .= " <tr>";
		$out .= "    <th colspan=\"2\">Neuen Account erstellen</th>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td colspan=\"2\"><i>(Einloggen um Profil zu bearbeiten)</i></td>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td>Benutzername*:</td>";
		$out .= "    <td><input name=\"username\" value=\"" . $name . "\"></td>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td>Passwort:</td>";
		$out .= "    <td><input type=\"password\" name=\"password\" value=\"" . $pass . "\"></td>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td>Passwort wiederholen:</td>";
		$out .= "    <td><input type=\"password\" name=\"rpt_password\" value=\"" . $rpt_pass . "\"></td>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td>E-Mail (<i>optional</i>):</td>";
		$out .= "    <td><input name=\"email\" value=\"" . $email . "\"></td>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td>Layout:</td>";
		$out .= "    <td><select name=\"layout\">";
		
		foreach($this->_get_layouts() as $entry){
			
			$selected = '';
			
		  	if($layout == $entry) $selected = " selected";
		  	$out .= "<option value=\"" . $entry . "\"" . $selected . ">" . $entry . "</option>";
		  	if($layout == $entry) $selected = "";
		}
		
		if(!empty($err)){
		  $out .= "  <tr><td colspan=\"2\">&nbsp;</td></tr>";
		  $out .= "  <tr>";
		  $out .= "    <td colspan=\"2\"><span class=\"error\">Fehler: </span><i>" . $err . "</i></td>";
		  $out .= "  </tr>";
		  $out .= "  <tr><td colspan=\"2\">&nbsp;</td></tr>";
	    }
		
		$out .= "    </select></td>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td colspan=\"2\">* <i>Kann nur aus Buchstaben, Zahlen, [Leertaste], \"-\" und \"_\" bestehen.</i></td>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td><input type=\"reset\" value=\"Zur&uuml;cksetzen\"></td>";
		$out .= "    <td><input type=\"submit\" value=\"Account erstellen\"></td>";
		$out .= "  </tr>";
		$out .= "</table>";
		$out .= "<input type=\"hidden\" name=\"CreateNewUser\" value=\"true\">";
		$out .= "</form>";
		
		return $out;
	}
	
	/**
	 * 
	 * @param string $err
	 * @param string $succ
	 * @return string
	 */
	private function _show_edit_user_form($err="", $succ="")
	{    
		$out  = "<form action=\"index.php?" . $this->_page->getName() . "\" method=\"post\">";
		$out .= "<table class=\"UserTable\" width=\"475\">";
		$out .= " <tr>";
		$out .= "    <th colspan=\"2\">Profil bearbeiten</th>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td>Benutzername:</td>";
		$out .= "    <td>" . $this->_user->getName() . "</td>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td>Passwort:</td>";
		$out .= "    <td><input type=\"password\" name=\"password\" value=\"" . $this->_user->getPass() . "\"></td>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td>Passwort wiederholen:</td>";
		$out .= "    <td><input type=\"password\" name=\"rpt_password\" value=\"" . $this->_user->getPass() . "\"></td>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td>E-Mail (<i>optional</i>):</td>";
		$out .= "    <td><input name=\"email\" value=\"" . $this->_user->getEmail() . "\"></td>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td>Layout:</td>";
		$out .= "    <td><select name=\"layout\">";
		
		foreach($this->_get_layouts() as $entry){
			
			$selected = '';
		  	if ($this->_user->getLayout() == $entry) $selected = " selected";
		  	$out .= "<option value=\"" . $entry . "\"" . $selected . ">" . $entry . "</option>";
		  	//if ($layout == $entry) $selected = "";
		}
		
		if(!empty($err)){
			$out .= "  <tr><td colspan=\"2\">&nbsp;</td></tr>";
		  	$out .= "  <tr>";
		  	$out .= "    <td colspan=\"2\"><span class=\"error\">Fehler: </span><i>".$err."</i></td>";
		  	$out .= "  </tr>";
		  	$out .= "  <tr><td colspan=\"2\">&nbsp;</td></tr>";
	    }
	    
		if(!empty($succ)){
			$out .= "  <tr><td colspan=\"2\">&nbsp;</td></tr>";
		  	$out .= "  <tr>";
		  	$out .= "    <td colspan=\"2\"><span class=\"success\">".$succ."</span></td>";
		  	$out .= "  </tr>";
		  	$out .= "  <tr><td colspan=\"2\">&nbsp;</td></tr>";
	    }
		
		$out .= "    </select></td>";
		$out .= "  </tr>";
		$out .= "  <tr>";
		$out .= "    <td><input type=\"reset\" value=\"Zur&uuml;cksetzen\"></td>";
		$out .= "    <td><input type=\"submit\" value=\"Einstellungen &auml;ndern\"></td>";
		$out .= "  </tr>";
		$out .= "</table>";
		$out .= "<input type=\"hidden\" name=\"EditUser\" value=\"true\">";
		$out .= "</form>";
		
		return $out;
	}
	
	/**
	 * 
	 * @param string $name
	 * @param string $pass
	 * @param string $layout
	 * @param string $email
	 */
	private function _new_user($name, $pass, $layout, $email)
	{
	  $fp = fopen("user/" . $name . ".usr", "w");
	  
	  $phash = FWikiPassword::getHash( $pass );
	  
	  fputs($fp, 	$phash 	. "\n" 
	  			. 	$layout . "\n" 
	  			. 	$email
	  			);
	  
	  fclose($fp);
	}
	
	/**
	 * @return string
	 */
	private function _get_layouts()
	{
		$dh = opendir("layout");
		while ($file = readdir($dh)){
			if ($file != "." && $file != ".." && is_dir("layout/".$file)){
				$styles[] = $file;
			}
		}
		closedir($dh);
	  
		return $styles;
	}
}


?>

