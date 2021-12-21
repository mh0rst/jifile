/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
function CheckVer() {
	this.url = 'index.php?option=com_jifile&task=checkVer';
	this.string = new Array();
	
	this.loadString = function(string) {
		this.string = string;
	};
    this.getString = function(key) {
    	if(this.string[key]) 
    		return this.string[key];
    	return false;
    };
	this.onComplete = function(data) {
		arrayDaJSON = JSON.decode(data);

		$('checkVer').removeClass('ajax-loading1');
		
		if(arrayDaJSON && arrayDaJSON['state'] == '1') {
			link = ' <a target="_blank" href="http://www.isapp.it/download-jifile.html">JiFile '+arrayDaJSON['version']+'</a>';
			$('checkVer').addClass('alert alert-success');
			img = '<div class="result_img success_img" title="'+this.string['success']+'"/>';
			msg = this.string['update']+link+img;
			imgUpdate = new Element('img', {src: 'components/com_jifile/images/icon_refresh.png', alt: 'Update'});
			$('cpanel-panel-update').set('html', $('cpanel-panel-update').get('html')+' <span style="background-color: #89D64F;color: #000000;padding-right: 7px;">Update</span>').adopt(imgUpdate);
		} else {
			$('checkVer').addClass('alert alert-info');
			msg = this.string['no_update'];
		}
		
		$('checkVer').set('html', msg);
	};
	this.onRequest = function(i) {
		$('checkVer').set('html', this.string['search_update']+'...').addClass('ajax-loading1');//Ricerca Aggiornamento
	};
}

window.addEvent('domready', function() {
	startCheckVer = function() {
		if(objVer.getString('search_update') == false) {
			setTimeout('startCheckVer', 200);
			return false;
		}
		new Request({
		    url: objVer.url,
		    onRequest: function() {
				objVer.onRequest();
			},
			onComplete: function(data) {
				objVer.onComplete(data);
			}
		}).send();
	};
	
	startCheckVer();
});