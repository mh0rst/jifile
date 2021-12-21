<?php
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage servercheck
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.1.2 LuceneServerCheck.php 2012-07-30
 */

/** ReportCheck */
require_once 'ReportCheck.php';
require_once dirname(__FILE__)."/../IFileConfig.php";

/**
 * Verifica se ci sono tutti i requisiti per utilizzare la libreria
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage servercheck
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class LuceneServerCheck {
	/**
	 * Istanza di LuceneServerCheck
	 * 
	 * @var LuceneServerCheck
	 */
	private static $_instance;
	/**
	 * Controllo esecuzione della check
	 * 
	 * @var boolean
	 */
	private static $_check = false;
	/**
	 * Libreria di Zend Lucene 
	 */	
	const ZEND_SEARCH_LUCENE = 'Zend/Search/Lucene.php';
	/**
	 * Libreria di Zend PDF
	 */
	const ZEND_PDF = 'Zend/Pdf.php';
	/**
	 * Versione Zend 
	 */
	const ZENDVERSION = '1.10.1';
	/**
	 * Versione Zend Last
	 */
	const TOZENDVERSION = '1.12.1';
	/**
	 * Percorso degli Adapters
	 */
	const ADAPTERS_PATH = 'adapter/';
	/**
	 * XPDF installato nel sistema
	 */
	const BINARIES_DEFAULT = '/usr/bin/pdftotext';	
	/**
	 * ANTIWORD per Windows 
	 */	
	const BINARIES_WIN_DOC = 'adapter/helpers/binaries/windows/antiword.exe';
	
	/**
	 * ANTIWORD per Linux
	 */	
	const BINARIES_LIN_DOC = 'adapter/helpers/binaries/linux/antiword';
	
	/**
	 * ANTIWORD per OS
	 */	
	const BINARIES_OSX_DOC = 'adapter/helpers/binaries/osx/antiword';
	
	/**
	 * XPDF per Windows 
	 */	
	const BINARIES_WIN = 'adapter/helpers/binaries/windows/pdftotext.exe';
	/**
	 * XPDF per Windows 64bit
	 */	
	const BINARIES_WIN_64 = 'adapter/helpers/binaries/windows/bin64/pdftotext.exe';
	/**
	 * XPDF INFO per Windows 
	 */	
	const BINARIES_INFO_WIN = 'adapter/helpers/binaries/windows/pdfinfo.exe';
	/**
	 * XPDF INFO per Windows 
	 */	
	const BINARIES_INFO_WIN_64 = 'adapter/helpers/binaries/windows/bin64/pdfinfo.exe';
		
	/**
	 * XPDF per OSX 
	 */	
	const BINARIES_OSX = 'adapter/helpers/binaries/osx/pdftotext';
	/**
	 * XPDF per FREEBSD 
	 */	
	const BINARIES_FRE = 'adapter/helpers/binaries/freebsd/pdftotext';	
	
	/**
	 * XPDF per Linux 
	 */
	const BINARIES_LIN = 'adapter/helpers/binaries/linux/pdftotext';	
	/**
	 * XPDF per Linux 64bit
	 */
	const BINARIES_LIN_64 = 'adapter/helpers/binaries/linux/bin64/pdftotext';
	
	/**
	 * XPDF INFO per Linux 
	 */
	const BINARIES_INFO_LIN = 'adapter/helpers/binaries/linux/pdfinfo';	
	/**
	 * XPDF INFO per Linux 64bit
	 */
	const BINARIES_INFO_LIN_64 = 'adapter/helpers/binaries/linux/bin64/pdfinfo';
	
	/**
	 * XPDF per universal 
	 */
	const BINARIES_UNV = 'adapter/helpers/binaries/custom/pdftotext';
		
	/**
	 * Versione minima di PHP 
	 */	
	const PHPVERSION = '5.1.0';
	
	/**
	 * Array di oggetti ReportCheck
	 * 
	 * @var array
	 */
	private $registry = array();	
	/**
	 * Array dei path include configurati nel php.ini
	 * 
	 * @var array
	 */
	private $include_path = array();
	/**
	 * Stringa dei permessi
	 * 
	 * @var string
	 */
	private $configmod = '';
	/**
	 * Lista delle estensioni consentite
	 *
	 * @var array
	 */
	private $extensionsAllows = array();
	
	/**
	 * Costruttore privato per la gestione del Singleton
	 */
	private function __construct() {}
	
	/**
	 * Ritorna una istanza dell'oggetto LuceneServerCheck
	 * 
	 * @return LuceneServerCheck  
	 */
	static function getInstance() {
		if (self::$_instance == null) 
			self::$_instance = new LuceneServerCheck();			
			
		return self::$_instance;		
	}
	
	/**
	 * Verifica tutti i requisiti richiesti
	 * 
	 * @return void 
	 */
	public function serverCheck() {
		if (!self::$_check) {
			$this->checkZendFramework();
			$this->checkPCRE();
			$this->checkServer();
			$this->checkPermissionXPDF();
			$this->checkPermissionINFOXPDF();
			$this->checkPermissionANTIWORD();
			$this->checkPHPVersion();
			$this->checkPHPLib();
			$this->checkPHPFunction();
			$this->checkExtensionsAllows();
			
			self::$_check = true; 
		}
	}
	
	/**
	 * Ritorna il registro degli oggetti ReportCheck
	 * 
	 * @return array
	 */
	public function getReportCheck() {
		return $this->registry;
	}
	
	public function printReportCheckCLI() {
		foreach($this->registry as $caption => $check) {
			echo "***********************************************************\n";
			echo strtoupper($caption)."\n";
			echo "***********************************************************\n";
			foreach($check as $obj){				
				echo "Component: ".$obj->getLabel()."\n";
				echo "Check: ".$obj->getMessage()."\n";
				echo "Requirements: ".$obj->getRequire()."\n";
				echo "Info: ".$obj->getInfo()."\n";
				echo "Use: ".$obj->getInfoUse()."\n";
				echo "WebSite:".$obj->getSite()."\n\n";
			}
		}
	}
	
	/**
	 * Presenta a video i risultati in formato HTML
	 *  
	 * @return void
	 */
	public function printReportCheck() {
		echo "<html>\n";
		echo "<head>\n";
			echo "<style>\n";
				echo "body {text-align: center;}";
				echo "table {margin:auto;border-top:1px solid #000;border-left:1px solid #000;}\n";
				echo "td, th {padding:4px;border-bottom:1px solid #000; border-right:1px solid #000}\n";
			echo "</style>\n";
		echo "</head>\n";
		echo "<body>\n";
		echo "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
		echo "<thead>";
			echo "<tr style =\"background-color:#ccc;text-align:center;\">";
				echo "<th>Component</th>\n";
				echo "<th>Check</th>\n";
				echo "<th>Requirements</th>\n";
				echo "<th>Info</th>\n";
				echo "<th>Use</th>\n";
				echo "<th>WebSite</th>\n";
			echo "</tr>\n"; 
		
		echo "</thead>";
		
		echo "<tbody>";
		foreach($this->registry as $caption => $check) {
			
			echo "<tr><td style =\"text-align:center;\" colspan=\"6\"><strong>".$caption."</strong></td></tr>\n";
			foreach($check as $obj){
				$background = ($obj->getCheck()) ? '#8f8' : '#f88';
				echo "<tr style=\"background-color:{$background}\">";
				echo "<td><strong>".$obj->getLabel()."</strong></td>\n";
				echo "<td>".$obj->getMessage()."</td>\n";
				echo "<td>".$obj->getRequire()."</td>\n";
				echo "<td width=\"25%\">".$obj->getInfo()."</td>\n";
				echo "<td width=\"20%\">".$obj->getInfoUse()."</td>\n";
				echo "<td><a href=\"".$obj->getSite()."\" target=\"_blanck\">".$obj->getSite()."</a></td>\n";
				echo "</tr>\n"; 
			}			
		}
		echo "</tbody>";
		echo "</table>";
		echo "</body>\n";
		echo "</html>\n";
	}
	
	/**
	 * Ritorna l'array delle estensioni solo se richiamata im metodo "serverCheck"
	 * 
	 * @return array
	 */
	public function getExtensionsAllowed() {
		if (empty($this->extensionsAllows)) {
			$this->readExtensionsAllows();
		}
		
		return $this->extensionsAllows;
	}
	
	/**
	 * 
	 * Verifica se il server e' a 32 o 64 BIT
	 * 
	 * @return 
	 */
	private function getServerBit() {
		$int = "9223372036854775807";
		$int = intval($int);
		if ($int == 9223372036854775807) {
		  /* 64bit */
		  return "64bit";
		} elseif ($int == 2147483647) {
		  /* 32bit */
		  return "32bit";
		} else {
		  /* error */
		  return "Not defined";
		} 
	}
	
	/**
	 * Legge la directory degli Adapter e setta le estensioni consentite.
	 * 
	 * @return void 
	 */
	private function readExtensionsAllows() {
		// @TODO da rivedere il recupero del Path
		$dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.LuceneServerCheck::ADAPTERS_PATH;
		
		//$it = new FilesystemIterator($path);
		//foreach ($it as $fileinfo) {
			
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if (is_file($dir.$file) && strpos($file, "Adapter_Search_Lucene_Document_") !== false) {
						//$filename =  $file;
						// delete extension				
						$filename = preg_replace('/\.[^.]*$/', '', $file);
						// get extension file indexing
						$extension = preg_replace('/Adapter_Search_Lucene_Document_/', '', $filename);
						
						switch ($extension) {
							case 'Abstract':
							case 'Interface':
							case 'OpenOffice':
								break;
							default:
								$this->extensionsAllows[strtolower($extension)] = strtolower($extension);
						}
					}
				}
				closedir($dh);
			}
		}
	}
	
	/**
	 * Verifica le estensioni dei file consentiti
	 * 
	 * @return void
	 */
	private function checkExtensionsAllows() {
		
		if (empty($this->extensionsAllows)) {
			$this->readExtensionsAllows();
		}
		
		$reportCheck = new ReportCheck(true, 'Extensions', 'OK', "Not defined", implode(", ", $this->extensionsAllows), 'http://ifile.isapp.it', 'All extensions files allowed for Automatic Indexing');
		$this->pushReportCheck('Extensions allowed', 'Extensions', $reportCheck);
		
	}
	
	/**
	 * Verifica che la versione di PHP sia uguale o superiore alla 5.1.0
	 * 
	 * @return void
	 */
	private function checkServer() {
		// server 32/64bit
		$server = $this->getServerBit();
		$use = ($server == '64bit') ? 'Only for linux/windows.</br>Copy adapter/helpers/binaries/[linux|windows]/bin64/pdftotext in adapter/helpers/binaries/[linux|windows]' : 'Not defined'; 		
		// inizializza l'oggetto per il report
		$reportCheck = new ReportCheck(true, 'Server', $server, 'Not defined' , 'Note: If the OS is 64bit but PHP running a 32 bit, the check will return (32 bit)', 'http://www.php.net/manual/en/install.php', $use);
				
		$this->pushReportCheck('SERVER', 'Server', $reportCheck);
	}
	
	/**
	 * Verifica l'esistenza delle librerie di lucene della ZEND FRAMEWORK
	 * 
	 * @return void 
	 */
	private function checkZendFramework() {
		// recupero la lista dei path include 
		//$this->include_path  = preg_split('/(:|;)/' ,get_include_path());
		$this->include_path = explode(PATH_SEPARATOR, get_include_path());
		// inizializza l'oggetto per il report
		$reportCheckLucene = new ReportCheck(false, 'Zend Lucene', 'Not present', 'Version '.LuceneServerCheck::ZENDVERSION.' to '.LuceneServerCheck::TOZENDVERSION, 'Install Zend Framework', 'http://www.zend.com', 'Used by Lucene and MySqli Interface');		
		// verifica l'esistenza delle librerie Zend_Search_Lucene (LUCENE) 
		if ($includepath = $this->checkPearFile(LuceneServerCheck::ZEND_SEARCH_LUCENE)) {
			include_once $includepath.DIRECTORY_SEPARATOR.'Zend'.DIRECTORY_SEPARATOR.'Version.php';
			$checkZversion = (version_compare(Zend_Version::VERSION, LuceneServerCheck::ZENDVERSION, '<')) ? ' but Wrong version' : '';
			
			$reportCheckLucene->setCheck(empty($checkZversion));
			$reportCheckLucene->setMessage('Exists'.$checkZversion);
			$reportCheckLucene->setInfo('Zend Lucene is installed in '.$includepath.' - Version '.Zend_Version::VERSION);
		}
		
		$this->pushReportCheck('Zend Framework', 'Lucene', $reportCheckLucene);
	} 
	
	/**
	 * Verifica se si hanno i permessi per utilizzare le XPDF
	 *  
	 * @return void 
	 */
	private function checkPermissionXPDF() {
		// inizializza l'oggetto per il report
		$reportCheck = new ReportCheck(false, 'XPDF Binaries File', 'Unexecutable', 'CHMOD 0755' , 'For more information visit web site', 'http://www.foolabs.com', 'Used only for PDF file parser');
		// configurazione di IFile
		$ifileConfig = IFileConfig::getInstance();
		// server
		$server = $ifileConfig->getConfig("server");
		$serverbit = $server['bit'];
		// pdftotext personalizzata
		$pdftotextConfig = $ifileConfig->getXpdf('pdftotext');
		
		// controlla se esiste una configurazione di una XPDF personalizzata
		if (!empty($pdftotextConfig['executable'])) {
			$path  = $pdftotextConfig['executable'];
			$perms = $this->checkPermits($path, "0755", false, true);			
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$path  = LuceneServerCheck::BINARIES_WIN;
			if ($serverbit == '64') {
				$path  = LuceneServerCheck::BINARIES_WIN_64;
			}
			$perms = $this->checkPermits($path, "0755");			
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'FRE'){
			$perms = $this->checkPermits(LuceneServerCheck::BINARIES_FRE, "0755");	
			$path  = LuceneServerCheck::BINARIES_FRE;		
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'DAR'){			
			$perms = $this->checkPermits(LuceneServerCheck::BINARIES_OSX, "755", true);
			$path  = LuceneServerCheck::BINARIES_OSX;	
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'LIN'){
			$path  = LuceneServerCheck::BINARIES_LIN;
			if ($serverbit == '64') {
				$path  = LuceneServerCheck::BINARIES_LIN_64;
			}
			$perms = $this->checkPermits($path, "0755");
				
		}else{
			$perms = $this->checkPermits(LuceneServerCheck::BINARIES_UNV, "0755");
			$path  = LuceneServerCheck::BINARIES_UNV;
		}
		
		if (!$perms) {									
			$reportCheck->setMessage('Unexecutable');	
			$reportCheck->setInfo('Permission XPDF Binaries File ('.$path.'): '.$this->configmod.' - Please set to 0755 for binaries XPDF in '.strtoupper(substr(PHP_OS, 0, 3)));	
		} else {
			$reportCheck->setCheck(true);
			$reportCheck->setMessage('Executable');
			$reportCheck->setInfo('Permission XPDF Binaries File ('.$path.'): '.$this->configmod);
		}
		
		$this->pushReportCheck('XPDF', 'PDFTOTEXT', $reportCheck);
	}
	
	/**
	 * Verifica se si hanno i permessi per utilizzare le XPDF
	 *  
	 * @return void 
	 */
	private function checkPermissionINFOXPDF() {
		// inizializza l'oggetto per il report
		$reportCheck = new ReportCheck(false, 'XPDF INFO Binaries File', 'Unexecutable', 'CHMOD 0755' , 'For more information visit web site', 'http://www.foolabs.com', 'Used only for PDF file parser');
		$supported = true;
		// configurazione di IFile
		$ifileConfig = IFileConfig::getInstance();
		// server
		$server = $ifileConfig->getConfig("server");
		$serverbit = $server['bit'];
		// pdfinfo personalizzata
		$pdfinfoConfig = $ifileConfig->getXpdf('pdfinfo');
		
		// controlla se esiste una configurazione di una XPDF INFO personalizzata
		if (!empty($pdfinfoConfig['executable'])) {
			$path  = $pdfinfoConfig['executable'];
			$perms = $this->checkPermits($path, "0755", false, true);	
		} else if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$path  = LuceneServerCheck::BINARIES_INFO_WIN;
			if ($serverbit == '64') {
				$path  = LuceneServerCheck::BINARIES_INFO_WIN_64;
			}
			$perms = $this->checkPermits($path, "0755");
			
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'LIN'){
			$path  = LuceneServerCheck::BINARIES_INFO_LIN;
			if ($serverbit == '64') {
				$path  = LuceneServerCheck::BINARIES_INFO_LIN_64;
			}
			$perms = $this->checkPermits($path, "0755");
				
		}else{
			$supported = false;			
		}
		
		if (!$supported) {
			$reportCheck->setMessage('Unsupported');	
			$reportCheck->setRequire('Not defined');	
			$reportCheck->setInfo('XPDF INFO Binaries File isn\'t supported - for '.strtoupper(substr(PHP_OS, 0, 3)));
		} else {
			if (!$perms) {									
			$reportCheck->setMessage('Unexecutable');	
				$reportCheck->setInfo('Permission XPDF INFO Binaries File ('.$path.'): '.$this->configmod.' - Please set to 0755 for binaries XPDF in '.strtoupper(substr(PHP_OS, 0, 3)));	
			} else {
				$reportCheck->setCheck(true);
				$reportCheck->setMessage('Executable');
				$reportCheck->setInfo('Permission XPDF INFO Binaries File ('.$path.'): '.$this->configmod);
			}	
		}
		
		
		$this->pushReportCheck('XPDF', 'PDFINFO', $reportCheck);
	}
	
	/**
	 * Verifica se si hanno i permessi per utilizzare le ANTIWORD
	 *  
	 * @return void 
	 */
	private function checkPermissionANTIWORD() {
		// inizializza l'oggetto per il report
		$reportCheck = new ReportCheck(false, 'ANTIWORD Binaries File', 'Unexecutable', 'CHMOD 0755' , 'For more information visit web site', 'http://www.winfield.demon.nl/', 'Used only for DOC file parser');
		// verifica se antiword e' supportato da IFile
		$supported = true;
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$perms = $this->checkPermits(LuceneServerCheck::BINARIES_WIN_DOC, "0755");
			$path  = LuceneServerCheck::BINARIES_WIN_DOC;
		} else if(strtoupper(substr(PHP_OS, 0, 3)) === 'DAR'){			
			$perms = $this->checkPermits(LuceneServerCheck::BINARIES_OSX_DOC, "755", true);
			$path  = LuceneServerCheck::BINARIES_OSX_DOC;	
		}else if(strtoupper(substr(PHP_OS, 0, 3)) === 'LIN'){
			$perms = $this->checkPermits(LuceneServerCheck::BINARIES_LIN_DOC, "0755");
			$path  = LuceneServerCheck::BINARIES_LIN_DOC;	
		} else {
			$supported = false;
		}
		
		if (!$supported) {
				$reportCheck->setMessage('Unsupported');	
				$reportCheck->setInfo('ANTIWORD Binaries File isn\'t supported - for '.strtoupper(substr(PHP_OS, 0, 3)). ' system operation. Check configuration for use COM or PHP parser');
		} else {
			if (!$perms) {									
				$reportCheck->setMessage('Unexecutable');	
				$reportCheck->setInfo('Permission ANTIWORD Binaries File ('.$path.'): '.$this->configmod.' - Please set to 0755 for binaries ANTIWORD in '.strtoupper(substr(PHP_OS, 0, 3)));	
			} else {
				$reportCheck->setCheck(true);
				$reportCheck->setMessage('Executable');
				$reportCheck->setInfo('Permission ANTIWORD Binaries File ('.$path.'): '.$this->configmod);
			}	
		}
		
		$this->pushReportCheck('ANTIWORD', 'ANTIWORD', $reportCheck);
	}
	
	/**
	 * Verifica che la versione di PHP sia uguale o superiore alla 5.1.0
	 * 
	 * @return void
	 */
	private function checkPHPVersion() {
		// versione di PHP
		$phpversion = phpversion();		
		// inizializza l'oggetto per il report
		$reportCheck = new ReportCheck(false, 'PHP Version', 'KO', 'Version '.LuceneServerCheck::PHPVERSION.' or later', 'Version installed is '.$phpversion, 'http://www.php.net');
		// verifica che la versione di PHP
		if (version_compare($phpversion, LuceneServerCheck::PHPVERSION, '>=')) {
			$reportCheck->setCheck(true);
			$reportCheck->setMessage("OK");	
		}
		
		$this->pushReportCheck('PHP', 'PHPVersion', $reportCheck);
	}
	
	/**
	 * Verifica se ci sono le librerie necessarie
	 * 
	 * @return void 
	 */
	private function checkPHPLib() {
		// recupera la lista delle librerie installate 
		$extension = get_loaded_extensions();
		
		// librerie da verificare
		$checkExt = $this->getListExtension();
		// effettua un lower dei nomi delle librerie
		//array_walk($extension, create_function('&$v,$k','$v = strtolower($v);'));
		// ritorna un array con le librerie non installate
		$diff = array_diff($checkExt['ext'], $extension);
		$keyDiff = array_keys($diff);
		
		foreach ($checkExt['ext'] as $k => $ext) {
			// versione minima richiesta
			$version = ($checkExt['version'][$k]) ? 'Version '.$checkExt['version'][$k].' or later' : 'Not defined' ; 
			// inizializza l'oggetto per il report
			$reportCheck = new ReportCheck(false, $ext, 'KO', $version, 'Install library in PHP', $checkExt['link'][$k], $checkExt['use'][$k]);
			
			// controllo se la libreria e' installata  			
			if (!in_array($k, $keyDiff)) {
				$version = $checkExt['version'][$k];
				$extVersion = phpversion($ext); 
				// verifico la versione solo se esiste nell'estensione
				if ($version && !empty($extVersion) && (strnatcmp($extVersion, $version) < 0)) {
					$reportCheck->setInfo('Install new version in PHP');
				} else {
					$reportCheck->setCheck(true);
					$reportCheck->setMessage('OK');
					$version = (!empty($extVersion)) ? 'Version installed is '.$extVersion : 'Not check version';
					$reportCheck->setInfo($version);	
				} 
			}
			
			$this->pushReportCheck('Extension', $k, $reportCheck);
		}
	}
	
	/**
	 * Verifica se esistone la PCRE 
	 * @return void
	 */
	private function checkPCRE() {
		
		// inizializza l'oggetto per il report
			$reportCheck = new ReportCheck(false, 'PCRE', 'KO', 'Not defined', 'PCRE unicode support is not enabled in PHP', 'http://www.php.net/manual/en/book.pcre.php', 'Used by Zend Search Lucene Framework');
		if (@preg_match('/\pL/u', 'a') == 1) {
			$reportCheck->setCheck(true);
			$reportCheck->setMessage('OK');
			$reportCheck->setInfo('PCRE unicode support is enabled in PHP');
		}
		
		$this->pushReportCheck('Encoding', 'PCRE', $reportCheck);
	}
	
	/**
	 * Verifica se esistono le funzioni 
	 * @return void
	 */
	private function checkPHPFunction() {
		$funct = $this->getListFunction();
		
		foreach($funct['fun'] as $k => $fun) {
			// inizializza l'oggetto per il report
			$reportCheck = new ReportCheck(false, $fun, 'KO', 'Not defined', 'This function not exist in PHP', $funct['link'][$k], $funct['use'][$k]);
			if (function_exists($fun)) {
				$reportCheck->setCheck(true);
				$reportCheck->setMessage('OK');
				$reportCheck->setInfo('Function exists');
			}
			
			$this->pushReportCheck('Function', $k, $reportCheck);
		}
	}
	
	/**
	 * Inserisce un nuovo oggetto nel registro
	 * 
	 * @param string $cption
	 * @param object $reportCheck	 
	 * @return void
	 */
	private function pushReportCheck($caption, $type, $reportCheck) {
		if (!isset($this->registry[$caption])) $this->registry[$caption] = array();
		if (!isset($this->registry[$caption][$type])) $this->registry[$caption][$type] = array();
		$this->registry[$caption][$type] = $reportCheck;
	}
	
	/**
	 * Ritorna la lista delle funzioni di PHP
	 * 
	 * @return array
	 */
	private function getListFunction() {
		$fun = array();
		// funzioni
		$fun['fun']['popen'] = 'popen';
		$fun['fun']['strip_tags'] = 'strip_tags';
		
		// link
		$fun['link']['popen'] = 'http://www.php.net/manual/en/function.popen.php';
		$fun['link']['strip_tags'] = 'http://php.net/manual/en/function.strip-tags.php';
		
		// use
		$fun['use']['popen'] = 'Used only for PDF file parser';
		$fun['use']['strip_tags'] = 'Used only for XML file parser';
		
		return $fun;
	}
	
	/**
	 * Ritorna la lista delle estensioni necessarie
	 * 
	 * @return array
	 */
	private function getListExtension() {
		$ext = array();
		// librerie 
		$ext['ext']['libxml'] 	= 'libxml'; 	// Parserizzazione file OpenXml
		$ext['ext']['dom'] 		= 'dom';		// Parserizzazione file OpenXml	   
		$ext['ext']['SimpleXML']= 'SimpleXML';	// Parserizzazione file OpenXml
		$ext['ext']['mbstring'] = 'mbstring';	// Gestione multilingua
		$ext['ext']['zip'] 		= 'zip';		// Parserizzazione file OpenXml
		$ext['ext']['zlib'] 	= 'zlib';		// Parserizzazione file OpenXml
		$ext['ext']['iconv'] 	= 'iconv';		// Gestione multilingua
		$ext['ext']['id3'] 		= 'id3';		// Gestione TAG ID3
		$ext['ext']['mysqli'] 	= 'mysqli';		// Gestione Interfaccia MySqli
		$ext['ext']['exif'] 	= 'exif';		// Gestione TAG Exif
		$ext['ext']['com'] 		= 'com_dotnet';	// Gestione file DOC
		
		// versione minima della libreria
		$ext['version']['libxml'] 	= '2.6.0';
		$ext['version']['dom'] 		= false;
		$ext['version']['SimpleXML']= false;
		$ext['version']['mbstring'] = false;
		$ext['version']['zip'] 		= false;
		$ext['version']['zlib'] 	= '1.0.9';
		$ext['version']['iconv'] 	= false;
		$ext['version']['id3'] 		= '0.1';
		$ext['version']['mysqli'] 	= false;  
		$ext['version']['exif'] 	= '1.4';  
		$ext['version']['com'] 		= '0.1';  
		
		// use 
		$ext['use']['libxml'] 	= 'Used for Office Open Xml (OOXML) and OpenDocument (ODF) file parser';
		$ext['use']['dom'] 		= 'Used for Office Open Xml (OOXML) and OpenDocument (ODF) file parser';
		$ext['use']['SimpleXML']= 'Used for Office Open Xml (OOXML) and OpenDocument (ODF) file parser';
		$ext['use']['mbstring'] = 'Used by Zend Search Lucene';
		$ext['use']['zip'] 		= 'Used for Office Open Xml (OOXML) and OpenDocument (ODF) file parser';
		$ext['use']['zlib'] 	= 'Used for Office Open Xml (OOXML) and OpenDocument (ODF) file parser';
		$ext['use']['iconv'] 	= 'Used by Zend Search Lucene'; 
		$ext['use']['id3'] 		= 'Used for MP3 file parser';
		$ext['use']['mysqli'] 	= 'Used only for MySqli Interface'; 
		$ext['use']['exif'] 	= 'Used for JPG file parser'; 
		$ext['use']['com'] 		= 'Used for DOC file parser'; 
		
		// link
		$ext['link']['libxml'] 	 = 'http://www.php.net/manual/en/book.libxml.php';
		$ext['link']['dom'] 	 = 'http://www.php.net/manual/en/book.dom.php';
		$ext['link']['SimpleXML']= 'http://www.php.net/manual/en/book.simplexml.php';
		$ext['link']['mbstring'] = 'http://www.php.net/manual/en/book.mbstring.php';
		$ext['link']['zip'] 	 = 'http://www.php.net/manual/en/class.ziparchive.php';
		$ext['link']['zlib'] 	 = 'http://www.php.net/manual/en/book.zlib.php';
		$ext['link']['iconv'] 	 = 'http://www.php.net/manual/en/book.iconv.php'; 
		$ext['link']['id3'] 	 = 'http://www.php.net/manual/en/book.id3.php';
		$ext['link']['mysqli'] 	 = 'http://www.php.net/manual/en/book.mysqli.php';
		$ext['link']['exif'] 	 = 'http://www.php.net/manual/en/book.exif.php';
		$ext['link']['com'] 	 = 'http://www.php.net/manual/en/book.com.php';
		
		
		return $ext;
	}
	
	/**
	 * Verifica l'esistenza dell'ultimo "directory separetor"
	 * @param object $path
	 * @return void
	 */
	private function lastDS($path) {
		$path = realpath($path);
		
		$lastChar = $path{strlen($path)-1};
		if($lastChar != DIRECTORY_SEPARATOR) {
			$path .= DIRECTORY_SEPARATOR;
		}
		return $path;
	}
		
	/**
	 * Ritorna true se il file esiste
	 *  
	 * @param string $file
	 * @return bool
	 */
	private function checkPearFile($file) {
		// cicla per tutti i path fdefiniti nel php.ini
		foreach ($this->include_path as $val) {
			if (file_exists($this->lastDS($val).$file)) {
				 return $val;
			}
		}
		
		return false;
	}
	
	/**
	 * Controlla i chmod dei file di esecuzione
	 * 
	 * @param string $path
	 * @param string $perm [optional]
	 * @param object $oct [optional]
	 * 
	 * @return boolean
	 */
	function checkPermits($path, $perm = '0755', $oct = false, $custom = false)
	{
		if (!$custom) {
			// @TODO da rivedere il recupero del Path
			$path = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$path;	
		}
		
		// cerca di forzare i permessi
		// chmod($path, $perm);
	    clearstatcache();
		
		// alcuni sistemi operativi ritornano il valore n ottale
		// vedi Macintosh o FreeBSD 
		if (!$oct) {
			$configmod = substr(sprintf('%o', fileperms($path)), -4);
			$trcss = (($configmod == $perm || $configmod == "0777") ?  true : false) ;
		} else {
			$configmod = octdec(substr(sprintf('%o', fileperms($path)), -4));
			$trcss = (($configmod == $perm || $configmod == "777") ?  true : false) ;
		}
		
		$this->configmod = $configmod;
		return $trcss;		  
	}  
}
?>