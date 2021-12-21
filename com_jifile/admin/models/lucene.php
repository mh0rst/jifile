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
require_once(JPATH_SITE.'/administrator/components/com_jifile/helpers/jifilehelper.php');
require_once JPATH_IFILE_LIBRARY.'/ifile/IFileFactory.php';

if(file_exists(JPATH_IFILE_LIBRARY.'/ifile/Zend')) {
	$include_path = get_include_path();
	$include_path .= PATH_SEPARATOR.JPATH_IFILE_LIBRARY.'/ifile';
	set_include_path($include_path);
}

class JifileModelLucene extends JModelLegacy {
	
	var $_data;
	private $index_path = null;
	private $lucene = null;
	private $tableDocuments = null;
	
	function __construct() {
		
		$this->index_path = jifilehelper::getIndexPath();

		if(!empty($this->index_path)) {
			$this->index_path = JPATH_SITE.$this->index_path;
			try {
				$IFileFactory = IFileFactory::getInstance();
				$this->lucene = $IFileFactory->getIFileIndexing('Lucene', $this->index_path);
			} catch (Exception $e) {
				$this->setError(jifilehelper::JText($e->getMessage()));
				parent::__construct();
				return false;
			}
		} else {
			$this->setError(JText::_('NO_PATH_CONFIGURED_FOR_THE_INDEX'));
			parent::__construct();
			return false;
		}

		parent::__construct();
	}
	
	public function getIndex_path() {
		return $this->index_path;
	}
	
	public function getIndex() {
		return $this->lucene;
	}
	
	/**
	 * Return the document from KEY field
	 * 
	 * @param string $key
	 * @return Zend_Search_Document
	 */
	public function getDocumentFromKey($key) {
		$query = new IFileQueryRegistry();
		$query->setQuery($key, "key", IFileQuery::MATCH_REQUIRED);
		$hits = $this->getIndex()->query($query);
		
		return $hits;		
	}
	
	private function _buildQuery($search, $field, $searchphrase) {
		$field 	  = ($field == '') ? null : $field;
		$query 	  = new IFileQueryRegistry();
		$jCharset = JFactory::getDocument()->getCharset();
		
		//controllo analyzer, se il nome contiene "caseinsensitive" faccio strtolower
		$iFileConfig = IFileConfig::getInstance();
		$analyzer = $iFileConfig->getConfig('analyzer');
		if(stripos($analyzer, 'caseinsensitive')) {
			$search = JString::strtolower($search);
		}
		
		if($searchphrase == 'exact') {
			$query->setQuery($search, $field, null, null, $jCharset);
			return $query;
		}
		
		// trasforma il termine in token 
		$tokens = Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize($search, $jCharset);
		
		foreach ($tokens as $parola) {
			switch($searchphrase) {
				case 'all':
					$query->setQuery($parola->getTermText(), $field, IFileQuery::MATCH_REQUIRED, null, $jCharset);
				break;
				case 'any':
					$query->setQuery($parola->getTermText(), $field, IFileQuery::MATCH_OPTIONAL, null, $jCharset);
				break;
				case 'wildcard':					
					$query->setQuery('*'.$parola->getTermText()."*", $field, IFileQuery::MATCH_OPTIONAL, null, $jCharset);
					return $query;
				break;
			}
		}
		return $query;
	}
	
	public function search($search, $field = null, $searchphrase = 'any', $order = null, $order_dir = null, $limit = false) {
		$hits = array();
		if(empty($search)) {
			return $hits;
		}
		$lucene = $this->getIndex();
		$query = $this->_buildQuery($search, $field, $searchphrase);	
		
		if($query) {
			if($limit > 0) {
				$lucene->setResultLimit($limit);
			}
			if(!empty($order) && !empty($order_dir)) {
				$order_dir = ($order_dir == 'asc') ? SORT_ASC : SORT_DESC;
				$lucene->setSort($order, SORT_REGULAR, $order_dir);
			}
			
			switch ($searchphrase) {
				case 'exact':
					$hits = $lucene->queryPhrase($query);
					break;
				case 'wildcard':
					Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength(0);
					$hits = $lucene->queryWildcard($query);
					break;
				case 'all':
				case 'any':
				default:
					$hits = $lucene->query($query);
			}
		}
		return $hits;
	}
	
	public function getIdByFile($source) {

		$key = md5_file($source);
		$result = $this->search($key, 'key');
		if($result && count($result) == 1){ 
			$result = array($result[0]->id);
		} else {
			$result = array(false);
		}
		
		return $result[0];
	}
	
	public function index($source, $addField = array()) {
		
		$lucene = $this->getIndex();
		$lucene->setIndexFile($source);
		
		try {
			$this->setMoreInfo($addField);
			$doc = $lucene->addDocument();
			$lucene->commit();
			jifilehelper::deleteFileCache($source);
			jifilehelper::clearCache(array('lucene'));
			// save keyid information in #__jifiledocuments
			$keyid = $doc->getFieldValue('key');
			// add documents in DB
			$this->insertDocuments($keyid);
		} catch (Exception $e) {
			$this->setError(jifilehelper::JText($e->getMessage()));
			return false;
		}
		
		return true;
	}
	
	public function indexManual($fields) {
		// decode filename
		$filename = urldecode($fields['filename']);
		
		if(empty($fields['body'])){
			$this->setError(JText::_('Body').': '.JText::_('REQUIRED_FIELD'));
			return false;
		}
		
		require_once JPATH_IFILE_LIBRARY.'/ifile/adapter/beans/LuceneDataIndexBean.php';
		
		$addField = $fields['add'];
		
		$lucene = $this->getIndex();
		
		try {
			$bean = new LuceneDataIndexBean();
			
			$bean->setBody($fields['body']);
			$bean->setCreated($fields['created']);
			$bean->setCreator($fields['creator']);
			$bean->setKeywords($fields['keywords']);
			$bean->setModified($fields['modified']);
			$bean->setSubject($fields['subject']);
			$bean->setDescription($fields['description']);
			$bean->setTitle($fields['title']);
			
			$doc = $bean->getLuceneDocument();
			
			$IfileConfig = IFileConfig::getInstance();
			// Latitudine Decimal
			if (trim($fields['GPSLatitudeGoogleDecimal']) != '') {
				// latitudine nel formato googlemap Decimal
				$doc->addField(Zend_Search_Lucene_Field::Keyword('GPSLatitudeGoogleDecimal', $fields['GPSLatitudeGoogleDecimal'], $IfileConfig->getConfig('encoding')));
			}
			// Longitudine Decimal
			if (trim($fields['GPSLongitudeGoogleDecimal']) != '') {
				// longitudine nel formato googlemap DMS 
				$doc->addField(Zend_Search_Lucene_Field::Keyword('GPSLongitudeGoogleDecimal', $fields['GPSLongitudeGoogleDecimal'], $IfileConfig->getConfig('encoding')));
			}
			
			$lucene->setIndexFile($filename);
			$this->setMoreInfo($addField);
		
			$resDoc = $lucene->addDocument($doc);
			$lucene->commit();
			//jifilehelper::deleteFileCache($fields['filename']);
			jifilehelper::deleteFileCache($filename);
			jifilehelper::clearCache(array('lucene'));
			// save keyid information in #__jifiledocuments
			$keyid = $doc->getFieldValue('key');
			// add documents in DB
			$this->insertDocuments($keyid);
		} catch (Exception $e) {
			$this->setError(jifilehelper::JText($e->getMessage()));
			return false;
		}
		
		return true;
	}
	
	/**
	 * Insert Key Document in DB
	 * @return 
	 */
	public function insertDocuments($keyid) {
		// save file information in #__jifiledocuments
		// get instance of #__jifiledocuments Table
		if (empty($this->tableDocuments)) {
			$this->tableDocuments = JTable::getInstance("Documents", "JifileTable");
		}
		// check if empty
		if (!empty($keyid) && $this->tableDocuments) {
			// add documents in DB
			$this->tableDocuments->insertDocuments($keyid);
		}
	}
	
	/**
	 * Update field "Delete" of one documents in DB
	 * @return 
	 */
	public function deleteDocuments($keyid) {
		// delete file information in #__jifiledocuments
		// get instance of #__jifiledocuments Table
		if (empty($this->tableDocuments)) {
			$this->tableDocuments = JTable::getInstance("Documents", "JifileTable");
		}
		// check if empty
		if (!empty($keyid)) {
			// add documents in DB
			$this->tableDocuments->updateDeleteDocuments($keyid);
		}
	}
	
	/**
	 * Update field "Delete" of all documents in DB
	 * @return 
	 */
	public function deleteAllDocuments() {
		// delete file information in #__jifiledocuments
		// get instance of #__jifiledocuments Table
		if (empty($this->tableDocuments)) {
			$this->tableDocuments = JTable::getInstance("Documents", "JifileTable");
		}
		// add documents in DB
		$this->tableDocuments->truncateDocuments();
	}
	
	/**
	 * Delete All Documents in DB
	 * @return 
	 */
	public function optimazeDocuments() {
		// delete file information in #__jifiledocuments
		// get instance of #__jifiledocuments Table
		if (empty($this->tableDocuments)) {
			$this->tableDocuments = JTable::getInstance("Documents", "JifileTable");
		}
		// add documents in DB
		$this->tableDocuments->deleteAllDocuments();
	}
	
	/**
	 * Indexing Images Fields
	 * @param array $fields
	 * @return 
	 */
	public function indexManualImages($fields) {
		
		// decode filename
		$filename = urldecode($fields['filename']);
				
		if(empty($fields['body'])){
			$this->setError(JText::_('Body').': '.JText::_('REQUIRED_FIELD'));
			return false;
		}
		
		require_once JPATH_IFILE_LIBRARY.'/ifile/adapter/beans/LuceneDataIndexBean.php';
		$addField = $fields['add'];
		$lucene = $this->getIndex();
		
		try {
			$bean = new LuceneDataIndexBean();
			$bean->setBody($fields['body']);
			$bean->setCreated($fields['created']);
			$bean->setCreator($fields['creator']);
			$bean->setKeywords($fields['keywords']);
			$bean->setModified($fields['modified']);
			$bean->setSubject($fields['subject']);
			$bean->setDescription($fields['ImageDescription']);
			$bean->setTitle($fields['title']);
			
			$doc = $bean->getLuceneDocument();
			
			$IfileConfig = IFileConfig::getInstance();
			// Inserisce i dati del file all'interno dell'indice come Field
			// Dimensione del file in byte
			if (trim($fields['FileSize']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('FileSize', $fields['FileSize'], $IfileConfig->getConfig('encoding')));
			}
			// Altezza dell'immagine in pixel
			if (trim($fields['Height']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('Height', $fields['Height'], $IfileConfig->getConfig('encoding')));
			}
			// Larghezza dell'immagine in pixel
			if (trim($fields['Width']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('Width', $fields['Width'], $IfileConfig->getConfig('encoding')));
			}
			// Immagine a colori
			if (trim($fields['IsColor']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('IsColor', $fields['IsColor'], $IfileConfig->getConfig('encoding')));
			}
			// Apertura dell'obiettivo
			if (trim($fields['ApertureFNumber']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::UnIndexed('ApertureFNumber', $fields['ApertureFNumber'], $IfileConfig->getConfig('encoding')));
			}
			// Commento dell'utente
			if (trim($fields['UserComment']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('UserComment', $fields['UserComment'], $IfileConfig->getConfig('encoding')));
			}
			// Descrizione dell'immagine
			if (trim($fields['ImageDescription']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('ImageDescription', $fields['ImageDescription'], $IfileConfig->getConfig('encoding')));
			}
			// Orientamento
			if (trim($fields['Orientation']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('Orientation', $fields['Orientation'], $IfileConfig->getConfig('encoding')));
			}
			// Macchina
			if (trim($fields['Make']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('Make', $fields['Make'], $IfileConfig->getConfig('encoding')));
			}
			// Modello Macchina
			if (trim($fields['Model']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('Model', $fields['Model'], $IfileConfig->getConfig('encoding')));
			}
			// Software
			if (trim($fields['Software']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('Software', $fields['Software'], $IfileConfig->getConfig('encoding')));
			}
			// Copyright
			if (trim($fields['Copyright']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::UnIndexed('Copyright', $fields['Copyright'], $IfileConfig->getConfig('encoding')));
			}
			// Latitudine GPS
			if (trim($fields['GPSLatitude']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('GPSLatitude', $fields['GPSLatitude'], $IfileConfig->getConfig('encoding')));
			}
			// Latitudine DMS
			if (trim($fields['GPSLatitudeGoogle']) != '') {
				// latitudine nel formato googlemap DMS
				$doc->addField(Zend_Search_Lucene_Field::Keyword('GPSLatitudeGoogle', $fields['GPSLatitudeGoogle'], $IfileConfig->getConfig('encoding')));
			}
			// Latitudine Decimal
			if (trim($fields['GPSLatitudeGoogleDecimal']) != '') {
				// latitudine nel formato googlemap Decimal
				$doc->addField(Zend_Search_Lucene_Field::Keyword('GPSLatitudeGoogleDecimal', $fields['GPSLatitudeGoogleDecimal'], $IfileConfig->getConfig('encoding')));
			}
			// Longitudine
			if (trim($fields['GPSLongitude']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('GPSLongitude', $fields['GPSLongitude'], $IfileConfig->getConfig('encoding')));
			}
			// Longitudine DMS
			if (trim($fields['GPSLongitudeGoogle']) != '') {
				// longitudine nel formato googlemap DMS 
				$doc->addField(Zend_Search_Lucene_Field::Keyword('GPSLongitudeGoogle', $fields['GPSLongitudeGoogle'], $IfileConfig->getConfig('encoding')));
			}
			// Longitudine Decimal
			if (trim($fields['GPSLongitudeGoogleDecimal']) != '') {
				// longitudine nel formato googlemap DMS 
				$doc->addField(Zend_Search_Lucene_Field::Keyword('GPSLongitudeGoogleDecimal', $fields['GPSLongitudeGoogleDecimal'], $IfileConfig->getConfig('encoding')));
			}
			// XResolution
			if (trim($fields['XResolution']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('XResolution', $fields['XResolution'], $IfileConfig->getConfig('encoding')));
			}
			// YResolution
			if (trim($fields['YResolution']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('YResolution', $fields['YResolution'], $IfileConfig->getConfig('encoding')));
			}		
			// Data creazione
			if (trim($fields['DateTime']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('DateTime', $fields['DateTime'], $IfileConfig->getConfig('encoding')));
			}
			// Modalita' di esposizione
			if (trim($fields['ExposureMode']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('ExposureMode', $fields['ExposureMode'], $IfileConfig->getConfig('encoding')));
			}
			// Tempo di esposizione
			if (trim($fields['ExposureTime']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('ExposureTime', $fields['ExposureTime'], $IfileConfig->getConfig('encoding')));
			}
			// Tipo di Scena
			if (trim($fields['SceneCaptureType']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('SceneCaptureType', $fields['SceneCaptureType'], $IfileConfig->getConfig('encoding')));
			}
			// Risorsa di luce
			if (trim($fields['LightSource']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('LightSource', $fields['LightSource'], $IfileConfig->getConfig('encoding')));
			}
			
			//$lucene->setIndexFile($fields['filename']);
			$lucene->setIndexFile($filename);
			$this->setMoreInfo($addField);
		
			$resDoc = $lucene->addDocument($doc);
			$lucene->commit();
			//jifilehelper::deleteFileCache($fields['filename']);
			jifilehelper::deleteFileCache($filename);
			jifilehelper::clearCache(array('lucene'));
			// save keyid information in #__jifiledocuments
			$keyid = $resDoc->getFieldValue('key');
			// add documents in DB
			$this->insertDocuments($keyid);
		} catch (Exception $e) {
			$this->setError(jifilehelper::JText($e->getMessage()));
			return false;
		}
		
		return true;
	}
	
	/**
	 * Indexing Multimedia Fields
	 * @param array $fields
	 * @return 
	 */
	public function indexManualMultimedia($fields) {
		
		// decode filename
		$filename = urldecode($fields['filename']);
		
		if(empty($fields['body'])){
			$this->setError(JText::_('Body').': '.JText::_('REQUIRED_FIELD'));
			return false;
		}
		
		require_once JPATH_IFILE_LIBRARY.'/ifile/adapter/beans/LuceneDataIndexBean.php';
		$addField = $fields['add'];
		$lucene = $this->getIndex();
		
		try {
			$bean = new LuceneDataIndexBean();
			$bean->setBody($fields['body']);
			$bean->setCreated($fields['created']);
			$bean->setCreator($fields['creator']);
			$bean->setKeywords($fields['keywords']);
			$bean->setModified($fields['modified']);
			$bean->setSubject($fields['subject']);
			$bean->setDescription($fields['description']);
			$bean->setTitle($fields['title']);
			
			$doc = $bean->getLuceneDocument();
			
			$IfileConfig = IFileConfig::getInstance();
			// Inserisce i dati del file all'interno dell'indice come Field
			// Codificato da
			if (trim($fields['encodedBy']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('encodedBy', $fields['encodedBy'], $IfileConfig->getConfig('encoding')));
			}
			// Traccia
			if (trim($fields['track']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('track', $fields['track'], $IfileConfig->getConfig('encoding')));
			}
			// Pubblicato
			if (trim($fields['publisher']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('publisher', $fields['publisher'], $IfileConfig->getConfig('encoding')));
			}
			// Disco
			if (trim($fields['partOfASet']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('partOfASet', $fields['partOfASet'], $IfileConfig->getConfig('encoding')));
			}
			// Battiti al minuto
			if (trim($fields['bpm']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('bpm', $fields['bpm'], $IfileConfig->getConfig('encoding')));
			}
			// Artista Originale
			if (trim($fields['originalArtist']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('originalArtist', $fields['originalArtist'], $IfileConfig->getConfig('encoding')));
			}
			// Copyright
			if (trim($fields['copyright']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('copyright', $fields['copyright'], $IfileConfig->getConfig('encoding')));
			}
			// Gruppo
			if (trim($fields['band']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('band', $fields['band'], $IfileConfig->getConfig('encoding')));
			}
			// Genere
			if (trim($fields['genre']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('genre', $fields['genre'], $IfileConfig->getConfig('encoding')));
			}
			// Compositore
			if (trim($fields['composer']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('composer', $fields['composer'], $IfileConfig->getConfig('encoding')));
			}
			// Anno
			if (trim($fields['year']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('year', $fields['year'], $IfileConfig->getConfig('encoding')));
			}
			// Album
			if (trim($fields['album']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('album', $fields['album'], $IfileConfig->getConfig('encoding')));
			}
			// Artista
			if (trim($fields['artist']) != '') {
				$doc->addField(Zend_Search_Lucene_Field::Text('artist', $fields['artist'], $IfileConfig->getConfig('encoding')));
			}
			// Latitudine Decimal
			if (trim($fields['GPSLatitudeGoogleDecimal']) != '') {
				// latitudine nel formato googlemap Decimal
				$doc->addField(Zend_Search_Lucene_Field::Keyword('GPSLatitudeGoogleDecimal', $fields['GPSLatitudeGoogleDecimal'], $IfileConfig->getConfig('encoding')));
			}
			// Longitudine Decimal
			if (trim($fields['GPSLongitudeGoogleDecimal']) != '') {
				// longitudine nel formato googlemap DMS 
				$doc->addField(Zend_Search_Lucene_Field::Keyword('GPSLongitudeGoogleDecimal', $fields['GPSLongitudeGoogleDecimal'], $IfileConfig->getConfig('encoding')));
			}
						
			//$lucene->setIndexFile($fields['filename']);
			$lucene->setIndexFile($filename);
			$this->setMoreInfo($addField);
		
			$resDoc = $lucene->addDocument($doc);
			$lucene->commit();
			//jifilehelper::deleteFileCache($fields['filename']);
			jifilehelper::deleteFileCache($filename);
			jifilehelper::clearCache(array('lucene'));
			// save keyid information in #__jifiledocuments
			$keyid = $resDoc->getFieldValue('key');
			// add documents in DB
			$this->insertDocuments($keyid);
		} catch (Exception $e) {
			$this->setError(jifilehelper::JText($e->getMessage()));
			return false;
		}
		
		return true;
	}
	
	public function delete($id, $isUpdate = false) {
		if($id !== false) {
			$lucene = $this->getIndex();
			$lucene->delete($id);
			$lucene->commit();
			$doc = $lucene->getDocument($id);
			jifilehelper::deleteFileCache($doc->getFieldValue('filename'));
			jifilehelper::clearCache(array('lucene'));
			// save keyid information in #__jifiledocuments
			$keyid = $doc->getFieldValue('key');
			// delete logic documents in DB
			if (!$isUpdate) {
				$this->deleteDocuments($keyid);
			}
		} else {
			return false;
		}
	}

	public function deleteAll() {
			$lucene = $this->getIndex();
			$lucene->deleteAll();
			//$lucene->commit();
			//$doc = $lucene->getDocument($id);
			jifilehelper::clearCache();
			// delete logic documents in DB
			$this->deleteAllDocuments();
	}
	
	public function optimize() {
		$lucene = $this->getIndex();
		set_time_limit(0);
		$lucene->optimize();
		jifilehelper::clearCache(array('lucene'));
		// delete logic documents in DB
		$this->optimazeDocuments();
	}

	public function getLuceneDoc($filename, $toArray = false, $clear = false) {
		jimport('joomla.filesystem.file');
		
		try {
			$factory = IFileFactory::getInstance();
			
			$adapter = $factory->getAdapterSearchLuceneDocument(JFile::getExt($filename));
	
			if ($adapter === false) {
				return false;
			}
			
			// chiamata al metodo per il parser del file
			$adapter->setFilename($filename);
		
			$doc = $adapter->loadParserFile();
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
		
		try {
			if($toArray) {
				return jifilehelper::luceneDocToArray($doc, $clear);
			}
			return $doc;
		} catch (Exception $e) {
			$this->setError($e);
			return false;
		}
	}

	/**
	 * Set type of indexing
	 * @param object $addField [optional]
	 * @return 
	 */
	private function setMoreInfo($addField = array()) {
		if(!is_array($addField)) {
			return false;
		}
		$lucene = $this->getIndex();
		foreach ($addField as $name => $value) {
			
			// define type for custom field created on Manually Index
			$type = '';				
			if (strpos($name, '|@@|') !== false) {
				list($name, $type) = explode("|@@|", $name);	
			}
			
			if (!is_array($value)) {
				$field['value'] = $value;
				$field['type'] = (empty($type)) ? "Text" : $type;
			} else {
				$field['value'] = $value['value'];
				$field['type'] = (empty($value['type'])) ? 'Text' : $value['type'];
			}
			
			if ($name == 'class') {
				$field['type'] = 'UnIndexed';
			}
			
			unset($value);
			
			$lucene->addCustomField($name, $field['value'], $field['type']);
		}
		return true;
	}
}