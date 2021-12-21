<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access'); 

if(isset($this->errors)) {
	?>
	<div>
	<?php echo JText::_('THE_FOLLOWING_ERRORS_HAVE_OCCURRED').': <br/><br/>'.implode('<br/>', $this->errors) ?>
	</div>
	<?php 
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_jifile&task=config.'); ?>" method="post" name="adminForm" id="adminForm">
	<?php // are not permission duplicate files ?>
	<input type="hidden" name="xml[duplicate]" value="0" />
<table class="adminform table table-striped">
	<tr>
		<th colspan="2">
			<h3><?php echo JText::_( 'Configuration' ); ?></h3>
		</th>
	</tr>
	<tr>
		<td width="110"><label><?php echo JText::_( 'Root-application' ); ?> <span class="nook">*</span></label></td>
		<td>
			<!--input class="inputbox" readonly="readonly" type="text" name="root-application_view" id="root-application_view" value="<?php echo JPATH_SITE.DS ?>" size="50" maxlength="255" /-->
			<label ><?php echo JPATH_SITE.DS ?></label>
			<input type="hidden" name="xml[root-application]" id="root-application" value="<?php echo $this->xmlValue['root-application'] ?>" size="50" maxlength="255" />
		</td>
	</tr>
<?php
// Non necessaria in questa versione. 
/* 
	<tr>
		<td width="110"><label for="table-name"><?php echo JText::_( 'Table-name' ); ?></label></td>
		<td>
			<input class="inputbox" type="text" name="xml[table-name]" id="table-name" value="<?php echo $this->xmlValue['table-name'] ?>" size="50" maxlength="255">
		</td>
	</tr>
*/ 
?>
	<tr>
		<td width="110"><label for="timelimit"><?php echo JText::_( 'Timelimit' ); ?></label></td>
		<td>
			<input class="inputbox" type="text" name="xml[timelimit]" id="timelimit" value="<?php echo $this->xmlValue['timelimit'] ?>" size="50" maxlength="255" />
		</td>
	</tr>
	<tr>
		<td width="110"><label for="memorylimit"><?php echo JText::_( 'Memorylimit' ); ?></label></td>
		<td>
			<div class="input-append">
				<input class="inputbox" type="text" name="xml[memorylimit]" id="memorylimit" value="<?php echo $this->xmlValue['memorylimit'] ?>" size="50" maxlength="255" />
				<span class="add-on">Mb</span>
			</div>
		</td>
	</tr>			
	<tr>
		<td width="110"><label for="xmlencoding"><?php echo JText::_( 'Encoding' ); ?></label></td>
		<td>
			<?php echo $this->xmlValue['encoding'] ?>
			<span style="font-size: 10px; font-style: italic; padding-left: 25px;">(<?php echo JText::_('ENCODING_RECOMMENDED') ?>)</span>
		</td>
	</tr>	
	<tr>	
		<td width="110"><label for="server_bit"><?php echo JText::_( 'Server bit' ); ?></label></td>
		<td>
			<?php echo $this->xmlValue['serverBit'] ?>
			<span style="font-size: 10px; font-style: italic; padding-left: 25px;">(<?php echo JText::_('IMPORTANT_4_PDF') ?>)</span>
		</td>
	</tr>
	<!--tr><td colspan="2">&nbsp;</td></tr-->
	<tr>
		<th colspan="2"><?php echo JText::_( 'Microsoft Word To Text' ); ?></th>
	</tr>
	<tr>
		<td class="rientro2" width="60"><label for="xmldoctotxtattributestype"><?php echo JText::_( 'Type' ); ?></label></td>
		<td>
			<?php echo $this->xmlValue['doctotxt']['attributes']['type'] ?>
			<br /><span style="font-size: 10px; font-style: italic; padding-left: 25px;"><?php echo JText::_( 'INFO_ANTIWORD' ); ?></span>
		</td>
	</tr>
	<tr>
		<td class="rientro2" width="60"><label for="xmldoctotxtattributesencodind"><?php echo JText::_( 'Encoding' ); ?></label></td>
		<td>
			<input class="inputbox" type="text" name="xml[doctotxt][attributes][encoding]" id="xmldoctotxtattributesencodind" value="<?php echo $this->xmlValue['doctotxt']['attributes']['encoding'] ?>" size="50" maxlength="255" />
			<br /><span style="font-size: 10px; font-style: italic; padding-left: 25px;"><?php echo JText::_( 'INFO_DOCTOTEXT' ); ?></span>
		</td>
	</tr>
	
	<tr><td colspan="2">&nbsp;</td></tr>
	
	<tr>
		<th colspan="2"><?php echo JText::_( 'XPDF' ); ?> (<a href="javascript:void(0)" id="openXPDF"><?php echo JText::_( 'Open' ); ?></a>)</th>
	</tr>	
	<tr>
		<td colspan="2">
			<div id="xpdf-section" style="display: none">
			<table class="adminform table table-striped">				
				<tr>
					<td colspan="2">			
						<label for="table-name"><a id="xpdfrcfile" href="index.php?option=com_jifile&task=config.xpdfrc&view=xpdfrc&tmpl=component"><?php echo JText::_( 'JIFILE_EDIT_XPDFRC' ); ?></a></label>
						<span style="font-size: 10px; font-style: italic; padding-left: 25px;"><?php echo JText::_( 'JIFILE_INFO_EDIT_XPDFRC' ); ?></span>			
					</td>		
				</tr>
			</table>
			<table class="adminform table table-striped">
				<tr>
					<th colspan="2"><?php echo JText::_( 'JIFILE_PDFTOTEXT' ); ?></th>
				</tr>
				<tr>
					<td class="rientro2" width="60">
						<label for="xmlxpdfpdftotext"><?php echo JText::_( 'JIFILE_EXECUTABLE' ); ?></label>
					</td>
					<td>
						<input class="inputbox" type="text" name="xml[xpdf][pdftotext][executable]" id="xmlxpdfpdftotext" value="<?php echo $this->xmlValue['xpdf']['pdftotext']['executable'] ?>" size="50" />
						<br /><span style="font-size: 10px; font-style: italic; padding-left: 25px;"><?php echo JText::_( 'JIFILE_INFO_EXECUTABLE_PDFTOTEXT' ); ?></span>
					</td>
				</tr>
				<tr>
					<td class="rientro2" width="60">
						<label for="xmlxpdfpdftotextrc"><?php echo JText::_( 'JIFILE_XPDFRC' ); ?></label>
					</td>
					<td>
						<input class="inputbox" type="text" name="xml[xpdf][pdftotext][xpdfrc]" id="xmlxpdfpdftotextrc" value="<?php echo $this->xmlValue['xpdf']['pdftotext']['xpdfrc'] ?>" size="50" />
						<br /><span style="font-size: 10px; font-style: italic; padding-left: 25px;"><?php echo JText::_( 'JIFILE_INFO_XPDFRC_PDFTOTEXT' ); ?></span>
					</td>
				</tr>
			</table>
			<table class="adminform table table-striped" >
				<tr>
					<th colspan="2"><?php echo JText::_( 'JIFILE_XPDFINFO' ); ?></th>
				</tr>
				<tr>
					<td class="rientro2" width="60">
						<label for="xmlxpdfinfo"><?php echo JText::_( 'JIFILE_EXECUTABLE' ); ?></label>
					</td>
					<td>
						<input class="inputbox" type="text" name="xml[xpdf][pdfinfo][executable]" id="xmlxpdfinfo" value="<?php echo $this->xmlValue['xpdf']['pdfinfo']['executable'] ?>" size="50" />
						<br /><span style="font-size: 10px; font-style: italic; padding-left: 25px;"><?php echo JText::_( 'JIFILE_INFO_EXECUTABLE_PDFINFO' ); ?></span>
					</td>
				</tr>
				<tr>
					<td class="rientro2" width="60">
						<label for="xmlxpdfpdftotextrc"><?php echo JText::_( 'JIFILE_XPDFRC' ); ?></label>
					</td>
					<td>
						<input class="inputbox" type="text" name="xml[xpdf][pdfinfo][xpdfrc]" id="xmlxpdfpdftotextrc" value="<?php echo $this->xmlValue['xpdf']['pdfinfo']['xpdfrc'] ?>" size="50" />
						<br /><span style="font-size: 10px; font-style: italic; padding-left: 25px;"><?php echo JText::_( 'JIFILE_INFO_XPDFRC_PDFINFO' ); ?></span>
					</td>
				</tr>
			</table>	
			</div>	
		</td>
	</tr>
		
	<tr>
		<th colspan="2"><?php echo JText::_( 'Fileds' ); ?>: (<a href="javascript:void(0)" id="openField"><?php echo JText::_( 'Open' ); ?></a>)</th>
	</tr>
	<tr><td colspan="2">
	<div id="zend-document" style="display: none">
	<table>
	<tr>
		<th class="rientro"><?php echo JText::_( 'Field' ); ?></th>
		<th><?php echo JText::_( 'Type' ); ?></th>
		<?php /*
		<th>Encoding <span style="font-weight: normal; font-style: italic;">(default <span id="default_encoding"><?php echo $this->xmlValue['encoding'] ?></span>)</span></th>
		*/ ?>
	</tr>
	<?php 
		$i = 0;
		foreach ($this->xmlValue['zend-document'] as $key => $field) { ?>
	<tr class="<?php echo 'row'.($i%2); $i++; ?>">
		<td class="rientro"><label for="xmltype<?php echo $key ?>"><?php echo $key ?></label></td>
		<td>
			<input type="hidden" name="xml[zend-document][fields][field][<?php echo $field['id'] ?>][attributes][name]" value="<?php echo $key ?>" />
			<?php echo $field['type']; ?>
		</td>
		<?php /*
		<td>
			<?php echo $field['encoding']; ?>
		</td>
		*/ ?>
		<?php if($i === 1) { ?>
		<td rowspan="<?php echo count($this->xmlValue['zend-document']) ?>">
			<table class="zend-field-info table">
				<caption><?php echo JText::_( 'Fields_Types_Legend' ); ?> (<a href="http://framework.zend.com/manual/en/zend.search.lucene.overview.html#zend.search.lucene.index-creation.understanding-field-types" target="_blank">info</a>)</caption>
				<tr>
					<th><?php echo JText::_( 'Field_Type' ); ?></th>
					<th><?php echo JText::_( 'Stored' ); ?></th>
					<th><?php echo JText::_( 'Indexed' ); ?></th>
					<th><?php echo JText::_( 'Tokenized' ); ?></th>
					<th><?php echo JText::_( 'Binary' ); ?></th>
				</tr>
				<tr>
					<td>Keyword</td>
					<td><span class="label label-success"><?php echo JText::_( 'JYes' ); ?></span></td>
					<td><span class="label label-success"><?php echo JText::_( 'JYes' ); ?></span></td>
					<td><span class="label label-important"><?php echo JText::_( 'JNo' ); ?></span></td>
					<td><span class="label label-important"><?php echo JText::_( 'JNo' ); ?></span></td>
				</tr>
				<tr>
					<td>UnIndexed</td>
					<td><span class="label label-success"><?php echo JText::_( 'JYes' ); ?></span></td>
					<td><span class="label label-important"><?php echo JText::_( 'JNo' ); ?></span></td>
					<td><span class="label label-important"><?php echo JText::_( 'JNo' ); ?></span></td>
					<td><span class="label label-important"><?php echo JText::_( 'JNo' ); ?></span></td>
				</tr>
				<tr>
					<td>Binary</td>
					<td><span class="label label-success"><?php echo JText::_( 'JYes' ); ?></span></td>
					<td><span class="label label-important"><?php echo JText::_( 'JNo' ); ?></span></td>
					<td><span class="label label-important"><?php echo JText::_( 'JNo' ); ?></span></td>
					<td><span class="label label-success"><?php echo JText::_( 'JYes' ); ?></span></td>
				</tr>
				<tr>
					<td>Text</td>
					<td><span class="label label-success"><?php echo JText::_( 'JYes' ); ?></span></td>
					<td><span class="label label-success"><?php echo JText::_( 'JYes' ); ?></span></td>
					<td><span class="label label-success"><?php echo JText::_( 'JYes' ); ?></span></td>
					<td><span class="label label-important"><?php echo JText::_( 'JNo' ); ?></span></td>
				</tr>
				<tr>
					<td>UnStored</td>
					<td><span class="label label-important"><?php echo JText::_( 'JNo' ); ?></span></td>
					<td><span class="label label-success"><?php echo JText::_( 'JYes' ); ?></span></td>
					<td><span class="label label-success"><?php echo JText::_( 'JYes' ); ?></span></td>
					<td><span class="label label-important"><?php echo JText::_( 'JNo' ); ?></span></td>
				</tr>
			</table>
		</td>
		<?php } ?>
	</tr>
	<?php } ?>
	</table>
	</div>
	</td></tr>	
	<tr>
		<th colspan="2"><?php echo JText::_( 'Analyzer' ); ?>:</th>
	</tr>
	<tr>
		<td class="rientro" width="60"><label for="xmlanalyzertypedefault"><?php echo JText::_( 'Type' ); ?></label></td>
		<td>
			<?php echo $this->xmlValue['analyzer'] ?>
			<div id="custom">
				<?php if(isset($this->xmlValue['analyzer_path'])) { ?>
				<br>
				<label class="custom" for="custom-default_path" style="padding: 0 20px 0 0">Path</label>
				<input type="text" id="custom-default_path" class="inputbox custom" maxlength="255" size="50" name="xml[analyzer][type][custom-default][@value]" value="<?php echo $this->xmlValue['analyzer_path'] ?>" />
				<label class="custom" for="custom-default_class" style="padding: 0 20px">Class</label>
				<input type="text" id="custom-default_class" class="inputbox custom" maxlength="255" size="50" name="xml[analyzer][type][custom-default][attributes][class]" value="<?php echo $this->xmlValue['analyzer_class'] ?>" />
				<?php } ?>
			</div>
		</td>
	</tr>
	<tr>
		<th class="rientro" colspan="2"><?php echo JText::_( 'Filters' ); ?>:</th>
	</tr>
	<tr>
		<td class="rientro2" width="60"><label for="stop-words"><?php echo JText::_( 'Stop-words' ); ?></label></td>
		<td>
			<input class="inputbox" type="text" name="xml[analyzer][filters][stop-words]" id="stop-words" value="<?php echo $this->xmlValue['stop-words'] ?>" size="50" maxlength="255" />
		</td>
	</tr>
	<tr>
		<td class="rientro2" width="60"><label for="xmlanalyzerfiltersshort-words"><?php echo JText::_( 'Short-words' ); ?></label></td>
		<td>
			<?php echo $this->xmlValue['short-words'] ?>
		</td>
	</tr>
	<tr>
		<th class="rientro2" colspan="2"><?php echo JText::_( 'Custom-filters' ); ?>: <img alt="add" src="components/com_jifile/images/expandall.png" id="addFilter" style="cursor:pointer"></th>
	</tr>
	<tr>
		<td class="rientro3" width="60">&nbsp;</td>
		<td>
			<div id="filters">
			<?php 
				if(!empty($this->xmlValue['filters'])) {
					foreach ($this->xmlValue['filters'] as $key => $filter) {
						$i = $key+1;
						$path = isset($filter['@value']) ? $filter['@value'] : $filter['file'];
						$class = isset($filter['attributes']['class']) ? $filter['attributes']['class'] : $filter['class'];
						?>
						<p>
						<label style="padding: 0 17px 0 0" for="path<?php echo $key ?>">(<?php echo $i ?>) <?php echo JText::_( 'Path' ); ?></label>
						<input class="inputbox filters" type="text" name="xml[analyzer][filters][custom-filters][filter][<?php echo $key ?>][@value]" id="path<?php echo $key ?>" value="<?php echo $path ?>" size="50" maxlength="255" />
						<label style="padding: 0 17px" for="class<?php echo $key ?>"><?php echo JText::_( 'Class' ); ?></label>
						<input class="inputbox" type="text" name="xml[analyzer][filters][custom-filters][filter][<?php echo $key ?>][attributes][class]" id="class<?php echo $key ?>" value="<?php echo $class ?>" size="50" maxlength="255" />
						</p>
						<?php 
					}
				}
			?>
			</div>
		</td>
	</tr>
</table>
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>
<?php echo jifilehelper::getFirma(); ?>
<script type="text/javascript">
	jQuery('#openField').click(function(){
		jQuery('#zend-document').slideToggle('slow', function() {
				if(jQuery('#zend-document').css('display') == 'block') {
					jQuery('#openField').html('<?php echo JText::_( 'Close' ); ?>')
				} else {
					jQuery('#openField').html('<?php echo JText::_( 'Open' ); ?>')
				}
			});
	});
	
	jQuery('#openXPDF').click(function(){
		jQuery('#xpdf-section').slideToggle('slow', function() {
				if(jQuery('#xpdf-section').css('display') == 'block') {
					jQuery('#openXPDF').html('<?php echo JText::_( 'Close' ); ?>')
				} else {
					jQuery('#openXPDF').html('<?php echo JText::_( 'Open' ); ?>')
				}
			});
	});
	jQuery(document).ready(function() {
		jQuery('#default_encoding').html(jQuery('#xmlencoding option:selected').text());
		jQuery('#xmlencoding').change(function(){
			jQuery('#default_encoding').html(jQuery(this).find("option:selected").text());
		});
	});
</script>