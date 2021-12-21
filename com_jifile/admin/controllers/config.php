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

class JifileControllerConfig extends JControllerLegacy {
	
	function __construct() {
		parent::__construct();
	}
	
	function execute( $task ){
		if (!JFactory::getUser()->authorise('core.admin', 'com_jifile'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		switch ($task){						
			case 'savexpdfrc':
				$this->saveXpdfrc();				
			break;
			case 'save':
			case 'apply':	
				$this->save($task);
			case 'xpdfrc':				
			default:
				$this->display();
			
		}
	}
	
	function display($cachable = false, $urlparams = array()) {
		$view = JRequest::getVar('view');
		
		if (!$view) {
			JRequest::setVar('view', 'config');
		}

		parent::display();
	}
	
	/**
	 * Save content of the xpdfrc
	 * @return json
	 */
	function saveXpdfrc() {
		$model = $this->getModel('Config');
		$data = JRequest::getVar('xpdfrc');
		
		$responce = array();
		$responce['error'] = "0";
		$responce['state'] = "1";
		$responce['message'] = JText::_("JIFILE_FILE_SAVE_SUCCESS");
		
		if (empty($data)) {
			$responce['message'] = JText::_("JIFILE_ERROR_TEXT_XPDFRC_EMPTY");
			$responce['error'] = "1";			
		} else {
			$res = $model->saveXpdfrcSource($data);
			if (!$res) {
				$responce['message'] = $model->getError();
				$responce['error'] = "1"; 
			}	
		}
		
		echo json_encode($responce);	
		jexit();
	}
	
	function save($task) {
		$model = $this->getModel('Config');
		$xml = JRequest::getVar('xml');
		if($model->save($xml)) {
			switch ($task)
			{
				case 'apply':
					$link = 'index.php?option=com_jifile&task=config.';
					break;
	
				case 'save':
				default:
					$link = 'index.php?option=com_jifile';
					break;
			}
	
			$this->setRedirect( $link, JText::_( 'CONFIGURATION_SAVED' ) );
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('THE_FOLLOWING_ERRORS_HAVE_OCCURRED').': <br/><br/>'.implode('<br/>', $model->getErrors()), 'error');
		}
	}
}