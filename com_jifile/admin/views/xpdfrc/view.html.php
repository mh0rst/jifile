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

class JifileViewXpdfrc extends JViewLegacy {
	
	protected $xpdfrc;
		
	function display($tpl = null) {
		
		$tpl = JRequest::getVar('tmpl', null);
		
		if(!is_null($tpl)) {
			require_once JPATH_COMPONENT_ADMINISTRATOR."/models/config.php";
			$model = new JifileModelConfig();;
			// get source from XPDFRC
			$this->xpdfrc = $model->getXpdfrcSource();
			
			JHtml::_('behavior.framework', true);
			$doc = JFactory::getDocument();
			if(file_exists('templates/bluestork/css/rounded.css')) {
				$doc->addStyleSheet( 'templates/bluestork/css/rounded.css' );
			}
			jifilehelper::addJQuery();
			$doc->addScript( '../administrator/components/com_jifile/js/xpdfrc.js?'.JIFILEVER );
			$doc->addStyleSheet( '../administrator/components/com_jifile/css/ifile.css?'.JIFILEVER );
		}
		parent::display($tpl);
	}	
}
