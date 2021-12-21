<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );

class JifileViewManualindex extends JViewLegacy {
	
	function display($tpl = null) {
		$tpl = JRequest::getVar('tmpl', null);
		
		$model = $this->getModel();
		$params = JComponentHelper::getParams( 'com_jifile' );
		$basepath = jifilehelper::getCorrectPath($params->get( 'base_path' ));
		$i = JRequest::getVar('id');
		
		$filename = JRequest::getVar('filename', null);
		// initialize fields
		$fields = array('title'=>'', 'subject'=>'', 'description'=>'', 'creator'=>'', 'keywords'=>'', 'created'=>'', 'modified'=>'', 'GPSLatitudeGoogleDecimal' => '', 'GPSLongitudeGoogleDecimal' => '', 'body'=>'');
		// get info document
		$ifiledoc = $model->getLuceneDoc($filename, true, false);
		$error = (!$ifiledoc) ? $model->getError() : false;
		$ext = strtolower(JFile::getExt($filename));
		
		// define template and fields for type of file
		switch ($ext) {
			case "bmp":
			case "gif":
			case "ico":
			case "jpg":				
			case "png":
			case "psd":
			case "svg":
			case "tif":
			case "tiff":
				$this->createImagesFields($ifiledoc);
				$tpl = "component_images";
				break;								
			case "mp3":
				$this->createMultimediaFields($ifiledoc);
				$tpl = "component_multimedia";
				break;
			// @TODO
			// integrazione di altri formati multimediali
			// in caso venga utilizzata la libreria getID3
			default:
				break;
		}
		
		// get file time
		$datec = array();
		$datec['created']  = @filectime($filename);
		$datec['modified'] = @filemtime($filename);				
		$datec['created']  = (!empty($datec['created'])) ? JHTML::_('date', $datec['created'], 'Y-m-d H:i:s') : '';
		$datec['modified'] = (!empty($datec['modified'])) ? JHTML::_('date', $datec['modified'], 'Y-m-d H:i:s') : '';
		// format date
		$this->formatFieldDate($ifiledoc, $datec);
		
		$ifiledoc = array_merge($fields, (empty($ifiledoc) ? array() : $ifiledoc));
		
		$config = IFileConfig::getInstance();
		$bodyField = $config->getDocumentField('body');
		$encoding = (empty($bodyField['encoding'])) ? '' : $bodyField['encoding'];
		
		$this->assign('extension', $ext);
		$this->assign('basepath', $basepath);
		$this->assign('encoding', $encoding);
		$this->assign('filename', $filename);
		$this->assign('i', $i);
		$this->assign('date', $datec);
		$this->assignRef('ifiledoc', $ifiledoc);
		$this->assignRef('error', $error);
		
		unset($ifiledoc);
		
		if(!is_null($tpl)) {
			$doc = JFactory::getDocument();
			jifilehelper::addJQuery(array('colorbox'));
			if(file_exists('templates/bluestork/css/rounded.css')) {
				$doc->addStyleSheet( 'templates/bluestork/css/rounded.css' );
			}
			$doc->addStyleSheet( '../administrator/components/com_jifile/css/ifile.css?'.JIFILEVER );
			$doc->addScript( '../administrator/components/com_jifile/js/manualindex.js?'.JIFILEVER );
		}
		parent::display($tpl);
	}
	
	/**
	 * Format the date for display "Created" and "Modified" date
	 * @param string $dateField
	 * 
	 * @return string date format
	 */
	public function formatFieldDate(&$ifiledoc, $datec) {
		
		if (isset($ifiledoc['created']) && !empty($ifiledoc['created'])) {
			// @TODO 
			// si dovrebbe recuperare dalla configurazione il formato della data
			// in caso di formattazioni della data non conforme alla lingua 
		} else {
			$ifiledoc['created'] = $datec['created'];
		}
		
		if (isset($ifiledoc['modified']) && !empty($ifiledoc['modified'])) {
			// @TODO
			// si dovrebbe recuperare dalla configurazione il formato della data
			// in caso di formattazioni della data non conforme alla lingua
		} else {
			$ifiledoc['modified'] = $datec['modified'];
		}
	}
	
	/**
	 * Set Multimedia elements for Template
	 * @param object $ifiledoc
	 * @return void
	 */
	public function createMultimediaFields(&$ifiledoc) {
		
		$fieldsMultimedia = array(
							'created' => '',
							'modified' => '',
							'encodedBy'=>'',
							'track'=>'',
							'publisher'=>'',
							'partOfASet'=>'',
							'bpm'=>'',
							'originalArtist'=>'',
							'copyright'=>'',
							'band'=>'',
							'genre'=>'',
							'composer'=>'',
							'year'=>'',
							'title'=>'',
							'album'=>'',
							'artist'=>'',
							'GPSLatitudeGoogleDecimal' => '',
							'GPSLongitudeGoogleDecimal' => '');
		
		$default 	= (isset($ifiledoc['genre']) ? $ifiledoc['genre'] : "" );
		// @TODO
		// Integrazione della libreria getID3
		// verifica se esiste la funzione per il recupero dei generi
		if (function_exists('id3_get_genre_list')) {
			$genreList 	= id3_get_genre_list();	
		} else {
			$genreList = $this->id3_get_genre_list();
		}
		
		$listGenre 	= array();
		$listGenre[]= JHTML::_('select.option',  '', '' );
		
		foreach ($genreList as $genreLabel) {
			$listGenre[] = 	JHTML::_('select.option',  $genreLabel, $genreLabel );
		} 
		$this->assign('genre', JHTML::_('select.genericlist',  $listGenre, 'fields[genre]', '', 'value', 'text', $default));
		
		$ifiledoc = array_merge($fieldsMultimedia, (empty($ifiledoc) ? array() : $ifiledoc));
	}
	
	/**
	 * Set Image elements for Template
	 * @param object $ifiledoc
	 * @return void
	 */
	public function createImagesFields(&$ifiledoc) {
		
		$fieldsImage = array('ImageDescription'=>'', 'created'=>'', 'modified'=>'', 'FileSize'=>'', 'Height'=>'', 'Width'=>'', 'Make'=>'', 'Model'=>'', 
							 'Software'=>'', 'Copyright'=>'', 'GPSLatitudeGoogleDecimal'=>'', 'GPSLongitudeGoogleDecimal'=>'', 'XResolution'=>'', 'YResolution'=>'', 
							 'ExposureTime'=>'', 'ApertureFNumber'=>'', 'UserComment'=>'', 'GPSLatitude'=>'', 'GPSLongitude'=>'', 'GPSLatitudeGoogle'=>'', 
							 'GPSLongitudeGoogle'=>'');
		
		$default = (isset($ifiledoc['Orientation']) ? $ifiledoc['Orientation'] : "" );
		$listOrientation[] 	= JHTML::_('select.option',  '', '' );
		$listOrientation[] 	= JHTML::_('select.option',  'left', 'left' );
		$listOrientation[] 	= JHTML::_('select.option',  'right', 'right' );
		$listOrientation[] 	= JHTML::_('select.option',  'top', 'top' );
		$listOrientation[] 	= JHTML::_('select.option',  'bottom', 'bottom' );
		$listOrientation[] 	= JHTML::_('select.option',  'reserved', 'reserved' );
		$this->assign('orientation', JHTML::_('select.genericlist',  $listOrientation, 'fields[Orientation]', '', 'value', 'text', $default));
		
		$default = (isset($ifiledoc['ExposureMode']) ? $ifiledoc['ExposureMode'] : "" );
		$listExposeMode[] 	= JHTML::_('select.option',  '', '' );
		$listExposeMode[] 	= JHTML::_('select.option',  'Auto exposure', 'Auto exposure' );
		$listExposeMode[] 	= JHTML::_('select.option',  'Manual exposure', 'Manual exposure' );
		$listExposeMode[] 	= JHTML::_('select.option',  'Auto bracket', 'Auto bracket' );
		$listExposeMode[] 	= JHTML::_('select.option',  'None', 'None' );
		$listExposeMode[] 	= JHTML::_('select.option',  'reserved', 'reserved' );
		$this->assign('exposureMode', JHTML::_('select.genericlist',  $listExposeMode, 'fields[ExposureMode]', '', 'value', 'text', $default));
		
		$default = (isset($ifiledoc['SceneCaptureType']) ? $ifiledoc['SceneCaptureType'] : "" );
		$listSceneCaptureType[] 	= JHTML::_('select.option',  '', '' );
		$listSceneCaptureType[] 	= JHTML::_('select.option',  'Standard', 'Standard' );
		$listSceneCaptureType[] 	= JHTML::_('select.option',  'Landscape', 'Landscape' );
		$listSceneCaptureType[] 	= JHTML::_('select.option',  'Portrait', 'Portrait' );
		$listSceneCaptureType[] 	= JHTML::_('select.option',  'Night scene', 'Night scene' );
		$listSceneCaptureType[] 	= JHTML::_('select.option',  'reserved', 'reserved' );
		$this->assign('sceneCaptureType', JHTML::_('select.genericlist',  $listSceneCaptureType, 'fields[SceneCaptureType]', '', 'value', 'text', $default));
		
		$default = (isset($ifiledoc['LightSource']) ? $ifiledoc['LightSource'] : "" );
		$listLightSource[] 	= JHTML::_('select.option',  '', '' );
		$listLightSource[] 	= JHTML::_('select.option',  'unknown', 'unknown' );
		$listLightSource[] 	= JHTML::_('select.option',  'Daylight', 'Daylight' );
		$listLightSource[] 	= JHTML::_('select.option',  'Fluorescent', 'Fluorescent' );
		$listLightSource[] 	= JHTML::_('select.option',  'Tungsten (incandescent light)', 'Tungsten (incandescent light)' );
		$listLightSource[] 	= JHTML::_('select.option',  'Flash', 'Flash' );
		$listLightSource[] 	= JHTML::_('select.option',  'Fine weather', 'Fine weather' );
		$listLightSource[] 	= JHTML::_('select.option',  'Cloudy weather', 'Cloudy weather' );
		$listLightSource[] 	= JHTML::_('select.option',  'Shade', 'Shade' );
		$listLightSource[] 	= JHTML::_('select.option',  'Daylight fluorescent (D 5700 - 7100K)', 'Daylight fluorescent (D 5700 - 7100K)' );
		$listLightSource[] 	= JHTML::_('select.option',  'Day white fluorescent (N 4600 - 5400K)', 'Day white fluorescent (N 4600 - 5400K)' );
		$listLightSource[] 	= JHTML::_('select.option',  'Cool white fluorescent (W 3900 - 4500K)', 'Cool white fluorescent (W 3900 - 4500K)' );
		$listLightSource[] 	= JHTML::_('select.option',  'White fluorescent (WW 3200 - 3700K)', 'White fluorescent (WW 3200 - 3700K)' );
		$listLightSource[] 	= JHTML::_('select.option',  'Standard light A', 'Standard light A' );
		$listLightSource[] 	= JHTML::_('select.option',  'Standard light B', 'Standard light B' );
		$listLightSource[] 	= JHTML::_('select.option',  'Standard light C', 'Standard light C' );
		$listLightSource[] 	= JHTML::_('select.option',  'D55', 'D55' );
		$listLightSource[] 	= JHTML::_('select.option',  'D65', 'D65' );
		$listLightSource[] 	= JHTML::_('select.option',  'D75', 'D75' );
		$listLightSource[] 	= JHTML::_('select.option',  'D50', 'D50' );
		$listLightSource[] 	= JHTML::_('select.option',  'ISO studio tungsten', 'ISO studio tungsten' );
		$listLightSource[] 	= JHTML::_('select.option',  'Other light source', 'Other light source' );
		$listLightSource[] 	= JHTML::_('select.option',  'reserved', 'reserved' );
		$this->assign('lightSource', JHTML::_('select.genericlist',  $listLightSource, 'fields[LightSource]', '', 'value', 'text', $default));
		
		$default = (isset($ifiledoc['IsColor']) ? $ifiledoc['IsColor'] : "" );
		$listIsColor[] 	= JHTML::_('select.option',  '0', '0' );
		$listIsColor[] 	= JHTML::_('select.option',  '1', '1' );
		$this->assign('isColor', JHTML::_('select.genericlist',  $listIsColor, 'fields[IsColor]', '', 'value', 'text', $default));
		
		$ifiledoc = array_merge($fieldsImage, (empty($ifiledoc) ? array() : $ifiledoc));
	}
	
	/**
	 * Return array to genre type 
	 * @return array
	 */
	public function id3_get_genre_list() {
		
		$genre = array();
		$genre[0] = "Blues";
	    $genre[1] = "Classic Rock";
	    $genre[2] = "Country";
	    $genre[3] = "Dance";
	    $genre[4] = "Disco";
	    $genre[5] = "Funk";
	    $genre[6] = "Grunge";
	    $genre[7] = "Hip-Hop";
	    $genre[8] = "Jazz";
	    $genre[9] = "Metal";
	    $genre[10] = "New Age";
	    $genre[11] = "Oldies";
	    $genre[12] = "Other";
	    $genre[13] = "Pop";
	    $genre[14] = "R&B";
	    $genre[15] = "Rap";
	    $genre[16] = "Reggae";
	    $genre[17] = "Rock";
	    $genre[18] = "Techno";
	    $genre[19] = "Industrial";
	    $genre[20] = "Alternative";
	    $genre[21] = "Ska";
	    $genre[22] = "Death Metal";
	    $genre[23] = "Pranks";
	    $genre[24] = "Soundtrack";
	    $genre[25] = "Euro-Techno";
	    $genre[26] = "Ambient";
	    $genre[27] = "Trip-Hop";
	    $genre[28] = "Vocal";
	    $genre[29] = "Jazz+Funk";
	    $genre[30] = "Fusion";
	    $genre[31] = "Trance";
	    $genre[32] = "Classical";
	    $genre[33] = "Instrumental";
	    $genre[34] = "Acid";
	    $genre[35] = "House";
	    $genre[36] = "Game";
	    $genre[37] = "Sound Clip";
	    $genre[38] = "Gospel";
	    $genre[39] = "Noise";
	    $genre[40] = "Alternative Rock";
	    $genre[41] = "Bass";
	    $genre[42] = "Soul";
	    $genre[43] = "Punk";
	    $genre[44] = "Space";
	    $genre[45] = "Meditative";
	    $genre[46] = "Instrumental Pop";
	    $genre[47] = "Instrumental Rock";
	    $genre[48] = "Ethnic";
	    $genre[49] = "Gothic";
	    $genre[50] = "Darkwave";
	    $genre[51] = "Techno-Industrial";
	    $genre[52] = "Electronic";
	    $genre[53] = "Pop-Folk";
	    $genre[54] = "Eurodance";
	    $genre[55] = "Dream";
	    $genre[56] = "Southern Rock";
	    $genre[57] = "Comedy";
	    $genre[58] = "Cult";
	    $genre[59] = "Gangsta";
	    $genre[60] = "Top 40";
	    $genre[61] = "Christian Rap";
	    $genre[62] = "Pop/Funk";
	    $genre[63] = "Jungle";
	    $genre[64] = "Native US";
	    $genre[65] = "Cabaret";
	    $genre[66] = "New Wave";
	    $genre[67] = "Psychadelic";
	    $genre[68] = "Rave";
	    $genre[69] = "Showtunes";
	    $genre[70] = "Trailer";
	    $genre[71] = "Lo-Fi";
	    $genre[72] = "Tribal";
	    $genre[73] = "Acid Punk";
	    $genre[74] = "Acid Jazz";
	    $genre[75] = "Polka";
	    $genre[76] = "Retro";
	    $genre[77] = "Musical";
	    $genre[78] = "Rock & Roll";
	    $genre[79] = "Hard Rock";
	    $genre[80] = "Folk";
	    $genre[81] = "Folk-Rock";
	    $genre[82] = "National Folk";
	    $genre[83] = "Swing";
	    $genre[84] = "Fast Fusion";
	    $genre[85] = "Bebob";
	    $genre[86] = "Latin";
	    $genre[87] = "Revival";
	    $genre[88] = "Celtic";
	    $genre[89] = "Bluegrass";
	    $genre[90] = "Avantgarde";
	    $genre[91] = "Gothic Rock";
	    $genre[92] = "Progressive Rock";
	    $genre[93] = "Psychedelic Rock";
	    $genre[94] = "Symphonic Rock";
	    $genre[95] = "Slow Rock";
	    $genre[96] = "Big Band";
	    $genre[97] = "Chorus";
	    $genre[98] = "Easy Listening";
	    $genre[99] = "Acoustic";
	    $genre[100] = "Humour";
	    $genre[101] = "Speech";
	    $genre[102] = "Chanson";
	    $genre[103] = "Opera";
	    $genre[104] = "Chamber Music";
	    $genre[105] = "Sonata";
	    $genre[106] = "Symphony";
	    $genre[107] = "Booty Bass";
	    $genre[108] = "Primus";
	    $genre[109] = "Porn Groove";
	    $genre[110] = "Satire";
	    $genre[111] = "Slow Jam";
	    $genre[112] = "Club";
	    $genre[113] = "Tango";
	    $genre[114] = "Samba";
	    $genre[115] = "Folklore";
	    $genre[116] = "Ballad";
	    $genre[117] = "Power Ballad";
	    $genre[118] = "Rhytmic Soul";
	    $genre[119] = "Freestyle";
	    $genre[120] = "Duet";
	    $genre[121] = "Punk Rock";
	    $genre[122] = "Drum Solo";
	    $genre[123] = "Acapella";
	    $genre[124] = "Euro-House";
	    $genre[125] = "Dance Hall";
	    $genre[126] = "Goa";
	    $genre[127] = "Drum & Bass";
	    $genre[128] = "Club-House";
	    $genre[129] = "Hardcore";
	    $genre[130] = "Terror";
	    $genre[131] = "Indie";
	    $genre[132] = "BritPop";
	    $genre[133] = "Negerpunk";
	    $genre[134] = "Polsk Punk";
	    $genre[135] = "Beat";
	    $genre[136] = "Christian Gangsta";
	    $genre[137] = "Heavy Metal";
	    $genre[138] = "Black Metal";
	    $genre[139] = "Crossover";
	    $genre[140] = "Contemporary C";
	    $genre[141] = "Christian Rock";
	    $genre[142] = "Merengue";
	    $genre[143] = "Salsa";
	    $genre[144] = "Thrash Metal";
	    $genre[145] = "Anime";
	    $genre[146] = "JPop";
	    $genre[147] = "SynthPop";
		
		return $genre; 

	}
}
