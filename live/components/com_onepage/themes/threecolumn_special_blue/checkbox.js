/*

CUSTOM FORM ELEMENTS

Created by Ryan Fait
www.ryanfait.com

The only things you may need to change in this file are the following
variables: checkboxHeight, radioHeight and selectWidth (lines 24, 25, 26)

The numbers you set for checkboxHeight and radioHeight should be one quarter
of the total height of the image want to use for checkboxes and radio
buttons. Both images should contain the four stages of both inputs stacked
on top of each other in this order: unchecked, unchecked-clicked, checked,
checked-clicked.

You may need to adjust your images a bit if there is a slight vertical
movement during the different stages of the button activation.

The value of selectWidth should be the width of your select list image.

Visit http://ryanfait.com/ for more information.

*/

var checkboxHeight = "25";
var radioHeight = "25";
var selectWidth = "190";


/* No need to change anything after this */



var Custom = {
	init: function(evt) {
		var inputs = document.getElementsByTagName("label"); 
		for(a = 0; a < inputs.length; a++) {
			if (inputs[a].htmlFor != null)
			{
			id = inputs[a].htmlFor; 
			if (id != null && id!='')
			{
			  radio = document.getElementById(id); 
			  if (radio != null)
			  {
			    if (radio.checked)
				{
				  inputs[a].className = 'radiochecked'; 
				}
				else
				{
				 //if (inputs[a].className == 'radiochecked')
				 inputs[a].className = 'radio'; 
				}
				
				document.onmouseup = Custom.clear;
				
				
			  }
			  
			}
			}

		}
	},
	
	clear: function(evt) {
		var inputs = document.getElementsByTagName("label"); 
		for(a = 0; a < inputs.length; a++) {
			if (inputs[a].htmlFor != null)
			{
			id = inputs[a].htmlFor; 
			if (id != null && id!='')
			{
			  radio = document.getElementById(id); 
			  if (radio != null)
			  {
			    if (evt.target != null && evt.target != inputs[a] )
				{
				if (radio.checked)
				{
				  
				  inputs[a].className = 'radiochecked'; 
				}
				else
				{
				 //if (inputs[a].className == 'radiochecked')
				 inputs[a].className = 'radio'; 
				}

				}
				else
				{
			    if (((evt.target != null && evt.target == inputs[a])))
				{
				  inputs[a].className = 'radiochecked'; 
				}
				else
				{
				 //if (inputs[a].className == 'radiochecked')
				 inputs[a].className = 'radio'; 
				}
				}
				
				
				
				
				
			  }
			  
			}
			}

		}
	}
	
	
}
window.onload = Custom.init;