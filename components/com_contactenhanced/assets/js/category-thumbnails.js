var ceCatThumb = ({
	getInfo: function(contactId,Itemid,link)
	{
		link	= link+'index.php?option=com_contactenhanced&task=jsonGetContactDeatails';
		var jSonRequest = new Request.JSON({url:link+'&contactId='+contactId+'&Itemid='+Itemid,onComplete: function(response){
			//did it return as good, or bad?
			if(response.action == 'success'){
				$('ce-thumbnails-contact').set('html',response.text);
				// Google Analytics Support 
				if(typeof(pageTracker) != 'undefined'){
					pageTracker._trackPageview('/contact/category/view/'+contactId);
				}
				// infinity page tracking Support
				if(typeof(__NAS) != 'undefined'){
					__NAS.page('/contact/category/view/'+contactId);
				}
			}
		}
		}).send();
	}
	/*, addThumbEvent: function(){
		
		Array.each($$('ul#ce-thumbnails li a'), function(link, index){
			link.addEvent('click',function(e) {
				e.stop();
			});
		});
	}*/
	
});
