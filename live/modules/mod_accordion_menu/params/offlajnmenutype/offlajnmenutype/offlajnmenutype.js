
dojo.declare("TypeConfigurator", null, {
	constructor: function(args) {
	  dojo.mixin(this,args);
	  //var pane = dojo.place(this.typeSelector, dojo.byId('module-sliders') ? dojo.byId('module-sliders') : dojo.byId('menu-pane'), 'first');
    var pane = null;
    if(!this.joomfish){
     // pane = dojo.place(this.typeSelector, dojo.byId('module-sliders') ? dojo.byId('module-sliders') : dojo.byId('menu-pane'), 'first');
    }else{
      var hides = dojo.query('.translateparams td .toolbar');
      dojo.forEach(hides,function(el){dojo.style(el, 'display', 'none')});
      var el = null;
      if(this.control == 'orig_params'){
        el = dojo.byId('original_value_params');
      }else if(this.control == 'defaultvalue_params'){
        el = dojo.byId('original_value_params');
      }else if(this.control == 'refField_params'){
        el = dojo.query('.translateparams .translateparams');
        el = el[0];
      }
      pane = dojo.place(this.typeSelector, el, 'first');
      if(this.control == 'defaultvalue_params'){
        dojo.style(pane, 'display', 'none');
      }
    }
    this.tpc = dojo.byId('typeparamcontainer');
    this.typeDetails = dojo.byId(this.control+'-details');
    this.title = dojo.byId(this.control+'-title');
    
    this.selectType = dojo.byId(this.selectorId);
    
    dojo.connect(this.selectType, 'onchange', this, 'changeType');
    this.changeType();
  },
  
  changeType: function(e){
    //this.type = this.selectType.options[this.selectType.selectedIndex].value;
    this.type = this.selectType.value;
    if(this.type == '' || this.type == 'joomla') this.type = 'joomla';
    
    if(this.typeParams[this.type].length == 32){
      dojo.addClass(this.title, 'offlajnloading');
      this.typeDetails.innerHTML = '';
      var xhrArgs = {
        url: '',
        content: {
          'offlajnformrenderer': '1',
          'control': this.control,
          'key': this.typeParams[this.type]
        },
        load: dojo.hitch(this, function(data){
          dojo.removeClass(this.title, 'offlajnloading');
          this.typeDetails.innerHTML = data;
          window.head = document.getElementsByTagName('head')[0];
          dojo.query('link',this.typeDetails).forEach(function(el){
            dojo.place(el, head);
          });
          dojo.query('script',this.typeDetails).forEach(function(el){
            var fileref=document.createElement('script');
            fileref.setAttribute("type","text/javascript");
            fileref.setAttribute("src", dojo.attr(el, 'src'));
            dojo.place(fileref, head);
          });
          dojo.global.toolTips.connectToolTips(this.typeDetails);
        }),
        error: function(error){
        }
      }
      var deferred = dojo.xhrPost(xhrArgs);
    }else{
      this.typeDetails.innerHTML = this.typeParams[this.type];
    }
    
    
    /*this.typeDetails.innerHTML = eval('this.typeParams.'+this.type);

    eval(this.typeScripts[this.type]);
    dojo.global.toolTips.connectToolTips();*/
  }
});