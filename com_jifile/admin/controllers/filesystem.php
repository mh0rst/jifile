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

class JifileControllerFilesystem extends JControllerLegacy {
	
	function __construct() {
		parent::__construct();
	}
	
	function execute( $task ){
		if (!JFactory::getUser()->authorise('core.filesystem', 'com_jifile'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		switch ($task){
			case 'download':
				$this->download();
			break;
			case 'delete':
				$this->delete();
			break;
			case 'getFile2index':
				$this->getFile2index();
			break;
			case 'debug':
				$this->debug();
			default:
				$this->display();
		}
	}
	
	function display($cachable = false, $urlparams = array()) {
		$view = JRequest::getVar('view');
		
		if (!$view) {
			JRequest::setVar('view', 'filesystem');
		}
		$viewObj = $this->getView('filesystem', 'html');
		$viewObj->setModel($this->getModel('Lucene'));

		parent::display();
	}
	
	/**
	 * Scarica il file 
	 * @return void
	 */
	function download() {
		$filesystem = $this->getModel('Filesystem');
		$filesystem->download();
	}
	
	/**
	 * Cancella il file
	 * @return void
	 */
	function delete() {
		$option = JRequest::getCmd('option');
		$filesystem = $this->getModel('Filesystem');
		$files = JRequest::getVar('file');
		$s = (count($files) > 1) ? 's' : '';
		if($filesystem->deleteFile($files)) {
			$this->setRedirect('index.php?option='.$option.'&task=filesystem.', 'Deleted file'.$s.'!');
		} else {
			$this->setRedirect('index.php?option='.$option, 'Error delete file'.$s.'!', 'error');
		}
	}
	
	/**
	 * Recupera le informazioni del File
	 * @return string (json) 
	 */
	function getFile2index() {
		$files = JRequest::getVar('file', array());
		$json = array();
		
		$filesystem = $this->getModel('Filesystem');
		foreach ($files as $key => $file) {
			$file = urldecode($file);
			if (is_dir($file)) {
				$fileList = $filesystem->getListfile(jifilehelper::retrievePath($file, true).DS, array(), true, true);
				foreach ($fileList as $value) {
					$currentFile = $this->getInfoFile($value);
					array_push($json, $currentFile);
				}
			} else {
				$currentFile = $this->getInfoFile($file, $key);
				array_push($json, $currentFile);
			}
		}
		echo json_encode($json);
		jexit();
	}
	
	/**
	 * Recupera le informazioni del file
	 * @param object $file
	 * @param object $key [optional]
	 * @return array
	 */
	function getInfoFile($file, $key = null) {
		
		return array('key' 		=> $key,
					 'filename' => urlencode($file),
					 'size' 	=> jifilehelper::getFormatSize(filesize($file)),
					 'shortname'=> jifilehelper::encodingCharset(jifilehelper::retrievePath($file, true))
		);
	}
	
	function debug() {
		$dbg = JRequest::getCmd('dbg', 0);
		jifilehelper::setDebug($dbg);
	}
}
