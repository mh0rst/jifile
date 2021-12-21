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
 * @version    1.1.1 Adapter_Search_Lucene_Document_JPG.php 2011-09-16 19:10:14
 */

/** Adatpter_Search_Lucene_Document_Abstract */
require_once 'Adapter_Search_Lucene_Document_Abstract.php';
/** MP32txt */
require_once 'helpers/class.image2txt.php';

/**
 * Adapter per il recupero del contenuto degli EXIF TAG dei file JPG
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class Adapter_Search_Lucene_Document_JPG extends Adapter_Search_Lucene_Document_Abstract 
{
	public function __construct() {
		// verifica che esista dell'estenzione EXIF per il parser dei TAG EXIF
		$serverCheck = LuceneServerCheck::getInstance();
		$serverCheck->serverCheck();
		$reportServerCheck = $serverCheck->getReportCheck();
		$reportCheck = $reportServerCheck['Extension']['exif']; 
		if (!$reportCheck->getCheck()) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception("Extension EXIF not found");
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
		$image = new Image2Txt();
    	$result = $image->parseTagExif($this->getFilename()); 
		
		if ($result === false) {
			require_once 'Adapter_Search_Lucene_Exception.php';
			throw new Adapter_Search_Lucene_Exception('EXIF not found');
		}
		
		/** Zend_Search_Lucene_Document */
		require_once 'Zend/Search/Lucene/Document.php';
		
		// Recupera i dati di configurazione 		
		$IfileConfig = IFileConfig::getInstance();		
		// creazuione dell'oggetto Zend_Search_Lucene_Document 
		$doc = new Zend_Search_Lucene_Document();
		// @TODO 
		// da rivedere il meccanismo di gestione degli standard Fields
					
		// Inserisce i dati del file all'interno dell'indice come Field
		// Dimensione del file in byte
		if ($image->issetNotEmpty('fileSize')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('FileSize', $image->getFileSize(), $IfileConfig->getConfig('encoding')));
		}
		// Altezza dell'immagine in pixel
		if ($image->issetNotEmpty('height')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('Height', $image->getHeight(), $IfileConfig->getConfig('encoding')));
		}
		// Larghezza dell'immagine in pixel
		if ($image->issetNotEmpty('width')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('Width', $image->getWidth(), $IfileConfig->getConfig('encoding')));
		}
		// Immagine a colori
		if ($image->issetNotEmpty('isColor')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('IsColor', $image->getIsColor(), $IfileConfig->getConfig('encoding')));
		}
		// Apertura dell'obiettivo
		if ($image->issetNotEmpty('apertureFNumber')) {
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('ApertureFNumber', $image->getApertureFNumber(), $IfileConfig->getConfig('encoding')));
		}
		// Commento dell'utente
		if ($image->issetNotEmpty('userComment')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('UserComment', $image->getUserComment(), $IfileConfig->getConfig('encoding')));
		}
		// Descrizione dell'immagine
		if ($image->issetNotEmpty('imageDescription')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('ImageDescription', $image->getImageDescription(), $IfileConfig->getConfig('encoding')));
		}
		// Orientamento
		if ($image->issetNotEmpty('orientation')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('Orientation', $image->getOrientation(), $IfileConfig->getConfig('encoding')));
		}
		// Macchina
		if ($image->issetNotEmpty('make')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('Make', $image->getMake(), $IfileConfig->getConfig('encoding')));
		}
		// Modello Macchina
		if ($image->issetNotEmpty('model')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('Model', $image->getModel(), $IfileConfig->getConfig('encoding')));
		}
		// Software
		if ($image->issetNotEmpty('software')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('Software', $image->getSoftware(), $IfileConfig->getConfig('encoding')));
		}
		// Copyright
		if ($image->issetNotEmpty('copyright')) {
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('Copyright', $image->getCopyright(), $IfileConfig->getConfig('encoding')));
		}
		// Latitudione
		if ($image->issetNotEmpty('GPSLatitude')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('GPSLatitude', $image->getGPSLatitude(), $IfileConfig->getConfig('encoding')));
			// latitudine nel formato googlemap DMS
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('GPSLatitudeGoogle', $image->getGPSLatitudeGoogle(), $IfileConfig->getConfig('encoding')));
			// latitudine nel formato googlemap Decimal
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('GPSLatitudeGoogleDecimal', $image->DMStoDecimal($image->getGPSLatitudeGoogle(), 'LT'), $IfileConfig->getConfig('encoding')));
		}
		// Longitudine
		if ($image->issetNotEmpty('GPSLongitude')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('GPSLongitude', $image->getGPSLongitude(), $IfileConfig->getConfig('encoding')));
			// longitudine nel formato googlemap DMS
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('GPSLongitudeGoogle', $image->getGPSLongitudeGoogle(), $IfileConfig->getConfig('encoding')));
			// longitudine nel formato googlemap Decimal
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('GPSLongitudeGoogleDecimal', $image->DMStoDecimal($image->getGPSLongitudeGoogle(), 'LG'), $IfileConfig->getConfig('encoding')));
			
		}
		// XResolution
		if ($image->issetNotEmpty('XResolution')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('XResolution', $image->getXResolution(), $IfileConfig->getConfig('encoding')));
		}
		// YResolution
		if ($image->issetNotEmpty('YResolution')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('YResolution', $image->getYResolution(), $IfileConfig->getConfig('encoding')));
		}		
		// Data creazione
		if ($image->issetNotEmpty('dateTime')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('DateTime', $image->getDateTime(), $IfileConfig->getConfig('encoding')));
			// Data creazione nel formato standard (vedi Bean)
			$doc->addField(Zend_Search_Lucene_Field::Keyword('created', $image->getDateTime(), $IfileConfig->getConfig('encoding')));
		}
		
		// Modalita' di esposizione
		if ($image->issetNotEmpty('exposureMode')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('ExposureMode', $image->getExposureMode(), $IfileConfig->getConfig('encoding')));
		}
		// Tempo di esposizione
		if ($image->issetNotEmpty('exposureTime')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('ExposureTime', $image->getExposureTime(), $IfileConfig->getConfig('encoding')));
		}
		// Tipo di Scena
		if ($image->issetNotEmpty('sceneCaptureType')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('SceneCaptureType', $image->getSceneCaptureType(), $IfileConfig->getConfig('encoding')));
		}
		// Risorsa di luce
		if ($image->issetNotEmpty('lightSource')) {
			$doc->addField(Zend_Search_Lucene_Field::Keyword('LightSource', $image->getLightSource(), $IfileConfig->getConfig('encoding')));
		}
		
		// Contenuto dei TAG
		$doc->addField(Zend_Search_Lucene_Field::UnStored('body', $image->getTextTag(), $IfileConfig->getConfig('encoding')));
		
		return $doc;
    }
}
?> 