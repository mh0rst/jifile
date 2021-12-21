<?php
/**
* @version		CHANGELOG.php
* @package		JiFile
* @copyright	Copyright (C) 2005 - 2009 Open Source Matters. All rights reserved.
* @author		Antonio Di Girolamo & Giampaolo Losito
* @license		GNU/GPL, see LICENSE.php
* JiFile is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
1. Copyright and disclaimer
---------------------------
This application is opensource software released under the GPL.  
Please see source code and the LICENSE file.


2. Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
JiFile, including beta and release candidate versions.
Our thanks to all those people who've contributed bug reports and
code fixes.

Legend:

* -> Security Fix
# -> Bug Fix
$ -> Language fix or change
+ -> Addition
^ -> Change
- -> Removed
! -> Note


-------------------- 2.3 Stable [08-06-2014] ------------------
08-Jun-2014
 # fixed the strict error in the Lucene controller.
 # fixed the problem for the stored of the "created date" in the PDF and Images Files.
 # fixed the problem of the Highlight and SubString in the view of JiFile
 # fixed the error "Root-application does not exist" 
 + Integrate ordering for field "name" if selected "Alphabetica" in the search view 

-------------------- 2.2 Stable [24-03-2014] ------------------
24-Mar-2014
 # fixed minor problems

-------------------- 2.1 Stable [31-10-2013] ------------------

31-Oct-2013
 + Manual indexing for MP3 files
 + In the manual indexing templates, now is possible define the type of index in the "Custom fields" 
 + Integrate the Geolocalization on the Manual Indexing of the Documents
 # Solved problem on the download of document from search form 

-------------------- 2.0 RC Release [01-10-2013] ------------------

30-July-2013
 + Now is possible read and write the XPDFRC file from configuration section
 + Define a personal XPDF for read text from PDF 
 + All extensions that not are indexed automatically, can be indexed manually
 ^ Integrated the library of IFile 1.2

28-June-2013
 + Created compatibility with Joomla!3.x  

06-Febrary-2013
 ^ New managment of the root-application. 
   Now is possible create a index in another server and use this everywhere
   
28-Dicember-2012
 + All functions are Add-on. Is possible create and install new feature on the JiFile

15-October-2012
 ! Started the development of JiFile 2.0 
