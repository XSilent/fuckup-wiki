<?php
/* FUCKUP-Wiki - A free Wiki Software
 * 
 * Copyright (C) 2011		Michael Neuhaus
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

require_once('lib/password.php');

/**
 * FWikiUser
 * 
 * @author Michael Neuhaus / Mutwin Kraus and Lukas Bombach
 */
class FWikiUser
{
  /**
   * Username
   * 
   * @var string
   */
  private $_name;
 
  /**
   * Password
   * 
   * @var string
   */
  private $_pass;
  
  /**
   * Email 
   * 
   * @var string
   */
  private $_email;
  
  /**
   * Is user identified
   * and authorized?
   * 
   * @var bool
   */
  private $_authorized;
  
  /**
   * Layout
   * 
   * @var string
   */
  private $_layout;
  
  /**
   * Login error by name
   * 
   * @var bool
   */
  private $_name_err;
  
  /**
   * Login error by password
   * 
   * @var bool
   */
  private $_pass_err;
  
  
  /**
   * 
   * @param string $name
   * @param string $pass
   */
  public function __construct($name="", $pass="")
  {
	if (!empty($name)){
	  	if (!empty($pass)) {
	    	$this->login($name, $pass);
		} else {
        	$this->_pass_err = true;
		}
	}
  }
    
  /**
   * Autologin, oder logout
   * Enter description here ...
   */
  public function setup()
  {
  	if (isset($_COOKIE["Wiki_logged_in"]) && isset($_COOKIE["Wiki_login"]) && $_COOKIE["Wiki_logged_in"] == true) $this->login($_COOKIE["Wiki_login"], $_COOKIE["Wiki_pass"]);
	if (isset($_GET["login_sent"]) && $_GET["login_sent"] == "Login") $this->login($_GET["username"], $_GET["password"]);
	if (isset($_POST["login_sent"]) && $_POST["login_sent"] == "Login") $this->login($_POST["username"], $_POST["password"]);

	if (isset($_GET["logout"]) && $_GET["logout"] == 1) $this->logout();
  }
  
  /**
   * Login user
   * 
   * @param string $name
   * @param string $pass
   * @param bool $nocookie
   */
  public function login($name, $pass, $nocookie=false)
  {
	if (file_exists("user/" . $name . ".usr")){
		$fp = fopen("user/" . $name . ".usr", "r");
		
		$phash = trim(fgets($fp));
		
		if (FWikiPassword::isEqual($pass, $phash)){		
        	$this->_name = $name;
        	$this->_pass = $pass;
        	$this->_authorized = true;
        
        	if (!$nocookie){
          		setcookie("Wiki_logged_in", "true", time()+30758400);
          		setcookie("Wiki_login", $this->_name, time()+30758400);
          		setcookie("Wiki_pass", $this->_pass, time()+30758400);
        	}
        
        	$this->_layout = trim(fgets($fp));
        	$this->_email = trim(fgets($fp));
        
      	} else {
        	$this->_pass_err = true;
      	}
      	fclose($fp);
    } else {
    	$this->_name_err = true;
    }
  }
  
  /**
   * User logout
   */
  public function logout()
  {
	setcookie("Wiki_logged_in", "true", time()-30758400);
	setcookie("Wiki_login", $this->_name, time()-30758400);
	setcookie("Wiki_pass", $this->_pass, time()-30758400);  
	
	$this->_authorized = false;
	$this->_name = "";
	$this->_pass = "";
  }
  
  /**
   * Is user authorized to access system?
   * 
   * @return bool
   */
  public function isAuthorized()
  {
  	return $this->_authorized;
  }
  
  /**
   * Layout
   * 
   * @return string
   */
  public function getLayout()
  {
  	return $this->_layout;
  }
  
  /**
   * User name
   * 
   * @return string
   */
  public function getName()
  {
  	return $this->_name;
  }
  
  /**
   * Password
   * 
   * @return string
   */
  public function getPass()
  {
  	return $this->_pass;
  }
  
  /**
   * Email
   * 
   * @return string
   */
  public function getEmail()
  {
  	return $this->_email;
  }
  
  /**
   * Loginfailure by password
   * 
   * @return bool
   */
  public function isPass_err()
  {
  	return $this->_pass_err;
  }
  
  /**
   * Loginfailure by name
   * 
   * @return bool
   */
  public function isName_err()
  {
  	return $this->_name_err;
  }
  
  /**
   * 
   * @param string $name
   * @param mixed $value
   */
  public function __set($name, $value)
  {
  	throw new Exception( 'Method ' . $name . ' not available!');
  }
  
  /**
   * 
   * @param string $name
   */
  public function __get($name)
  {
  	throw new Exception( 'Method ' . $name . ' not available!');
  }
}

?>
