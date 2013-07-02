
window.addEvent('domready', function(){

  var slider = $('slider');

  new Slider(slider, slider.getElement('.knob_border_radius'), {
    range: [0, 30],
    initialStep:  $('jform_group_border_radius').value,
    onChange: function(value){
      if (value){
    	  $('jform_group_border_radius').set('value',value);
      } 
    }
  });
});