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

class JifileControllerLucene extends JControllerLegacy {
	
	function __construct() {
		parent::__construct();
	}
	
	function execute( $task ){
		if (!JFactory::getUser()->authorise('core.index', 'com_jifile'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		$ajax = false;
		switch ($task) {
			case 'indexAjax':
				$ajax = true;
				error_reporting(E_PARSE);
			case 'index':
				$this->index($ajax);
			break;
			case 'indexManualAjax':
				$ajax = true;
				error_reporting(E_PARSE);
			case 'indexManual':
				$this->indexManual($ajax);
			break;
			case 'updateAjax':
				$ajax = true;
				error_reporting(E_PARSE);
			case 'update':
				$this->update($ajax);
			break;
			case 'optimizeAjax':
				$ajax = true;
			case 'optimize':
				$this->optimize($ajax);
			break;
			case 'checkIndexAjax':
				$ajax = true;
			case 'checkIndex':
				$this->checkIndex($ajax);
			break;
			case 'deleteAll':
				$all = true;
			case 'delete':
				$this->delete($all ? true : false);
			break;
			case 'googlemap':
			case 'indexing':
			case 'startoptimize':
			default:
				$this->display();
		}
	}
	
	function display($cachable = false, $urlparams = array()) {
		$view = JRequest::getVar('view');
		
		if (!$view) {
			$view = 'lucene';
			JRequest::setVar('view', $view);
		} else {
			$view = $this->getView($view, 'html');
			$view->setModel($this->getModel('Lucene'), true);
		}
		$state = JRequest::getVar('state', false);
		if($state) {
			switch ($state) {
				case 'optimize':
					if($time = JRequest::getVar('time', '')) {
						$time = ' in '.$time;
					}
					JFactory::getApplication()->enqueueMessage(JText::_('OPTIMIZATION_COMPLETED').$time);
				break;
			}
		}
		
		parent::display();
	}
	
	function index($ajax = false) {
		
		if (!$ajax && jifilehelper::inDebug()) {
			error_reporting(E_ALL ^ E_NOTICE);
		}
		
		$option = JRequest::getCmd('option');
		// 20130125: gestione dei file con caratteri speciali
		$source = urldecode(JRequest::getVar('file'));
		$model = $this->getModel('Lucene');
		$result = $model->index($source, array('class' => 'jifile'));

		if($ajax) {
			$document =& JFactory::getDocument();
			// Set the MIME type for JSON output.
			$document->setMimeEncoding( 'application/json' );
			
			$json['file'] = urlencode($source);
			$json['div'] = JRequest::getVar('div');
			$json['cb']	 = JRequest::getVar('cb');
			$json['message'] = (!$result) ? htmlentities($model->getError()) : JText::_('INDEXING_COMPLETED');
			$json['result'] = (!$result) ? 'false' : 'true';
			//die(print_r($json));
			echo json_encode($json);
			jexit();
		} else {
			$url = 'index.php?option='.$option.'&task=filesystem.';
			$url .= jifilehelper::inDebug() ? '&tmpl=component&tpl=debug&id='.JRequest::getVar('id') : ''; 
			
			if(!$result) {
				$this->setRedirect($url, $model->getError(), 'error');
			} else {
				$this->setRedirect($url, JText::_('INDEXING_COMPLETED'));
			}
		}
	}
	
	function indexManual($ajax = false) {
		$option = JRequest::getCmd('option');
		$fields = JRequest::getVar('fields');

		$model = $this->getModel('Lucene');
		
		$ext = (isset($fields['extensionfile'])) ? strtolower($fields['extensionfile']) : null;
		
		switch($ext) {
			case 'bmp':
			case 'gif':
			case 'ico':
			case 'jpg':
			case 'png':
			case 'psd':
			case 'svg':
			case 'tif':
			case 'tiff':
				$result = $model->indexManualImages($fields);
				break;
			case 'mp3':
				$result = $model->indexManualMultimedia($fields);
				break;
			default:
				$result = $model->indexManual($fields);
		}
				
		if($ajax) {
			$document =& JFactory::getDocument();
			// Set the MIME type for JSON output.
			$document->setMimeEncoding( 'application/json' );
			
			$json['i']	 = JRequest::getVar('i');
			$json['message'] = (!$result) ? htmlentities($model->getError()) : JText::_('INDEXING_COMPLETED');
			$json['result'] = (!$result) ? 'false' : 'true';
			//die(print_r($json));
			echo json_encode($json);
			jexit();
		} else {
			if(!$result) {
				$this->setRedirect('index.php?option='.$option, $model->getError(), 'error');
			} else {
				$this->setRedirect('index.php?option='.$option, JText::_('INDEXING_COMPLETED'));
			}
		}
	}
	
	function update($ajax = false) {
		
		$source = JRequest::getVar('file');
		$model = $this->getModel('Lucene');
		$indexId = $model->getIdByFile($source);
		if($indexId !== FALSE) {
			$model->delete($indexId, true);
		}
		
		$this->index($ajax);
	}
	
	function optimize($ajax = false) {
		$option = JRequest::getCmd('option');
		
		$model = $this->getModel('Lucene');
		
		$model->optimize();
		if(!$ajax){
			$this->setRedirect('index.php?task=lucene.&option=' . $option, JText::_('OPTIMIZATION_COMPLETED') );
		}
		jexit();
	}

	function checkIndex($ajax = false) {
		$source = JRequest::getVar('file');
		$model = $this->getModel('Lucene');
		// decode file path from GET
		$sourceDecode = urldecode($source);

		
		//$result = $model->getIdByFile($source);
		$result = jifilehelper::checkIndex($sourceDecode, $model);
		if($ajax) {
			$document = JFactory::getDocument();
			// Set the MIME type for JSON output.
			$document->setMimeEncoding( 'application/json' );
			if($result === FALSE) {
				$json['result'] = 'false';
				$json['text'] = JText::_('NO_INDEXED');
				$json['text_man'] = JText::_('MANUAL_INDEXING');
			} else {
				$json['result'] = 'true';
				$json['text'] = JText::_('Indexed');
			}
			echo json_encode($json);
			jexit();
		}
		
		return $result;
	}
	
	function delete($all) {
		$option = JRequest::getCmd('option');
		$model = $this->getModel('Lucene');
		
		if ($all) {
			$model->deleteAll();
		} else {
			$ids = JRequest::getVar('indexId', array());
			
			foreach ($ids as $id) {
				list($iddoc, $key) = explode("|", $id);
				$model->delete($iddoc);
			}
		}
		$this->setRedirect('index.php?option='.$option.'&task=lucene.', JText::_('FILES_DELETED_FROM_THE_INDEX'));
	}

}
