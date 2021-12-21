<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR.'/version.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/jifilehelper.php';
// Adapter for Joomla version
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/adapter/adapterforjoomlaversion.php';
$jAdapter = AdapterForJoomlaVersion::getInstance();

// define path addon controller
$config = array();
// @TODO: da integrare al momento della creazione e gestione degli ADDON
//if (($addon = jifilehelper::isControllerAddon()) !== FALSE) {
//	$config['base_path'] = JIFILE_ADDON_PATH.'/'.$addon;
//}
// instance controller
$controller = JControllerLegacy::getInstance('Jifile', $config);

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();

//$controller = JControllerLegacy::getInstance('Jifile');
//$controller->execute(JRequest::getCmd('task'));
//$controller->redirect();