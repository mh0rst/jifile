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
 * @version    1.1.1 Adapter_Search_Lucene_Document_MP3.php 2011-08-09 09:57:14
 */

/** Adatpter_Search_Lucene_Document_Abstract */
require_once 'Adapter_Search_Lucene_Document_Abstract.php';
/** MP32txt */
require_once 'helpers/class.mp32txt.php';

/**
 * Adapter per il recupero del contenuto degli ID3 TAG dei file MP3
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class Adapter_Search_Lucene_Document_MP3 extends Adapter_Search_Lucene_Document_Abstract 
{
	public function __construct() {
		// verifica che esista dell'estenzione ID3 per il parser dei TAG ID3
		$serverCheck = LuceneServerCheck::getInstance();
		$serverCheck->serverCheck();
		$reportServerCheck = $serverCheck->getReportCheck();
		$reportCheck = $reportServerCheck['Extension']['id3']; 
		if (!$reportCheck->getCheck()) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception("Extension ID3 not found");
		}
		
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
		return $this->parse();;
    }
	
	/**
	 * Recupera le informazioni del file MP3 el il testo della canzone
	 * 
	 * @return void
	 */
	protected function parse() {
		
		// istanzia la classe per la parserizzazione dei file MP3
		$mp3 = new MP32txt();
    	$result = $mp3->parseTagID3($this->getFilename()); 
		
		if ($result === false) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception('ID3 not found');
		}
		
		require_once 'Zend/Search/Lucene/Document.php';
		
		// Recupera i dati di configurazione 		
		$IfileConfig = IFileConfig::getInstance();		
		// creazuione dell'oggetto Zend_Search_Lucene_Document 
		$doc = new Zend_Search_Lucene_Document();
					
		// Inserisce i dati del file all'interno dell'indice come Field
		// Codificato da
		if ($mp3->issetNotEmpty('encodedBy')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('encodedBy', $mp3->getEncodedBy(), $IfileConfig->getConfig('encoding')));
		}
		// Traccia
		if ($mp3->issetNotEmpty('track')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('track', $mp3->getTrack(), $IfileConfig->getConfig('encoding')));
		}
		// Pubblicato
		if ($mp3->issetNotEmpty('publisher')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('publisher', $mp3->getPublisher(), $IfileConfig->getConfig('encoding')));
		}
		// Disco
		if ($mp3->issetNotEmpty('partOfASet')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('partOfASet', $mp3->getPartOfASet(), $IfileConfig->getConfig('encoding')));
		}
		// Battiti al minuto
		if ($mp3->issetNotEmpty('bpm')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('bpm', $mp3->getBpm(), $IfileConfig->getConfig('encoding')));
		}
		// originalArtist
		if ($mp3->issetNotEmpty('originalArtist')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('originalArtist', $mp3->getOriginalArtist(), $IfileConfig->getConfig('encoding')));
		}
		// Copyright
		if ($mp3->issetNotEmpty('copyright')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('copyright', $mp3->getCopyright(), $IfileConfig->getConfig('encoding')));
		}
		// Gruppo
		if ($mp3->issetNotEmpty('band')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('band', $mp3->getBand(), $IfileConfig->getConfig('encoding')));
		}
		// Genere
		if ($mp3->issetNotEmpty('genre')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('genre', $mp3->getGenre(), $IfileConfig->getConfig('encoding')));
		}
		// Compositore
		if ($mp3->issetNotEmpty('composer')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('composer', $mp3->getComposer(), $IfileConfig->getConfig('encoding')));
		}
		// Anno
		if ($mp3->issetNotEmpty('year')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('year', $mp3->getYear(), $IfileConfig->getConfig('encoding')));
		}
		// Titolo
		if ($mp3->issetNotEmpty('title')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('title', $mp3->getTitle(), $IfileConfig->getConfig('encoding')));
		}
		// Album
		if ($mp3->issetNotEmpty('album')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('album', $mp3->getAlbum(), $IfileConfig->getConfig('encoding')));
		}
		// Artista
		if ($mp3->issetNotEmpty('artist')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('artist', $mp3->getArtist(), $IfileConfig->getConfig('encoding')));
		}
		// Contenuto del TAG
		$doc->addField(Zend_Search_Lucene_Field::UnStored('body', $mp3->getTextTag(), $IfileConfig->getConfig('encoding')));
		
		return $doc;
    }
}
?> 