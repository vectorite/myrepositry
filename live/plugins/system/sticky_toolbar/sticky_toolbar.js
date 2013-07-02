dojo.declare("StickyToolbar", null, {
	constructor: function(args) {
    dojo.mixin(this,args);
    if(this.joomla17){
      this.padding = -10;
    }else{
      this.padding = 0;
    }
    this.contentBox = dojo.byId('content-box');
    if(!this.joomla17){
      this.contentBox = dojo.query('.padding',this.contentBox)[0];
    }
    this.toolbarBox = dojo.byId('toolbar-box');
    this.toolbarBox.fixed = 0;
    this.substituteBox = dojo.create("div", { id: "substitute-box"}, this.contentBox, "first");
    this.toolbarApply = dojo.byId('toolbar-apply');
    this.submit = dojo.query('a',this.toolbarApply)[0];
    this.submit = dojo.attr(this.submit, "onclick");
    
    this.offlajnToolbar = dojo.create("div", { id: "offlajn-toolbar"}, dojo.body(), "last");
    this.offlajnToolbar = dojo.create("div", { id: "offlajn-toolbar-inner"}, this.offlajnToolbar, "last");
    
    dojo.connect(window, "onscroll", this, "scrollToolbar");
    if(this.toolbarApply)
      dojo.connect(document, "onkeypress", this, "keyPressed");
      
    dojo.forEach(
      dojo.query("a",this.toolbarBox),
      function(a){
        dojo.connect(a,"onclick",dojo.attr(a, "onclick"));
        /*if(!dojo.isIE)
          dojo.attr(a, "onclick","return false;");*/
      },
      this
    );
  },

  scrollToolbar : function(event){
    if (dojo.position( this.toolbarBox).y < this.padding && this.toolbarBox.fixed == 0){
      this.toolbarBox.fixed = 1;
      dojo.style(this.substituteBox,'height',dojo.marginBox(this.toolbarBox).h+"px");
      dojo.place(this.toolbarBox, this.offlajnToolbar);
    }
    if (dojo.position( this.substituteBox).y >this.padding && this.toolbarBox.fixed == 1){
      this.toolbarBox.fixed = 0;
      dojo.style(this.substituteBox,'height',"0px");
      dojo.place(this.toolbarBox, this.contentBox, "first");
    }    
  },
  
  keyPressed : function(event){
    if (!(event.which == 115 && event.ctrlKey) && !(event.which == 19)) return true;
    event.preventDefault();
    this.submit();
  }
});