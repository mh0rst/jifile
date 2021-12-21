<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access'); ?>
<script type="text/javascript">
	var objVer = new CheckVer();
	string = new Array();
	string['error'] = '<?php echo JText::_( 'Error' ); ?>';
	string['success'] = '<?php echo JText::_( 'Success' ); ?>';
	string['search_update'] = '<?php echo JText::_( 'SEARCH_UPDATE' ); ?>';
	string['no_update'] = "<?php echo JText::_( 'NO_UPDATE' ); ?>";
	string['update'] = "<?php echo JText::_( 'COM_UPDATE' ); ?>";
	objVer.loadString(string);
	window.addEvent('domready', function(){ new Accordion($$('.panel h3.jpane-toggler'), $$('.panel div.jpane-slider'), {onActive: function(toggler, i) { toggler.addClass('jpane-toggler-down'); toggler.removeClass('jpane-toggler'); },onBackground: function(toggler, i) { toggler.addClass('jpane-toggler'); toggler.removeClass('jpane-toggler-down'); },duration: 300,opacity: false,alwaysHide: true}); });
</script>

<?php 
$ahrefPref = '<a rel="{handler: \'iframe\', size: {x: 875, y: 550}, onClose: function() {}}" href="index.php?option=com_config&amp;view=component&amp;component=com_jifile&amp;path=&amp;tmpl=component" class="modal">'.JText::_('Configure').'</a>';
?>

<table width="100%" border="0" class="adminlist table table-hover">
	<tr>
		
		<td width="55%" valign="top" align="left" id="ifile">
			<?php
				echo JHtml::_('sliders.start','panel-sliders',array('useCookie'=>'1'));
				echo JHtml::_('sliders.panel', 'Report check system <span id="attention_report_check">'.JText::_('WARNING_REPORT_CHECK').'</span>', 'cpanel-panel-reportcheck');
			?>
			<table class="adminlist" cellspacing="1">
				<thead>
					<tr>
						<th>Label</th>
						<th>Check</th>
						<th>Requirements</th>
						<th>Info</th>
						<th>Use & WebSite</th>
					</tr>
				</thead>
				<tbody>
				<?php 
					$nook = false;
					foreach($this->reportCheck as $caption => $check) {
						?>
						<tr><td style="text-align:center;background:#F9F9F9;" colspan="6"><strong><?php echo $caption ?></strong></td></tr>
						<?php 
						foreach($check as $obj){
							if($obj->getCheck()) {
								$class = 'ok'; 
							} else {
								$class = 'nook';
								$nook = true;
							}
							?>
							<tr>
								<td><?php echo $obj->getLabel() ?></td>
								<td class="<?php echo $class ?> center"><?php echo $obj->getMessage() ?></td>
								<td><?php echo $obj->getRequire() ?></td>
								<td><?php echo $obj->getInfo() ?></td>
								<td>
									<?php echo $obj->getInfoUse() ?><br /><br />
									WebSite: <a href=<?php echo $obj->getSite() ?> target="_blank"><?php echo $obj->getSite() ?></a>
								</td>
							</tr>
							<?php  
						}			
					}
				?>
				</tbody>
			</table>			
			<?php 
			echo JHtml::_('sliders.end');
			if($nook) {
				?>
				<style type="text/css">
					#attention_report_check {
						display: inline;
					}
				</style>
				<?php 
			}
			?>
		</td>
	</tr>
</table>
<div id="fb-root"></div>
<?php echo jifilehelper::getFirma(); ?>