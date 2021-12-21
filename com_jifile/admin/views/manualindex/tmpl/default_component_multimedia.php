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
	if (!empty($this->encoding)) {
		$this->ifiledoc['body'] = htmlentities($this->ifiledoc['body'], ENT_COMPAT|ENT_SUBSTITUTE, $this->encoding);
		$formCharset = 'accept-charset="'.$this->encoding.'"';
	} else {
		$this->ifiledoc['body'] = htmlentities($this->ifiledoc['body'], ENT_COMPAT|ENT_SUBSTITUTE);
		$formCharset = '';
	}
?>
<script type="text/javascript">
	string = new Array();
	string['error'] = '<?php echo JText::_( 'Error' ); ?>';
	string['success'] = '<?php echo JText::_( 'Success' ); ?>';
	string['field_required'] = '<?php echo JText::_( 'ENTER_THE_NAME_OF_THE_FIELD' ); ?>!';
	string['remove'] = '<?php echo JText::_( 'Remove' ); ?>';
	string['close'] = '<?php echo JText::_( 'Close' ); ?>';
	string['already_exists'] = '<?php echo JText::_( 'FIELD_NAME_ALREADY_EXISTS' ); ?>!';
	string['reserved_field'] = '<?php echo JText::_( 'FIELD_NAME_RESERVED' ); ?>!';
	objAddField.loadString(string);
</script>
<script type="text/javascript">
	function openGoogleMap() {
		var lat = document.getElementById("GPSLatitudeGoogleDecimal").value;
		var lng = document.getElementById("GPSLongitudeGoogleDecimal").value;
		var googlehref ="index.php?option=com_jifile&task=lucene.googlemap&view=googlemap&tmpl=component&lat="+lat+"&lng="+lng+"&zoom=15";
		jQuery.colorbox({ href: googlehref, width: '80%', height: '100%', iframe: true }); 
		return true;
	}
</script>
<div id="ifileOverlay" style="display: none"></div>
<div id="ifileLoaderOverlay" class="ifileLoad" style="display: none"></div>
<div id="ifileLoader" class="ifileLoad" style="display: none"></div>
<div id="addField" style="display: none">
	<table class="adminform">
		<tr><th colspan="2"><?php echo JText::_( 'ADD_FIELD' ); ?></th></tr>
		<tr>
			<td width="60"><label for="tmp_name"><?php echo JText::_( 'Name' ); ?>:</label></td>
			<td>
				<input class="inputbox" type="text" name="tmp_name" id="tmp_name" value="" size="50" maxlength="255">
				<br/><span id="msgErr"></span>
			</td>
		</tr>
		<tr>
			<td width="100"><label for="tmp_typefield"><?php echo JText::_( 'Field_Type' ); ?>:</label></td>
			<td>
				<select id ="tmp_typefield" name="tmp_typefield" >
					<option value="Text">Text</option>
					<option value="Keyword">Keyword</option>
					<option value="UnStored">UnStored</option>
					<option value="UnIndexed">UnIndexed</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2"><button class="btn" onclick="addField()" type="button">
		<?php echo JText::_( 'Add' ); ?></button></td>
		</tr>
	</table>
	<div id="cboxClose" style="" class="">close</div>
</div>

<div id="toolbar-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">
			<div class="pagetitle"><h2><?php echo JText::_( 'MANUAL_INDEXING' ); ?></h2></div>
		<div class="clr"></div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>

<div class="clr"></div>
<div id="element-box">
	<div class="t">
 		<div class="t">
			<div class="t"></div>
 		</div>
	</div>
	<div class="m" id="ifile">
	<form action="<?php echo JRoute::_('index.php?option=com_jifile'); ?>" method="post" name="adminForm" id="adminFormManual" <?php echo $formCharset ?> class="form-inline">
		<div style="float: right;">
			<button class="btn btn-success" id="send" type="button">
				<?php echo JText::_( 'Save' ); ?></button>
			<button class="btn" id="close" onclick="objAddField.pw.jQuery.colorbox.close();" type="button">
				<?php echo JText::_( 'Cancel' ); ?></button>
		</div>
		<table class="adminform table">
			<tr>
				<th colspan="4">
					<?php echo JText::_( 'DETAIL_FILE' ); ?>
					<div id="result" class="result">
						
					</div>
				</th>
			</tr>
			<tr>
				<td colspan="1" width="120"><label for="filename"><?php echo JText::_( 'Filename' ); ?>:</label></td>
				<td colspan="3">
					<label><?php echo $this->filename; ?></label>&nbsp;					
					<input type="hidden" name="fields[filename]" id="filename" value="<?php echo urlencode($this->filename); ?>" />
					<a title="download" href="index.php?option=com_jifile&task=filesystem.download&filename=<?php echo $this->basepath.urlencode(jifilehelper::retrievePath($this->filename, true)); ?>"><img alt="download" src="components/com_jifile/images/download_f2.png" style="width: 16px; height:16px" /></a>
				</td>				
			</tr>
			<tr>
				<td colspan="1" width="120"><label for="title"><?php echo JText::_( 'Title' ); ?>:</label></td>
				<td colspan="3">
					<input class="inputbox" type="text" name="fields[title]" id="title" size="40" maxlength="255" value="<?php echo $this->ifiledoc['title']; ?>" />
				</td>
			</tr>
			<tr>
				<td colspan="1" width="120"><label for="subject"><?php echo JText::_( 'Subject' ); ?>:</label></td>
				<td colspan="3">
					<input class="inputbox" type="text" name="fields[subject]" id="subject" size="40" maxlength="255" value="<?php echo $this->ifiledoc['subject']; ?>" />
				</td>
			</tr>
			<tr>
				<td colspan="1" width="120"><label for="description"><?php echo JText::_( 'Description' ); ?>:</label></td>
				<td colspan="3">
					<input class="inputbox" type="text" name="fields[description]" id="description" size="40" maxlength="255" value="<?php echo $this->ifiledoc['description']; ?>" />
				</td>
			</tr>
			<tr>
				<td colspan="1" width="120"><label for="author"><?php echo JText::_( 'Author' ); ?>:</label></td>
				<td colspan="3">
					<input class="inputbox" type="text" name="fields[creator]" id="author" size="40" maxlength="255" value="<?php echo $this->ifiledoc['creator']; ?>" />
				</td>
			</tr>
			<tr>
				<td colspan="1" width="120"><label for="keywords"><?php echo JText::_( 'Keywords' ); ?>:</label></td>
				<td colspan="3">
					<input class="inputbox" type="text" name="fields[keywords]" id="keywords" size="100" maxlength="255" value="<?php echo $this->ifiledoc['keywords']; ?>" />
				</td>
			</tr>
			<tr>
				<td width="120"><label for="created"><?php echo JText::_( 'CREATION_DATE' ); ?>:</label></td>
				<td>
					<?php echo JHTML::calendar($this->ifiledoc['created'], 'fields[created]', 'created', '%Y-%m-%d'); ?>
				</td>
				<td width="120"><label for="modified"><?php echo JText::_( 'MODIFIED_DATE' ); ?>:</label></td>
				<td>
					<?php echo JHTML::calendar($this->ifiledoc['modified'], 'fields[modified]', 'modified', '%Y-%m-%d'); ?>
				</td>
			</tr>
			
			<!-- START ID3 TAG -->		
			<tr>
				<th colspan="4">
					<?php echo JText::_( 'ID3_DECTAIL' ); ?>
					<div id="result" class="result">
						
					</div>
				</th>
			</tr>	
			<tr>
				<td width="120"><label for="track"><?php echo JText::_( 'Track' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[track]" id="track" size="40" maxlength="255" value="<?php echo $this->ifiledoc['track']; ?>" />
				</td>
				<td width="120"><label for="bpm"><?php echo JText::_( 'bpm' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[bpm]" id="bpm" size="40" maxlength="255" value="<?php echo $this->ifiledoc['bpm']; ?>" />
				</td>
			</tr>
			<tr>
				<td width="120"><label for="partOfASet"><?php echo JText::_( 'partOfASet' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[partOfASet]" id="partOfASet" size="40" maxlength="255" value="<?php echo $this->ifiledoc['partOfASet']; ?>" />
				</td>
				<td width="120"><label for="artist"><?php echo JText::_( 'artist' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[artist]" id="artist" size="40" maxlength="255" value="<?php echo $this->ifiledoc['artist']; ?>" />
				</td>
			</tr>			
			<tr>
				<td width="120"><label for="album"><?php echo JText::_( 'album' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[album]" id="album" size="40" maxlength="255" value="<?php echo $this->ifiledoc['album']; ?>" />
				</td>
				<td width="120"><label for="year"><?php echo JText::_( 'year' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[year]" id="year" size="40" maxlength="255" value="<?php echo $this->ifiledoc['year']; ?>" />
				</td>
			</tr>
			<tr>
				<td width="120"><label for="originalArtist"><?php echo JText::_( 'originalArtist' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[originalArtist]" id="originalArtist" size="40" maxlength="255" value="<?php echo $this->ifiledoc['originalArtist']; ?>" />
				</td>
				<td width="120"><label for="copyright"><?php echo JText::_( 'copyright' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[copyright]" id="copyright" size="40" maxlength="255" value="<?php echo $this->ifiledoc['copyright']; ?>" />
				</td>
			</tr>
			<tr>
				<td width="120"><label for="band"><?php echo JText::_( 'band' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[band]" id="band" size="40" maxlength="255" value="<?php echo $this->ifiledoc['band']; ?>" />
				</td>
				<td width="120"><label for="genre"><?php echo JText::_( 'genre' ); ?>:</label></td>
				<td>
					<?php echo $this->genre ?>					
				</td>
			</tr>
						
			<tr>
				<td width="120"><label for="GPSLatitudeGoogleDecimal"><?php echo JText::_( 'GPSLatitudeGoogleDecimal' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[GPSLatitudeGoogleDecimal]" id="GPSLatitudeGoogleDecimal" size="40" maxlength="255" value="<?php echo $this->ifiledoc['GPSLatitudeGoogleDecimal']; ?>" />
				</td>
				<td width="120"><label for="GPSLongitudeGoogleDecimal"><?php echo JText::_( 'GPSLongitudeGoogleDecimal' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[GPSLongitudeGoogleDecimal]" id="GPSLongitudeGoogleDecimal" size="40" maxlength="255" value="<?php echo $this->ifiledoc['GPSLongitudeGoogleDecimal']; ?>" />
				</td>
			</tr>
			<tr>
				<td colspan="1">&nbsp;</td>
				<td colspan="3"><input class="btn" type="button" name="googleMap" value="<?php echo JText::_( 'SetGoogleCoordinate' ); ?>" onclick="openGoogleMap();" /></td>
			</tr>
			
			<tr>
				<td width="120"><label for="publisher"><?php echo JText::_( 'publisher' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[publisher]" id="publisher" size="40" maxlength="255" value="<?php echo $this->ifiledoc['publisher']; ?>" />
				</td>
				<td width="120"><label for="composer"><?php echo JText::_( 'composer' ); ?>:</label></td>
				<td>
					<input class="inputbox" type="text" name="fields[composer]" id="composer" size="40" maxlength="255" value="<?php echo $this->ifiledoc['composer']; ?>" />
				</td>
			</tr>
			<tr>
				<td width="120"><label for="encodedBy"><?php echo JText::_( 'encodedBy' ); ?>:</label></td>
				<td colspan="3">
					<input class="inputbox" type="text" name="fields[encodedBy]" id="encodedBy" size="40" maxlength="255" value="<?php echo $this->ifiledoc['encodedBy']; ?>" />
				</td>
			</tr>
			<!-- FINISH ID3 TAG -->
			
			<tr class="contentAddField">
				<td colspan="1"><input class="btn" type="button" id="openAddfield" value="<?php echo JText::_( 'ADD_FIELD' ); ?>"></td>
				<td colspan="3"><span class="muted"><?php echo JText::_( 'ADD_FIELD_INFO' ); ?></span></td>
			</tr>
			<tr>
				<td colspan="1">
					<label for="content"><?php echo JText::_( 'body' ); ?>
					<span title="<?php echo JText::_('Required'); ?>" style="color:#ff0000;vertical-align: super;">(<span style="vertical-align: sub;">*</span>)</span>
					<br /><?php echo JText::_( 'body_lyrics' ); ?>
					</label>
				</td>
				<td colspan="3"><textarea class="filed span12" name="fields[body]" id="content" rows="25" cols="90"><?php echo $this->ifiledoc['body']; ?></textarea></td>
			</tr>
		</table>
		<input type="hidden" name="fields[extensionfile]" value="<?php echo $this->extension; ?>" />
		<input type="hidden" name="i" value="<?php echo $this->i ?>" />
		<input type="hidden" name="fields[add][class]" value="jifile" />
		<input type="hidden" name="task" value="lucene.indexManualAjax" />
	</form>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>
<?php unset($this->ifiledoc); ?>
