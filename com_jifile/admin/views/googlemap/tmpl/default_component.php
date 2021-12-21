<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access');
	if($this->error) {
		?>
		<script type="text/javascript">
		alert('<?php echo JText::_('ERROR_RECOVERY_DOCUMENT').': '.jifilehelper::JText($this->error, 1) ?>');
		</script>
		<?php
	}	
?>
<div id="system-message-container">
</div>
	<div id="JiFilemaps">
		<div class="p-add-address">
		<p>
			<form action="#" onsubimt="return false;" >
				<p><?php echo JText::_( 'GPSLatitudeGoogleDecimal' ); ?>: 
				<input type="text" 
					name="JiFileLat" 
					id="JiFileLat" 
					value="<?php echo $this->lat ?>"  
					size="30" />
				<?php echo JText::_( 'GPSLongitudeGoogleDecimal' ); ?>: 
				<input type="text" 
					name="JiFileLng" 
					id="JiFileLng" 
					value="<?php echo $this->lng ?>"  
					size="30" />
				<input class="btn" type="button" name="find" value="<?php echo JText::_( 'ViewInMap' ); ?>" onclick="addAddressToMap()"/>
				<input class="btn" type="button" name="setAddress" value="<?php echo JText::_( 'SetCoordinate' ); ?>" onclick="setAddAddressToMap()"/>
			</form>
		</p>
	</div>			
		<div align="center" style="margin:0;padding:0">
				<div id="JiFileMap" style="margin:0;padding:0;width:100%;height:300px"></div>
		</div>
		<script type="text/javascript">
			 //<![CDATA[
			 google.load("maps", "3.x", {"other_params":"sensor=false"});
			 google.load("search", "1");

			 var defJiFileMap = document.getElementById('JiFileMap');
			 var defIntJiFileMap;
			 var mapJiFileMap;
			 var JiFileGeoCoder;
			 var markerJiFileMarkerGlobal;
			 var infoJiFileWindowGlobal;


			 function CancelEventJiFileMap(event) { 
			   var e = event; 
			   if (typeof e.preventDefault == 'function') e.preventDefault(); 
			   if (typeof e.stopPropagation == 'function') e.stopPropagation(); 
			   if (window.event) { 
			      window.event.cancelBubble = true; /* for IE */
			      window.event.returnValue = false; /* for IE */
			   } 
			 }
			
			 function CheckJiFileMap() {
			   if (defJiFileMap) {
			      if (defJiFileMap.offsetWidth != defJiFileMap.getAttribute("oldValue")) {
			         defJiFileMap.setAttribute("oldValue",defJiFileMap.offsetWidth);
			             if (defJiFileMap.getAttribute("refreshMap")==0) {
			                if (defJiFileMap.offsetWidth > 0) {
			                   clearInterval(defIntJiFileMap);
			                   getJiFileMap();
			                  defJiFileMap.setAttribute("refreshMap", 1);
			                } 
			             }
			         }
			     }
			 }
			
			 function getJiFileMap(){
			   if (defJiFileMap.offsetWidth > 0) {
				 	var jifileLat = '<?php echo $this->lat ?>';
				 	var jifileLng = '<?php echo $this->lng ?>';
					var JiFileLatLng = new google.maps.LatLng(<?php echo $this->lat ?>, <?php echo $this->lng ?>);
					var JiFileOptions = {
				   zoom: <?php echo $this->zoom ?>,
				   center: JiFileLatLng,
				   mapTypeControl: true,
				   mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DEFAULT, 
				   position: google.maps.ControlPosition.TOP_RIGHT },
				   navigationControl: true,
				   navigationControlOptions: {style: google.maps.NavigationControlStyle.DEFAULT},
				   scaleControl: true,
				   scrollwheel: 1,
				   disableDoubleClickZoom: 0,
				   mapTypeId: google.maps.MapTypeId.ROADMAP
			 };
			
			mapJiFileMap = new google.maps.Map(document.getElementById('JiFileMap'), JiFileOptions);
			 var JiFileStartZoom = <?php echo $this->zoom ?>;
			 var JiFileZoom = null;
			 google.maps.event.addListener(mapJiFileMap, "zoom_changed", function(JiFileStartZoom, JiFileZoom) {
			   JiFileZoom = mapJiFileMap.getZoom();
			});
			 var JiFilePointGlobal = new google.maps.LatLng(<?php echo $this->lat ?>, <?php echo $this->lng ?>);
			 markerJiFileMarkerGlobal = new google.maps.Marker({
			   position: JiFilePointGlobal,
			   map: mapJiFileMap,
			   draggable: true
			 });
			
			 infoJiFileWindowGlobal = new google.maps.InfoWindow({
			   content: markerJiFileMarkerGlobal.getPosition().toUrlValue(6)
			 });
			
			 google.maps.event.addListener(markerJiFileMarkerGlobal, 'dragend', function() {
			   var JiFilePointTmp = markerJiFileMarkerGlobal.getPosition();
			   markerJiFileMarkerGlobal.setPosition(JiFilePointTmp);
			   closeMarkerInfoGlobal();
			   exportPointGlobal(JiFilePointTmp);
			 });
			
			 google.maps.event.addListener(mapJiFileMap, 'click', function(event) {
			   var JiFilePointTmp2 = event.latLng;
			   markerJiFileMarkerGlobal.setPosition(JiFilePointTmp2);
			   closeMarkerInfoGlobal();
			   exportPointGlobal(JiFilePointTmp2);
			 });
			
			 google.maps.event.addListener(markerJiFileMarkerGlobal, 'click', function(event) {
			   openMarkerInfoGlobal();
			 });
			
			 function openMarkerInfoGlobal() {
			   infoJiFileWindowGlobal.content = markerJiFileMarkerGlobal.getPosition().toUrlValue(6);
			   infoJiFileWindowGlobal.open(mapJiFileMap, markerJiFileMarkerGlobal );
			 }
			
			 function closeMarkerInfoGlobal() {
			   infoJiFileWindowGlobal.close(mapJiFileMap, markerJiFileMarkerGlobal );
			 }
			
			function exportPointGlobal(JiFilePointTmp3) {
				document.getElementById("JiFileLat").value = JiFilePointTmp3.lat();
				document.getElementById("JiFileLng").value = JiFilePointTmp3.lng();			   
			 }
			
			 google.maps.event.addDomListener(defJiFileMap, 'DOMMouseScroll', CancelEventJiFileMap);
			 google.maps.event.addDomListener(defJiFileMap, 'mousewheel', CancelEventJiFileMap);
			 JiFileGeoCoder = new google.maps.Geocoder();
			   }
			 }
			 
			function setAddAddressToMap() {
				var lat = document.getElementById("JiFileLat").value;
				var lng = document.getElementById("JiFileLng").value;
				
				if (window.parent) (window.parent.document.getElementById("GPSLatitudeGoogleDecimal").value = lat);
				if (window.parent) (window.parent.document.getElementById("GPSLongitudeGoogleDecimal").value = lng);
				
				window.parent.jQuery.colorbox.close();
			} 
			
			function addAddressToMap() {
			   var JiFileLat = document.getElementById("JiFileLat").value;
			   var JiFileLng = document.getElementById("JiFileLng").value;
			   var JiFileAddress = JiFileLat+","+JiFileLng;
			   
			   if (JiFileGeoCoder) {
			      JiFileGeoCoder.geocode( { 'address': JiFileAddress}, function(results, status) {
			         if (status == google.maps.GeocoderStatus.OK) {
			            var JiFileLocation = results[0].geometry.location;
			            var JiFileLocationAddress = results[0].formatted_address
			            mapJiFileMap.setCenter(JiFileLocation);
			            markerJiFileMarkerGlobal.setPosition(JiFileLocation);
			            infoJiFileWindowGlobal.content = '<div>'+ JiFileLocationAddress +'</div><div>&nbsp;</div><div>'+ JiFileLocation +'</div>';
			            infoJiFileWindowGlobal.open(mapJiFileMap, markerJiFileMarkerGlobal );
			         } else {
			            alert("Geocode not found (" + status + ")");
			         }
			      });
			   }
			}
			
			 function initialize() {
			   defJiFileMap.setAttribute("oldValue",0);
			   defJiFileMap.setAttribute("refreshMap",0);
			   defIntJiFileMap = setInterval("CheckJiFileMap()",500);
			 }
			
			 google.setOnLoadCallback(initialize);
	//]]></script>
	<noscript><p class="p-noscript">JavaScript must be enabled in order for you to use Google Maps. However, it seems JavaScript is either disabled or not supported by your browser. To view Google Maps, enable JavaScript by changing your browser options, and then try again.</p><p>&nbsp;</p></noscript>	
</div>
