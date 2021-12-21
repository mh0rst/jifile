/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
function Indexing() {

    this.indexUrl = 'index.php?option=com_jifile&task=lucene.indexAjax';
    this.updateUrl = 'index.php?option=com_jifile&task=lucene.updateAjax';
    this.arrayAjax = new Array();
    this.arrayTimer = new Array();
    this.pw = null;
    this.current = new Array();
    this.string = new Array();
	this.stop = false;
	this.total = 0;

    this.init = function() {
        this.setPW();
    };
    this.getData = function() {
		return { 'file' : this.current['value'], 'div': this.current['id'], 'cb': this.current['cb'] };
	};
    this.setCurrent = function(i, divId, value, cb, size, shortname) {
        this.current = new Array();
        this.current['i'] = i;
        this.current['id'] = divId;
        this.current['value'] = value;
        this.current['cb'] = cb;
        this.current['size'] = size;
        str_max = 52;
		if (shortname.length > str_max ) {
			start = shortname.length-str_max;
			shortname = shortname.substr(start,str_max);
			shortname = '...'+shortname;
		}
        this.current['shortname'] = shortname;
    };
    this.setTotal = function(tot) {
    	this.total = tot;
    };
    this.setPW = function() {
        this.pw = (window.opener == null) ? window.parent : window;
    };
    this.loadString = function(string) {
		this.string = string;
	}
    this.getString = function(key) {
    	if(this.string[key]) 
    		return this.string[key];
    	return false;
    }
    this.formatTimer = function(s) {
        time = '';
        m = h = d = 0;
        if(s >= 86400) {
            d = Math.floor(s/86400);
            s = (s%86400);
        }
        if(s >= 3600) {
            h = Math.floor(s/3600);
            s = (s%3600);
        }
        if(s >= 60) {
            m = Math.floor(s/60);
            s = (s%60);
        }
        s = Math.floor(s);

        if(d > 0) {
            time = d+'d ';
        }
        if(s < 10) {
            s = '0'+s;
        }
        if(m < 10) {
            m = '0'+m;
        }
        if(h < 10) {
            h = '0'+h;
        }
        time += h+':'+m+':'+s;
        return time;
    }
    this.onRequest = function() {
        divId = this.current['id'];
        div = new Element('div', {id: divId, "class": 'result'});
        span = new Element('span', {"class": 'result_msg ajax-loading'});
        spanT = new Element('span', {"class": 'timer', id: divId+'timer'});
        div.innerHTML = '<b>'+this.current['shortname']+'</b><span class="size small">('+this.current['size']+')</span><br/>';
        span.innerHTML = this.string['loading']+'...';
        div.adopt(span);
        div.adopt(spanT);
        $('result').adopt(div);
        startTime(0, divId+'timer');
    }
    this.onComplete = function(data, i) {
        arrayDaJSON = JSON.decode(data);
        if(!arrayDaJSON) {
        	arrayDaJSON = new Array();
        	arrayDaJSON['div'] = this.current['id'];
        	arrayDaJSON['result'] = 'false';
        	arrayDaJSON['cb'] = this.current['cb'];
        	arrayDaJSON['file'] = this.current['value'];
        	arrayDaJSON['message'] = this.string['unknown_error'];
        }
        
        divId = arrayDaJSON['div'];
        this.stopper(divId+'timer');

        if(this.arrayAjax[divId]) {
            delete this.arrayAjax[divId];
        }

        if(arrayDaJSON['result'] == 'false') {
            $(divId).addClass('error alert alert-error');
            divUpdate = divId+'update';
            if(!$(divUpdate)) {
                span = new Element('span', {"class": 'update icon', id: divUpdate, onClick: 'update(\''+escape(arrayDaJSON['file'])+'\', \''+divId+'\', '+arrayDaJSON['cb']+')'});
                span.innerHTML = this.string['update']+'!';
                $(divId).adopt(span);
            } else {
                $(divUpdate).style.display = 'inline';
            }
            img = '<div class="result_img error_img" title="'+this.string['error']+'"/>';
        } else {
            $(divId).addClass('success alert alert-success');
            img = '<div class="result_img success_img" title="'+this.string['success']+'"/>';
            if(arrayDaJSON['cb']) {
	        	this.pw.startCheck(arrayDaJSON['cb'], true);
	        }
        }
        
        $$('#'+divId+' .result_msg').removeClass('ajax-loading').set('html', arrayDaJSON['message']+img);
        if(i >= 0) {
        	startIndexing(this.current['i']+1, this.stop);
        }
    };
    this.requestUpdate = function() {
    	divId = this.current['id'];
    	divUpdate = divId+'update';
		$(divUpdate).style.display = 'none';
		$(divId).removeClass('error');
		$$('#'+divId+' .result_msg').addClass('ajax-loading').set('html', this.string['loading']+'... &nbsp;&nbsp;&nbsp;');
		startTime(0, divId+'timer');
    };
    this.updateTime = function(cc, idTimer) {
        if(!cc) {
            cc = 0;
        }
        cc++;
        document.getElementById(idTimer).innerHTML=this.formatTimer(cc);
        return cc;
    }
    this.stopAll = function(noCurrent) {
        this.stop = true;
    	if(noCurrent) {
    		img = '<div class="result_img alert_img" title="'+this.string['canceled']+'"/>';
    		div = new Element('div', {id: divId, "class": 'result notice alert'});
            span = new Element('span', {"class": 'result_msg'}).set('html', this.string['op_canceled']+img);
            div.innerHTML = '<b>'+this.current['shortname']+'</b><br/>';
            div.adopt(span);
            $('result').adopt(div);
            startIndexing(this.current['i']+1, true);
    	}
    }
    this.stopper = function(idTimer) {
        clearTimeout(this.arrayTimer[idTimer]);
    };

    this.init();
}

i = 0;
var objIndexing = new Indexing();
//on dom ready...
jQuery(document).ready(function() {
    jQuery('#stopAll').click(function() {
    	jQuery(this).attr("disabled", "disabled");
    	objIndexing.stopAll();
    });
    
    _pw = objIndexing.pw;
    aform = _pw.document.adminForm;

    startIndexing = function(i, stop) {
    	if(!arrayFile[i]) {
    		jQuery('#indexingProgress').removeClass('active').removeClass('progress-striped').addClass('progress-success');
    		return false;
    	}
    	if(objIndexing.getString('loading') == false) {
    		setTimeout('startIndexing('+i+')', 200);
    		return false;
    	}
    	jQuery('#numfile').html(i+1);
    	el = arrayFile[i];
        divId = 'result'+i;
        objIndexing.setCurrent(i, divId, el.filename, el.key, el.size, el.shortname);
        perc = ((i+1)*100)/objIndexing.total;
		if(stop) {
			jQuery('#indexingProgress .bar').css('width',perc+'%');
			objIndexing.stopAll(true);
        } else {
			objIndexing.arrayAjax[divId] = new Request({
				url: objIndexing.indexUrl,
				data: objIndexing.getData(),
				onRequest: function() {
					objIndexing.onRequest();
				},
				onComplete: function(data) {
					jQuery('#indexingProgress .bar').css('width',perc+'%');
					objIndexing.onComplete(data, i);
				}
			}).send();
		}
    };
    
    startTime = function(cc, idTimer) {
    	cc = objIndexing.updateTime(cc, idTimer);
    	objIndexing.arrayTimer[idTimer] = setTimeout('startTime('+cc+', \''+idTimer+'\')',1000);
    };
    update = function(file, divId, cb) {
    	objIndexing.setCurrent(null, divId, unescape(file), cb);
    	objIndexing.arrayAjax[divId] = new Request({
    		url: objIndexing.updateUrl,
			data: objIndexing.getData(),
			onRequest: function() {
    			objIndexing.requestUpdate();
			},
			onComplete: function(data) {
				objIndexing.onComplete(data);
			}
		}).send();
    };
    
    arrayFile = new Array();
    jQuery.ajax({
		type: 'POST',
		url: 'index.php?option=com_jifile&task=filesystem.getFile2index',
		data: jQuery(aform).serialize(),
		success: function(data) {
			jQuery.each(data, function(index) {
				arrayFile.push(data[index]);
				i++;
			});
			if(i == 0) {
				jQuery('#result').addClass('error alert alert-error').html(objIndexing.getString('no_file_selected'));
			} else {
				objIndexing.setTotal(i);
				jQuery('#totfile').html(i);
				jQuery('#indexingProgress').show();
				startIndexing(0);
			}
		},
		dataType: 'json'
	});
});