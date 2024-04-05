/**
* CG Scroll - Joomla Module 
* Version			: 4.3.2
* Package			: Joomla 3.10.x - 4.x - 5.x
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
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
        ascroll = "#cg_scroll_"+myid+" ";
        cgscroll_options = Joomla.getOptions('mod_cg_scroll_'+myid);
        if (typeof cgscroll_options === 'undefined' ) {return false}
        cgscroll[myid] = new CGScroll(myid,ascroll,cgscroll_options);
        cgscroll[myid].go_scroll(myid);
    }
});
function CGScroll(myid,me,options) {
	this.options = options;
	this.myid = myid;
	this.me = me;
	this.container = document.querySelector(this.me + '#sfdmarqueecontainer');
    this.container.scrollBehavior = 'smooth';
	this.cross_marquee = document.querySelector(this.me + '#vmarquee');
	this.items_ul_0 = document.querySelector(this.me + 'ul.cg-scroll-items-0');
	this.items_ul_1 = document.querySelector(this.me + 'ul.cg-scroll-items-1');
	this.container.style.height = this.options.height+"px";
	this.pauseit=this.options.pause;
	this.delay=parseInt(this.options.delay);
    
    this.slowdown = this.options.slowdown;

    ico = document.querySelector(this.me +"#toDirection")
    if (ico) ico.style.display = 'block';
	this.me_up = document.querySelector(this.me + ".icon-dir-up");
	this.me_down = document.querySelector(this.me + ".icon-dir-down");
	this.me_left = document.querySelector(this.me + ".icon-dir-left");
	this.me_right = document.querySelector(this.me + ".icon-dir-right");
	
	items = document.querySelectorAll(me + 'ul li');
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
		$total_width = $total_width / 2; // on a doublé les articles
		this.cross_marquee.style.float = "left";
		this.cross_marquee.style.width = $total_width+"px";
		this.cross_marquee.style.height =  this.options.height+"%";
		this.items_ul_0.style.width = $total_width + "px"; 
		this.items_ul_1.style.width = $total_width + "px"; 
	}

}
CGScroll.prototype.go_scroll = function (myid) {
	$this = cgscroll[myid];

	if ($this.options.direction == 1) {
        translate = "translateY"; // up/down
        height = parseInt($this.items_ul_0.clientHeight);
        if ($this.items_ul_1.clientHeight != height) $this.items_ul_1.style.height = height+'px'; // adjust 2nd ul height
        duration = (height * (1/$this.options.speed)) * (50 + (15 * $this.slowdown));
    } else {
        translate = "translateX"; // left/right
        width = parseInt($this.items_ul_0.clientWidth);
        duration = (width * (1/$this.options.speed)) * (50 + (15 * $this.slowdown));
    }
    direction = "normal";
    $this.effect0 = new KeyframeEffect(
       $this.items_ul_0, // element to animate
        [
            { transform: translate+"(0%)" }, // keyframe
            { transform: translate+"(-100%)" }, // keyframe
        ],
        { direction:direction,duration: duration,iterations : 9999,delay:0}, // keyframe options
    );
    $this.effect1 = new KeyframeEffect(
        $this.items_ul_1, // element to animate
        [
            { transform: translate+"(100%)" }, // keyframe
            { transform: translate+"(0%)" }, // keyframe
        ],
        { direction:direction,duration: duration,iterations : 9999,delay:0}, // keyframe options
    );
    $this.animation0 = new Animation(
        $this.effect0,
        document.timeline,
    );
    $this.animation1 = new Animation(
        $this.effect1,
        document.timeline,
    );
    setTimeout((me) => {
        me.animation1.play();
        me.animation0.play();
    },$this.delay,$this); 
    
    if ($this.pauseit == "1") { // enable pause on mouse over ?
        $this.container.addEventListener('mouseover',function() {
            id = this.getAttribute('data');
            cgscroll[id].animation0.pause();
            cgscroll[id].animation1.pause();
        })
        $this.container.addEventListener('mouseout',function() {
            id = this.getAttribute('data');
            cgscroll[id].animation0.play();
            cgscroll[id].animation1.play();
        })
	}
	if ($this.me_up) {
		$this.me_up.style.display = "none";
		$this.me_up.addEventListener("click",function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgscroll[id];
            cgscroll[id].animation0.reverse();
            cgscroll[id].animation1.reverse();
			$this.me_up.style.display = "none";
			$this.me_down.style.display = "block";
			return false;
		});	
		$this.me_down.addEventListener("click",function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgscroll[id];
            cgscroll[id].animation0.reverse();
            cgscroll[id].animation1.reverse();
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
            cgscroll[id].animation0.reverse();
            cgscroll[id].animation1.reverse();
			$this.me_left.style.display = "none";
			$this.me_right.style.display = "block";
			return false;
		});
		$this.me_right.addEventListener('click', function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgscroll[id];
            cgscroll[id].animation0.reverse();
            cgscroll[id].animation1.reverse();
			$this.me_left.style.display = "block";
			$this.me_right.style.display = "none";
			return false;
		});
	}
	if ($this.options.direction == 1) {
		if ($this.me_down) $this.me_down.style.display = "block";
	} else {
        if ($this.me_right) $this.me_right.style.display = "block";
	}
    this.animation0.onfinish = e => {
        e.currentTarget.play(); // restart it
    };
    this.animation1.onfinish = e => {
        e.currentTarget.play(); // restart it
    };
}

