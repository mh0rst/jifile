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

class JifileController extends JControllerLegacy {
	
	function __construct() {
		parent::__construct();
	}
	
	function execute( $task ){
		
		switch ($task) {
			case 'clearCache':
				jifilehelper::clearCache();
				
				$option = JRequest::getCmd('option');
				$controller = JRequest::getVar('from');
				$this->setRedirect('index.php?option='.$option.'&task='.$controller.'.', JText::_('CACHE_CLEARED').'!');
			break;
			case 'checkVer':
				$this->checkVer();
			break;
			case 'sysinfo':
				$this->sysinfo();
			break;
			case 'updateifile':
				$this->updateIFile();
			break;
			default:
				$this->display();
			break;
		}
	}
	
	function checkVer() {
		
		$data = jifilehelper::checkVersion();

		$result = json_decode($data, true);

		$document =& JFactory::getDocument();
		// Set the MIME type for JSON output.
		$document->setMimeEncoding( 'application/json' );
		
		echo $data;
		jexit();
	}
	
	function sysinfo() {
		$sysInfo = jifilehelper::getSysInfo();
		$host = parse_url( JURI::root( false ) );
		$host = strtolower( $host['host'] );
		$name = 'sysinfo_'.microtime().'_'.$host.'_'.rand(0, 1000).'.dat';
		$size = strlen($sysInfo);
		
		header("Content-Transfer-Encoding: binary");
		header('Content-Disposition: attachment;'
		. ' filename="' . $name . '";'
		. ' size=' . $size .';'
		);
		header("Content-Type: text/plain" );
		header("Content-Length: "  . $size);
			
		echo $sysInfo;
		jexit();
	}
	
	function display($cachable = false, $param = array()) {
		
		//controllo che iFile esista altrimenti errore
		if(!file_exists(JPATH_IFILE_LIBRARY.'/ifile')) {
			$error_msg = JText::_('LIBRARY_IFILE_MISSING').'.<br/><br/>';
			$error_msg .= JText::_('DOWNLOAD_IFILE');
			JFactory::getApplication()->enqueueMessage($error_msg, 'error');
			
			JRequest::setVar('view', 'error');
			$view = $this->getView('error', 'html');
			parent::display($cachable);
			return false;
		} else {
			//controllo che iFile sia aggiornato altrimenti errore
			require_once(JPATH_IFILE_LIBRARY.'/ifile/IFileVersion.php');
			
			if (version_compare(IFILEVER_REQ, IFileVersion::VERSION, '>'))  {
				$error_msg = JText::_('LIBRARY_IFILE_OLD').'.<br/><br/>';
				$error_msg .= JText::_('DOWNLOAD_IFILE');
				JFactory::getApplication()->enqueueMessage($error_msg, 'error');
				
				JRequest::setVar('view', 'error');
				$view = $this->getView('error', 'html');
				parent::display($cachable);
				return false;
			}
		}
		
		$view = JRequest::getVar('view');
		
		if (!$view) {
			//controllo se config ok
			$config = $this->getModel('Config');
			$config->getConfig(true);
			if($config->getError()) {
				$link = 'index.php?option=com_jifile&task=config.';
				$this->setRedirect( $link );
				return false;
			}
			
			//require_once(JPATH_SITE.'/administrator'.DS.'components'.DS.'com_jifile'.DS.'helpers'.DS.'jifilehelper.php');
			$rootApp = '';
			if(!jifilehelper::checkPathConfig($rootApp)) {
				JFactory::getApplication()->enqueueMessage(JText::_('ERROR_PATH_FILE').': '.$rootApp, 'error');
			}
			
			JRequest::setVar('view', 'frontpage');
			$view = $this->getView('frontpage', 'html');
			// set model of Lucene
			$model = $view->setModel($this->getModel('Lucene'));
			if($model->getError()) {
				JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			}
			// set model of Addon
			$model = $view->setModel($this->getModel('Addon'));
			if($model->getError()) {
				JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			}
		}
		
		$state = JRequest::getVar('state', false);
		if($state) {
			switch ($state) {
				case 'sync':
					if($time = JRequest::getVar('time', '')) {
						$time = ' in '.$time;
					}
					JFactory::getApplication()->enqueueMessage(JText::_('JIFILE_SYNCHRONIZE_COMPLETED').$time);
					break;
			}
		}

		parent::display($cachable, array());
		//$this->display($cachable);
	}
	
	/*
	 * Download e installazione automatico della librearia iFile
	 */
	function updateIFile() {
		$json = array('result' => false);
		$update = false;
		
		// @TODO:
		// sarebbe necessario recuperare la version necessaria di IFILE
		// e poi verificare quella presente nella installazione per 
		// capire se questa va aggiornata
		
		$uploadDir = JPATH_COMPONENT_ADMINISTRATOR.'/libraries/';
		$url = 'http://www.isapp.it/download/jifile/lib/ifile.zip';
		$length = 5120;
		
		//download zip file
		$handle = fopen($url, 'rb');
		$filename = $uploadDir . substr(strrchr($url, '/'), 1);
		$write = fopen($filename, 'w');
		
		while (!feof($handle))
		{
			$buffer = fread($handle, $length);
			fwrite($write, $buffer);
		}
		
		fclose($handle);
		fclose($write);
		
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');
		
		//cancella eventuale vecchia versione
		if(JFolder::exists(JPATH_IFILE_LIBRARY.'/ifile')) {
			$update = true;
			JFolder::delete(JPATH_IFILE_LIBRARY.'/ifile');
		}
		
		//estraggo lo zip
		$result = JArchive::extract($filename, $uploadDir);
		
		if ($result === false)
		{
			$json['message'] = JText::_('JIFILE_EXTRACT_IFILE_ERROR');
			echo json_encode($json);
			jexit();
		}
		
		//cancello zip
		JFile::delete($filename);
		
		//ripristino eventuale configurazione
		if(JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.'/IFileConfig.xml')) {
			rename(JPATH_COMPONENT_ADMINISTRATOR.'/IFileConfig.xml', JPATH_IFILE_LIBRARY.'/ifile/config/IFileConfig.xml');
		}
		
		$json['result'] = true;
		$json['message'] = $update ? JText::_('JIFILE_UPDATE_IFILE_SUCCESS') : JText::_('JIFILE_INSTALL_IFILE_SUCCESS');
		
		echo json_encode($json);
		jexit();
		
		return true;
	}
}
