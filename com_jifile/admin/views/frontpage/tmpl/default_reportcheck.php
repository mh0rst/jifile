<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access'); ?>

<table class="adminlist table table-hover">
  <caption>Report check system</caption>
  <thead>
    <tr>
      <th>Label</th>
		<th>Check</th>
		<th>Requirements</th>
		<th class="center">Info</th>
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
				$rowClass = '';
				if($obj->getCheck()) {
					$class = 'ok';
					$span = "label-success";
				} else {
					$class = 'nook';
					$span = "label-warning";
					$nook = true;
					$rowClass = 'class="error"';
				}
				?>
				<tr <?php echo $rowClass ?>>
					<td><?php echo $obj->getLabel() ?></td>
					<td class="<?php echo $class ?> center"><span class="label <?php echo $span ?>"><?php echo $obj->getMessage() ?></span></td>
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