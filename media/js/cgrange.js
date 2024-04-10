/**
* CG Scroll - Joomla Module 
* Version			: 4.3.4
* Package			: Joomla 3.10.x - 4.x - 5.x
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
/* handle CGRange field */
document.addEventListener('DOMContentLoaded', function() {
    let cgranges = document.querySelectorAll('.form-cgrange');
    for(var i=0; i< cgranges.length; i++) {
        cgranges[i].addEventListener('input',function() {
            let $id = this.getAttribute('id');
            label = document.querySelector('#cgrange-label-'+$id);
            label.innerHTML = this.value;
        })
    }
    // initialize
    let cglabels = document.querySelectorAll('.cgrange-label');
    for(var i=0; i< cglabels.length; i++) {
        let $id = cglabels[i].getAttribute('data');
        var value = document.querySelector('#'+$id).getAttribute('value');
        cglabels[i].innerHTML = value;
    }

})