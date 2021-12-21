<?php
/**
 * IFile Framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter/helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright 
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.0 class.openoffice2txt.php 2011-12-12 14:34:54
 */

/**
 * Recuperare il contenuto di un documento OpenOffice in formato testo. 
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter/helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class PHPOpenOfficeLib {
	const OPENOFFICEXML  = "content.xml";
	const OPENOFFICEMETA = "meta.xml";
	/**
	 * Documento OpenOffice
	 *  
	 * @var string
	 */
	private $archive = null;
	/**
	 * Istanza della classe ZipArchive
	 * 
	 * @var ZipArchive
	 */
	private $zip = null;
	
	/**
	 * Open Document
	 * 
	 * @param string $archiveFile
	 * @return void
	 */
	function __construct($archiveFile) {
		$this->archive = $archiveFile;
		// Create new ZIP archive
	    $this->zip = new ZipArchive;
		$code = $this->zip->open($this->archive);
		// Open received archive file
	    if (true !== $code) {
	    	require_once 'Helper_Exception.php';
			throw new Helper_Exception(AdapterHelper::getZipError($code));			
	    }
	}
	
	/**
     * Convert a ODT into text.
     * 
     * @throws Helper_Exception
     * @param string $archiveFile The archiveFile to extract the data from.
     * @return string The extracted text from the ODT
     */
    function openoffice2txt() {
	    
		// recupera i contenuti
		$index = @$this->zip->locateName(PHPOpenOfficeLib::OPENOFFICEXML);
		
        // If done, search for the data file in the archive
        if ($index === false) {
            require_once 'Helper_Exception.php';
			throw new Helper_Exception(AdapterHelper::getZipError(21));
        }
	    // If found, read it to the string
        $data = $this->zip->getFromIndex($index);
		// se non esiste contenuto 
		if (empty($data)) {
			return null;
		}
		// get SimpleXmlElement from XML 
		$sxe = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOBLANKS & LIBXML_COMPACT & LIBXML_NOEMPTYTAG);
		$text = $sxe->xpath('//text:p');
		
		$data = array();
		
		foreach ($text as $txt) {
			$tmp = trim((string)$txt);
			if ($tmp != '') 
				$data[] = $tmp;
		}
					
        return implode(chr(10), $data);
	} 
	
	/**
	 * Return Metadata from document
	 * 
	 * @throws Helper_Exception
	 * @return array 
	 */
	public function getMetadata() {
		/**
		 * @var SimpleXmlElement
		 */
		$sxe = null;
		$meta = array();
		$index = @$this->zip->locateName(PHPOpenOfficeLib::OPENOFFICEMETA);
		if ($index === false) {
			require_once 'Helper_Exception.php';
			throw new Helper_Exception(AdapterHelper::getZipError(21));
		}
		// If found, read it to the string
        $data = $this->zip->getFromIndex($index);            
		// get SimpleXmlElement from XML 
		$sxe = simplexml_load_string($data);
		
		if (is_object($sxe) && $sxe instanceof SimpleXMLElement) {
			// title
			$meta['Title'] = $this->getMetaFromArray($sxe->xpath('//dc:title'));
			// subject
			$meta['Subject'] = $this->getMetaFromArray($sxe->xpath('//dc:subject'));
			// creator
			$meta['Creator'] = $this->getMetaFromArray($sxe->xpath('//meta:initial-creator'));
			// keywords
			$meta['Keywords'] = $this->getMetaFromArray($sxe->xpath('//meta:keyword'));
			// creation date
			$meta['CreationDate'] = str_replace('T', ' ', $this->getMetaFromArray($sxe->xpath('//meta:creation-date')));
			// modifier date
			$meta['ModDate'] = str_replace('T', ' ', $this->getMetaFromArray($sxe->xpath('//dc:date')));
		}

		return $meta;
	}
		
	/**
	 * Return value from metadatada
	 * 
	 * @param SimpleXmlElement $metadata
	 * @param string $separetor [optional]
	 * @return string 
	 */
	private function getMetaFromArray($metadata, $separetor = ' ') {
		$words = array();
		
		foreach ($metadata as $value) {
			$words[] = (string)$value;
		}
		
		return (implode($separetor, $words));
	} 
	
	private function getTextFromSimpleXmlElement() {
		$words[] = (string)$value;
	}
	
	/**
	 * Close document
	 * 
	 * @return void 
	 */
	function __destruct() {
       // Close archive file
       $this->zip->close();
   }
}
?>