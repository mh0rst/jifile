<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link			http://jifile.isapp.it
*/
// No direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_IFILE_LIBRARY.'/ifile/IFileConfig.php';
class jiFileConfig extends IFileConfig {
	
	private static $_instance;
	private $_errors = array();
	
	/**
	 * OverWrite del costruttore
	 * @return 
	 */
	protected function __construct() {
		try {
			parent::__construct();			
		} catch (Exception $e) {
			$this->setError(jifilehelper::JText($e->getMessage()));
		}		
	}
	
	/**
	 * Ritorna l'istanza jiFileConfig
	 * @return jiFileConfig
	 */
	static function getInstance() {		
		if (self::$_instance == null) 
			self::$_instance = new jiFileConfig();			
			
		return self::$_instance;		
	}
	
	/**
	 * Setta il messaggio di errore
	 * @param string $error
	 * @return 
	 */
	public function setError($error) {
		array_push($this->_errors, $error);
	}
	
	/**
	 * Ritorna il messaggio di errore
	 * @return string
	 */
	public function getErrors() {
		return $this->_errors;
	}
	
	/**
	 * Resetta l'array dei messagi di errore
	 * @param null
	 * @return
	 */
	public function resetErrors() {
		$this->_errors = array();
	}
	
	/**
	 * Verifica la root application
	 * @param string $root
	 * @return void
	 */	
	public function checkRootApplication($root) {
		if(empty($root)) {
			$this->setError(jifilehelper::JText('Root-application does not exist'));
			return false;
		}
		try {
			parent::checkRootApplication($root);
		} catch (Exception $e) {
			$this->setError(jifilehelper::JText($e->getMessage()));
		}
	}
	/**
	 * Nuova non presente nella ConfigIFile
	 * Verifica che il timelimit sia superiore a 180
	 * @param integer $timelimit
	 * @return 
	 */
	public function checkTimelimit($timelimit) {
		if(!is_numeric($timelimit)) {
			$this->setError(jifilehelper::JText('Timelimit must be integer'));
		} elseif($timelimit < 180) {
			$this->setError(jifilehelper::JText('Timelimit must be at least 180'));
		}
	}
	/**
	 * Nuova non presente nella ConfigIFile
	 * Verifica che la memorylimit sia un numerico
	 * @param object $memorylimit
	 * @return 
	 */
	public function Memorylimit($memorylimit) {
		if(!is_numeric($memorylimit)) {
			$this->setError(jifilehelper::JText('Memorylimit must be integer'));
		}
	}
	/*
	 * Sovrascrive i metodi della IFileConfig e ne gestisce eventuali eccezioni
	 * per i controlli sui dati inseriti nella sezione di configurazione
	 */
	public function checkStopWords ($file) {
		try {
			parent::checkStopWords($file);
		} catch (Exception $e) {
			$this->setError(jifilehelper::JText($e->getMessage()));
		}
	}
	
	public function checkAnalyzer ($fileAnalyzer, $classAnalyzer) {
		try {
			parent::checkAnalyzer($fileAnalyzer, $classAnalyzer);
		} catch (Exception $e) {
			$this->setError(jifilehelper::JText($e->getMessage()));
		}
	}
	
	public function checkTokenFilter ($fileFilter, $classFilter, $prefix = '') {
		try {
			parent::checkTokenFilter ($fileFilter, $classFilter);
		} catch (Exception $e) {
			$this->setError($prefix.jifilehelper::JText($e->getMessage()));
		}
	} 
	
	public function checkCustomXPDF($file, $type = "") {
		try {
			parent::checkCustomXPDF ($file, $type);
		} catch (Exception $e) {
			$this->setError(jifilehelper::JText($e->getMessage()));
		}
	}
}