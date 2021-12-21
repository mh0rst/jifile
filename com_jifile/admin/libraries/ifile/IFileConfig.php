<?php
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.0.1 IFileConfig.php 2011-01-10 13:54:11
 */

/** IFileXpdfConfig */
require_once 'config/helpers/IFileXpdfConfig.php';

/**
 * Gestisce il file di configurazione IFileConfig.xml
 *
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileConfig {
	
	/**
	 * Classe per la gestione dei token
	 */
	const ZEND_TOKENFILTER = 'Zend_Search_Lucene_Analysis_TokenFilter';
	/**
	 * Classe per la gestione degli analyzer
	 */
	const ZEND_ANALYZER = 'Zend_Search_Lucene_Analysis_Analyzer';
	/**
	 * Istanza di IFileConfig
	 * 
	 * @var IFileConfig
	 */
	private static $_instance;
	/**
	 * File XML di configuzione
	 * 
	 * @var string
	 */
	private $xml;
	/**
	 * File XSD di validazione
	 * 
	 * @var string
	 */
	private $xsd;
	/**
	 * Array della configurazione
	 * 
	 * @var array
	 */
	protected $config = array();
	/**
	 * Array della configurazione originale
	 * 
	 * @var array
	 */
	private $originalConfig = null;
	/**
	 * Valore di default dell'encoding
	 * 
	 * @var string 
	 */
	private $encoding = '';
	/**
	 * Valore di default dell'analyzer
	 * 
	 * @var string
	 */
	private $analyzer = 'Utf8_CaseInsensitive';
	
	/**
	 * Il metodo non e' invocabile per il pattern Singleton  
	 *
	 * @return void 
	 */
	protected function __construct() {
		$this->xml = dirname(__FILE__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'IFileConfig.xml';		
		$this->xsd = dirname(__FILE__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'IFileConfig.xsd';
		
		$this->parserConfig();		
	}
		
	/**
	 * Ritorna una istanza dell'oggetto IFileFactory
	 * 
	 * @return IFileConfig  
	 */
	static function getInstance() {
		if (self::$_instance == null) 
			self::$_instance = new IFileConfig();			
			
		return self::$_instance;		
	}
	
	/**
	 * Parserizza il file di configurazione per leggere i valori
	 * 
	 * @TODO
	 * 1. andrebbero gestiti anche altri parametri per la gestione delle
	 * wildcard, maxlength......
	 * 2. Serializzazione dei dati per evitare ogni volta di ricostruirli
	 * 
	 * @throws IFile_Exception
	 * @return void
	 */
	private function parserConfig() {
		// permette di disabilitare la visualizzazione
		// degli errori creati dalla LIBXML - PHP 5.1.0
		libxml_use_internal_errors(true);
		// pulizia del buffer degli errori
		libxml_clear_errors();
		// istanzio un oggetto DOM
		$dom = new DOMDocument();
		//carico il file XML
		if (!$dom->load($this->xml)) {
			$errors = libxml_get_errors();			
			require_once 'IFile_Exception.php';
			throw new IFile_Exception('The file IFileConfig.xml may not be formatted correctly');
		}
		// Valido il file XML
		if (@!$dom->schemaValidate($this->xsd)) {
			$errors = libxml_get_errors();
			
			foreach ($errors as $val) {
				if ($val->code == 1549) {
					require_once 'IFile_Exception.php';
					throw new IFile_Exception('IFileConfig.xsd File not found');
				}
			}
			
			require_once 'IFile_Exception.php';
			throw new IFile_Exception('File XML is not valid according to the XSD.&#10;Error: '.$errors[0]->message);
		}
		
		// creo un oggetto Xpath
		$xpath = new DOMXPath($dom);
		// recupero i nodi action
		$nodeIFile = $xpath->query("//ifile");
		
		foreach ($nodeIFile as $ifile) {
			// root-application
			$this->config['root-application'] = ($xpath->query("root-application", $ifile)->item(0)) ? trim($xpath->query("root-application", $ifile)->item(0)->nodeValue) : null;
			// controlla che la root application sia una directory realmente esistente 
			if (!empty($this->config['root-application'])) {$this->checkRootApplication($this->config['root-application']);}
			// table-name
			$this->config['table-name'] = ($xpath->query("table-name", $ifile)->item(0)) ? trim($xpath->query("table-name", $ifile)->item(0)->nodeValue) : null;
			if (!empty($this->config['table-name'])) {
				$collation = $xpath->query("table-name", $ifile)->item(0)->getAttributeNode("collation")->value;				
				$this->config['table-collation'] = (empty($collation)) ? null : $collation;				
			}
			
			// timelimit
			$this->config['timelimit'] = ($xpath->query("timelimit", $ifile)->item(0)) ? $xpath->query("timelimit", $ifile)->item(0)->nodeValue : null;
			// memorylimit
			$this->config['memorylimit'] = ($xpath->query("memorylimit", $ifile)->item(0)) ? $xpath->query("memorylimit", $ifile)->item(0)->nodeValue."M" : null;
			// resultlimit
			$this->config['resultlimit'] = ($xpath->query("resultlimit", $ifile)->item(0)) ? $xpath->query("resultlimit", $ifile)->item(0)->nodeValue : null;
			// default-search-field
			$this->config['default-search-field'] = ($xpath->query("default-search-field", $ifile)->item(0)) ? $xpath->query("default-search-field", $ifile)->item(0)->nodeValue : null;
			// duplicate
			$this->config['duplicate'] = ($xpath->query("duplicate", $ifile)->item(0)) ? $xpath->query("duplicate", $ifile)->item(0)->nodeValue : null;
			// encoding
			$this->config['encoding'] = ($xpath->query("encoding", $ifile)->item(0)) ? trim($xpath->query("encoding", $ifile)->item(0)->nodeValue) : $this->encoding;
			
			// doctotxt
			$doctotxt = ($xpath->query("doctotxt", $ifile)->item(0)) ? $xpath->query("doctotxt", $ifile)->item(0) : null;
			$this->config['doctotxt'] = array();
			if (!empty($doctotxt)) {
					
				$encoding = ($doctotxt->getAttributeNode("encoding")) ? $doctotxt->getAttributeNode("encoding")->value : null;
				$type = ($doctotxt->getAttributeNode("type")) ? $doctotxt->getAttributeNode("type")->value : null;
				
				$this->config['doctotxt']['encoding'] = $encoding;
				$this->config['doctotxt']['type'] 	  = $type;
				
			} else {
				$this->config['doctotxt']['encoding'] = null;
				$this->config['doctotxt']['type'] 	  = "PHP";
			}
			
			// servet
			$server = ($xpath->query("server", $ifile)->item(0)) ? $xpath->query("server", $ifile)->item(0) : null;
			$this->config['server'] = array();
			if (!empty($server)) {					
				$bit = ($server->getAttributeNode("bit")) ? $server->getAttributeNode("bit")->value : null;
				$this->config['server']['bit'] = $bit;
			} else {
				$this->config['server']['bit'] = 32;
			}
			
			// XPDF
			$nodeXpdf = ($xpath->query("xpdf", $ifile)) ? $xpath->query("xpdf", $ifile) : null;
			$this->config['xpdf'] = array();		
			if (!empty($nodeXpdf)) {
				foreach($nodeXpdf as $xpdf) {
					// opw
					$this->config['xpdf']['opw'] = ($xpath->query("opw", $xpdf)->item(0)) ? $xpath->query("opw", $xpdf)->item(0)->nodeValue : null;
					// pdftotext
					$this->config['xpdf']['pdftotext'] = array();
					if ($xpath->query("pdftotext", $xpdf)) {
						$pdftotext = $xpath->query("pdftotext", $xpdf);
						
						foreach($pdftotext as $txt) {
							// executable
							$this->config['xpdf']['pdftotext']['executable'] = ($xpath->query("executable", $txt)->item(0)) ? trim($xpath->query("executable", $txt)->item(0)->nodeValue) : null;
							$this->config['xpdf']['pdftotext']['xpdfrc'] = ($xpath->query("xpdfrc", $txt)->item(0)) ? trim($xpath->query("xpdfrc", $txt)->item(0)->nodeValue) : null;
						}
						
						if (!empty($this->config['xpdf']['pdftotext']['executable'])) {
							$this->checkCustomXPDF($this->config['xpdf']['pdftotext']['executable'], "pdftotext");
						} 
						
						if (!empty($this->config['xpdf']['pdftotext']['xpdfrc'])) {
							$this->checkCustomXPDF($this->config['xpdf']['pdftotext']['xpdfrc'], "xpdfrc for pdftotext");
						}
						
					}
					// pdfinfo
					$this->config['xpdf']['pdfinfo'] = array();
					if ($xpath->query("pdfinfo", $xpdf)) {
						$pdfinfo = $xpath->query("pdfinfo", $xpdf);
						
						foreach($pdfinfo as $txt) {
							// executable
							$this->config['xpdf']['pdfinfo']['executable'] = ($xpath->query("executable", $txt)->item(0)) ? trim($xpath->query("executable", $txt)->item(0)->nodeValue) : null;
							$this->config['xpdf']['pdfinfo']['xpdfrc'] = ($xpath->query("xpdfrc", $txt)->item(0)) ? trim($xpath->query("xpdfrc", $txt)->item(0)->nodeValue) : null;
						}
						
						if (!empty($this->config['xpdf']['pdfinfo']['executable'])) {
							$this->checkCustomXPDF($this->config['xpdf']['pdfinfo']['executable'], "pdfinfo");
						} 
						
						if (!empty($this->config['xpdf']['pdfinfo']['xpdfrc'])) {
							$this->checkCustomXPDF($this->config['xpdf']['pdfinfo']['xpdfrc'], "xpdfrc for pdfinfo");
						}
					}
				}				
			}			
			
			// questo permette di avere l'intero TAG <analyzer> 
			// opzionale all'interno del file di configurazione
			$this->config['analyzer'] = $this->analyzer;
			// analyzer type
			$nodeAnalyzerType = $xpath->query("//ifile/analyzer/type");	
			
			foreach($nodeAnalyzerType as $analyzer) {
				// default analyzer
				$this->config['analyzer'] = ($xpath->query("default", $analyzer)->item(0)) ? $xpath->query("default", $analyzer)->item(0)->nodeValue : $this->analyzer;
				// custom analyzer
				$fileAnalyzer = ($xpath->query("custom-default", $analyzer)->item(0)) ? trim($xpath->query("custom-default", $analyzer)->item(0)->nodeValue) : null;
				
				if (!empty($fileAnalyzer)) {
					$classAnalyzer = $xpath->query("custom-default", $analyzer)->item(0)->getAttributeNode("class")->value;				
					$obj = $this->checkAnalyzer($fileAnalyzer, $classAnalyzer);
					$this->config['custom-analyzer'] = $obj;
					// salvo anche le stringhe di configurazione
					$this->config['xml-custom-analyzer'] = array('file' => $fileAnalyzer, 'class' => $classAnalyzer);
				}				
			}
			
			// fields
			$tmpField = $this->getDefineFieldsType();
			$nodeFields = $xpath->query("//ifile/zend-document/fields/field");			
			foreach($nodeFields as $field) {
				
				$fieldName = $field->getAttributeNode("name")->value;
				$fieldType = $field->getAttributeNode("type")->value;
				// se non trova l'encoding allora gli fissa quello di default
				$fieldEncoding = ($field->getAttributeNode("encoding")) ? $field->getAttributeNode("encoding")->value : $this->config['encoding'];
				
				$tmpField[$fieldName]['type'] = $fieldType;
				$tmpField[$fieldName]['encoding'] = $fieldEncoding;
			}
			// se non e' vuoto l'array temporaneo
			// setto l'array della zend-document
			$this->config['zend-document-fields'] = (!empty($tmpField)) ? $tmpField : null;
			
			// analyzer filter
			$nodeAnalyzerFiltes = $xpath->query("//ifile/analyzer/filters");			
			foreach($nodeAnalyzerFiltes as $filter) {
				// stop-words
				$this->config['stop-words'] = ($xpath->query("stop-words", $filter)->item(0)) ? trim($xpath->query("stop-words", $filter)->item(0)->nodeValue) : null;
				// short-words
				$this->config['short-words'] = ($xpath->query("short-words", $filter)->item(0)) ? trim($xpath->query("short-words", $filter)->item(0)->nodeValue) : null;
				// controlla che sia stato inserito un file esistente
				if (!empty($this->config['stop-words'])) {$this->checkStopWords($this->config['stop-words']);}
				
				// custom filters				
				$registryFilter = array();
				$xmlRegistryFilter = array();
				
				$nodeAnalyzerFiltesCustom = $xpath->query("//ifile/analyzer/filters/custom-filters/filter");
				foreach($nodeAnalyzerFiltesCustom as $customFilter) {
					
					$fileFilter = trim($customFilter->nodeValue);
					$classFilter = $customFilter->getAttributeNode("class")->value;
					
					if (!empty($fileFilter)) {					
						// conbtrollo esistenza della classe						
						$obj =& $this->checkTokenFilter($fileFilter, $classFilter);
						// inserisce il riferimento all'oggetto
						array_push($registryFilter, $obj);
						// salvo anche le stringhe di configurazione
						array_push($xmlRegistryFilter, (array('file'=>$fileFilter, 'class' => $classFilter )));						
					}
				}
				
				$this->config['filters'] = (!empty($registryFilter)) ? $registryFilter : null;
				// salvo anche le stringhe di configurazione
				$this->config['xml-filters'] = (!empty($xmlRegistryFilter)) ? $xmlRegistryFilter : null;
			}
		}	
	}
	
	/**
	 * Configurazione di Default dei fields "Standard" di IFile	 
	 * Fields:
	 * - name:Binary
	 * - extensionfile:Keyword
	 * - path:Binary
	 * - filename:Binary
	 * - introtext:UnIndexed		
	 * - body:UnStored
	 * - title:Text
	 * - subject:Text
	 * - description:Text
	 * - creator:Text
	 * - keywords:Keyword
	 * - created:UnStored
	 * - modified:UnStored
	 * 
	 * Sono esclusi 
	 * - root
	 * - key
	 * 
	 * @return array 
	 */
	public function getDefineFieldsType() {
		$fields = array();
		$fields['name']['type'] 		= 'Binary';
		$fields['extensionfile']['type']= 'Keyword';
		$fields['path']['type'] 		= 'Binary';
		$fields['filename']['type'] 	= 'Binary';
		$fields['introtext']['type'] 	= 'UnIndexed';
		$fields['body']['type'] 		= 'UnStored';
		$fields['title']['type'] 		= 'Text';
		$fields['subject']['type'] 		= 'Text';
		$fields['description']['type'] 	= 'Text';
		$fields['creator']['type'] 		= 'Text';
		$fields['keywords']['type'] 	= 'Keyword';
		$fields['created']['type'] 		= 'Keyword';
		$fields['modified']['type'] 	= 'Keyword';
		$fields['pages']['type'] 	 	= 'UnStored';
		
		return $fields;
	}
	
	/**
	 * Verifica che sia stato configurato un path esistente
	 * 
	 * @return void
	 * @throws IFile_Exception  
	 */
	protected function checkRootApplication ($root) {
		
		if (!is_dir(realpath($root))) {
			require_once 'IFile_Exception.php';
			throw new IFile_Exception('Root-application does not exist');
		}
	}
	
	/**
	 * Verifica che sia stato configurato un file esistente
	 * @param path $file
	 * @param string $type
	 * @return void
	 * @throws IFile_Exception 
	 */
	protected function checkCustomXPDF($file, $type = "") {
		if (is_dir(realpath($file)) || !is_file($file)) {
			require_once 'IFile_Exception.php';
			throw new IFile_Exception("{$type} file does not exist");
		}
	}
	
	/**
	 * Verifica che sia stato configurato un file esistente
	 * 
	 * @param path $file
	 * @return void
	 * @throws IFile_Exception  
	 */
	protected function checkStopWords ($file) {
		
		if (is_dir(realpath($file)) || !is_file($file)) {
			require_once 'IFile_Exception.php';
			throw new IFile_Exception('Stop-words file does not exist');
		}
	}
	
	/**
	 * Verifica che l'oggetto analyzer esista
	 * 
	 * @return Zend_Search_Lucene_Analysis_Analyzer
	 * @throws ReflectionException , IFile_Exception  
	 */
	protected function checkAnalyzer ($fileAnalyzer, $classAnalyzer) {
		
		if (is_dir(realpath($fileAnalyzer)) || !is_file($fileAnalyzer)) {
			require_once 'IFile_Exception.php';
			throw new IFile_Exception('File Analyzer does not exist');
		}
		
		// require della classe
		require_once($fileAnalyzer);
		// recupera tutte le classi che estende
		$classes = $this->getAncestors($classAnalyzer);
		
		// verifico che la classe estenda la Zend_Search_Lucene_Analysis_TokenFilter			
		if(!in_array(self::ZEND_ANALYZER, $classes)) {
			require_once 'IFile_Exception.php';
			throw new IFile_Exception('The class does not implement Zend_Search_Lucene_Analysis_Analyzer');
		}
		// Reflection		
		$reflection = new ReflectionClass($classAnalyzer);
		// ritorna l'oggetto
		return $reflection->newInstance();
	}
	
	/**
	 * Verifica che l'oggetto Token Filter esista
	 * 
	 * @return Zend_Search_Lucene_Analysis_TokenFilter
	 * @throws ReflectionException , IFile_Exception  
	 */
	protected function checkTokenFilter ($fileFilter, $classFilter) {
		
		if (is_dir(realpath($fileFilter)) || !is_file($fileFilter)) {
			require_once 'IFile_Exception.php';
			throw new IFile_Exception('File TokenFilters does not exist');
		}
		
		// require della classe
		require_once($fileFilter);
		// recupera tutte le classi che estende
		$classes = $this->getAncestors($classFilter);
		
		// verifico che la classe estenda la Zend_Search_Lucene_Analysis_TokenFilter			
		if(!in_array(self::ZEND_TOKENFILTER, $classes)) {
			require_once 'IFile_Exception.php';
			throw new IFile_Exception('The class does not implement Zend_Search_Lucene_Analysis_TokenFilter');
		}

		// Reflection		
		$reflection = new ReflectionClass($classFilter);
		// ritorna l'oggetto
		return $reflection->newInstance();
	}
	
	/**
	 * Ritorna un array delle estensioni della classe
	 * 
	 * @param string $class
	 * @return array
	 */
	private function getAncestors ($class) {
    	$classes = array($class);
    	while($class = get_parent_class($class)) { $classes[] = $class; }
	    return $classes;
	}
	
	/**
	 * Ritorna l'array dei tipi di Fields
	 * @param string $fieldName
	 * @return array
	 */
	public function getDocumentField($fieldName) {
		if (isset($this->config['zend-document-fields'][$fieldName])) {
			return $this->config['zend-document-fields'][$fieldName];
		} 
		
		return null;
	}
	
	/**
	 * Ritorna la proprieta' della configurazione per la XPDF
	 * @param string $property
	 * @return mixed
	 */
	public function getXpdf($property) {
		if (isset($this->config['xpdf'][$property])) {			
			return $this->config['xpdf'][$property];
		} 
		
		return null;
	}
	
	/**
	 * Sovrascrive o aggiunge elementi alla configurazione creando una copia di quella originale
	 * 
	 * @param string $key stringa separata da @ per sotto strutture 
	 * @param mixed $value 
	 * @return void;
	 */
	public function overrideConfig($replacements) {
		if ($this->originalConfig == null) {
			$this->originalConfig = $this->config;
		}
		// PHP < 5.3
		if (!function_exists('array_replace_recursive')) {
			$this->config = $this->array_replace_recursive($this->config, $replacements);	
		} else {
			$this->config = array_replace_recursive($this->config, $replacements);
		}
	}
	
	/**
	 * Versione per la versione di PHP < 5.3  
	 * 
	 * @param array $base
	 * @param array $replacements 
	 * @return array
	 */
	private function array_replace_recursive($base, $replacements) 
	{ 
		foreach (array_slice(func_get_args(), 1) as $replacements) { 
			$bref_stack = array(&$base); 
			$head_stack = array($replacements); 
			
			do { 
				end($bref_stack); 
				
				$bref = &$bref_stack[key($bref_stack)]; 
				$head = array_pop($head_stack); 
				
				unset($bref_stack[key($bref_stack)]); 
				
				foreach (array_keys($head) as $key) { 
					if (isset($key, $bref) && 
						@is_array($bref[$key]) && 
						@is_array($head[$key])) { 
						$bref_stack[] = &$bref[$key]; 
						$head_stack[] = $head[$key]; 
					} else { 
						$bref[$key] = $head[$key]; 
					} 
				} 
			} while(count($head_stack)); 
		} 
		
		return $base; 
	} 
	
	/** 
	 * Setta la configurazione originale se e' stato effettuato un Override
	 * 
	 * @return void; 
	 */
	public function setOriginalConfig() {
		if ($this->originalConfig != null) {
			$this->config = $this->originalConfig;
		} 
	}	 
	
	/**
	 * Ritorna il valore della proprieta' o NULL se non esiste.
	 * Se non viene passata nessuna proprieta' ritorna tutta la struttura
	 * 	 
	 * @param string $config
	 * @return mixed
	 */
	public function getConfig($config = null) {
		
		if ($config === null) {
			return $this->config;
		} elseif (isset($this->config[$config])) {
			return $this->config[$config];
		}					
		
		return null;
	}		
}
?>