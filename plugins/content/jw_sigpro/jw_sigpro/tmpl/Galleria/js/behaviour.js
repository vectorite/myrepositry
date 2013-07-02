/**
 * @version		2.5.7
 * @package		Simple Image Gallery Pro
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

jQuery.noConflict();
jQuery(document).ready(function($){

	$('.sigProGalleriaLink').click(function(event){

		event.preventDefault();

		// Prevent clicks upon animation
		if($(':animated').length) return false;

		// Assign element
		var el = $(this);

		// Parent container
		var outerContainer = el.parent().parent().parent().parent().parent();
		var placeholderContainer = outerContainer.find(".sigProGalleriaPlaceholderContainer div:first");

		// Placeholder elements
	  var targetLink = placeholderContainer.find("a:first");
	  var targetTitle = placeholderContainer.find("p:first");
	  var targetImg = targetLink.find("img:first");

		// Source elements
	  var sourceLinkHref = el.attr("href");
	  var sourceLinkTitle = el.attr("title");
	  var sourceImage = el.find("img:first");

	  if(targetLink.attr("href")!==sourceLinkHref && targetLink.hasClass('sigProGalleriaTargetLink')){

		  if(el.find("span:nth-child(2)")){
		  	var sourceTitle = el.find(".sigProCaption").html();
		  } else {
		  	var sourceTitle = false;
		  }

			placeholderContainer.animate({'opacity':0},300,function(){
				targetImg.attr("src",sourceLinkHref);
				var counter = 0;
				targetImg.load(function(){
					if (counter++ == 0) {
					targetImg.attr("title",sourceImage.attr("title"));
					targetImg.attr("alt",sourceImage.attr("alt"));
					targetLink.attr("href",sourceLinkHref);
					targetLink.attr("title",sourceLinkTitle);
					if(targetTitle.hasClass('sigProGalleriaTargetTitle') && sourceTitle) targetTitle.html(sourceTitle);
					placeholderContainer.animate({'opacity':1},600);
					}
				});
			}); //.delay(500).animate({'opacity':1},300);

	  }

		// Set class for current thumb
		var thumbs = outerContainer.find("ul:first").find("a");
		thumbs.each(function(){
			if($(this).hasClass('sigProLinkSelected')){
				$(this).removeClass('sigProLinkSelected');
			}
		});
		el.addClass('sigProLinkSelected');

	});

});
