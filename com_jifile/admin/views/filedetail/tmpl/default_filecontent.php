<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link			http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access');

if($this->error) {
	echo JText::_('ERROR_RECOVERY_DOCUMENT').': '.jifilehelper::JText($this->error, 1);
} else {
	if (!empty($this->encoding)) {
		echo htmlentities($this->ifiledoc['body'], ENT_COMPAT|ENT_SUBSTITUTE, $this->encoding);
	} else {
		echo htmlentities($this->ifiledoc['body'], ENT_COMPAT|ENT_SUBSTITUTE);
	}
	
	unset($this->ifiledoc);
}

?>