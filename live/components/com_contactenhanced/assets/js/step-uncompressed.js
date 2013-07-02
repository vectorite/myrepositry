function stepStart(step){
			if(step.progessBar){
				$('step-progressBar-'+step.group).setStyle('display','block');
			}
			$('buttonContainer-'+step.group).setStyle('display','block');
			updateStepButtons(step);
		}
function stepValidation(step, validator){
	var valid	= true;
	Object.each(step.required[step.index], function(field, index){
		if(validator.validateField(field,true) != true){
	    	valid	=  false;
	    }
	});
	if(valid){
		return true;
	}
	return false;
}
function stepNext(step, validator){
	if(stepValidation(step,validator)){
		step.index++;
	    if(step.max >= step.index){
	        step.slider.display(step.index);
	        updateStepButtons(step);
	        if(step.max == step.index){
	           $('step-next-'+step.group).setStyle('display','none');   
	        }
	    }
	    $('step-back-'+step.group).setStyle('display','inline');
	    stepUpdateProgressBar(step);
	}
}

function updateStepButtons(step){
	$('step-next-span-'+step.group).set('text',step.nextButton[step.index]);
}

function stepBack(step){
    step.index--;
	if(step.index >= 0){
		step.slider.display(step.index);
		updateStepButtons(step);
    }
    if(step.index < 1){
        $('step-back-'+step.group).setStyle('display','none');
    }
    $('step-next-'+step.group).setStyle('display','inline');
    stepUpdateProgressBar(step);
}


function stepUpdateProgressBar(step){
    if(step.index > 0){
        var percentage = (step.index/step.max)*100;
    }else{
        var percentage = 0;
    }
    $('step-progress-'+step.group).setStyle('width',percentage+'%');
    if(step.progessBarText){
		$('step-text-'+step.group).set('html',
	    			Joomla.JText._('COM_CONTACTENHANCED_PAGINATION_JS_STEP','Step')
	    			+' '
	    			+(step.index+1)
	    			+' '
	    			+Joomla.JText._('COM_CONTACTENHANCED_PAGINATION_JS_OF','of')
	    			+(step.max+1)
    			);
	}
}
function stepDisableEnterKey(){
	var inputs = $$('.ce-cf-container input');
	$each(inputs,function(el,i) {
	  el.addEvent('keypress',function(e) {
	    if(e.key == 'enter') { 
	      e.stop(); 
	      if(inputs[i+1]){
	    	  if( !inputs[i].hasClass('required') || (inputs[i].hasClass('required') && inputs[i].get('value').length > 0 ))
	    	  inputs[i+1].focus();
	      }
	    }
	  });
	});
}