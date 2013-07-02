var AJAXSearch = {};

dojo.declare("AJAXSearchsimple", AJAXSearchBase, {
  constructor: function(args) {
    this.resultboxTopOffset = -3;
  },
  
  getResultBoxAnimation: function(){
    if(this.fadeInResult){ //fade-in and down
      dojo.style(this.searchResultsMoovable, "height", this.innerHeight+"px");
      
      this.textBoxPos = dojo.position(this.searchForm, true);
      dojo.style(this.searchResultsMoovable, "opacity", 0);
      dojo.style(this.searchResultsMoovable, "top", '-10px');
      this.fadeInResult=0;
      return dojo.animateProperty({
        node: this.searchResultsMoovable, 
        properties: {
            opacity: 1,
            top: { end:0, units:"px" }
        }, 
        duration: 300
      }).play();
    }else{
      return dojo.animateProperty({
        node: this.searchResultsMoovable, 
        properties: {
          height: {start: dojo.style(this.searchResultsMoovable, 'height'), end: this.innerHeight}
        }, 
        duration: 500
      }).play();
    }
  },
  
  getCloseResultBoxAnimation: function(){
    return dojo.animateProperty({
      node: this.searchResultsMoovable, 
      properties: {
        opacity: 0, 
        top: { end:10, units:"px" }
      }, 
      duration: 300, 
      onEnd : dojo.hitch(this,'removeResults')
    }).play();
  },
  
  getCategoryLeftPosition: function(){
    var categorySize = dojo.marginBox(this.searchCategories);
    return this.textBoxPosition.x+this.textBoxPosition.w-categorySize.w;
  }
});