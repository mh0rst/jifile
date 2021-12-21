<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access'); ?>

<div id="donate">
	<div style="text-align: center;">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="paypal">
			<fieldset>
				<legend><?php echo JText::_('WHY_DONATION'); ?></legend>
				<label><?php echo JText::_('DESC_DONATION'); ?></label><br />
				<input type="hidden" name="cmd" value="_donations" /> 
				<input type="hidden" name="undefined_quantity" value="0" /> 
				<input type="hidden" name="item_name" value="Donation for JiFile" /> 
				<input type="hidden" name="item_number" value="jifile" /> 
				<input type="hidden" name="business" value="2XYDWYB8NHUF6" /> 
				<input type="hidden" name="lc" value="<?php echo JText::_('LOCAL_DONATE'); ?>">
				<input style="text-align: right;" type="text" name="amount" value="5" size="4" maxlength="10" /> 
				<select name="currency_code"> <option value="USD">USD</option> 
					<option selected="selected" value="EUR">EUR</option> 
					<option value="GBP">GBP</option> 
					<option value="CHF">CHF</option> 
					<option value="AUD">AUD</option> 
					<option value="HKD">HKD</option> 
					<option value="CAD">CAD</option> 
					<option value="JPY">JPY</option> 
					<option value="NZD">NZD</option> 
					<option value="SGD">SGD</option> 
					<option value="SEK">SEK</option> 
					<option value="DKK">DKK</option> 
					<option value="PLN">PLN</option> 
					<option value="HUF">HUF</option> 
					<option value="CZK">CZK</option> 
					<option value="ILS">ILS</option> 
					<option value="MXN">MXN</option> 
				</select>
				<br/>
				<input type="image" name="submit" class="btn" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" alt="PayPal - Il sistema di pagamento online pi&ugrave; facile e sicuro!" /> 
				<img src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" alt="" width="1" height="1" border="0" />
			</fieldset>
		</form>
		<span style="font-size: 12px;"><?php echo JText::_('THX_DONATION'); ?></span>
	</div>
</div>