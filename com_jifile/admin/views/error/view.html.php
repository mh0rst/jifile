<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class JifileViewError extends JViewLegacy {
	function display($tpl = null) {
		
		$this->setToolbar();
		$this->setDocument();
		
		parent::display($tpl);
	}
	
	function setToolbar($option = null) {
		JToolBarHelper::title('JiFile', 'logo');
		JToolBarHelper::preferences('com_jifile', 200);
	}
	
	function setDocument() {
		$doc = JFactory::getDocument();
		$doc->addStyleSheet( '../administrator/components/com_jifile/css/ifile.css?'.JIFILEVER );
		jifilehelper::addJQuery();
		$doc->addScript( '../administrator/components/com_jifile/js/updateIFile.js?'.JIFILEVER );
	}
}
