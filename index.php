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

#################
## initialize  ##
#################

// Load required libraries
require_once("config/settings.php");
require_once("lib/io.php");
require_once("lib/parser.php");
require_once("lib/elements.php");
require_once("lib/user.php");
require_once("lib/functions.php");
require_once("lib/page.php");
require_once("lib/layout.php");

// Create required Objects
$user = new FWikiUser();
$page = new FWikiPage('', $user);

// Set up pagename
$page->createNameByURL();

// Create more Wiki objects
$parser = new FWikiParser($user, $page);
$func = new FWikiFunctions($parser, $user, $_GET);
$elements = new FWikiElements($user, $page);

// Setup user
$user->setup();	// Auto-Login, Logout ...

$layout = new FWikiLayout($user, $page, $func, $elements);


#########################
## Begin HTML - Header ##
#########################
require_once( 'layout/' . $layout->getLayoutName() . '/header.php' );


#####################
##  Post Wikipage  ##
#####################
$page->handlePOST();


##########################
## Load & process page  ##
##########################
$out = $layout->render();


#################
## View page  ##
#################
echo $out;

#######################
## End HTML - Footer ##
#######################
require_once( 'layout/' . $layout->getLayoutName() . '/footer.php' );

