<?php
/**
 * IFile Framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter/helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright 
 * @license
 * @version    1.0 class.imagetxt.php 2011-09-16 19:17:24
 */

//require_once 'simplehtmldom/simple_html_dom.php';

/**
 * Recupera le informazioni di un file Immagine  
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter/helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license
 */
class Image2Txt {
	
	/**
	 * Tutto il contenuto del TAG
	 * 
	 * @var string
	 */
	private $textTag;
	/**
	 * Dimensione dfel file in byte
	 * 
	 * @var integer
	 */
	private $fileSize;
	/**
	 * Altezza in pixel dell'immagine
	 * 
	 * @var integer
	 */
	private $height;
	/**
	 * Larghezza in pixel dell'immagine
	 * 
	 * @var integer
	 */
	private $width;
	/**
	 * Immagini a colori
	 * 
	 * @var integer
	 */
	private $isColor;
	/**
	 * Apertura dell'obiettivo
	 * 
	 * @var string
	 */
	private $apertureFNumber;
	/**
	 * Commenti
	 * 
	 * @var string
	 */
	private $userComment;
	/**
	 * Descrizione dell'immagine
	 * 
	 * @var string
	 */
	private $imageDescription;
	/**
	 * Orientamento dell'immagine
	 * 
	 * @var integer
	 */
	private $orientation;
	/**
	 * Macchina
	 * 
	 * @var string
	 */
	private $make;
	/**
	 * Modello della macchina
	 * 
	 * @var string
	 */
	private $model;
	/**
	 * Software che ha creato l'immagine
	 * 
	 * @var string
	 */
	private $software;
	/**
	 * Copyright sull'immagine
	 * 
	 * @var string
	 */
	private $copyright;
	/**
	 * Latitudine salvata dal dispositivo
	 * 
	 * @var string
	 */
	private $GPSLatitude;
	
	/**
	 * Punto cardinale per la latitudine
	 * 
	 * @var string
	 */
	private $GPSLatitudeRef;
	/**
	 * Longitudine salvata dal dispositivo
	 * 
	 * @var string
	 */
	private $GPSLongitude;
	/**
	 * Punto cardinale per la longitudine
	 * 
	 * @var string
	 */
	private $GPSLongitudeRef;
	/**
	 * Risoluzione per la coordinata X
	 * 
	 * @var string
	 */
	private $XResolution;
	/**
	 * Risoluzione per la coordinata Y
	 * 
	 * @var string
	 */
	private $YResolution;
	/**
	 * Risoluzione per la coordinata Y
	 * 
	 * @var string
	 */
	private $dateTime;
	/**
	 * Modalita' di esposizione
	 * 
	 * @var string
	 */
	private $exposureMode;
	/**
	 * Tempo di esposizione
	 * 
	 * @var string
	 */
	private $exposureTime;
	/**
	 * Scena di cattura
	 * 
	 * @var string
	 */
	private $sceneCaptureType;
	/**
	 * Risorsa di luce
	 * 
	 * @var string
	 */
	private $lightSource;	
	
	
	
	/** 
	 * Setta la risorsa di luce
	 *  
	 * @param string $lightSource
	 * @return void
	 */
	public function setLightSource($lightSource) { $this->lightSource = $lightSource; }
	/** 
	 * Setta la scena di cattura
	 *  
	 * @param string $sceneCaptureType
	 * @return void
	 */
	public function setSceneCaptureType($sceneCaptureType) { $this->sceneCaptureType = $sceneCaptureType; }
	/** 
	 * Setta il tempo di esposizione
	 *  
	 * @param string $exposureTime
	 * @return void
	 */
	public function setExposureTime($exposureTime) { $this->exposureTime = $exposureTime; }
	/** 
	 * Setta la modalita' di esposizione
	 *  
	 * @param string $exposureMode
	 * @return void
	 */
	public function setExposureMode($exposureMode) { $this->exposureMode = $exposureMode; }
	/** 
	 * Setta la data di creazione dell'immagine nel formato yyyy:mm:gg hh:mm:ss
	 *  
	 * @param string $dateTime
	 * @return void
	 */
	public function setDateTime($dateTime) { $this->dateTime = $dateTime; }
	/** 
	 * Setta la risoluzione Y dell'immagine
	 * 
	 * @param string $YResolution
	 * @return void
	 */
	public function setYResolution($YResolution) { $this->YResolution = $YResolution; }
	/** 
	 * Setta la risoluzione X dell'immagine
	 * 
	 * @param string $XResolution
	 * @return void
	 */
	public function setXResolution($XResolution) { $this->XResolution = $XResolution; }
	/** 
	 * Setta il punto cardinale della longitudine
	 * 
	 * @param string $GPSLatitudeRef
	 * @return void
	 */
	public function setGPSLongitudeRef($GPSLongitudeRef) { $this->GPSLongitudeRef = $GPSLongitudeRef; }
	/** 
	 * Setta le coordinate della longitudine
	 * 
	 * @param string $GPSLatitude
	 * @return void
	 */
	public function setGPSLongitude($GPSLongitude) { $this->GPSLongitude = $GPSLongitude; }
	/** 
	 * Setta il punto cardinale della latitudine
	 * 
	 * @param string $GPSLatitudeRef
	 * @return void
	 */
	public function setGPSLatitudeRef($GPSLatitudeRef) { $this->GPSLatitudeRef = $GPSLatitudeRef; }
	/** 
	 * Setta le coordinate della latitudine
	 * 
	 * @param string $GPSLatitude
	 * @return void
	 */
	public function setGPSLatitude($GPSLatitude) { $this->GPSLatitude = $GPSLatitude; }
	/** 
	 * Setta il copyright dell'immagine
	 * 
	 * @param string $copyright
	 * @return void
	 */
	public function setCopyright($copyright) { $this->copyright = $copyright; }
	/** 
	 * Setta il software che ha creato l'immagine
	 * 
	 * @param string $software
	 * @return void
	 */
	public function setSoftware($software) { $this->software = $software; }
	/** 
	 * Setta il modello della macchina utilizzata per l'immagine
	 * 
	 * @param string $model
	 * @return void
	 */
	public function setModel($model) { $this->model = $model; }
	/** 
	 * Setta la macchina utilizzata per la foto
	 * 
	 * @param string $make
	 * @return void
	 */
	public function setMake($make) { $this->make = $make; }
	/** 
	 * Setta l'orientamento della pagina
	 * 
	 * @param integer $orientation
	 * @return void
	 */
	public function setOrientation($orientation) { $this->orientation = $orientation; }
	/** 
	 * Setta la descrizione dell'immagine
	 * 
	 * @param string $imageDescription
	 * @return void
	 */
	public function setImageDescription($imageDescription) { $this->imageDescription = $imageDescription; }
	/** 
	 * Setta il commento dell'utente
	 * 
	 * @param string $userComment
	 * @return void
	 */
	public function setUserComment($userComment) { $this->userComment = $userComment; }
	/** 
	 * Setta l'apertura dell'obiettivo
	 * 
	 * @param string $apertureFNumber
	 * @return void
	 */
	public function setApertureFNumber($apertureFNumber) { $this->apertureFNumber = $apertureFNumber; }
	/** 
	 * Setta se l'immagine e' a colori
	 * 
	 * @param mixed $isColor
	 * @return void
	 */
	public function setIsColor($isColor) { $this->isColor = $isColor; }
	/** 
	 * Setta la larghezza dell'immagine
	 * 
	 * @param integer $width
	 * @return void
	 */
	public function setWidth($width) { $this->width = $width; }
	/** 
	 * Setta l'altezza dell'immagine
	 * 
	 * @param integer $height
	 * @return void
	 */
	public function setHeight($height) { $this->height = $height; }
	/** 
	 * Setta la dimensione del file in byte 
	 * 
	 * @param integer $fileSize
	 * @return void
	 */
	public function setFileSize($filesize) { $this->fileSize = $filesize; }
	/** 
	 * Setta il contenuto del TAG in formato testuale 
	 * 
	 * @return string
	 */
	public function setTextTag($textTag) { $this->textTag = $textTag; }
	
	
	
	/** 
	 * Ritorna la risorsa di luce
	 *  
	 * @return string
	 */
	public function getLightSource() { 
		$lightSource = '';
		
		switch ($this->lightSource) {
			case '0':
				$lightSource = 'unknown';
				break;
			case '1':
				$lightSource = 'Daylight';
				break;
			case '2':
				$lightSource = 'Fluorescent';
				break;
			case '3':
				$lightSource = 'Tungsten (incandescent light)';
				break;
			case '4':
				$lightSource = 'Flash';
				break;
			case '9':
				$lightSource = 'Fine weather';
				break;
			case '10':
				$lightSource = 'Cloudy weather';
				break;
			case '11':
				$lightSource = 'Shade';
				break;
			case '12':
				$lightSource = 'Daylight fluorescent (D 5700 – 7100K)';
				break;
			case '13':
				$lightSource = 'Day white fluorescent (N 4600 – 5400K)';
				break;
			case '14':
				$lightSource = 'Cool white fluorescent (W 3900 – 4500K)';
				break;
			case '15':
				$lightSource = 'White fluorescent (WW 3200 – 3700K)';
				break;
			case '17':
				$lightSource = 'Standard light A';
				break;
			case '18':
				$lightSource = 'Standard light B';
				break;
			case '19':
				$lightSource = 'Standard light C';
				break;
			case '20':
				$lightSource = 'D55';
				break;
			case '21':
				$lightSource = 'D65';
				break;
			case '22':
				$lightSource = 'D75';
				break;
			case '23':
				$lightSource = 'D50';
				break;
			case '24':
				$lightSource = 'ISO studio tungsten';
				break;
			case '255':
				$lightSource = 'Other light source';
				break;
			default:
				$lightSource = 'reserved';
				break;
		}
		
		return $lightSource; 
	}
	/** 
	 * Ritorna la scena di cattura
	 *  
	 * @return string
	 */
	public function getSceneCaptureType() { 
		$sceneCaptureType = '';
		
		switch ($this->sceneCaptureType) {
			case '0':
				$sceneCaptureType = 'Standard';
				break;
			case '1':
				$sceneCaptureType = 'Landscape';
				break;
			case '2':
				$sceneCaptureType = 'Portrait';
				break;
			case '3':
				$sceneCaptureType = 'Night scene';
				break;
			default:
				$sceneCaptureType = 'reserved';
				break;
		}
		
		return $sceneCaptureType; 
	}
	/** 
	 * Ritorna il tempo di esposizione
	 *  
	 * @return string
	 */
	public function getExposureTime() { return $this->exposureTime; }
	/** 
	 * Riotrna la modalita' di esposizione
	 *  
	 * @return string
	 */
	public function getExposureMode() { 
		$exposureMode = '';
		
		switch ($this->exposureMode) {
			case '0':
				$exposureMode = 'Auto exposure';
				break;
			case '1':
				$exposureMode = 'Manual exposure';
				break;
			case '2':
				$exposureMode = 'Auto bracket';
				break;
			case 'none':
				$exposureMode = 'None';
				break;
			default:
				$exposureMode = 'reserved';
				break;
		}
		
		return $exposureMode;
	}
	/** 
	 * Ritorna la data di creazione dell'immagine nel formato yyyy:mm:gg hh:mm:ss
	 *  
	 * @return string
	 */
	public function getDateTime() { return $this->dateTime; }
	/** 
	 * Ritorna la risoluzione Y dell'immagine
	 * 
	 * @return string
	 */
	public function getYResolution() { return $this->YResolution; }
	/** 
	 * Ritorna la risoluzione X dell'immagine
	 * 
	 * @return string
	 */
	public function getXResolution() { return $this->XResolution; }
	/** 
	 * Ritorna il punto cardinale della longitudine
	 * 
	 * @return string
	 */
	public function getGPSLongitudeRef() { return $this->GPSLongitudeRef;	}
	/** 
	 * Ritorna la longitudine
	 * 
	 * @param boolean $toString
	 * @return string
	 */
	public function getGPSLongitude($toString = true) {		
		if ($toString) {
			return implode('@@', $this->GPSLongitude);	
		} else {
			return $this->GPSLongitude;
		}
	}
	/** 
	 * Ritorna la latitudine nel formato di googlemap
	 * 
	 * @return string
	 */
	public function getGPSLongitudeGoogle() { return $this->getGps($this->getGPSLongitude(false), $this->getGPSLongitudeRef()); }
	/** 
	 * Ritorna il punto cardinale della latitudine
	 * 
	 * @return string
	 */
	public function getGPSLatitudeRef() { return $this->GPSLatitudeRef;	}
	/** 
	 * Ritorna la latitudine
	 * 
	 * @param boolean $toString
	 * @return string
	 */
	public function getGPSLatitude($toString = true) { 
		if ($toString) {
			return implode('@@', $this->GPSLatitude);	
		} else {
			return $this->GPSLatitude;
		} 
		 
	}
	/** 
	 * Ritorna la latitudine nel formato di googlemap
	 * 
	 * @return string
	 */
	public function getGPSLatitudeGoogle() { return $this->getGps($this->getGPSLatitude(false), $this->getGPSLatitudeRef()); }
	/** 
	 * Ritorna il copyright dell'immagine
	 * 
	 * @return string
	 */
	public function getCopyright() { return $this->copyright; }
	/** 
	 * Ritorna il software che ha creato l'immagine
	 * 
	 * @return string
	 */
	public function getSoftware() { return $this->software; }
	/** 
	 * Ritorna il modello della macchina utilizzata per l'immagine
	 * 
	 * @return string
	 */
	public function getModel() { return $this->model; }
	/** 
	 * Ritorna la macchina utilizzata per la foto
	 * 
	 * @return string
	 */
	public function getMake() { return $this->make; }
	/** 
	 * Ritorna l'orientamento dell'immagine
	 * 
	 * @return string
	 */
	public function getOrientation() {
		$orientation = '';
		
		switch ($this->orientation) {
			case '1':
			case '4':
				$orientation = 'left';
				break;
			case '2':
			case '3':
				$orientation = 'right';
				break;
			case '5':
			case '6':
				$orientation = 'top';
				break;
			case '7':
			case '8':
				$orientation = 'bottom';
				break;
			default:
				$orientation = 'reserved';
				break;
		}
		
		return $orientation; 
	}
	/** 
	 * Ritorna la descrizione dell'immagine
	 * 
	 * @return string
	 */
	public function getImageDescription() { return $this->imageDescription; }
	/** 
	 * Ritorna il commento dell'utente
	 * 
	 * @return string 
	 */
	public function getUserComment() { return $this->userComment; }
	/** 
	 * Ritorna l'apertura dell'obiettivo
	 * 
	 * @return string 
	 */
	public function getApertureFNumber() { return $this->apertureFNumber; }
	/** 
	 * Ritorna se l'immagine e' a colori
	 * 
	 * @return integer
	 */
	public function getIsColor() { 
		if ($this->isColor == '1') {
			return '1';
		}  
		
		return '0';
	}
	/** 
	 * Ritorna la larghezza dell'immagine
	 * 
	 * @return integer
	 */
	public function getWidth() { return $this->width; }
	/** 
	 * Ritorna l'altezza dell'immagine
	 * 
	 * @return integer
	 */
	public function getHeight() { return $this->height; }
	/** 
	 * Ritorna la dimensione del file in byte 
	 * 
	 * @return integer
	 */
	public function getFileSize() { return $this->fileSize;}
	/** 
	 * Ritorna il contenuto del TAG in formato testuale 
	 * 
	 * @return string
	 */
	public function getTextTag() { return implode(' ' ,$this->textTag); }
	
	
	/**
	 * Ritorna True se riesce a parserizzare il file 
	 * 
	 * Recupera dal file Immagine le informazioni EXIF se questi sono presenti.
	 *  
	 * @param string $filename
	 * @return bool
	 */
	public function parseTagExif($filename){
		
		// array dei tag recuperai dal file MP3
		$tags = array();
		// recupera gli EXIF TAG delle immagini
		$tags = @exif_read_data($filename);

		if (empty($tags)) {return false;}
				
		// definsco in array
		$this->textTag = array();
		
		// recupero i dati dai TAG generici
		foreach ($tags as $key => $tag) {
			
				switch ($key) {
					// Dimensione del file in byte
					case 'FileSize':
						$this->setFileSize( $tag );						
						break;
					// Descrizione dell'immagine
					case 'ImageDescription':
						$this->setImageDescription( $tag );
						$this->textTag[$key] = $this->getImageDescription();
						break;
					// Orientamento 
					case 'Orientation':
						$this->setOrientation( $tag );
						break;
					// Macchina 
					case 'Make':
						$this->setMake( $tag );
						$this->textTag[$key] = $this->getMake();
						break;
					// Modello 
					case 'Model':
						$this->setModel( $tag );
						$this->textTag[$key] = $this->getModel();
						break;
					// Software 
					case 'Software':
						$this->setSoftware( $tag );
						$this->textTag[$key] = $this->getSoftware();
						break;
					// Copyright 
					case 'Copyright':
						$this->setCopyright( $tag );
						break;
					// Coordinate Latitudine 
					case 'GPSLatitude':
						$this->setGPSLatitude( $tag );
						break;
					// Punto cardinale della Latitudine 
					case 'GPSLatitudeRef':
						$this->setGPSLatitudeRef( $tag );
						break;
					// Coordinate Longitudine
					case 'GPSLongitude':
						$this->setGPSLongitude( $tag );
						break;
					// Punto cardinale della Longitudine 
					case 'GPSLongitudeRef':
						$this->setGPSLongitudeRef( $tag );
						break;
					// Risoluzione X 
					case 'XResolution':
						$this->setXResolution( $tag );
						break;
					// Risoluzione Y 
					case 'YResolution':
						$this->setYResolution( $tag );
						break;
					// Data di creazione 
					case 'DateTime':
						$this->setDateTime( $tag );
						break;
					// Modalita' di esposizione 
					case 'ExposureMode':
						$this->setExposureMode( $tag );
						break;
					// Tempo di esposizione 
					case 'ExposureTime':
						$this->setExposureTime( $tag );
						break;
					// Tipo di Scena 
					case 'SceneCaptureType':
						$this->setSceneCaptureType( $tag );
						break;
					// Risorsa di luce 
					case 'LightSource':
						$this->setLightSource( $tag );
						break;
					
					// COMPUTED
					case 'COMPUTED':
						$tagComputed =& $tags['COMPUTED'];
						
						// recupero i dati dal TAG COMPUTED
						if (!empty($tagComputed)) {
							foreach ($tagComputed as $key => $tag) {
								if (!is_array($tag)){
									switch ($key) {
										// Altezza in pixel
										case 'Height':
											$this->setHeight( $tag );
											$this->textTag[$key] = $this->getHeight();
											break;
										// Larghezza in pixel
										case 'Width':
											$this->setWidth( $tag );
											$this->textTag[$key] = $this->getWidth();
											break;
										// Immagine a colori
										case 'IsColor':
											$this->setIsColor( $tag );
											break;
										// Apertura dell'obiettivo
										case 'ApertureFNumber':
											$this->setApertureFNumber( $tag );
											break;
										// Commento dell'utente
										case 'UserComment':
											$this->setUserComment( $tag );
											$this->textTag[$key] = $this->getUserComment();
											break;
									}
								}
							}
						}
				}
		}
		
		return true;
	}
	
	/**
	 * Converte le coordinate GPS nel formato Google
	 * @param array $exifCoord
	 * @return string
	 */
	private function getGps($exifCoord, $ref)
	{
	  $degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
	  $minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
	  $seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;
	
	  //normalize
	  $minutes += 60 * ($degrees - floor($degrees));
	  $degrees = floor($degrees);
	
	  $seconds += 60 * ($minutes - floor($minutes));
	  $minutes = floor($minutes);
	
	  //extra normalization, probably not necessary unless you get weird data
	  if($seconds >= 60)
	  {
	    $minutes += floor($seconds/60.0);
	    $seconds -= 60*floor($seconds/60.0);
	  }
	
	  if($minutes >= 60)
	  {
	    $degrees += floor($minutes/60.0);
	    $minutes -= 60*floor($minutes/60.0);
	  }
	
	  $coord = $degrees.' '.$minutes.' '.$seconds.' '.$ref;; 
	  
	  return $coord;
	  //return array('degrees' => $degrees, 'minutes' => $minutes, 'seconds' => $seconds);
	}

	private function gps2Num($coordPart)
	{
	  $parts = explode('/', $coordPart);
	
	  if(count($parts) <= 0)
	    return 0;
	  if(count($parts) == 1)
	    return $parts[0];
	
	  return floatval($parts[0]) / floatval($parts[1]);
	}

		/**
	 * Trasforma le coordinate da DMS in Decimali
	 * @param String $DMS
	 * @param String $type [optional]
	 * @return decimalCoord
	 */
	public function DMStoDecimal($DMS, $type = 'LT') {
		// @TODO
		// creare una regular expression per la verifica del formato
		
		// pad 
		$pad_length = 6;
		// get DMS values
		list($d, $m, $s, $nsew) = explode(" ", $DMS); 
		$D = floatval($d);
		$m = floatval($m);
		$s = floatval($s);
		$nsew = strtoupper($nsew);
		$M = ($m/60);
		$S = ($s/(60*60));
		$dd = $D +  $M + $S ;
		$dd = (string)$dd;
		$ddArray = explode('.', $dd);			
		$ddArray[1] = str_pad($ddArray[1], $pad_length, '0', STR_PAD_RIGHT);
		$dd = implode('.', $ddArray);
		
		if ($type == 'LT' ) {
			//See if N or S was entered
			if ($nsew=="N" || $nsew=="S") {
				if ($nsew == "S") {
					$dd = "-".$dd;
				} else {
					$dd = "+".$dd;
				}
			}	
		} elseif ($type == 'LG') {
			//See if W is entered
			if ($nsew == "W") {
				$dd = "-".$dd;
			} else {
				$dd = "+".$dd;
			}
		}
		 
		return $dd;
	}
	
	/**
	 * Ritorna true se la proprieta' non e' vuota
	 * 
	 * @param string $property
	 * @return bool
	 */
	public function issetNotEmpty($property) {
		$isNotEmpty = false;
		if (is_array($this->$property)) {
			if (!empty($this->$property)) $isNotEmpty = true; 
		} else {
			if (trim($this->$property) !== '') $isNotEmpty = true;	
		}		
				
		return $isNotEmpty;
	}
}
?>