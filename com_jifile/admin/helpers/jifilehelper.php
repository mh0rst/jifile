<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
// No direct access
defined('_JEXEC') or die('Restricted access');

// addon 
if (!defined('JIFILE_ADDON_PATH')) {
	define('JIFILE_ADDON_PATH', JPATH_ADMINISTRATOR.'/components/com_jifile/addon');
}
if (!defined('JIFILE_ADDON_PATH_SITE')) {
	define('JIFILE_ADDON_PATH_SITE', JPATH_BASE.'/components/com_jifile/addon');
}
// plugin 
if (!defined('JIFILE_ADDON_PLUGIN_PATH')) {
	define('JIFILE_ADDON_PLUGIN_PATH', JPATH_ADMINISTRATOR.'/components/com_jifile/addon/plugins');
}
if (!defined('JIFILE_ADDON_PLUGIN_PATH_SITE')) {
	define('JIFILE_ADDON_PLUGIN_PATH_SITE', JPATH_BASE.'/components/com_jifile/addon/plugins');
}

if (!defined('JPATH_IFILE_LIBRARY')) {
	//path ifile library
	define('JPATH_IFILE_LIBRARY', JPATH_ADMINISTRATOR.'/components/com_jifile/libraries');
}

class jifilehelper {
	
	/**
	 * Restituisce il path dell'indice configurato dai parametri 
	 * 
	 * @return mixed
	 */
	static function getIndexPath() {
		$params = JComponentHelper::getParams( 'com_jifile' );
		return jifilehelper::getCorrectPath($params->get( 'index_path' ), true);
	}
	
	/**
	 * Restituisce il path pulito
	 * 
	 * @param path $path
	 * @param bool $startSlash [optional]
	 * @param bool $endSlash [optional]
	 * @return path
	 */
	static function getCorrectPath($path, $startSlash = false, $endSlash = true) {
		
		$ds = DIRECTORY_SEPARATOR;
		
		if(empty($path)) {
			return null;
		}
		$path = str_replace(array('\\','/'), $ds, $path);

		if($startSlash) {
			if ($path[0] != $ds) {
				$path = DS.$path;
			}
		} else {
			if ($path[0] == $ds) {
				$path = substr($path, 1);
			}
		}
		if($endSlash) {
			$cc = (strlen($path)-1);
			if ($path[$cc] != $ds) {
				$path .= $ds;
			}
		} else {
			$cc = (strlen($path)-1);
			if ($path[$cc] == $ds) {
				$path = substr($path, 0, $cc);
			}
		}
		
		return $path;
	}
	
	/**
	 * Return Jommla! Path Site for resolved the index on different server 
	 * @return 
	 */
	static function getRootApplication() {
//		$params = &JComponentHelper::getParams( 'com_jifile' );
//		$rootApp = $params->get( 'root-application' );
//		if (empty($rootApp)) {
//			require_once JPATH_IFILE_LIBRARY.'/ifile/IFileConfig.php';
//			$configObj = IFileConfig::getInstance();
//			$rootApp = realpath($configObj->getConfig('root-application'));
//			jifilehelper::saveParams(array('root-application'=>$rootApp));
//		}
		//@TODO
		// capire se è anche possibile definire una RootPath che non sia quella 
		// di Joomla. Forse si potrebbe inserire nella "Opzioni"
		$rootApp = JPATH_SITE.DS;
		return $rootApp;
	}
	
	static function checkPathConfig(&$rootApp) {
		$rootApp = jifilehelper::getRootApplication();
		
		$params = JComponentHelper::getParams( 'com_jifile' );
		$basepath = jifilehelper::getCorrectPath($params->get( 'base_path' ));
		if(empty($basepath) || empty($rootApp)) {
			return true;
		}
		if(strpos(realpath(JPATH_SITE.DS.$basepath), realpath($rootApp)) === false) {
			return false;
		}
		return true;
	}
	
	static function retrievePath($pathFilename, $no_basepath = false){
		
		$params = JComponentHelper::getParams( 'com_jifile' );
		$basepath = jifilehelper::getCorrectPath($params->get( 'base_path' ));
		$realpath = realpath(JPATH_SITE.DS.$basepath);

		$basepath = str_replace(JPATH_SITE, '', $realpath);
		$basepath = empty($basepath) ? JPATH_SITE : $basepath;
		$basepath = jifilehelper::getCorrectPath($params->get( 'base_path' ), true, true);
		
		$filename = '';
		if(($pos = strpos($pathFilename, $basepath)) !== false) {
			$filename = substr($pathFilename, $pos+strlen($basepath));
		}
		if($filename) {
			if (!$no_basepath) {
				$path = $basepath.$filename;
			} else {
				$path = $filename;
			}
			return jifilehelper::getCorrectPath($path, false, false);
		}
		return false;
	}
	
	static function getCorrectFilename($name, $getFull = false) {
		$name = jifilehelper::getCorrectPath($name, false, false);
		
		$rootApp = jifilehelper::getRootApplication();
		
		if (file_exists($rootApp.DS.$name)) {
			return ($getFull) ? $rootApp.DS.$name : $name;
		}
		return false;
	}
	
	/**
	 * Ritorna una stringa con la dimensione nel corretto formato 
	 *
	 * @param int $size
	 * @return string
	 */
	static function getFormatSize($size) {
		
		$kb = 1024;
        $mb = 1024 * $kb;
        $gb = 1024 * $mb;
        $tb = 1024 * $gb;

		if ($size < $kb) {
			$format = $size.' Bytes';
		}
		elseif ($size < $mb) {
			$final = round($size/$kb,2);
			$format = $final.' kB';
		}
		elseif ($size < $gb) {
			$final = round($size/$mb,2);
			$format = $final.' MB';
		}
		elseif($size < $tb) {
			$final = round($size/$gb,2);
			$format = $final.' GB';
		} else {
			$final = round($size/$tb,2);
			$format = $final.' TB';
		}

		return $format;
	}
	
	/**
	 * Ritorna la stringa del Mime Type di un file in funzione dell'estensione
	 * 
	 * @param string $ext
	 * @return string
	 */
	static function getMimetype($ext) {
		
		$mime_extension_map = array(
		    '3ds' => 'image/x-3ds',
		    'BLEND' => 'application/x-blender',
		    'C' => 'text/x-c++src',
		    'CSSL' => 'text/css',
		    'NSV' => 'video/x-nsv',
		    'XM' => 'audio/x-mod',
		    'Z' => 'application/x-compress',
		    'a' => 'application/x-archive',
		    'abw' => 'application/x-abiword',
		    'abw.gz' => 'application/x-abiword',
		    'ac3' => 'audio/ac3',
		    'adb' => 'text/x-adasrc',
		    'ads' => 'text/x-adasrc',
		    'afm' => 'application/x-font-afm',
		    'ag' => 'image/x-applix-graphics',
		    'ai' => 'application/illustrator',
		    'aif' => 'audio/x-aiff',
		    'aifc' => 'audio/x-aiff',
		    'aiff' => 'audio/x-aiff',
		    'al' => 'application/x-perl',
		    'arj' => 'application/x-arj',
		    'as' => 'application/x-applix-spreadsheet',
		    'asc' => 'text/plain',
		    'asf' => 'video/x-ms-asf',
		    'asp' => 'application/x-asp',
		    'asx' => 'video/x-ms-asf',
		    'au' => 'audio/basic',
		    'avi' => 'video/x-msvideo',
		    'aw' => 'application/x-applix-word',
		    'bak' => 'application/x-trash',
		    'bcpio' => 'application/x-bcpio',
		    'bdf' => 'application/x-font-bdf',
		    'bib' => 'text/x-bibtex',
		    'bin' => 'application/octet-stream',
		    'blend' => 'application/x-blender',
		    'blender' => 'application/x-blender',
		    'bmp' => 'image/bmp',
		    'bz' => 'application/x-bzip',
		    'bz2' => 'application/x-bzip',
		    'c' => 'text/x-csrc',
		    'c++' => 'text/x-c++src',
		    'cc' => 'text/x-c++src',
		    'cdf' => 'application/x-netcdf',
		    'cdr' => 'application/vnd.corel-draw',
		    'cer' => 'application/x-x509-ca-cert',
		    'cert' => 'application/x-x509-ca-cert',
		    'cgi' => 'application/x-cgi',
		    'cgm' => 'image/cgm',
		    'chrt' => 'application/x-kchart',
		    'class' => 'application/x-java',
		    'cls' => 'text/x-tex',
		    'cpio' => 'application/x-cpio',
		    'cpio.gz' => 'application/x-cpio-compressed',
		    'cpp' => 'text/x-c++src',
		    'cpt' => 'application/mac-compactpro',
		    'crt' => 'application/x-x509-ca-cert',
		    'cs' => 'text/x-csharp',
		    'csh' => 'application/x-shellscript',
		    'css' => 'text/css',
		    'csv' => 'text/x-comma-separated-values',
		    'cur' => 'image/x-win-bitmap',
		    'cxx' => 'text/x-c++src',
		    'dat' => 'video/mpeg',
		    'dbf' => 'application/x-dbase',
		    'dc' => 'application/x-dc-rom',
		    'dcl' => 'text/x-dcl',
		    'dcm' => 'image/x-dcm',
		    'dcr' => 'application/x-director',
		    'deb' => 'application/x-deb',
		    'der' => 'application/x-x509-ca-cert',
		    'desktop' => 'application/x-desktop',
		    'dia' => 'application/x-dia-diagram',
		    'diff' => 'text/x-patch',
		    'dir' => 'application/x-director',
		    'djv' => 'image/vnd.djvu',
		    'djvu' => 'image/vnd.djvu',
		    'dll' => 'application/octet-stream',
		    'dms' => 'application/octet-stream',
		    'doc' => 'application/msword',
		    'dsl' => 'text/x-dsl',
		    'dtd' => 'text/x-dtd',
		    'dvi' => 'application/x-dvi',
		    'dwg' => 'image/vnd.dwg',
		    'dxf' => 'image/vnd.dxf',
		    'dxr' => 'application/x-director',
		    'egon' => 'application/x-egon',
		    'el' => 'text/x-emacs-lisp',
		    'eps' => 'image/x-eps',
		    'epsf' => 'image/x-eps',
		    'epsi' => 'image/x-eps',
		    'etheme' => 'application/x-e-theme',
		    'etx' => 'text/x-setext',
		    'exe' => 'application/x-executable',
		    'ez' => 'application/andrew-inset',
		    'f' => 'text/x-fortran',
		    'fig' => 'image/x-xfig',
		    'fits' => 'image/x-fits',
		    'flac' => 'audio/x-flac',
		    'flc' => 'video/x-flic',
		    'fli' => 'video/x-flic',
		    'flw' => 'application/x-kivio',
		    'fo' => 'text/x-xslfo',
		    'g3' => 'image/fax-g3',
		    'gb' => 'application/x-gameboy-rom',
		    'gcrd' => 'text/x-vcard',
		    'gen' => 'application/x-genesis-rom',
		    'gg' => 'application/x-sms-rom',
		    'gif' => 'image/gif',
		    'glade' => 'application/x-glade',
		    'gmo' => 'application/x-gettext-translation',
		    'gnc' => 'application/x-gnucash',
		    'gnucash' => 'application/x-gnucash',
		    'gnumeric' => 'application/x-gnumeric',
		    'gra' => 'application/x-graphite',
		    'gsf' => 'application/x-font-type1',
		    'gtar' => 'application/x-gtar',
		    'gz' => 'application/x-gzip',
		    'h' => 'text/x-chdr',
		    'h++' => 'text/x-chdr',
		    'hdf' => 'application/x-hdf',
		    'hh' => 'text/x-c++hdr',
		    'hp' => 'text/x-chdr',
		    'hpgl' => 'application/vnd.hp-hpgl',
		    'hqx' => 'application/mac-binhex40',
		    'hs' => 'text/x-haskell',
		    'htm' => 'text/html',
		    'html' => 'text/html',
		    'icb' => 'image/x-icb',
		    'ice' => 'x-conference/x-cooltalk',
		    'ico' => 'image/x-ico',
		    'ics' => 'text/calendar',
		    'idl' => 'text/x-idl',
		    'ief' => 'image/ief',
		    'ifb' => 'text/calendar',
		    'iff' => 'image/x-iff',
		    'iges' => 'model/iges',
		    'igs' => 'model/iges',
		    'ilbm' => 'image/x-ilbm',
		    'iso' => 'application/x-cd-image',
		    'it' => 'audio/x-it',
		    'jar' => 'application/x-jar',
		    'java' => 'text/x-java',
		    'jng' => 'image/x-jng',
		    'jp2' => 'image/jpeg2000',
		    'jpg' => 'image/jpeg',
		    'jpe' => 'image/jpeg',
		    'jpeg' => 'image/jpeg',
		    'jpr' => 'application/x-jbuilder-project',
		    'jpx' => 'application/x-jbuilder-project',
		    'js' => 'application/x-javascript',
		    'kar' => 'audio/midi',
		    'karbon' => 'application/x-karbon',
		    'kdelnk' => 'application/x-desktop',
		    'kfo' => 'application/x-kformula',
		    'kil' => 'application/x-killustrator',
		    'kon' => 'application/x-kontour',
		    'kpm' => 'application/x-kpovmodeler',
		    'kpr' => 'application/x-kpresenter',
		    'kpt' => 'application/x-kpresenter',
		    'kra' => 'application/x-krita',
		    'ksp' => 'application/x-kspread',
		    'kud' => 'application/x-kugar',
		    'kwd' => 'application/x-kword',
		    'kwt' => 'application/x-kword',
		    'la' => 'application/x-shared-library-la',
		    'latex' => 'application/x-latex',
		    'lha' => 'application/x-lha',
		    'lhs' => 'text/x-literate-haskell',
		    'lhz' => 'application/x-lhz',
		    'log' => 'text/x-log',
		    'ltx' => 'text/x-tex',
		    'lwo' => 'image/x-lwo',
		    'lwob' => 'image/x-lwo',
		    'lws' => 'image/x-lws',
		    'lyx' => 'application/x-lyx',
		    'lzh' => 'application/x-lha',
		    'lzo' => 'application/x-lzop',
		    'm' => 'text/x-objcsrc',
		    'm15' => 'audio/x-mod',
		    'm3u' => 'audio/x-mpegurl',
		    'man' => 'application/x-troff-man',
		    'md' => 'application/x-genesis-rom',
		    'me' => 'text/x-troff-me',
		    'mesh' => 'model/mesh',
		    'mgp' => 'application/x-magicpoint',
		    'mid' => 'audio/midi',
		    'midi' => 'audio/midi',
		    'mif' => 'application/x-mif',
		    'mkv' => 'application/x-matroska',
		    'mm' => 'text/x-troff-mm',
		    'mml' => 'text/mathml',
		    'mng' => 'video/x-mng',
		    'moc' => 'text/x-moc',
		    'mod' => 'audio/x-mod',
		    'moov' => 'video/quicktime',
		    'mov' => 'video/quicktime',
		    'movie' => 'video/x-sgi-movie',
		    'mp2' => 'video/mpeg',
		    'mp3' => 'audio/x-mp3',
		    'mpe' => 'video/mpeg',
		    'mpeg' => 'video/mpeg',
		    'mpg' => 'video/mpeg',
		    'mpga' => 'audio/mpeg',
		    'ms' => 'text/x-troff-ms',
		    'msh' => 'model/mesh',
		    'msod' => 'image/x-msod',
		    'msx' => 'application/x-msx-rom',
		    'mtm' => 'audio/x-mod',
		    'mxu' => 'video/vnd.mpegurl',
		    'n64' => 'application/x-n64-rom',
		    'nc' => 'application/x-netcdf',
		    'nes' => 'application/x-nes-rom',
		    'nsv' => 'video/x-nsv',
		    'o' => 'application/x-object',
		    'obj' => 'application/x-tgif',
		    'oda' => 'application/oda',
		    'odb' => 'application/vnd.oasis.opendocument.database',
		    'odc' => 'application/vnd.oasis.opendocument.chart',
		    'odf' => 'application/vnd.oasis.opendocument.formula',
		    'odg' => 'application/vnd.oasis.opendocument.graphics',
		    'odi' => 'application/vnd.oasis.opendocument.image',
		    'odm' => 'application/vnd.oasis.opendocument.text-master',
		    'odp' => 'application/vnd.oasis.opendocument.presentation',
		    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		    'odt' => 'application/vnd.oasis.opendocument.text',
		    'ogg' => 'application/ogg',
		    'old' => 'application/x-trash',
		    'oleo' => 'application/x-oleo',
		    'otg' => 'application/vnd.oasis.opendocument.graphics-template',
		    'oth' => 'application/vnd.oasis.opendocument.text-web',
		    'otp' => 'application/vnd.oasis.opendocument.presentation-template',
		    'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
		    'ott' => 'application/vnd.oasis.opendocument.text-template',
		    'p' => 'text/x-pascal',
		    'p12' => 'application/x-pkcs12',
		    'p7s' => 'application/pkcs7-signature',
		    'pas' => 'text/x-pascal',
		    'patch' => 'text/x-patch',
		    'pbm' => 'image/x-portable-bitmap',
		    'pcd' => 'image/x-photo-cd',
		    'pcf' => 'application/x-font-pcf',
		    'pcf.Z' => 'application/x-font-type1',
		    'pcl' => 'application/vnd.hp-pcl',
		    'pdb' => 'application/vnd.palm',
		    'pdf' => 'application/pdf',
		    'pem' => 'application/x-x509-ca-cert',
		    'perl' => 'application/x-perl',
		    'pfa' => 'application/x-font-type1',
		    'pfb' => 'application/x-font-type1',
		    'pfx' => 'application/x-pkcs12',
		    'pgm' => 'image/x-portable-graymap',
		    'pgn' => 'application/x-chess-pgn',
		    'pgp' => 'application/pgp',
		    'php' => 'application/x-php',
		    'php3' => 'application/x-php',
		    'php4' => 'application/x-php',
		    'pict' => 'image/x-pict',
		    'pict1' => 'image/x-pict',
		    'pict2' => 'image/x-pict',
		    'pl' => 'application/x-perl',
		    'pls' => 'audio/x-scpls',
		    'pm' => 'application/x-perl',
		    'png' => 'image/png',
		    'pnm' => 'image/x-portable-anymap',
		    'po' => 'text/x-gettext-translation',
		    'pot' => 'application/vnd.ms-powerpoint',
		    'ppm' => 'image/x-portable-pixmap',
		    'pps' => 'application/vnd.ms-powerpoint',
		    'ppt' => 'application/vnd.ms-powerpoint',
		    'ppz' => 'application/vnd.ms-powerpoint',
		    'ps' => 'application/postscript',
		    'ps.gz' => 'application/x-gzpostscript',
		    'psd' => 'image/x-psd',
		    'psf' => 'application/x-font-linux-psf',
		    'psid' => 'audio/prs.sid',
		    'pw' => 'application/x-pw',
		    'py' => 'application/x-python',
		    'pyc' => 'application/x-python-bytecode',
		    'pyo' => 'application/x-python-bytecode',
		    'qif' => 'application/x-qw',
		    'qt' => 'video/quicktime',
		    'qtvr' => 'video/quicktime',
		    'ra' => 'audio/x-pn-realaudio',
		    'ram' => 'audio/x-pn-realaudio',
		    'rar' => 'application/x-rar',
		    'ras' => 'image/x-cmu-raster',
		    'rdf' => 'text/rdf',
		    'rej' => 'application/x-reject',
		    'rgb' => 'image/x-rgb',
		    'rle' => 'image/rle',
		    'rm' => 'audio/x-pn-realaudio',
		    'roff' => 'application/x-troff',
		    'rpm' => 'application/x-rpm',
		    'rss' => 'text/rss',
		    'rtf' => 'application/rtf',
		    'rtx' => 'text/richtext',
		    's3m' => 'audio/x-s3m',
		    'sam' => 'application/x-amipro',
		    'scm' => 'text/x-scheme',
		    'sda' => 'application/vnd.stardivision.draw',
		    'sdc' => 'application/vnd.stardivision.calc',
		    'sdd' => 'application/vnd.stardivision.impress',
		    'sdp' => 'application/vnd.stardivision.impress',
		    'sds' => 'application/vnd.stardivision.chart',
		    'sdw' => 'application/vnd.stardivision.writer',
		    'sgi' => 'image/x-sgi',
		    'sgl' => 'application/vnd.stardivision.writer',
		    'sgm' => 'text/sgml',
		    'sgml' => 'text/sgml',
		    'sh' => 'application/x-shellscript',
		    'shar' => 'application/x-shar',
		    'shtml' => 'text/html',
		    'siag' => 'application/x-siag',
		    'sid' => 'audio/prs.sid',
		    'sik' => 'application/x-trash',
		    'silo' => 'model/mesh',
		    'sit' => 'application/x-stuffit',
		    'skd' => 'application/x-koan',
		    'skm' => 'application/x-koan',
		    'skp' => 'application/x-koan',
		    'skt' => 'application/x-koan',
		    'slk' => 'text/spreadsheet',
		    'smd' => 'application/vnd.stardivision.mail',
		    'smf' => 'application/vnd.stardivision.math',
		    'smi' => 'application/smil',
		    'smil' => 'application/smil',
		    'sml' => 'application/smil',
		    'sms' => 'application/x-sms-rom',
		    'snd' => 'audio/basic',
		    'so' => 'application/x-sharedlib',
		    'spd' => 'application/x-font-speedo',
		    'spl' => 'application/x-futuresplash',
		    'sql' => 'text/x-sql',
		    'src' => 'application/x-wais-source',
		    'stc' => 'application/vnd.sun.xml.calc.template',
		    'std' => 'application/vnd.sun.xml.draw.template',
		    'sti' => 'application/vnd.sun.xml.impress.template',
		    'stm' => 'audio/x-stm',
		    'stw' => 'application/vnd.sun.xml.writer.template',
		    'sty' => 'text/x-tex',
		    'sun' => 'image/x-sun-raster',
		    'sv4cpio' => 'application/x-sv4cpio',
		    'sv4crc' => 'application/x-sv4crc',
		    'svg' => 'image/svg+xml',
		    'swf' => 'application/x-shockwave-flash',
		    'sxc' => 'application/vnd.sun.xml.calc',
		    'sxd' => 'application/vnd.sun.xml.draw',
		    'sxg' => 'application/vnd.sun.xml.writer.global',
		    'sxi' => 'application/vnd.sun.xml.impress',
		    'sxm' => 'application/vnd.sun.xml.math',
		    'sxw' => 'application/vnd.sun.xml.writer',
		    'sylk' => 'text/spreadsheet',
		    't' => 'application/x-troff',
		    'tar' => 'application/x-tar',
		    'tar.Z' => 'application/x-tarz',
		    'tar.bz' => 'application/x-bzip-compressed-tar',
		    'tar.bz2' => 'application/x-bzip-compressed-tar',
		    'tar.gz' => 'application/x-compressed-tar',
		    'tar.lzo' => 'application/x-tzo',
		    'tcl' => 'text/x-tcl',
		    'tex' => 'text/x-tex',
		    'texi' => 'text/x-texinfo',
		    'texinfo' => 'text/x-texinfo',
		    'tga' => 'image/x-tga',
		    'tgz' => 'application/x-compressed-tar',
		    'theme' => 'application/x-theme',
		    'tif' => 'image/tiff',
		    'tiff' => 'image/tiff',
		    'tk' => 'text/x-tcl',
		    'torrent' => 'application/x-bittorrent',
		    'tr' => 'application/x-troff',
		    'ts' => 'application/x-linguist',
		    'tsv' => 'text/tab-separated-values',
		    'ttf' => 'application/x-font-ttf',
		    'txt' => 'text/plain',
		    'tzo' => 'application/x-tzo',
		    'ui' => 'application/x-designer',
		    'uil' => 'text/x-uil',
		    'ult' => 'audio/x-mod',
		    'uni' => 'audio/x-mod',
		    'uri' => 'text/x-uri',
		    'url' => 'text/x-uri',
		    'ustar' => 'application/x-ustar',
		    'vcd' => 'application/x-cdlink',
		    'vcf' => 'text/x-vcalendar',
		    'vcs' => 'text/x-vcalendar',
		    'vct' => 'text/x-vcard',
		    'vfb' => 'text/calendar',
		    'vob' => 'video/mpeg',
		    'voc' => 'audio/x-voc',
		    'vor' => 'application/vnd.stardivision.writer',
		    'vrml' => 'model/vrml',
		    'vsd' => 'application/vnd.visio',
		    'wav' => 'audio/x-wav',
		    'wax' => 'audio/x-ms-wax',
		    'wb1' => 'application/x-quattropro',
		    'wb2' => 'application/x-quattropro',
		    'wb3' => 'application/x-quattropro',
		    'wbmp' => 'image/vnd.wap.wbmp',
		    'wbxml' => 'application/vnd.wap.wbxml',
		    'wk1' => 'application/vnd.lotus-1-2-3',
		    'wk3' => 'application/vnd.lotus-1-2-3',
		    'wk4' => 'application/vnd.lotus-1-2-3',
		    'wks' => 'application/vnd.lotus-1-2-3',
		    'wm' => 'video/x-ms-wm',
		    'wma' => 'audio/x-ms-wma',
		    'wmd' => 'application/x-ms-wmd',
		    'wmf' => 'image/x-wmf',
		    'wml' => 'text/vnd.wap.wml',
		    'wmlc' => 'application/vnd.wap.wmlc',
		    'wmls' => 'text/vnd.wap.wmlscript',
		    'wmlsc' => 'application/vnd.wap.wmlscriptc',
		    'wmv' => 'video/x-ms-wmv',
		    'wmx' => 'video/x-ms-wmx',
		    'wmz' => 'application/x-ms-wmz',
		    'wpd' => 'application/wordperfect',
		    'wpg' => 'application/x-wpg',
		    'wri' => 'application/x-mswrite',
		    'wrl' => 'model/vrml',
		    'wvx' => 'video/x-ms-wvx',
		    'xac' => 'application/x-gnucash',
		    'xbel' => 'application/x-xbel',
		    'xbm' => 'image/x-xbitmap',
		    'xcf' => 'image/x-xcf',
		    'xcf.bz2' => 'image/x-compressed-xcf',
		    'xcf.gz' => 'image/x-compressed-xcf',
		    'xht' => 'application/xhtml+xml',
		    'xhtml' => 'application/xhtml+xml',
		    'xi' => 'audio/x-xi',
		    'xls' => 'application/vnd.ms-excel',
		    'xla' => 'application/vnd.ms-excel',
		    'xlc' => 'application/vnd.ms-excel',
		    'xld' => 'application/vnd.ms-excel',
		    'xll' => 'application/vnd.ms-excel',
		    'xlm' => 'application/vnd.ms-excel',
		    'xlt' => 'application/vnd.ms-excel',
		    'xlw' => 'application/vnd.ms-excel',
		    'xm' => 'audio/x-xm',
		    'xml' => 'text/xml',
		    'xpm' => 'image/x-xpixmap',
		    'xsl' => 'text/x-xslt',
		    'xslfo' => 'text/x-xslfo',
		    'xslt' => 'text/x-xslt',
		    'xwd' => 'image/x-xwindowdump',
		    'xyz' => 'chemical/x-xyz',
		    'zabw' => 'application/x-abiword',
		    'zip' => 'application/zip',
		    'zoo' => 'application/x-zoo',
		    '123' => 'application/vnd.lotus-1-2-3',
		    '669' => 'audio/x-mod',
		    'docx' => 'application/vnd.openxmlformats',
		    'pptx' => 'application/vnd.openxmlformats',
		    'xlsx' => 'application/vnd.openxmlformats',
		    'xltx' => 'application/vnd.openxmlformats',
		    'xltm' => 'application/vnd.openxmlformats',
		    'dotx' => 'application/vnd.openxmlformats',
		    'potx' => 'application/vnd.openxmlformats',
		    'ppsx' => 'application/vnd.openxmlformats'
		    );
		$ext = strtolower($ext);
		return (isset($mime_extension_map[$ext])) ? $mime_extension_map[$ext] : 'none'; 
	}
	
	/**
	 * Ritorna il numero di tutti di match del pattern
	 * sul nome del file o false in caso di errore
	 * 
	 * @param string $filename
	 * @param string $filter
	 * @param string $ext
	 * @return int
	 */
	static function getRegexSearch($filename, $filter = '', $ext = '*') {
				
		// creazione delle regular expression
		// per la ricerca sul nome del file
		$search = "/".$filter.".*\.".$ext."$/i";
		$ris 	= preg_match($search, $filename, $test);
		
		return $ris; 
	}

	static function getDirectorySize($path, $ignore = array())
	{
		if(!is_dir($path)) {
			return false;
		}
		$totalsize = 0;
		$totalcount = 0;
		$dircount = 0;
		if ($handle = opendir ($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				$nextpath = $path . '/' . $file;
				if ($file != '.' && $file != '..' && !is_link ($nextpath) && !in_array($file, $ignore))
				{
					if (is_dir ($nextpath))
					{
						$dircount++;
						$result = jifilehelper::getDirectorySize($nextpath);
						$totalsize += $result['size'];
						$totalcount += $result['count'];
						$dircount += $result['dircount'];
					}
					elseif (is_file ($nextpath))
					{
						$totalsize += filesize ($nextpath);
						$totalcount++;
					}
				}
			}
		}
		closedir ($handle);
		$total['size'] = $totalsize;
		$total['count'] = $totalcount;
		$total['dircount'] = $dircount;
		return $total;
	}

	static function getFileContent($filename) {
		jimport('joomla.filesystem.file');
		require_once JPATH_IFILE_LIBRARY.'/ifile/IFileAdapterFactory.php';
		
		$factory = LuceneAdapterFactory::getInstance();
		
		$adapter = $factory->getDocFromExt(JFile::getExt($filename));

		if ($adapter === false) {
			//$this->setError($luceneFactory->getMessageError());
			return false;
		}
		
		// chiamata la metodo per il parser del file
		$doc = $adapter->loadParserFile($filename);
		
		if($adapter->getError()) {
			return false;
		}
		try {
			return $doc->getFieldValue('body');	
		} catch (Exception $e) {
			return false;
		}
	}

	static function luceneDocToArray(Zend_Search_Lucene_Document $doc, $clear = false) {
		$fieldNames = $doc->getFieldNames();
		$array = array();
		foreach ($fieldNames as $field) {
			$array[$field] = ($clear) ? htmlentities($doc->getFieldValue($field)) : $doc->getFieldValue($field);
		}
		return $array;
	}

	static function initCache($cache_dir = null) {
		require_once 'Zend'.DS.'Cache.php';
		
		$cache_dir = is_null($cache_dir) ? JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_jifile'.DS.'cache' : $cache_dir;
		
		$frontendOptions = array(
			'lifetime' => 86400,
			'automatic_serialization' => true
		);
		
		$backendOptions = array(
			'cache_dir' => $cache_dir, 
			'automatic_serialization' => true
		);
		
		$cache = Zend_Cache::factory(
			'Core',
			'File',
			$frontendOptions,
			$backendOptions
		);
		
		return $cache;
	}
	
	static function setCache($cacheId, $data, $tags = array()) {
		$cache = jifilehelper::initCache();
		$cache->save($data, $cacheId, $tags);
	}
	
	static function getCache($cacheId) {
		$cache = jifilehelper::initCache();
		$result = $cache->load($cacheId);
		
		return $result;
	}
	
	static function clearCache($tags = array()) {
		$cache = jifilehelper::initCache();
		$mode = Zend_Cache::CLEANING_MODE_ALL;
		if(!empty($tags)) {
			$mode = Zend_Cache::CLEANING_MODE_MATCHING_TAG;
		}
		$cache->clean($mode, $tags);
	}
	
	static function refreshCache() {
	
		if(!($filesystem = jifilehelper::getCache('filesystem'))) {
			return null;
		}
		$delete = false;
		foreach ($filesystem as $key => $file) {
			if(!file_exists($key)) {
				unset($filesystem[$key]);
				$delete = true;
			}
		}
		if($delete) {
			jifilehelper::setCache('filesystem', $filesystem, array('filesystem'));
		}
	}
	
	static function deleteFileCache($file) {
		if(!($filesystem = jifilehelper::getCache('filesystem'))) {
			return null;
		}
		if(isset($filesystem[$file])) {
			unset($filesystem[$file]);
			jifilehelper::setCache('filesystem', $filesystem, array('filesystem'));
		}
	}
	
	static function fileInCache($file, $filetime = null) {
		if(!($filesystem = jifilehelper::getCache('filesystem'))) {
			return false;
		}
		if(!isset($filesystem[$file])) {
			return false;
		}
		if(!is_null($filetime) && $filetime != $filesystem[$file]['modified']) {
			//file modificato
			return false;
		}
		return true;
	}
	
	static function checkIndex($file, $lucene) {
	
		$filesystem = jifilehelper::getCache('filesystem');
		$filetime = filemtime($file);
		
		$docache = false;
		if(!$filesystem) {
			//no cache
			$docache = true;
		} elseif(!jifilehelper::fileInCache($file, $filetime)) {
			//file non in cache
			$docache = true;
		}
		if($docache) {
			$filesystem[$file]['modified'] = $filetime;
			$filesystem[$file]['indexed'] = ($lucene->getIdByFile($file) !== false) ? true : false;
			jifilehelper::setCache('filesystem', $filesystem, array('filesystem'));
		}
	
		return $filesystem[$file]['indexed'];
	}
	
	static function addJQuery($plugins = array()) {
		$pathIfile = '../administrator/components/com_jifile/';
		$doc = JFactory::getDocument();
		
		if (AdapterForJoomlaVersion::getInstance()->is(AdapterForJoomlaVersion::JOOMLA_3X)) {
			JHtml::_('jquery.framework');
		} else {
			$doc->addScript( $pathIfile.'js/jquery/jquery.min.js?1.9.1' );
			$doc->addScript( $pathIfile.'js/jquery/jquery-noconflict.js' );
			//$doc->addScriptDeclaration ( "jQuery.noConflict();" );
		}
		
		foreach ($plugins as $plugin) {
			switch ($plugin) {
				case 'colorbox':
					$doc->addScript( $pathIfile.'js/jquery/jquery.colorbox-min.js?1.4.15' );
					$doc->addScriptDeclaration ( "
									jQuery(document).ready(function($) {
								      jQuery('a[rel*=modalx]').colorbox({current: 'document {current} of {total}'});
								    });" );
					$doc->addStyleSheet( $pathIfile.'css/colorbox.css' );
					break;
			}
		}
		
	}

	static function array2Xml($data, $rootNodeName = 'data', &$xml = NULL, $addChild = 0) {
		if (is_null($xml)) {
			$xml = new SimpleXMLElement('<' . $rootNodeName . '/>');
		}
		
		// loop through the data passed in.
		foreach($data as $key => $value) {
			// if numeric key, assume array of rootNodeName elements
			if ($key == '@value' && $addChild) {
				continue;
			}
			if (is_numeric($key)) {
				$key = $rootNodeName;
			}
			// Check if is attribute
			if($key == 'attributes') {
				// Add attributes to node
				foreach($value as $attr_name => $attr_value) {
					$xml->addAttribute($attr_name, $attr_value);
				}
			} else {
				// delete any char not allowed in XML element names
				$key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);
				
				// if there is another array found recrusively call this function
				if (is_array($value)) {
					$valueChild = (isset($value['attributes']) && isset($value['@value'])) ? $value['@value'] : null;
					$addChild = jifilehelper::isAssoc($value);
					// create a new node unless this is an array of elements
					$node = $addChild ? $xml->addChild($key, $valueChild) : $xml;
					// recrusive call - pass $key as the new rootNodeName
					jifilehelper::array2Xml($value, $key, $node, $addChild);
				} else {
					// add single node.
					$value = htmlentities($value);
					$xml->addChild($key,$value);
				}
			}
		}
		// pass back as string. or simple xml object if you want!
		return $xml->asXML();
	}
	
	static function isAssoc( $array ) {
    	return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
	}
	
	static function is_empty($value) {
		if(!is_array($value)) $value = trim($value);
		if($value !== 0 && $value !== '0' && empty($value)) {
			return false;
		}
		return true;
	}
	
	static function array_filter_recursive($input)
	{
		foreach ($input as &$value)
		{
			if (is_array($value))
			{
				$value = jifilehelper::array_filter_recursive($value);
			}
		}
		
		return array_filter($input, array('jifilehelper', 'is_empty'));
	}

	static function JText($string, $jsSafe = false) {
		
		//{}|&~![()^':.;#/
		$pattern = array('/\s/', '/(\{|\}|\||\&|\~|\!|\[|\(|\)|\^|\'|:|\.|;|#)/');
		$replace = array('_', '');
		$string = preg_replace($pattern, $replace, $string);
		return JText::_($string, $jsSafe);
	}
	
	static function getActions()
	{
		jimport('joomla.access.access');
		$user	= JFactory::getUser();
		$result	= new JObject;
	
		$assetName = 'com_jifile';
	
		$actions = JAccess::getActions('com_jifile', 'component');
	
		foreach ($actions as $action) {
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}
	
		return $result;
	}
	
	static function checkVersion($install = 1) {
		$host = parse_url( JURI::root( false ) );
		$host = strtolower( $host['host'] );
		
		$jver = JVERSION;
		$jifilever = JIFILEVER;
		
		$config = new JConfig();
		
		$key = md5($config->secret);
		
		$url = 'http://www.isapp.it/jifile/checkVer.php?host='.$host.'&jver='.$jver.'&key='.$key.'&jifilever='.$jifilever.'&install='.$install;
		
		$data = '';
		if ( function_exists( 'curl_init' ) ) {
			$curl_handle = curl_init();
		
			$options = array
			(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 3,
			CURLOPT_USERAGENT => "some crazy browser"
			);
			@curl_setopt_array( $curl_handle,$options );
		
			$data = curl_exec( $curl_handle );
			curl_close( $curl_handle );
		} else {
			$file = @fopen( $url, 'r' );
			if ( $file ) {
				$data = array();
				while ( !feof( $file ) ) {
					$data[] = fgets( $file, 1024 );
				}
				$data = implode( '', $data );
			}
		}
		
		return $data;
	}
	
	/*
	 * $addparams: array('server_bit'=>$server_bit)
	 */
	static function saveParams($addparams) {
		
		$table =& JTable::getInstance('extension');
		$params = & JComponentHelper::getParams('com_jifile');
		$params->loadArray($addparams);
		
		$row['extension_id'] = $table->find(array('element'=>'com_jifile'));
		$row['params'] = $params->toArray(); 
		
		$table->bind($row);
		$table->store();
	}
	
	static function getSysInfo() {
		require_once(JPATH_SITE.'/administrator/components/com_admin/models/sysinfo.php');
		require_once(JPATH_SITE.'/administrator/components/com_jifile/models/config.php');
		require_once(JPATH_SITE.'/administrator/components/com_jifile/views/frontpage/view.html.php');
		require_once(JPATH_SITE.'/administrator/components/com_jifile/tables/addon.php');
		require_once(JPATH_IFILE_LIBRARY.'/ifile/IFileVersion.php');
		
		$arrayInfo = array();
		
		try {
			$params = & JComponentHelper::getParams('com_jifile');
			$arrayInfo['jiFileParams'] 	= $params->toArray();
			$arrayInfo['jiFileParams']['jifile_ver'] = JIFILEVER;
			
			$jifileConfig = new JifileModelConfig();
			$arrayInfo['ifileConfig'] = $jifileConfig->getConfig()->getConfig();
			$arrayInfo['ifileConfig']['ifile_ver'] = IFileVersion::VERSION;
			
			//report Check
			$reportCheckObj = new JifileViewFrontpage();
			$reportCheck = array();
			foreach($reportCheckObj->getReportCheck() as $caption => $check) {
				$caption = strtolower($caption);
				$reportCheck[$caption] = array();
			
				foreach($check as $key => $obj){
					$reportCheck[$caption][$key]['check'] = $obj->getCheck();
					$reportCheck[$caption][$key]['message'] = $obj->getMessage();
					$reportCheck[$caption][$key]['info'] = $obj->getInfo();
				}
			}
			$arrayInfo['reportCheck'] = $reportCheck;
			
			//addon
			$addon = new JifileTableAddon(JFactory::getDBO());
			$arrayInfo['addons'] = $addon->find(array(), '*', 'addon');
			
			//joomla
			$sysinfo = new AdminModelSysInfo();
			
			$arrayInfo['info'] 		= $sysinfo->getInfo();
			$arrayInfo['phpSet'] 	= $sysinfo->getPhpSettings();
			//$arrayInfo['phpInfo'] 	= array('html' => $sysinfo->getPHPInfo());
			
			return json_encode($arrayInfo);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public static function encodingCharset($text) {
		$params = JComponentHelper::getParams( 'com_jifile' );
		$encoding_so = $params->get( 'encoding_so' );
	
		if (!empty($encoding_so) && strtolower($encoding_so) != strtolower(JFactory::getDocument()->getCharset())) {
			$text = iconv($encoding_so, "UTF-8//TRANSLIT", $text);
		}
	
		return $text;
	}
	
	public static function getFirma() {
		$firma = '<div id="firma"><a href="http://jifile.isapp.it" target="_blank">JiFile '.JIFILEVER.' - isApp.it</a></div>';
		return $firma;
	}
	
	public static function parseXMLInstallFile($path)
	{
		JLog::add('JApplicationHelper::parseXMLInstallFile is deprecated. Use JInstaller::parseXMLInstallFile instead.', JLog::WARNING, 'deprecated');
	
		// Read the file to see if it's a valid component XML file
		if (!$xml = JFactory::getXML($path))
		{
			return false;
		}
	
		// Check for a valid XML root tag.
	
		// Should be 'install', but for backward compatibility we will accept 'extension'.
		// Languages use 'metafile' instead
	
		if ($xml->getName() != 'addon')
		{
			unset($xml);
			return false;
		}
	
		$data = array();
	
		$data['name'] = (string) $xml->name;
	
		// Check if we're a language. If so use metafile.
		$data['type'] = $xml->getName() == 'metafile' ? 'language' : (string) $xml->attributes()->type;
	
		$data['creationDate'] = ((string) $xml->creationDate) ? (string) $xml->creationDate : JText::_('Unknown');
		$data['author'] = ((string) $xml->author) ? (string) $xml->author : JText::_('Unknown');
	
		$data['copyright'] = (string) $xml->copyright;
		$data['authorEmail'] = (string) $xml->authorEmail;
		$data['authorUrl'] = (string) $xml->authorUrl;
		$data['version'] = (string) $xml->version;
		$data['description'] = (string) $xml->description;
	
		return $data;
	}
	
	public static function isControllerAddon() {
		
		$addonPath = JIFILE_ADDON_PATH;
		$basePath = JPATH_COMPONENT;
		$format = JRequest::getWord('format');
		$command = JRequest::getVar('task');
		
		// Check for array format.
		$filter = JFilterInput::getInstance();
		
		if (is_array($command))
		{
			$command = $filter->clean(array_pop(array_keys($command)), 'cmd');
		}
		else
		{
			$command = $filter->clean($command, 'cmd');
		}
		
		// Check for a controller.task command.
		if (strpos($command, '.') !== false)
		{
			// Explode the controller.task command.
			list ($type, $task) = explode('.', $command);
		
			// Define the controller filename and path.
			$file = jifilehelper::createFileName('controller', array('name' => $type, 'format' => $format));
			$path = $basePath . '/controllers/' . $file;
			
			if (!file_exists($path)) {
				$file = jifilehelper::createFileName('controller', array('name' => $type, 'format' => $format));
				$path = $addonPath.'/'.$type.'/controllers/'.$file;
				
				if (file_exists($path)) {
					return $type;
				}
			}
		
			// Reset the task without the controller context.
			//JRequest::setVar('task', $task);
		}
		return false;
	}
	
	public static function createFileName($type, $parts = array())
	{
		$filename = '';
	
		switch ($type)
		{
			case 'controller':
				if (!empty($parts['format']))
				{
					if ($parts['format'] == 'html')
					{
						$parts['format'] = '';
					}
					else
					{
						$parts['format'] = '.' . $parts['format'];
					}
				}
				else
				{
					$parts['format'] = '';
				}
	
				$filename = strtolower($parts['name']) . $parts['format'] . '.php';
				break;
	
			case 'view':
				if (!empty($parts['type']))
				{
					$parts['type'] = '.' . $parts['type'];
				}
	
				$filename = strtolower($parts['name']) . '/view' . $parts['type'] . '.php';
				break;
		}
	
		return $filename;
	}
	
	/**
	 * Get Lucene Plugins Instance
	 * Return registry whit instance of Lucene Plugins
	 * @return array
	 */
	public static function getPluginLuceneInstance() {
		$registry = array();
		
		require_once(JPATH_ADMINISTRATOR.'/components/com_jifile/helpers/interface/jifilepluginfactory.php');
		
		$filter = array();
		$filter['context'] = array('type' => 's', 'value' => 'admin');
		$filter['published'] = array('type' => 'i', 'value' => 1);
		$filter['type'] = array('type' => 'i', 'value' => 2);
		$filter['plugin'] = array('type' => 's', 'value' => 'lucene');
		$order = array();
		$order['ordering'] = 'asc';
		$tableAddon = JTable::getInstance('Addon', 'JifileTable');
		$plugins = $tableAddon->getAddon($filter, $order);
		$jifilefactory = JiFilePluginFactory::getInstance(); 
		
		
		// create instance of the Lucene Plugins 
		if (!empty($plugins)) {
			
			foreach ($plugins as $plugin) {				
				//$registry[] =& $jifilefactory->getLucenePlugin($plugin);
				$registry[] = $jifilefactory->getJifileAddon($plugin);
			}	
		} 
		
		return $registry;
		
	}
	
	/**
	 * Add Languages from Addon
	 * @return void
	 */
	public static function addLanguages() {
		// set addons
		$addons = array();
		
		// get all addon not core		
		$filter['core'] = array('type' => 'i', 'value' => '0');
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_jifile/tables/');
		$tableAddon = JTable::getInstance('Addon', 'JifileTable');
		$addons = $tableAddon->getAddon($filter);
		
		if (!empty($addons)) {
			//load language addon
			jimport('joomla.client.helper');
			
			$lang_codes = JLanguageHelper::getLanguages('lang_code');
			if (isset($lang_codes[JFactory::getLanguage()->getTag()])) {
				$lang_code 	= $lang_codes[JFactory::getLanguage()->getTag()]->lang_code;
			} else {
				$lang_code = 'en-GB';
			}
				
			$lang = JFactory::getLanguage();
			
			foreach ($addons as $addon) {
				
				list($context, $name) = explode(".", $addon['addon']);
				
				if ($addon['context'] == 'plugin') {
					$plugin = $addon['plugin'];
					$basePath = JIFILE_ADDON_PLUGIN_PATH.'/'.$plugin.'/'.$name;
					$filename = 'plg_jifile_'.$name;
				} else {
					$basePath = JIFILE_ADDON_PATH.'/'.$name;
					$filename = 'addon_jifile_'.$name; 
				}
				$lang->load($filename, $basePath, $lang_code);
			}	
		}
	}
	
	public static function setDebug($state = 0) {
		JFactory::getSession()->set('JIFILE_DEBUG', $state);
	}
	public static function inDebug() {
		return JFactory::getSession()->get('JIFILE_DEBUG', 0);
	}
}