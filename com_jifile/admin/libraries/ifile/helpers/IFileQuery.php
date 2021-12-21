<?php
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage helpers
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.0 IFileQuery.php 2011-02-07 10:39:55
 */

/**
 * Oggetto di definizione dei termini da ricercare all'interno del documento 
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileQuery {
	
	/**
	 * Metodo di ricerca: Tutte le parole
	 */
	const MATCH_REQUIRED 	= true;
	/**
	 * Metodo di ricerca: Una delle parole
	 */
	const MATCH_PROHIBITEN 	= false;
	/**
	 * Metodo di ricerca: Frase esatta
	 */
	const MATCH_OPTIONAL  	= null;	
	/**
	 * Termine
	 * 
	 * @var string
	 */
	private $term;
	/**
	 * Campo
	 * 
	 * @var string
	 */
	private $field = null;
	/**
	 * Matching mode
	 * 
	 * @var mixed
	 */
	private $match = null;
	/**
	 * Posizione del termine
	 * 
	 * @var integer
	 */
	private $position = null;
	/**
	 * Encoding del termine
	 *  
	 * @var string
	 */
	private $encoding = '';
		
	public function __construct() {}
	
	/**
	 * Setta il termione 
	 * 
	 * @param string $term
	 * @return void
	 */
	public function setTerm($term) {$this->term = $term;}
	/**
	 * Ritorna il termine
	 * 
	 * @return string 
	 */
	public function getTerm() {return $this->term;}
	/**
	 * Setta il campo
	 * 
	 * @param string $field
	 * @return void
	 */
	public function setField($field) {$this->field = $field;}
	/**
	 * Ritorna il campo
	 * 
	 * @return string
	 */
	public function getField() {return $this->field;}
	/**
	 * Setta il tipo di confronto
	 * 
	 * @param mixed $match
	 * @return void
	 */	
	public function setMatch($match) {$this->match = $match;}
	/**
	 * Ritorna il tipo di confronto
	 * 
	 * @return mixed
	 */
	public function getMatch() {return $this->match;}
	/**
	 * Setta la posizione del termine all'interno del documento
	 * 
	 * @param integer $position
	 * @return void
	 */
	public function setPosition($position) {$this->position = $position;}
	/**
	 * Ritorna la posizione del termine
	 * 
	 * @return integer 
	 */
	public function getPosition() {return $this->position;}
	/**
	 * Setta l'encoding del termine
	 * 
	 * @param string $encoding
	 * @return void
	 */
	public function setEncoding($encoding) {$this->encoding = $encoding;}
	/**
	 * Ritorna l'encoding del termine
	 * Default: UTF-8
	 * 
	 * @return string 
	 */
	public function getEncoding() {return $this->encoding;}
}
?>