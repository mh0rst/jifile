<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access'); 

if ($this->ifiledoc || $this->error) {
	echo $this->loadTemplate('filecontent');
} else {
?>

<div id="toolbar-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">
			<div class="pagetitle"><h2><?php echo JText::_( 'DOCUMENT_DETAIL' ); ?></h2></div>
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
		<table class="adminform table" style="max-width: 700px;">
		<?php 
			if(!empty($this->doc)) {
				$body = null;
				foreach ($this->fieldNames as $fieldname) { 
					if ($fieldname == 'body') {
						try {
							$body = $this->doc->getFieldValue($fieldname);
						} catch (Zend_Search_Lucene_Exception $e) {
						}
						continue; 
					}
		?>
			<tr>
				<td width="120"><?php echo $fieldname; ?>:</td>
				<td>
					<?php 
					try {
						if (in_array($fieldname, array('name', 'path', 'filename'))) {
							echo jifilehelper::encodingCharset($this->doc->getFieldValue($fieldname));
						} else {
							echo $this->doc->getFieldValue($fieldname).($fieldname == 'introtext' ? '...' : '');
						}
					} catch (Zend_Search_Lucene_Exception $e) {
						echo '';
					}
					?>
				</td>
			</tr>
		<?php } 
		if(!empty($body)) {
		?>
			<tr>
				<td width="120"><?php echo JText::_('BODY').' '.JText::_('STORED'); ?>:</td>
				<td>
					<div style="max-height: 180px; overflow: auto;"><?php echo $body; ?></div>
				</td>
			</tr>
		<?php } ?>
			<tr>
				<td width="120">
				<?php echo JText::_('BODY').' '.JText::_('RETRIEVED'); ?>:<br />
						<span style="font-style: italic;font-size: 10px;"><?php echo JText::_('CONTENT_NOTIFY'); ?></span>
				</td>
				<td><iframe width="100%" height="180px" 
					src="index.php?option=com_jifile&task=lucene.display&view=filedetail&tmpl=component&act=filecontent&id=<?php echo $this->id ?>"
					style="border: 1px solid #000000;"></iframe>
				</td>
			</tr>
		<?php } else {
				?>
				<tr><td><?php echo JText::_('NO_DOCUMENTS_FOUND'); ?>!</td></tr>
				<?php 
			}
		?>
		</table>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>
<?php } ?>