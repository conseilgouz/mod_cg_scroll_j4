/**
* CG Scroll - Joomla Module 
* Version			: 4.2.6
* Package			: Joomla 3.10.x - 4.x - 5.x
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
var cgscroll = [];

document.addEventListener('DOMContentLoaded', function() {
	mains = document.querySelectorAll('.cg_scroll');
	for(var i=0; i<mains.length; i++) {
		myid = mains[i].getAttribute("data");
		if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
			console.error('Joomla.getOptions not found!\nThe Joomla core.js file is not being loaded.');
			return false;
		}
		me = "#cg_scroll_"+myid+" ";
		cgscroll_options = Joomla.getOptions('mod_cg_scroll_'+myid);
		if (typeof cgscroll_options === 'undefined' ) { // cache Joomla problem
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
					cgscroll_options = JSON.parse(response);
					cgscroll[myid] = new CGScroll(myid,me,cgscroll_options);
					cgscroll[myid].go_scroll();
					return true;
				}
			});
		}
		if (typeof cgscroll_options === 'undefined' ) {return false}
		
		cgscroll[myid] = new CGScroll(myid,me,cgscroll_options);
		cgscroll[myid].go_scroll(myid);
		
	}
});
function CGScroll(myid,me,options) {
	this.options = options;
	this.myid = myid;
	this.me = me;
	this.container = document.querySelector(this.me + '#sfdmarqueecontainer');
	this.cross_marquee = document.querySelector(this.me + '#vmarquee');
	this.items_ul = document.querySelector(this.me + 'ul.cg-scroll-items');
	this.container.style.height = this.options.height+"px";
	this.marqueespeed = this.options.speed;
	this.copyspeed=parseInt(this.marqueespeed);
	this.pauseit=this.options.pause;
	this.delay=parseInt(this.options.delay);
	this.actualheight='';
	this.actualwidth='';
	this.sens = 0;
	this.lefttime = 0;
	document.querySelector(this.me +"#toDirection").style.display = 'block';
	this.me_up = document.querySelector(this.me + ".icon-dir-up");
	this.me_down = document.querySelector(this.me + ".icon-dir-down");
	this.me_left = document.querySelector(this.me + ".icon-dir-left");
	this.me_right = document.querySelector(this.me + ".icon-dir-right");
	
	items = document.querySelectorAll(me + 'ul.cg-scroll-items li');
	$total_width = 0;
	for(var i=0; i<items.length; i++) {
		if (this.options.direction == 1) { // vertical scroll
			items[i].style.width = "auto";
		} else { // horizontal scroll
			items[i].style.float = "left";
			items[i].style.height = "100%";
			items[i].style.liststyleType = "circle";
		    if (this.options.width == 0) {
				items[i].style.width = "auto";
				items[i].style.margin = "12px";
			} else {
				items[i].style.width = this.options.width;
				items[i].style.margin = "12px";
			}
			$total_width += parseInt(items[i].clientWidth) + 24; // add margin
		}
	}
	if (this.options.direction != 1) { // horizontal scroll
		$total_width = $total_width  / 2; // on a doublé les articles
		this.cross_marquee.style.float = "left";
		this.cross_marquee.style.width = $total_width+"px";
		this.cross_marquee.style.height =  this.options.height+"%";
		this.items_ul.style.width = ($total_width * 2) + "px"; 
	}
	
}
CGScroll.prototype.go_scroll = function (myid) {
	$this = cgscroll[myid];
	if ($this.options.direction == 1) {
		$this.initializemarqueeup(myid);
	} else {
		$this.initializemarqueeleft(myid);
	}
	$this.container.addEventListener('mouseover',function() {
			id = this.getAttribute('data');
			cgscroll[id].copyspeed=0;
	})
	$this.container.addEventListener('mouseout',function() {
			id = this.getAttribute('data');
			cgscroll[id].copyspeed=cgscroll[id].marqueespeed;
	})
	
	if ($this.me_up)	{
		$this.me_up.style.display = "none";
		$this.me_up.addEventListener("click",function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgscroll[id];
			$this.sens = 0;
			clearInterval($this.lefttime);
			$this.lefttime = setInterval(function() { $this.scrollmarquee(id); },40);
			$this.me_up.style.display = "none";
			$this.me_down.style.display = "block";
			return false;
		});	
		$this.me_down.addEventListener("click",function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgscroll[id];
			$this.sens = 1;
			clearInterval($this.lefttime);
			$this.lefttime = setInterval(function() { $this.scrollmarquee(id); },40);
			$this.me_down.style.display = "none";
			$this.me_up.style.display = "block";
			return false;
		});
	}
	if ($this.me_left) {
		$this.me_left.style.display = "none";
		$this.me_left.addEventListener("click",function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgscroll[id];
			$this.sens = 0;
			clearInterval($this.lefttime);
			$this.lefttime = setInterval(function() { $this.leftmarquee(id); },25);
			$this.me_left.style.display = "none";
			$this.me_right.style.display = "block";
			return false;
		});
		$this.me_right.addEventListener('click', function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgscroll[id];
			$this.sens = 1;
			clearInterval($this.lefttime);
			$this.lefttime = setInterval(function() { $this.leftmarquee(id); },25);
			$this.me_left.style.display = "block";
			$this.me_right.style.display = "none";
			return false;
		});
	}
	if ($this.options.direction == 1) {
		document.querySelector(me + ".icon-dir-down").style.display = "block";
	} else {
		document.querySelector(me + ".icon-dir-right").style.display = "block";
	}
}
// up/down scroll
CGScroll.prototype.scrollmarquee = function (myid){
	$this = cgscroll[myid];
	if ($this.sens > 0) {
		if (parseInt($this.cross_marquee.style.top)< 0) {
			$this.cross_marquee.style.top = (parseInt($this.cross_marquee.style.top)+parseInt($this.copyspeed))+"px";
		} else {
			$this.cross_marquee.style.top = parseInt($this.actualheight*(-1)+8)+"px";
		}
	} else {
		if (parseInt($this.cross_marquee.style.top)>( $this.actualheight*(-1)+8))  {
			$this.cross_marquee.style.top = (parseInt($this.cross_marquee.style.top)-parseInt($this.copyspeed))+"px";
		} else {
			$this.cross_marquee.style.top = "0px";
		}
	}
}
// left/right scroll
CGScroll.prototype.leftmarquee = function(myid) {
	$this = cgscroll[myid];
	if ($this.sens > 0) {
			if (parseInt($this.cross_marquee.style.left) < 0) 
				$this.cross_marquee.style.left = (parseInt($this.cross_marquee.style.left)+parseInt($this.copyspeed))+"px";
			else
				$this.cross_marquee.style.left = ((parseInt($this.actualwidth)*(-1))/2)+"px";
		} 
	else {
		if (parseInt($this.cross_marquee.style.left) > (parseInt($this.actualwidth)*(-1) /2) )
			$this.cross_marquee.style.left = (parseInt($this.cross_marquee.style.left)-parseInt($this.copyspeed))+"px";
		else
			$this.cross_marquee.style.left = "0px";
		}
}
CGScroll.prototype.initializemarqueeup = function (myid){
	$this = cgscroll[myid];
	$this.cross_marquee.style.top = "0";
	$this.marqueeheight=parseInt($this.container.style.height);
	$this.actualheight=parseInt($this.cross_marquee.offsetHeight)/2;
	if (window.opera || navigator.userAgent.indexOf("Netscape/7")!=-1){ //if Opera or Netscape 7x, add scrollbars to scroll and exit
		$this.cross_marquee.style.height = $this.marqueeheight+"px";
		$this.cross_marquee.style.overflow = "scroll";
		return
	}
	setTimeout(function () {
		$this.lefttime = setInterval(function() { $this.scrollmarquee(myid); },40)
		}, $this.delay);
}
CGScroll.prototype.initializemarqueeleft = function (myid){
	$this = cgscroll[myid];
	$this.cross_marquee.style.left = "0px";
	$this.marqueewidth =parseInt($this.container.offsetWidth);
	$this.actualwidth=parseInt($this.items_ul.offsetWidth)+"px"; 
	$this.cross_marquee.style.width = $this.marqueewidth+"px";
	if (window.opera || navigator.userAgent.indexOf("Netscape/7")!=-1){ //if Opera or Netscape 7x, add scrollbars to scroll and exit
		$this.cross_marquee.style.height = $this.marqueeheight+"px";
		$this.cross_marquee.style.overflow = "scroll";
		return
	}
	setTimeout(function () {
			$this.lefttime = setInterval(function() { $this.leftmarquee(myid); },20)
			}, $this.delay);
} 