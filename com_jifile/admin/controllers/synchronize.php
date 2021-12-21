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

class JifileControllerSynchronize extends JControllerLegacy {
	
	function __construct() {		
		parent::__construct();
	}
	
	function execute( $task ){
		
		switch ($task){				
			case "synchronize";
				$this->synchronize();
				break;
			default:
				
		}
		$this->display();
	}
	
	function display($cachable = false, $urlparams = array()) {
		$view = JRequest::getVar('view');
		
		if (!$view) {
			JRequest::setVar('view', 'synchronize');
		}
		
		parent::display();
	}
	
	/**
	 * This function Syncronize old index in JiFile 2.0 
	 * @return void
	 */
	private function synchronize() {
		
		$model = $this->getModel('Synchronize');		
		$model->synchronize();				
		// @todo
		// gestione degli errori
		jexit();
	}	
}