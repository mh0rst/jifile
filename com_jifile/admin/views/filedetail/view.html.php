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

class JifileViewFiledetail extends JViewLegacy {
	
	function display($tpl = null) {
		$tpl = JRequest::getVar('tmpl', null);
		$act = JRequest::getVar('act', null);

		if(!is_null($tpl)) {
			$document = JFactory::getDocument();
			$document->addStyleSheet( '../administrator/components/com_jifile/css/ifile.css?'.JIFILEVER );
		}
		
		$id = JRequest::getVar('id', false);
		
		if($id === false) {
			$this->assign('doc', null);
			parent::display($tpl);
			return false;
		}
		
		$model = $this->getModel();
		$index = $model->getIndex();
		$doc = $index->getDocument($id);
		$fieldNames = $doc->getFieldNames();
		$ifiledoc = null;
		$error = null;
		
		if ($act == 'filecontent') {
			//$filename = jifilehelper::retrievePath($doc->getFieldValue('filename'));
			//$filename = $doc->getFieldValue('path');DS.$filename
			$filename = jifilehelper::getCorrectFilename($doc->getFieldValue('path'), true);
			$ifiledoc = $model->getLuceneDoc($filename, true, false);
			$error = (!$ifiledoc) ? $model->getError() : false;
			
			$config = IFileConfig::getInstance();
			$bodyField = $config->getDocumentField('body');
			$encoding = (empty($bodyField['encoding'])) ? '' : $bodyField['encoding'];
			
			$this->assign('encoding', $encoding);
		}

		$this->assign('doc', $doc);
		$this->assign('fieldNames', $fieldNames);
		$this->assign('id', $id);
		$this->assign('ifiledoc', $ifiledoc);
		$this->assignRef('error', $error);
		
		parent::display($tpl);
	}
}
