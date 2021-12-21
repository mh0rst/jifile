<?php
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.0 IFileFactory.php 2011-01-10 13:54:11
 */

/**
 * Permette di istanziare dinamicamente un oggetto di tipo
 * Adapter_Search_Lucene_Document_Interface o IFile_Indexing_Interface
 *
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileFactory {
	// error_reporting(E_PARSE);
	// Eliminati i NOTICE. 
	// Questo perche' in alcuni casi sia la ICONV 
	// utilizzata dal processo di indicizzazione da parte del framework ZEND 
	// ritornano un Notice dovuto a caratteri sporchi nei documenti
	// Elimando il NOTICE non si capisce se il testo e' stato realmente indicizzato
	// dato che la ICONV tronca il testo al carattere sporco
	// @TODO  
	// verificare se e' possibile per la ICONV continuare il processo
	// di encoding senza fermarsi anche su caratteri non corretti	
	/**
	 * Istanza di IFileFactory
	 * 
	 * @var IFileFactory
	 */
	private static $_instance;
	
	/**
	 * Nome della directory dove risiede il file
	 * 
	 * @var string
	 */
	private $dirname;
	
	/**
	 * Il metodo non e' invocabile per il Singleton pattern 
	 *
	 * @return void 
	 */
	private function __construct() {
		$this->dirname = dirname(__FILE__).DIRECTORY_SEPARATOR;		
	}
	
	/**
	 * Ritorna una istanza dell'oggetto IFileFactory
	 * 
	 * @return IFileFactory  
	 */
	static function getInstance() {
		if (self::$_instance == null) 
			self::$_instance = new IFileFactory();			
			
		return self::$_instance;		
	}
	
	/**
	 * Ritorna un oggetto IFile_Indexing_Interface
	 * 
	 * @throws Lucene_Exception
	 * @return IFile_Indexing_Interface
	 * @throws ReflectionException, IFile_Exception 
	 */
	public function getIFileIndexing($type, $resource) {
		
		// Recupera il nome della classe
		$className = "IFile_Indexing_".ucfirst(strtolower($type));
		$pathFile  = $this->dirname.$className.'.php';

		// controllo esistenza del file 
		if (!file_exists($pathFile)) {
			require_once 'IFile_Exception.php';
			throw new IFile_Exception('Type of indexing is not allowed');
		} 

		// require della classe
		require_once($pathFile);

		// Reflection		
		$reflection = new ReflectionClass($className);
		$found = false;
		// recupero le interfacce implementate dalla classe
		$interfaces = $reflection->getInterfaces();
		// verifico che la classe implementi l'interfaccia ActionInterface			
		foreach($interfaces as $interface) {
			if ($interface->getName() == 'IFile_Indexing_Interface') 
			{
				$found = true;
				break;	
			}				 
		} 
		if(!$found) {
			require_once 'IFile_Exception.php';
			throw new IFile_Exception('The class does not implement IFile_Indexing_Interface');
		}
		
		// ritorna l'oggetto
		return $reflection->newInstance($resource);
	}
	
	/**
	 * Ritorna un oggetto Adatpter_Search_Lucene_Document_Interface
	 * 
	 * @throws Lucene_Exception
	 * @return Adatpter_Search_Lucene_Document_Interface
	 * @throws ReflectionException, IFile_Exception
	 */
	public function getAdapterSearchLuceneDocument($ext) {
		
		// Recupera il nome della classe
		$className =  "Adapter_Search_Lucene_Document_".strtoupper($ext);
		$pathFile  = $this->dirname.'adapter'.DIRECTORY_SEPARATOR.$className.'.php';

		// controllo esistenza del file 
		if (!file_exists($pathFile)) {
			require_once 'IFile_Exception.php';
			throw new IFile_Exception('Type of file extension is not allowed');
		} 

		// require della classe
		require_once($pathFile);

		// Reflection		
		$reflection = new ReflectionClass($className);
		$found = false;
		// recupero le interfacce implementate dalla classe
		$interfaces = $reflection->getInterfaces();
		// verifico che la classe implementi l'interfaccia ActionInterface			
		foreach($interfaces as $interface) {
			if ($interface->getName() == 'Adapter_Search_Lucene_Document_Interface') 
			{
				$found = true;
				break;	
			}				 
		} 
		if(!$found) {
			require_once 'IFile_Exception.php';
			throw new IFile_Exception('The class does not implement Adapter_Search_Lucene_Document_Interface');
		}
		
		// ritorna l'oggetto
		return $reflection->newInstance();
	}
}
?>