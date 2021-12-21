<?php
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage helpers
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.0 IFileQueryRegistry.php 2011-02-07 12:31:45
 */

/** IFileQuery */
require_once 'IFileQuery.php';

/**
 * Registro dei termini da ricercare 
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileQueryRegistry {
	
	/**
	 * Registro dei termini da ricercare
	 * @var array
	 */
	private $registry = array();
	
	public function __construct() {}
	
	/**
	 * Setta le proprieta' della query
	 * 
	 * @param string $term
	 * @param string $field [optional]
	 * @param mixed $match [optional]
	 * @param integer $position [optional]
	 * @param string $encoding [optional]
	 * @return void
	 */
	public function setQuery($term, $field = null, $match = null, $position = null, $encoding = '') {
		// verifica la correttezza del valore del match
		$refl   = new ReflectionClass('IFileQuery');
		$consts = $refl->getConstants();
		
		if (!in_array($match, $consts, true)) {
			require_once 'IFile_Query_Exception.php';
			throw new IFile_Query_Exception('Query does not match correctly.');
		}	
		
		$this->checkTerm($term);
		$this->checkField($field);
		$this->checkPosition($position);
		$this->checkEncoding($encoding);
		
		$query = new IFileQuery();
		$query->setTerm($term);
		$query->setField($field);
		$query->setMatch($match);
		$query->setPosition($position);
		$query->setEncoding($encoding);
		
		array_push($this->registry, $query);
	}
	
	/**
	 * Ritorna l'array delle query
	 * 
	 * @return array 
	 */
	public function getQuery() {return $this->registry;}
	
	/**
	 * Ritorna il termine con ID ricercato.
	 * 
	 * Se ID del termine non esiste allora ritorna null 
	 * 
	 * @return IFileQuery 
	 */
	public function getTerm($id) {
		
		if (isset($this->registry[$id])) { 
			return $this->registry[$id];
		}
		
		return null;
	}
	
	/**
	 * Ritorna il numero di query settate
	 * 
	 * @return integer 
	 */
	public function count() {return count($this->registry);}
	
	/**
	 * Controllo sul termine
	 * 
	 * Questo metodo potrebbe essere implementato estendendo la 
	 * classe IFileQueryRegistry per gestire eventuali controlli 
	 * sui termini di ricerca.
	 * Invoca una eccezione di tipo IFile_Query_Exception in caso di errore
	 * 
	 * @param string $term
	 * @return void 
	 * @throws IFile_Query_Exception 
	 */
	public function checkTerm($term) {}	 
	
	/**
	 * Controllo sul field
	 * 
	 * Questo metodo potrebbe essere implementato estendendo la 
	 * classe IFileQueryRegistry per gestire eventuali controlli 
	 * sui campi di ricerca
	 * Invoca una eccezione di tipo IFile_Query_Exception in caso di errore
	 *  
	 * @param string $field
	 * @return void
	 * @throws IFile_Query_Exception 
	 */
	public function checkField($field) {}
	
	/**
	 * Controllo sulla posizione
	 * 
	 * Questo metodo potrebbe essere implementato estendendo la 
	 * classe IFileQueryRegistry per gestire eventuali controlli 
	 * sulla posizione del termine per le frasi
	 * Invoca una eccezione di tipo IFile_Query_Exception in caso di errore
	 *  
	 * @param string $field
	 * @return void
	 * @throws IFile_Query_Exception 
	 */
	public function checkPosition($position) {}
	/**
	 * Controllo sul tipo di Encoding
	 * 
	 * Questo metodo potrebbe essere implementato estendendo la 
	 * classe IFileQueryRegistry per gestire eventuali controlli 
	 * sul tipo di encoding permesso del termine per le frasi
	 * Invoca una eccezione di tipo IFile_Query_Exception in caso di errore
	 *  
	 * @param string $field
	 * @return void
	 * @throws IFile_Query_Exception 
	 */
	public function checkEncoding($encoding) {}
}
?>