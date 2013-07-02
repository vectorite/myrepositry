var AJAXSearch = {};

dojo.declare("AJAXSearchelegant", AJAXSearchBase, {
  constructor: function(args) {
    this.resultboxTopOffset = 0;
  },
  
  getResultBoxAnimation: function(){
    return dojo.animateProperty({
      node: this.searchResultsMoovable, 
      properties: {
        height: {start: dojo.style(this.searchResultsMoovable, 'height'), end: this.innerHeight}
      }, 
      duration: 500
    }).play();
  },
  
  getCloseResultBoxAnimation: function(){
    return dojo.animateProperty({
      node: this.searchResultsMoovable, 
      properties: {
        height: 0
      }, 
      duration: 500, 
      onEnd : dojo.hitch(this,'removeResults')
    }).play();
  },
  
  getCategoryLeftPosition: function(){
    return this.textBoxPosition.x;
  }
});