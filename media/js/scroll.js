/**
* CG Scroll - Joomla Module 
* Version			: 4.2.3
* Package			: Joomla 3.10.x - 4.x - 5.x
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
var cgscroll_options = [],	container = [], me_up = [],	me_down = [],me_left= [],me_right =[];
var cross_marquee = [],sens = [], copyspeed = [],actualwidth = [],actualheight = [];
var marqueeheight = [],marqueewidth = [],marqueespeed = [],lefttime = [],pauseit = [];
var delay = [],ids = [], items_ul = [];

document.addEventListener('DOMContentLoaded', function() {
	mains = document.querySelectorAll('.cg_scroll');
	for(var i=0; i<mains.length; i++) {
		myid = mains[i].getAttribute("data");
		if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
			console.error('Joomla.getOptions not found!\nThe Joomla core.js file is not being loaded.');
			return false;
		}
		me = "#cg_scroll_"+myid+" ";
		cgscroll_options[myid] = Joomla.getOptions('mod_cg_scroll_'+myid);
		if (typeof cgscroll_options[myid] === 'undefined' ) { // cache Joomla problem
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
					cgscroll_options[myid] = JSON.parse(response);
					go_scroll(myid,me);
					return true;
				}
			});
		}
		if (typeof cgscroll_options[myid] === 'undefined' ) {return false}
		
		go_scroll(myid,me);
		
		}
})
function go_scroll(myid,me) {
	container[myid] = document.querySelector(me + '#sfdmarqueecontainer');
	cross_marquee[myid] = document.querySelector(me + '#vmarquee');
	items_ul[myid] = document.querySelector(me + 'ul.cg-scroll-items');

	container[myid].style.height = cgscroll_options[myid].height+"px";
	items = document.querySelectorAll(me + 'ul.cg-scroll-items li');
	$total_width = 0;
	for(var i=0; i<items.length; i++) {
		if (cgscroll_options[myid].direction == 1) { // vertical scroll
			items[i].style.width = "auto";
		} else { // horizontal scroll
			items[i].style.float = "left";
			items[i].style.height = "100%";
			items[i].style.liststyleType = "circle";
		    if (cgscroll_options[myid].width == 0) {
				items[i].style.width = "auto";
				items[i].style.margin = "12px";
			} else {
				items[i].style.width = cgscroll_options[myid].width;
				items[i].style.margin = "12px";
			}
			$total_width += parseInt(items[i].clientWidth) + 24; // add margin
		}
	}
	if (cgscroll_options[myid].direction != 1) { // horizontal scroll
		$total_width = $total_width  / 2; // on a doublé les articles
		cross_marquee[myid].style.float = "left";
		cross_marquee[myid].style.width = $total_width+"px";
		// container[myid].style.width = $total_width+"px";
		cross_marquee[myid].style.height =  cgscroll_options[myid].height+"%";
		items_ul[myid].style.width = ($total_width * 2) + "px"; 
			
	}
	sens[myid] = 0;
	marqueespeed[myid]=cgscroll_options[myid].speed;
	copyspeed[myid]=parseInt(marqueespeed[myid]);
	pauseit[myid]=cgscroll_options[myid].pause;
	delay[myid]=parseInt(cgscroll_options[myid].delay);
	actualheight[myid]='';
	actualwidth[myid]='';
	if (cgscroll_options[myid].direction == 1) {
		initializemarqueeup(myid);
	} else {
		initializemarqueeleft(myid);
	}
	document.querySelector(me +"#toDirection").style.display = 'block';
	me_up[myid] = document.querySelector(me + ".icon-dir-up");
	me_down[myid] = document.querySelector(me + ".icon-dir-down");
	me_left[myid] = document.querySelector(me + ".icon-dir-left");
	me_right[myid] = document.querySelector(me + ".icon-dir-right");
	if (me_up[myid])	{
		me_up[myid].style.display = "none";
		me_up[myid].addEventListener("click",function() {
			sens[myid] = 0;
			clearInterval(lefttime[myid]);
			lefttime[myid] = setInterval(function() { scrollmarquee(myid); },40);
			me_up[myid].style.display = "none";
			me_down[myid].style.display = "block";
			return false;
		});	
		me_down[myid].addEventListener("click",function() {
			sens[myid] = 1;
			clearInterval(lefttime[myid]);
			lefttime[myid] = setInterval(function() { scrollmarquee(myid); },40);
			me_down[myid].style.display = "none";
			me_up[myid].style.display = "block";
			return false;
		});
	}
	if (me_left[myid]) {
		me_left[myid].style.display = "none";
		me_left[myid].addEventListener("click",function() {
			sens[myid] = 0;
			clearInterval(lefttime[myid]);
			lefttime[myid] = setInterval(function() { leftmarquee(myid); },15);
			me_left[myid].style.display = "none";
			me_right[myid].style.display = "block";
			return false;
		});
		me_right[myid].addEventListener('click', function() {
			sens[myid] = 1;
			clearInterval(lefttime[myid]);
			lefttime[myid] = setInterval(function() { leftmarquee(myid); },15);
			me_left[myid].style.display = "block";
			me_right[myid].style.display = "none";
			return false;
		});
	}
	if (cgscroll_options[myid].direction == 1) {
		document.querySelector(me + ".icon-dir-down").style.display = "block";
	} else {
		document.querySelector(me + ".icon-dir-right").style.display = "block";
	}
}
// up/down scroll
function scrollmarquee(id){
	if (sens[id] > 0) {
		if (parseInt(cross_marquee[id].style.top)< 0) {
			cross_marquee[id].style.top = (parseInt(cross_marquee[id].style.top)+parseInt(copyspeed[id]))+"px";
		} else {
			cross_marquee[id].style.top = parseInt(actualheight[id]*(-1)+8)+"px";
		}
	} else {
		if (parseInt(cross_marquee[id].style.top)>(actualheight[id]*(-1)+8))  {
			cross_marquee[id].style.top = (parseInt(cross_marquee[id].style.top)-parseInt(copyspeed[id]))+"px";
		} else {
			cross_marquee[id].style.top = "0px";
		}
	}
}
// left/right scroll
function leftmarquee(id){
	if (sens[id] > 0) {
			if (parseInt(cross_marquee[id].style.left) < 0) 
				cross_marquee[id].style.left = (parseInt(cross_marquee[id].style.left)+parseInt(copyspeed[id]))+"px";
			else
				cross_marquee[id].style.left = ((parseInt(actualwidth[id])*(-1))/2)+"px";
		} 
	else {
		if (parseInt(cross_marquee[id].style.left) > (parseInt(actualwidth[id])*(-1) /2) )
			cross_marquee[id].style.left = (parseInt(cross_marquee[id].style.left)-parseInt(copyspeed[id]))+"px";
		else
			cross_marquee[id].style.left = "0px";
		}
}
function initializemarqueeup(id){
	cross_marquee[id].style.top = "0";
	marqueeheight[id]=parseInt(container[id].style.height);
	actualheight[id]=parseInt(cross_marquee[id].offsetHeight)/2;
	if (window.opera || navigator.userAgent.indexOf("Netscape/7")!=-1){ //if Opera or Netscape 7x, add scrollbars to scroll and exit
		cross_marquee[id].style.height = marqueeheight[id]+"px";
		cross_marquee[id].style.overflow = "scroll";
		return
	}
	setTimeout(function () {400
		lefttime[id] = setInterval(function() { scrollmarquee(id); },40)
		}, delay[id]);
}
function initializemarqueeleft(id){
	cross_marquee[id].style.left = "0px";
	marqueewidth[id]=parseInt(container[id].offsetWidth);
	actualwidth[id]=parseInt(items_ul[id].offsetWidth)+"px"; 
	cross_marquee[id].style.width = marqueewidth[id]+"px";
	if (window.opera || navigator.userAgent.indexOf("Netscape/7")!=-1){ //if Opera or Netscape 7x, add scrollbars to scroll and exit
		cross_marquee[id].style.height = marqueeheight[id]+"px";
		cross_marquee[id].style.overflow = "scroll";
		return
	}
	setTimeout(function () {
			lefttime[id] = setInterval(function() { leftmarquee(id); },15)
			}, delay[id]);
} 