jQuery(document).ready(function($) {
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|BB10|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        $("div.crafty-social-share-buttons ul li a.crafty-social-button.csb-whatsapp").show();
    }
});