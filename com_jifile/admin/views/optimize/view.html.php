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

class JifileViewOptimize extends JViewLegacy {
	
	function display($tpl = null) {
		$tpl = JRequest::getVar('tmpl', null);
		
		if(!is_null($tpl)) {
			$doc = JFactory::getDocument();
			JHtml::_('behavior.framework', true);
			$doc->addStyleSheet( '../administrator/components/com_jifile/css/ifile.css?'.JIFILEVER );
		}
		parent::display($tpl);
	}
}
