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
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/jifilehelper.php');

class JifileViewFrontpage extends JViewLegacy {
	
	function display($tpl = null) {
		
		$filter = array();
		$filter['context'] = array('type' => 's', 'value' => 'admin');
		$filter['published'] = array('type' => 'i', 'value' => 1);
		$filter['type'] = array('type' => 'i', 'value' => 2, 'operation' => '!=');
		$order = array();
		$order['ordering'] = 'asc';
//		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_jifile/tables/');
		$tableAddon = JTable::getInstance('Addon', 'JifileTable');
		$addon = $tableAddon->getAddon($filter, $order);
		
		$this->setToolbar();
		$this->setDocument($addon);
		
		$params 			= JComponentHelper::getParams( 'com_jifile' );
		$basepath['dir'] 	= jifilehelper::getCorrectPath($params->get( 'base_path' ), true);
		$indexpath['dir'] 	= jifilehelper::getIndexPath();

		$basepath['exists'] = (!empty($basepath['dir']) && is_dir(JPATH_SITE.$basepath['dir']));
		$indexpath['exists'] = (!empty($indexpath['dir']) && is_dir(JPATH_SITE.$indexpath['dir']));
		
		if($basepath['exists']) {
			$ignoreFile = $params->get( 'ignoreFile', array() );
			if(!empty($ignoreFile)) {
				$ignoreFile = explode(',', preg_replace('/\\s/', '', $ignoreFile));
			}
			$basepath['size'] = jifilehelper::getDirectorySize(JPATH_SITE.$basepath['dir'], $ignoreFile);
		}
		
		if($indexpath['exists']) {
			$indexpath['size'] = jifilehelper::getDirectorySize(JPATH_SITE.$indexpath['dir']);
			
			$model = $this->getModel('Lucene');
			$error_lucene = $model->getError();
			$index = $model->getIndex();
			
			//$addons = JTable::getInstance('Documents', 'JiFileTable');
			//$totFileDB = $addons->find(array(), 'count(1) as tot', '');
			
			$count = ($error_lucene) ? 0 : $index->count(); //Totale file inseriti
			$numDocs = ($error_lucene) ? 0 : $index->numDocs(); //Totale file indicizzati
			$numDelete = ($error_lucene) ? 0 : $count-$numDocs; //Totale file eliminati
			$optimize = ($error_lucene) ? 1 : $index->hasDeletions(); //Ottimizzazione
			
			//$sync = ($count != intval($totFileDB[0]->tot));
			
			$this->assign('count', $count);
			$this->assign('numDocs', $numDocs);
			$this->assign('numDelete', $numDelete);
			$this->assign('optimize', $optimize);
			//$this->assign('sync', $sync);
		}
		
		$reportCheck = $this->getReportCheck();
		$canDo = jifilehelper::getActions();
		
		$this->assign('basepath', $basepath);
		$this->assign('indexpath', $indexpath);
		$this->assign('reportCheck', $reportCheck);
		$this->assign('addon', $addon);
		$this->assign('countaddon', count($addon));
		$this->assign('canDo', $canDo);
		
		parent::display($tpl);
	}
	
	/**
	 * Return string of link
	 * 
	 * @param array $values
	 * @return string
	 */
	function getHrefAddon($values) {
		// create href
		$arrayHref = array();
		// define option
		$arrayHref[] = "option=".$values['option'];
		// define task
		if (!empty($values['task'])) {
			$arrayHref[] = "task=".$values['task'];
		}												
		// define view
		if (!empty($values['view'])) {
			$arrayHref[] = "view=".$values['view'];
		}
		// define template
		if (!empty($values['template'])) {
			$arrayHref[] = "tmpl=".$values['template'];
		}
		// implode array href
		$strHref = implode("&", $arrayHref);
	
		return $strHref;	
	}
	
	function getReportCheck() {
		require_once JPATH_IFILE_LIBRARY.'/ifile/servercheck/LuceneServerCheck.php';
		
		$check = LuceneServerCheck::getInstance();
		$check->serverCheck();
		
		$reportCheckLucene = new ReportCheck(false, 'Zend Cache', 'Not present', 'Version 1.10 or later', 'Install Zend Framework', 'http://www.zend.com', 'Used by JiFile');
		
		// verifica l'esistenza delle librerie Zend_Cache 
		if ($includepath = $this->checkPearFile('Zend/Cache.php')) {
			include_once $includepath.'/Zend/Version.php';
			$checkZversion = (version_compare(Zend_Version::VERSION, LuceneServerCheck::ZENDVERSION, '<')) ? ' but Wrong version' : '';
			
			$reportCheckLucene->setCheck(empty($checkZversion));
			$reportCheckLucene->setMessage('Exists'.$checkZversion);
			$reportCheckLucene->setInfo('Zend Cache is installed in '.$includepath.' Version '.Zend_Version::VERSION);
		}
		
		$report = $check->getReportCheck();
		$ifile = array('iFile Framework' => array('iFile' => $this->checkIFile()));
		$report = array_merge($ifile, $report);
		$report['Zend Framework']['Cache'] = $reportCheckLucene;
		
		$os = strtoupper(substr(PHP_OS, 0, 3));
		if(
		($os === 'FRE' && !file_exists(JPATH_IFILE_LIBRARY.'/ifile/adapter/helpers/binaries/freebsd/pdftotext')) ||
		($os === 'DAR' && !file_exists(JPATH_IFILE_LIBRARY.'/ifile/adapter/helpers/binaries/osx/pdftotext')) ||
		!in_array($os, array('WIN', 'LIN'))
		)
		{
			$report['XPDF']['PDFTOTEXT']->setMessage('Download PATCH <a href="http://www.isapp.it/download-jifile.html?download=23">LINK</a>!');
		}
		return $report;
	}
	
	private function checkIFile() {
		$reportCheckIfile = new ReportCheck(false, 'iFile Framework', 'Not present', 'Version 1.1.6 or later', 'Install iFile Framework', 'http://ifile.isapp.it', 'Used by JiFile');
		
		// verifica l'esistenza delle librerie iFile
		$ifile_path = JPATH_IFILE_LIBRARY.'/ifile/IFileVersion.php';
		if (file_exists($ifile_path)) {
			include_once $ifile_path;
			$checkVersion = (version_compare(IFileVersion::VERSION, IFILEVER_REQ, '<')) ? ' but Wrong version' : '';
				
			$reportCheckIfile->setCheck(empty($checkVersion));
			$reportCheckIfile->setMessage('Exists'.$checkVersion);
			$reportCheckIfile->setInfo('iFile Framework is installed Version '.IFileVersion::VERSION);
		}
		return $reportCheckIfile;
	}
	
	/**
	 * Ritorna true se il file esiste
	 *  
	 * @param string $file
	 * @return bool
	 */
	private function checkPearFile($file) {
		// cicla per tutti i path fdefiniti nel php.ini
		//$include_path = explode(':', get_include_path());
		$include_path = explode(PATH_SEPARATOR, get_include_path());
		foreach ($include_path as $val) {
			if (file_exists(realpath($val).DS.$file)) {
				 return $val;
			}
		}
		//die();
		return false;
	}
	
	function setToolbar($option = null) {
		// @TODO
		// Inserirlo nella lingua e magari montare una descrizione del tipo
		// JiFile - Search in your documents
		JToolBarHelper::title('JiFile', 'logo');
		$canDo = jifilehelper::getActions();
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_jifile');
		}
	}
	
	function setDocument(&$addons = array()) {
		require_once(JPATH_ADMINISTRATOR.'/components/com_jifile/helpers/interface/jifilepluginfactory.php');
		$JiFilePluginFactory = JiFilePluginFactory::getInstance();
		// JDocument
		$doc = JFactory::getDocument();
		JHtml::_('behavior.framework', true);
		// jifile
		$doc->addStyleSheet( '../administrator/components/com_jifile/css/ifile.css?'.JIFILEVER );
		jifilehelper::addJQuery(array('colorbox'));
		$doc->addScript( '../administrator/components/com_jifile/js/checkVer.js?'.JIFILEVER );
		// addon
		foreach ($addons as $addon) {
			$addonPath = $JiFilePluginFactory->getPathAddon($addon, true);
			// se esiste il componente 
			if (!empty($addonPath)) {
				list ($context, $name) = explode('.', $addon['addon']);
				$js = "js/".$name."_admin_controlpanel.js";
				$filename = $addonPath['path'].$js;
				// verifico la presenza del file js				
				if (file_exists($filename)) {
					$doc->addScript( $addonPath['web'].$js.'?'.JIFILEVER );
				} 
			}		
		}
	}
}
