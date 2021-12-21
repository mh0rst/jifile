/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
function CheckIndex() {
	this.totFile = 0;
	this.url = 'index.php?option=com_jifile&task=lucene.checkIndexAjax';
	this.manualIndexUrl = 'index.php?option=com_jifile&task=lucene.indexing&view=manualindex&tmpl=component';
	this.checkindex = new Array();
	
	this.init = function() {
		this.setTotFile();
	}
	this.getData = function(i) {
		return {'file' : $('cb'+i).value};
	}
	this.setTotFile = function() {
		this.totFile = $(document.adminForm).getElements('input[name^=file]').length;
	}
	this.onComplete = function(data, i, stop, onload) {
		arrayDaJSON = JSON.decode(data);

		a = null;
		if(arrayDaJSON['result'] == 'false') {
			link = this.manualIndexUrl+'&filename='+escape($('cb'+i).value)+'&id='+i;
			img = jQuery(document.createElement('img')).attr({src: 'components/com_jifile/images/publish_x.png', title: arrayDaJSON['text']});//new Element('img', {src: 'components/com_jifile/images/publish_x.png', title: arrayDaJSON['text']});
			imgIns = jQuery(document.createElement('img')).attr({src: 'components/com_jifile/images/filesave.png', title: arrayDaJSON['text_man']});//new Element('img', {src: 'components/com_jifile/images/filesave.png', title: arrayDaJSON['text_man']});
			a = jQuery(document.createElement('a')).attr({href: link, onclick: 'jQuery.colorbox({ href: this.href, iframe: true, innerWidth: 787, innerHeight: 892 }); return false;'});//new Element('a', {href: link, onclick: 'jQuery.colorbox({ href: this.href, iframe: true, innerWidth: 787, innerHeight: 892 }); return false;'});
			a.append(imgIns)
		} else {
			img = jQuery(document.createElement('img')).attr({src: 'components/com_jifile/images/tick.png', title: arrayDaJSON['text']});//new Element('img', {src: 'components/com_jifile/images/tick.png', title: arrayDaJSON['text']});
		}
		jQuery(('#indexed'+i)).removeClass('ajax-loading1').append(img);
		if(a) { jQuery(('#indexed'+i)).append(a); }
		
		if(onload == undefined) {
			i++;
		} else {
			onload++;
			if(this.checkindex[onload]) {
				i = this.checkindex[onload];
			} else {
				stop = true;
			}
		}
		
		if(!stop && i < this.totFile) {
			startCheck(i, stop, onload);
		}
	}
	this.onRequest = function(i) {
		jQuery(('#indexed'+i)).html('').addClass('ajax-loading1');
	}
	
	this.init();
}

window.addEvent('domready', function() {
	
    jQuery(".cb-enable").click(function(){
        var parent = jQuery(this).parents('.switch');
        jQuery('.cb-disable',parent).removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery('.checkbox',parent).attr('checked', true);
        window.location = "index.php?option=com_jifile&task=filesystem.debug&dbg=1&dir="+getURLParameter('dir');
    });
    jQuery(".cb-disable").click(function(){
        var parent = jQuery(this).parents('.switch');
        jQuery('.cb-enable',parent).removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery('.checkbox',parent).attr('checked', false);
        window.location = "index.php?option=com_jifile&task=filesystem.debug&dbg=0&dir="+getURLParameter('dir');
    });
	
	var objCheck = new CheckIndex();
	
	startCheck = function(i, stop, onload) {
		if(!(objCheck.totFile > 0)) {
			return false;
		}
		new Request({
			    url: objCheck.url,
			    data: objCheck.getData(i),
			    onRequest: function() {
					objCheck.onRequest(i);
				},
				onComplete: function(data) {
					objCheck.onComplete(data, i, stop, onload);
				}
		}).send();
	}
	
	if(objCheck.totFile > 0) {
		$$('#ifile tr').addEvent('click',function(event) {
			if ((event.target || event.srcElement).type !== 'checkbox' && (event.target || event.srcElement).tagName != 'IMG') {
				checkb = this.getElement('input[name^=file]');
				if(checkb) {
					checkb.click();
				}
			}
		});
	
		$('refreshIndex').addEvent('click',function(event) {
			startCheck(0, false);
		});
		
		$(document.adminForm).getElements('input[class^=checkIndex]').each(function(el) {
			start = el.className.substring(10);
			objCheck.checkindex.push(start);
		});
		if(objCheck.checkindex[0]) {
			startCheck(objCheck.checkindex[0], false, 0);
		}
	}
});

function getURLParameter(name) {
    val =  decodeURI(
        (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
    );
    return (val == null || val == 'null') ? '' : val; 
}