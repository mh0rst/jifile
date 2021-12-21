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
/** IFileConfig */
require_once JPATH_IFILE_LIBRARY.'/ifile/IFileConfig.php';

class JifileViewAddon extends JViewLegacy {
	
	var $manifest = array();
	
	function display($tpl = null) {
		
		// get option
		$option = JRequest::getCmd('option');
		
		// define access (only admin)
		if (!JFactory::getUser()->authorise('core.admin', 'com_jifile'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}		
		// get model 
		$model = $this->getModel();
	
		$filter = array();
		$order = array();
		$order['ordering'] = 'asc';
		$addons = $model->getAddon($filter, $order);
		
		$addonInstance = array();
		// instance JiFilePluginJFactory		
		$jifilefactory = JiFilePluginFactory::getInstance();
		 
		// loop for verify if "Addon" not have error	
		foreach ($addons as &$addon) {
			$addonObj = null;
			// define a true all addon
			$addon['checkReport'] = true;
			// not check for "Core" Addon.
			if ($addon['core'] == 0) {
				// get Addon instance				  
				$addonObj = $jifilefactory->getJifileAddon($addon);
				// get Report Check
				$addon['checkReport'] = $addonObj->reportCheck();
								
				// if exist error, unpublish addon
				if (!$addon['checkReport'] && $addon['published'] == 1) {
					$model->publish(array($addon['id']), 0);
					$addon['published'] = 0;
				}
			}			
		}
		
		// get access control list
		$canDo = jifilehelper::getActions();
		
		$this->assign('addon', $addons);
		$this->assign('canDo', $canDo);
		// set toolbar
		$this->setToolbar($option);
		// set add file (css, js)
		$this->setDocument();
		
		parent::display($tpl);
	}
	
	function getManifestInfo($addon) {
		$name = $addon['addon'];
		if (!isset($this->manifest[$name])) {
			if (!empty($addon['manifest_cache'])) {
				$this->manifest[$name] = json_decode($addon['manifest_cache']);
				$this->manifest[$name]->authorInfo = $this->manifest[$name]->authorEmail.'<br/>'.$this->manifest[$name]->authorUrl;
			} else {
				$this->manifest[$name] = new stdClass();
				$this->manifest[$name]->authorInfo = '';
			}
		}
		return $this->manifest[$name];
	}
	
	function setToolbar($option) {
		$canDo = jifilehelper::getActions();
		$bar = JToolBar::getInstance('toolbar');
		
		JToolBarHelper::title( 'JiFile ['.JText::_( 'ADDON').']', 'logo' );
		
		//JToolBarHelper::help('JiFle Configuration', '', 'http://www.isapp.it/documentazione-jifile/17-configurare-jifile.html');
		JToolBarHelper::deleteList(JText::_('ARE_YOU_SURE_TO_DELETE_THE_ADDON_FILES').'?', 'addon.uninstall', 'JIFILE_UNISTALL');
		JToolBarHelper::divider();
		//JToolBarHelper::back('CONTROL_PANEL', 'index.php?option='.$option);
		$bar->appendButton( 'Link', 'home', 'CONTROL_PANEL', 'index.php?option='.$option );
	}
	
	function setDocument() {
		$doc = JFactory::getDocument();
		JHtml::_('behavior.framework', true);
		jifilehelper::addJQuery(array('colorbox'));
		$doc->addStyleSheet( '../administrator/components/com_jifile/css/ifile.css?'.JIFILEVER );
		//jifilehelper::addJQuery();
	}
}