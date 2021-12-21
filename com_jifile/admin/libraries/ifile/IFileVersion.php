<?php
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.2.1 IFileVersion.php 2013-09-10
 */

/**
 * Definisce la versione di IFile
 *
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
final class IFileVersion
{
    /**
     * IFile version
     */
    const VERSION = '1.2.2';
	
	/**
     * IFile Versione Date
     * YYYY-MM-DD
     */
    const VERSIONDATE = '2014-07-07';

	 /**
     * Versione dell'ultima release stabile
     *
     * @var string
     */
    protected static $_latestVersion;
	
    /**
     * Confronta la versione specificata di IFILE 
     * con la versione corrente IFileVersion::VERSION of IFile.
     *
     * @param  string  $version  Un versione in formato stringa (e.g. "1.0.1").
     * @return int           -1 se la versione e' minore,
     *                        0 se la versione e' uguale,
     *                       +1 se la versione e' maggiore
     *
     */
    public static function compareVersion($version)
    {
        $version = strtolower($version);
        $version = preg_replace('/(\d)pr(\d?)/', '$1a$2', $version);
        return version_compare($version, strtolower(self::VERSION));
    }

    /**
     * Recupera la versione dell'ultima release stabile
     *
     * @link http://www.isapp.it/en/download-ifile.html
     * @return string
     */
    public static function getLatest()
    {
        if (null === self::$_latestVersion) {
            self::$_latestVersion = 'Not available';

            $handle = fopen('http://www.isapp.it/ifile/ifile-version', 'r');
            if (false !== $handle) {
                self::$_latestVersion = stream_get_contents($handle);
                fclose($handle);
            }
        }

        return self::$_latestVersion;
    }
}
