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
/** JiFilePluginFactory */
require_once(JPATH_ADMINISTRATOR.'/components/com_jifile/helpers/interface/jifilepluginfactory.php');

class JifileViewCheckreport extends JViewLegacy {
	
	function display($tpl = null) {
	
		// define access (only admin)
		if (!JFactory::getUser()->authorise('core.admin', 'com_jifile'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}		
		// get model 
		$model = $this->getModel('Addon');
		// get Addon
		$filter = array();
		$filter['id'] = array('type' => 'i', 'value' => JRequest::getVar('plugin'));
		$addon = $model->getAddon($filter);
		// check if the addon exist 		
		if (count($addon) != 1) {
			return JError::raiseWarning(404, JText::_('JIFILE_LUCENEPLUGINS_NOTFOUND'));
		}
		// instance JiFilePluginJFactory		
		$jifilefactory = JiFilePluginFactory::getInstance();
		// get Addon instance				  
		$addonObj = $jifilefactory->getJifileAddon($addon[0]);
		// recupero l'array della check 
		$checkReport = $addonObj->getListReportCheck();
		
		$this->assign('reportCheck', $checkReport);
		// set add file (css, js)
		$this->setDocument();
		
		parent::display($tpl);
	}
	

	function setDocument() {
		$doc = JFactory::getDocument();
		JHtml::_('behavior.framework', true);
		$doc->addStyleSheet( '../administrator/components/com_jifile/css/ifile.css?'.JIFILEVER );
	}
}