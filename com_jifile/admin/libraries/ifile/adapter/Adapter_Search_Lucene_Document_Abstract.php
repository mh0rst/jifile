<?php
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.0 Adatpter_Search_Lucene_Document_Abstract.php 2011-01-10 13:54:11
 */

/** Adatpter_Search_Lucene_Document_Interface */
require_once 'Adapter_Search_Lucene_Document_Interface.php';
/** LuceneDataIndexBean */
require_once 'beans/LuceneDataIndexBean.php';
/** AdapterHelper */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'helpers/AdapterHelper.php';

/**
 * Classe astratta che implementa l'interfaccia per gli Adapter.
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
abstract class Adapter_Search_Lucene_Document_Abstract implements Adapter_Search_Lucene_Document_Interface {
	
	/**
	 * @var msgError
	 */
	private $msgError = '';
	
	/**
	 * @var string
	 */
	private $filename = '';
	
	/**
	 * Bean dei dati da indicizzare
	 * 
	 * @var LuceneDataIndexBean 
	 */
	protected $indexValues = null;
	
	/**
	 *  
	 * @return void 
	 */
	public function __construct() {}
	
	/**
	 * Implementa l'interfaccia  
	 * 
	 * @return Zend_Search_Lucene_Document
	 */
	public function loadParserFile() {
		return null;
	}
	
	/**
	 * Ritorna un oggetto Zend_Search_Lucene_Document per l'indicizzazione  
	 * 
	 * @return Zend_Search_Lucene_Document
	 */
	public function loadParserCustom($property) {
		return null;
	}
	
	/**
	 * Implementa l'interfaccia
	 * 
	 * @see Adatpter_Search_Lucene_Document_Interface
	 * @throws Lucene_Exception
	 */
	public function setFilename($filename) {
		
		// controlla che sia un file
		if(is_dir($filename) || !is_file($filename)) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception('File does not exist or is corrupted');
		}
		
		$this->filename = $filename;
	}
	
	/**
	 * Implementa l'interfaccia
	 * 
	 * @see Adatpter_Search_Lucene_Document_Interface
	 */
	public function getFilename() {
		return $this->filename;
	}
	
	/**
	 * Metodo astratto per il processo di parserizzazione del file
	 * che non sono gestiti da Zend Framework
	 * 
	 * @return void
	 */
	protected function parse() {
		return;	
	}	
}
?>