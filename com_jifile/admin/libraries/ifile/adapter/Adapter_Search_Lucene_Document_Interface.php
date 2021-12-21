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
 * @version    1.0 Adapter_Search_Lucene_Document_Interface.php 2011-01-10 13:54:11
 */

/**
 * Elemento di interfaccia per gli Adapter
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
interface Adapter_Search_Lucene_Document_Interface {
	/**
	 * Ritorna un oggetto Zend_Search_Lucene_Document per l'indicizzazione  
	 * 
	 * @return Zend_Search_Lucene_Document
	 */
	function loadParserFile();
	
	/**
	 * Ritorna un oggetto Zend_Search_Lucene_Document per l'indicizzazione  
	 * Il metodo permette di gestire parser diversi da File
	 * 
	 * @param mixed $property
	 * @return Zend_Search_Lucene_Document
	 */
	function loadParserCustom($property);
	
	/**
	 * Setta il path del file da parserizzare
	 * 
	 * @param string $filename
	 * @return void 
	 */
	function setFilename($filename);
	
	/**
	 * Ritorna il path del file da parserizzare
	 *  
	 * @return string 
	 */
	function getFilename();
}
?>