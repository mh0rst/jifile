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
 * @version    1.0.1 Adapter_Search_Lucene_Document_TXT.php 2011-08-16 17:15:39
 */

/** Adatpter_Search_Lucene_Document_Abstract */
require_once 'Adapter_Search_Lucene_Document_Abstract.php';

/**
 * Adapter per il recupero del contenuto dei file TXT
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class Adapter_Search_Lucene_Document_TXT extends Adapter_Search_Lucene_Document_Abstract 
{
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Ritorna un oggetto Zend_Search_Lucene_Document
	 *
	 * Implementa il metodo dell'interfaccia Adatpter_Search_Lucene_Document_Interface
	 * 
	 * @return Zend_Search_Lucene_Document
	 */
	public function loadParserFile()
    {
       // Parserizza il documento
		$this->parse();
		
		// il body deve essere valorizzato
		if (!$this->indexValues->issetNotEmpty('body')) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception('Empty body');	
		}
        
		return $this->indexValues->getLuceneDocument();
    }
	
	/**
	 * Recupera il testo dal file TXT
	 * 
	 * @throws Adapter_Search_Lucene_Exception
	 * @return void
	 */
	protected function parse()
	{
		// creazione del Bean
		$this->indexValues = new LuceneDataIndexBean();
		// recupero il contenuto del file 
		$data = @file_get_contents($this->getFilename());
		
		if ($data === false) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception('Error retrieving the contents of the file');
		}
		
		//$datec = array();
		//$datec['created']  = @filectime($filename);
		//$datec['modified'] = @filemtime($filename);
		
		$this->indexValues->setBody($data);
		//$this->indexValues->setModified( $datec['modified'] );
		//$this->indexValues->setCreated( $datec['created'] );
	}
}