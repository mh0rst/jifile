<?php
/**
 * JiFile
 * 
 * @category   Joomla1.6!
 * @package    com_jifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo 
 * @copyright  Copyright (C) 2011 isApp.it - All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once("jifileaddoninterface.php");
require_once("jifileluceneplugininterface.php");
require_once("jifileplugininterface.php");
require_once JPATH_IFILE_LIBRARY.'/ifile/servercheck/ReportCheck.php';

/**
 * Interface of JiFile Plugin
 *
 * @category   Joomla1.6!
 * @package    com_jifile
 * @subpackage helpers/interface
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright  Copyright (C) 2011 isApp.it - All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
abstract class JiFileAddonAbstract implements JiFileAddonInterface, JiFileLucenePluginInterface, JiFilePluginInterface
{
	/**
	 * Information of Plugin/Addon
	 * @var array 
	 */
	private $plugin;
	
	/**
	 * Information ReportCheck of Plugin/Addon
	 * @var array
	 */
	private $reportCheck;
	
	/**
	 * @var
	 */
	private $check = true;
	
	/**
	 * list of function
	 * @var array
	 */
	private $fun;
	
	/**
	 * Return the registry of the ReportCheck
	 * 
	 * @return array
	 */
	public function getReportCheck() {
		return $this->reportCheck;
	}
	
	// method common
	
	/**
	 * Insert ReportCheck Object in registry 
	 *
	 * @param string $addon
	 * @param string $caption
	 * @param object $reportCheck	 
	 * @return void
	 */
	protected function pushReportCheck($caption, $type, $reportCheck) {
		if (!isset($this->reportCheck[$caption])) $this->reportCheck[$caption] = array();
		if (!isset($this->reportCheck[$caption][$type])) $this->reportCheck[$caption][$type] = array();
		$this->reportCheck[$caption][$type] = $reportCheck;
	}
	
	/**
	 * Check Library
	 * 
	 * @param array $checkExt
	 * @return void 
	 */
	protected function checkPHPLib() {
		// recupera la lista delle librerie installate 
		$extension = get_loaded_extensions();
		
		// librerie da verificare
		$checkExt = $this->getListExtension();
		// ritorna un array con le librerie non installate
		$diff = array_diff($checkExt['ext'], $extension);
		$keyDiff = array_keys($diff);
		
		foreach ($checkExt['ext'] as $k => $ext) {
			// versione minima richiesta
			$version = ($checkExt['version'][$k]) ? 'Version '.$checkExt['version'][$k].' or later' : 'Not defined' ; 
			// inizializza l'oggetto per il report
			$reportCheck = new ReportCheck(false, $ext, 'KO', $version, 'Install library in PHP', $checkExt['link'][$k], $checkExt['use'][$k]);
			
			// controllo se la libreria e' installata  			
			if (!in_array($k, $keyDiff)) {
				$version = $checkExt['version'][$k];
				$extVersion = phpversion($ext); 
				// verifico la versione solo se esiste nell'estensione
				if ($version && !empty($extVersion) && (strnatcmp($extVersion, $version) < 0)) {
					$reportCheck->setInfo('Install new version in PHP');
				} else {
					$reportCheck->setCheck(true);
					$reportCheck->setMessage('OK');
					$version = (!empty($extVersion)) ? 'Version installed is '.$extVersion : 'Not check version';
					$reportCheck->setInfo($version);	
				} 
			}
			
			$this->pushReportCheck('Extension', $k, $reportCheck);
		}
	}
	
	/**
	 * Check PHP Function
	 * 
	 * @param array $funct
	 * @return void
	 */
	protected function checkPHPFunction() {
		$funct = $this->getListFunction();
		
		if (!empty($funct)) {
			foreach($funct['fun'] as $k => $fun) {
				// inizializza l'oggetto per il report
				$reportCheck = new ReportCheck(false, $fun, 'KO', 'Not defined', 'This function not exist in PHP', $funct['link'][$k], $funct['use'][$k]);
				if (function_exists($fun)) {
					$reportCheck->setCheck(true);
					$reportCheck->setMessage('OK');
					$reportCheck->setInfo('Function exists');
				}
				
				$this->pushReportCheck('Function', $k, $reportCheck);
			}			
		}
	}
	
	/**
	 * Set list of function to check
	 * 
	 * @return void
	 */
	protected function setListFunction($fun, $link = false, $use = false) {
		$this->fun['fun'][$fun] 	= $fun;
		$this->fun['link'][$fun] 	= $link;
		$this->fun['use'][$fun] 	= $use;
	}
	
	/**
	 * Return list of the function to check
	 * 
	 * @return array
	 */
	protected function getListFunction() {
		return $this->fun;
	}
	
	/**
	 * Set list of Extension to check
	 * 
	 * @return void
	 */
	protected function setListExtension($ext, $version = false, $link = false, $use = false) {
		$this->ext['ext'][$ext] 	= $ext;
		$this->ext['version'][$ext] = $version;
		$this->ext['link'][$ext] 	= $link;
		$this->ext['use'][$ext] 	= $use;
	}
	
	/**
	 * Return list of the Extension to check
	 * 
	 * @return array
	 */
	protected function getListExtension() {
		return $this->ext;
	}
	
	/**
	 * Get check from report check
	 * 
	 * @return bool
	 */
	protected function getCheck() {
		foreach($this->reportCheck as $caption => $check) {
			foreach($check as $obj){
				if(!$obj->getCheck()) {
					return false;
				}
			}
		}
		return true;
	}  
	
	/**
	 * Set Plugin
	 * @param array Plugin Informations
	 * @return void
	 */
	public function setPlugin($plugin) {
		$this->plugin = $plugin;
	}
	
	/**
	 * Get Plugin
	 * 
	 * @return array
	 */
	public function getPlugin() {
		return $this->plugin;
	}  
	
	/**
	 * Get Plugin information
	 * 
	 * @param string $option
	 * @return mixed
	 */
	public function getPluginOption($option) {
		if (isset($this->plugin[$option]))
			return $this->plugin[$option];		
		
		return null;
	} 
	
	// JiFileAddonInterface
	
	/**
	 * Return FALSE if addon present an error
	 * 
	 * @return bool
	 */
	public function reportCheck() {
		return true;
	}
	
	/**
	 * Return list of report check
	 * 
	 * @return array
	 */
	public function getListReportCheck() {
		return array();
	}
	
	// JiFileLucenePluginInterface

	/**
	 * Set Toolbar
	 *  
	 * @return void 
	 */
	public function setToolbar() {}
	
	/**
	 * Set Document
	 *  
	 * @return void  
	 */
	public function setDocument() {}
	
	/**
	 * Print Filters
	 * 
	 * @return void
	 */
	public function printFilter() {}
	
	/**
	 * Print Action
	 * @param int $id Id of the Lucene Document
	 * @param obj $doc document of Lucene Search Document
	 * @return void
	 */
	public function printAction($id, $doc) {}
	
	/**
	 * Set Tag Hidden 
	 * 
	 * @return void
	 */
	public function setTagHidden() {}
	
	/**
	 * Return filter for Lucene Documents 
	 * 
	 * @return array
	 */
	public function luceneFilter() {}
	
	/**
	 * Return list of filter name 
	 * 
	 * @return array
	 */
	public function getFiltersName() {return false;}
	
	// JiFilePluginInterface
	
	/**
	 * Content Search
	 * 
	 * @return 
	 */
	public function onContentSearch() {}
	
}
?>