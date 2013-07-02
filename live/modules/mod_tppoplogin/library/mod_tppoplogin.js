jQuery.noConflict()
jQuery(document).ready(function(){					   		   
	//When you click on a link with class of poplogin and the href starts with a # 
jQuery('a.poplogin[href^=#]').click(function() {
    var popID = jQuery(this).attr('rel'); //Get Popup Name
    var popURL = jQuery(this).attr('href'); //Get Popup href to define size

    //Pull Query & Variables from href URL
    //var query= popURL.split('?');
    //var dim= query[1].split('&');
    //var popWidth = dim[0].split('=')[1]; //Gets the first query string value
    var popWidth = 600; //Gets the first query string value

    //Fade in the Popup and add close button
    jQuery('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<a href="#" class="close">Close</a>');

    //Define margin for center alignment (vertical   horizontal) - we add 80px to the height/width to accomodate for the padding  and border width defined in the css
    var popMargTop = (jQuery('#' + popID).height() + 80) / 2;
    var popMargLeft = (jQuery('#' + popID).width() + 80) / 2;

    //Apply Margin to Popup
    jQuery('#' + popID).css({
        'margin-top' : -popMargTop,
        'margin-left' : -popMargLeft
    });

    //Fade in Background
    jQuery('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
    jQuery('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies 

    return false;
});

//Close Popups and Fade Layer
jQuery('a.close, #fade').live('click', function() { //When clicking on the close or fade layer...
    jQuery('#fade , .popbox').fadeOut(function() {
        jQuery('#fade, a.close').remove();  //fade them both out
    });
    return false;
});

	
});
