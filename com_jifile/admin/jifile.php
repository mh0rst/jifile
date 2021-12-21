<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
//no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT.'/version.php';
require_once JPATH_COMPONENT.'/helpers/jifilehelper.php';
// Adapter for Joomla version
require_once ("helpers/adapter/adapterforjoomlaversion.php");

$jAdapter = AdapterForJoomlaVersion::getInstance();
//define('JIFILE_ADDON_PATH', JPATH_COMPONENT.'/addon');
//define('JIFILE_ADDON_PLUGIN_PATH', JPATH_COMPONENT.'/addon/plugins');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_jifile')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// @TODO
// va verificato anche per il FrontEnd la stessa cosa
if(file_exists(JPATH_IFILE_LIBRARY.'/ifile/Zend')) {
	$include_path = get_include_path();
	$include_path .= PATH_SEPARATOR.JPATH_IFILE_LIBRARY.'/ifile';
	set_include_path($include_path);
}

jifilehelper::getRootApplication(); //salva nei params

// Richiede la classe Controller principale del componente
//require_once(JPATH_COMPONENT.DS.'controller.php');
jimport('joomla.application.component.controller');
// get language from addon
jifilehelper::addLanguages();
// define path addon controller
$config = array();
if (($addon = jifilehelper::isControllerAddon()) !== FALSE) {
	$config['base_path'] = JIFILE_ADDON_PATH.'/'.$addon;
}
// instance controller
$controller = JControllerLegacy::getInstance('Jifile', $config);

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));
 
// Redirect if set by the controller
$controller->redirect();