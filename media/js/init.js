/**
* CG Scroll - Joomla Module 
* Version			: 4.1.2
* Package			: Joomla 3.10.x - 4.0
* copyright 		: Copyright (C) 2021 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
var cross_marquee = {};
var sens = {};
var copyspeed = {};
var actualwidth = {};
var actualheight = {};
var marqueeheight = {};
var marqueewidth = {};
var marqueespeed = {};
var lefttime = {};
var pauseit = {};
var delay = {};
var ids = {};
var lesoptions = {};
// up/down scroll
function scrollmarquee(id){
	if (sens[id] > 0) {
		if (parseInt(cross_marquee[id].css("top"))< 0) {
			cross_marquee[id].css("top",(parseInt(cross_marquee[id].css("top"))+parseInt(copyspeed[id]))+"px")
		} else {
			cross_marquee[id].css("top",parseInt(actualheight[id]*(-1)+8)+"px")
		}
	} else {
		if (parseInt(cross_marquee[id].css("top"))>(actualheight[id]*(-1)+8))  {
			cross_marquee[id].css("top",(parseInt(cross_marquee[id].css("top"))-parseInt(copyspeed[id]))+"px")
		} else {
			cross_marquee[id].css("top","0px")
		}
	}
}
// left/right scroll
function leftmarquee(id){
	if (sens[id] > 0) {
			if (parseInt(cross_marquee[id].css("left")) < 0) 
				cross_marquee[id].css("left",(parseInt(cross_marquee[id].css("left"))+parseInt(copyspeed[id])))
			else
				cross_marquee[id].css("left", (actualwidth[id]*(-1))/2)
		} 
	else {
		if (parseInt(cross_marquee[id].css("left")) > (actualwidth[id]*(-1) /2) )
			cross_marquee[id].css("left",(parseInt(cross_marquee[id].css("left"))-parseInt(copyspeed[id])))
		else
			cross_marquee[id].css("left",0);
		}
}
function initializemarqueeup(id){
	var me = "#cg_scroll_"+id+" ";	
	cross_marquee[id]=jQuery(me + "#vmarquee");
	cross_marquee[id].css("top","0");
	cont = jQuery(me + "#sfdmarqueecontainer");
	marqueeheight[id]=cont.height();
	actualheight[id]=(cross_marquee[id].height())/2;
	if (window.opera || navigator.userAgent.indexOf("Netscape/7")!=-1){ //if Opera or Netscape 7x, add scrollbars to scroll and exit
		cross_marquee[id].css("height",marqueeheight[id]+"px");
		cross_marquee[id].css("overflow","scroll");
		return
	}
	setTimeout(function () {
		lefttime[id] = setInterval(function() { scrollmarquee(id); },40)
		}, delay[id]);
}
function initializemarqueeleft(id){
	var me = "#cg_scroll_"+id+" ";
	cross_marquee[id]=jQuery(me + "#vmarquee");	
	cross_marquee[id].css("left",0);
	marqueewidth[id]=parseInt(jQuery(me + "#sfdmarqueecontainer").outerWidth(true));
	actualwidth[id]=parseInt(jQuery(me + "ul.cg-scroll-items").outerWidth(true)); 
	cross_marquee[id].css("width",marqueewidth[id]);
	if (window.opera || navigator.userAgent.indexOf("Netscape/7")!=-1){ //if Opera or Netscape 7x, add scrollbars to scroll and exit
		cross_marquee[id].css("height",marqueeheight[id]+"px");
		cross_marquee[id].css("overflow","scroll");
		return
	}
	setTimeout(function () {
			lefttime[id] = setInterval(function() { leftmarquee(id); },15)
			}, delay[id]);
} 
