<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once(JPATH_SITE.'/administrator/components/com_jifile/helpers/jiFileConfig.php');

// JModelForm
class JifileModelConfig extends JModelLegacy {
	
	private $fileXml = '';
	private $fileXpdfrc = '';
	private $xml = null;
	private $config = null;
	
	function __construct() {
		$this->fileXml 		= JPATH_IFILE_LIBRARY.'/ifile/config/IFileConfig.xml';
		$this->fileXpdfrc 	= JPATH_IFILE_LIBRARY."/ifile/adapter/helpers/binaries/xpdfrc/xpdfrc";
		
		parent::__construct();
	}
	
	function getConfig($check = false) {
		if(is_null($this->config)) {
			$this->config = jiFileConfig::getInstance();
			if(!JRequest::getVar('xml', false)) {				
				if($this->config->getErrors()) {
					$msg = JText::_('THE_FOLLOWING_ERRORS_HAVE_OCCURRED').': <br/><br/>'.implode('<br/>', $this->config->getErrors());
					if(!$check) {
						JFactory::getApplication()->enqueueMessage($msg, 'error');
					}
					$this->setError($msg);
				}
			}
		}		
		return $this->config;
	}
	
	function save($reqXml) {
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/jifilehelper.php');
		$this->delete();
		$xml = $this->getXml();
		
		$reqXml = jifilehelper::array_filter_recursive($reqXml);

		if($this->check($reqXml)) { 
			$newXml = jifilehelper::array2Xml($reqXml, 'ifile', $xml);
			$this->saveToFile($newXml);
			
			// @TODO
			// chiamata al metodo di set del plugin
			// $plugin->saveConfigParams();
			
			return true;
		}
		
		return false;
	}
	
	function check(&$reqXml) {
		
		$this->getConfig();
		
		$this->config->resetErrors();
		
		//check root-application
		$this->config->checkRootApplication(isset($reqXml['root-application']) ? $reqXml['root-application'] : '');
			
		//check timelimit
		if(isset($reqXml['timelimit'])) {
			$this->config->checkTimelimit($reqXml['timelimit']);
		}
		//check memorylimit
		if(isset($reqXml['memorylimit'])) {
			$this->config->Memorylimit($reqXml['memorylimit']);
		}
		//check analyzer
		$analyzerFile = isset($reqXml['analyzer']['type']['custom-default']['@value']) ? $reqXml['analyzer']['type']['custom-default']['@value'] : null;
		$analyzerClass = isset($reqXml['analyzer']['type']['custom-default']['attributes']['class']) ? $reqXml['analyzer']['type']['custom-default']['attributes']['class'] : null;
		if($analyzerFile || $analyzerClass ) {
			$this->config->checkAnalyzer($analyzerFile, $analyzerClass);
		}
		//check stop-words
		if(isset($reqXml['analyzer']['filters']['stop-words'])) {
			$this->config->checkStopWords($reqXml['analyzer']['filters']['stop-words']);
		}
		// check doctotxt
		if (isset($reqXml['doctotxt']['attributes']['type']) && $reqXml['doctotxt']['attributes']['type'] != 'ANTIWORD') {
			$reqXml['doctotxt']['attributes']['encoding'] = "";
		}		
		//check custom-filters
		if(isset($reqXml['analyzer']['filters']['custom-filters']['filter'])) {
			foreach ($reqXml['analyzer']['filters']['custom-filters']['filter'] as $key => $filter) {
				$filterFile = isset($filter['@value']) ? $filter['@value'] : null;
				$filterClass = isset($filter['attributes']['class']) ? $filter['attributes']['class'] : null;
				$this->config->checkTokenFilter($filterFile, $filterClass, '('.($key+1).')');
			}
		}
		// check XPDF
		if (isset($reqXml['xpdf'])) {
			$pdftotext = trim($reqXml['xpdf']['pdftotext']['executable']);
			$pdftotextRC = trim($reqXml['xpdf']['pdftotext']['xpdfrc']);
			$pdfinfo = trim($reqXml['xpdf']['pdftotext']['executable']);
			$pdfinfoRC = trim($reqXml['xpdf']['pdftotext']['xpdfrc']);
		}
		
		if(!empty($pdftotext)) {
			$this->config->checkCustomXPDF($pdftotext, "pdftotext");
		}
		if(!empty($pdftotextRC)) {
			$this->config->checkCustomXPDF($pdftotextRC, "xpdfrc");
		}
		if(!empty($pdfinfo)) {
			$this->config->checkCustomXPDF($pdfinfo, "pdftotext");
		}
		if(!empty($pdfinfoRC)) {
			$this->config->checkCustomXPDF($pdfinfoRC, "xpdfrc");
		}
		
		// @TODO
		// Addon:
		// chiamata al metodo per il controllo dei dati 
		// $plugin->checkConfigXmlFields($this, $reqXml);

		if($this->config->getErrors()) {
			$this->_errors = $this->config->getErrors();
			return false;
		}
		
		return true;
	}
	
	function delete() {
		$xml = $this->getXml();
		$nodes = array();
		foreach ($xml as $key => $node) {
			$nodes[$key] = $key; 
		}
		
		foreach ($nodes as $key => $node) {
			unset($xml->$key);
		}
		
		$this->xml = $xml;
	}
	
	function saveToFile($xml) {
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = false;
		$dom->loadXML($xml);
		$dom->formatOutput = true;
		$formatedXML = $dom->saveXML();
		
		file_put_contents($this->fileXml, $formatedXML);
	} 
	
	private function setXml() {
		if(is_null($this->xml)) {
			$xml = simplexml_load_file($this->fileXml);
			$this->xml = $xml;
		}
	}
	
	public function getXml() {
		if(is_null($this->xml)) {
			$this->setXml();
		}
		
		return $this->xml;
	}
	
	/**
	 * Get content from XPDFRC file
	 * @return stdClass
	 */
	public function getXpdfrcSource() {
		
		$item = new stdClass;
		$item->filename = $this->fileXpdfrc;
		
		if (!empty($this->fileXpdfrc) && file_exists($this->fileXpdfrc)) {
			// check Permission writable:
			if (!JPath::setPermissions($this->fileXpdfrc, '0644')) {
				$item->error = JText::sprintf('JIFILE_ERROR_SOURCE_FILE_NOT_WRITABLE', $this->fileXpdfrc);				
			} else {
				$item->source = file_get_contents($this->fileXpdfrc);	
			}						
		} else {
			$item->error = JText::sprintf('JIFILE_ERROR_SOURCE_FILE_NOT_FOUND', $this->fileXpdfrc);			
		}
		
		return $item;
	}
		
	/**
	 * Method to store the source file contents.
	 *
	 * @param   array  The souce data to save.
	 *
	 * @return  boolean  True on success, false otherwise and internal error set.
	 * @since   1.6
	 */
	public function saveXpdfrcSource($data)
	{
		jimport('joomla.filesystem.file');
		// save file
		$return = JFile::write($this->fileXpdfrc, $data);

		if (!$return)
		{
			$this->setError(JText::sprintf('JIFILE_ERROR_FAILED_TO_SAVE_FILENAME', $this->fileXpdfrc));
			return false;
		}

		return true;
	}
}