<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access');

Jimport('joomla.application.component.controller');

class JifileControllerAddon extends JControllerLegacy {
	
	function __construct() {
		parent::__construct();
	}
	
	function execute( $task ){
		/*if (!JFactory::getUser()->authorise('core.admin', 'com_jifile'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}*/
		
		switch ($task){
			case 'install':
				$this->install();
				break;
			case 'uninstall':
				$this->uninstall();
				break;
			case 'unpublish':
				$this->unpublish();
				break;			
			case 'publish':
				$this->publish();
				break;				
			case 'checkreport':				
			default:
				// @TODO
		}
		$this->display();
	}
	
	function display($cachable = false, $urlparams = array()) {
		$view = JRequest::getVar('view');
		
		if (!$view) {
			JRequest::setVar('view', 'addon');
		} else {
			$view = $this->getView($view, 'html');
			$view->setModel($this->getModel('Addon'), true);
		}

		parent::display();
	}
		
	/**
	 * UnPublish Addon
	 * @return void
	 */
	function unpublish () {
		// get model
		$model = $this->getModel('Addon');
		// get cid
		$cid = JRequest::getVar('cid', array());
		// publish addon
		$model->publish($cid, 0);
	}
	
	/**
	 * Publish Addon
	 * @return void
	 */
	function publish () {
		// get model
		$model = $this->getModel('Addon');
		// get cid
		$cid = JRequest::getVar('cid', array());
		// publish addon
		$model->publish($cid, 1);
	}
	
	/**
	 * Install Addon
	 * @return void
	 */
	function install() {
		$model = $this->getModel('Addon');
		if ($model->install()) {
			$this->setRedirect( 'index.php?option=com_jifile&task=addon.', JText::_( 'JIFILE_INSTALL_SUCCESS' ) );
		} else {
			JFactory::getApplication()->enqueueMessage(implode('<br/>', $model->getErrors()), 'error');
		}
	}
	
	/**
	 * Uninstall Addon
	 * @return void
	 */
	function uninstall() {
		
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		
		if (empty($cid)) {
			JFactory::getApplication()->enqueueMessage(JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'), 'error');
		}
		
		foreach ($cid as $id) {
			$model = $this->getModel('Addon');
			if (($addon = $model->uninstall($id)) !== FALSE) {
				JFactory::getApplication()->enqueueMessage(JText::_( 'JIFILE_UNINSTALL_SUCCESS' ).': '.$addon);
			} else {
				JFactory::getApplication()->enqueueMessage(implode('<br/>', $model->getErrors()), 'error');
			}
		}
		
		$this->setRedirect( 'index.php?option=com_jifile&task=addon.');
		
	}
}