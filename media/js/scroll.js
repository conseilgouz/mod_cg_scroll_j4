/**
* CG Scroll - Joomla Module 
* Version			: 4.1.1
* Package			: Joomla 3.10.x - 4.0
* copyright 		: Copyright (C) 2021 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
jQuery(document).ready(function($) {
	$('.cg_scroll').each(function() {
		var $this = $(this);
		var myid = $this.attr("data");;
		if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
			console.error('Joomla.getOptions not found!\nThe Joomla core.js file is not being loaded.');
			return false;
		}
		var me = "#cg_scroll_"+myid+" ";
		var options = Joomla.getOptions('mod_cg_scroll_'+myid);
		if (typeof options === 'undefined' ) { // cache Joomla problem
			request = {
				'option' : 'com_ajax',
				'module' : 'cg_scroll',
				'data'   : 'param',
				'id'     : myid,
				'format' : 'raw'
				};
			jQuery.ajax({
				type   : 'POST',
				data   : request,
				success: function (response) {
					options = JSON.parse(response);
					go_scroll(myid,me,options);
					return true;
				}
			});
		}
		if (typeof options === 'undefined' ) {return false}
		
		go_scroll(myid,me,options);
		
	});
	function go_scroll(myid,me,options) {
		$(me + '#sfdmarqueecontainer').css("height",options.height);
		if (options.direction == 1) { // vertical scroll
			$(me + 'ul.cg-scroll-items li').css("width","auto");
		} else { // horizontal scroll
			$(me + 'ul.cg-scroll-items').css({	float: "left",height:"100%","list-style-type" : "circle"})
		    if (options.width == 0) {
				$(me + 'ul.cg-scroll-items li').css({float: "left",height:"100%",width:"auto",margin:"1em"})
			} else {
				$(me + 'ul.cg-scroll-items li').css({float: "left",height:"100%",width: options.width,margin:"1em"})
			}
			$total_width = 0;
			$(me + 'ul.cg-scroll-items li').each(function() {
				$total_width += parseInt($(this).outerWidth(true)); // add list-style size
			})
			$total_width = $total_width  / 2; // on a doublé les articles
			$(me + '#vmarquee').css({float: "left",width: $total_width,height: options.height});
			$(me + 'ul.cg-scroll-items').css({width: $total_width * 2}); 
			
		}
		sens[myid] = 0;
		marqueespeed[myid]=options.speed;
		copyspeed[myid]=parseInt(marqueespeed[myid]);
		pauseit[myid]=options.pause;
		delay[myid]=parseInt(options.delay);
		actualheight[myid]='';
		actualwidth[myid]='';
		lesoptions[myid] = options;
		if (options.direction == 1) {
			initializemarqueeup(myid);
		} else {
			initializemarqueeleft(myid);
		}
		$(me +"#toDirection").show();
		$(me + ".icon-dir-up").hide();
		$(me + ".icon-dir-left").hide();
		if (options.direction == 1) {
			$(me + ".icon-dir-down").show();
		} else {
			$(me + ".icon-dir-right").show();
		}
		$(me + ".icon-dir-down").click(function() {
			sens[myid] = 1;
			clearInterval(lefttime[myid]);
			lefttime[myid] = setInterval(function() { scrollmarquee(myid); },40);
			$(me + ".icon-dir-down").hide();
			$(me + ".icon-dir-up").show();
			return false;
		});
		$(me + ".icon-dir-up").click(function() {
			sens[myid] = 0;
			clearInterval(lefttime[myid]);
			lefttime[myid] = setInterval(function() { scrollmarquee(myid); },40);
			$(me + ".icon-dir-up").hide();
			$(me + ".icon-dir-down").show();
			return false;
		});	
		jQuery(me + ".icon-dir-left").click(function() {
			sens[myid] = 0;
			clearInterval(lefttime[myid]);
			lefttime[myid] = setInterval(function() { leftmarquee(myid); },15);
			jQuery(me + ".icon-dir-left").hide();
			jQuery(me + ".icon-dir-right").show();
			return false;
		});
		jQuery(me + ".icon-dir-right").click(function() {
			sens[myid] = 1;
			clearInterval(lefttime[myid]);
			lefttime[myid] = setInterval(function() { leftmarquee(myid); },15);
			jQuery(me + ".icon-dir-right").hide();
			jQuery(me + ".icon-dir-left").show();
			return false;
		});
	};
});