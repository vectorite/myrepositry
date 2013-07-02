
dojo.declare("TypeConfigurator", null, {
	constructor: function(args) {
	  dojo.mixin(this,args);
	  //var pane = dojo.place(this.typeSelector, dojo.byId('module-sliders') ? dojo.byId('module-sliders') : dojo.byId('menu-pane'), 'first');
    var pane = null;
    if(!this.joomfish){
      pane = dojo.place(this.typeSelector, dojo.byId('module-sliders') ? dojo.byId('module-sliders') : dojo.byId('menu-pane'), 'first');
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
    this.typeDetails = dojo.byId(this.control+'type-details');
    
    this.selectType = dojo.byId(this.selectorId);
    
    dojo.connect(this.selectType, 'onchange', this, 'changeType');
    this.changeType();
  },
  
  changeType: function(e){
    this.type = this.selectType.options[this.selectType.selectedIndex].value;
    if(this.type == '' || this.type == 'joomla') this.type = 'joomla';
    dojo.byId(this.control+'type-details').innerHTML = eval('this.typeParams.'+this.type);
    eval(eval('this.typeScripts.'+this.type));
  }
});

dojo.declare("JoomlaType", null, {
  constructor: function(args){
    dojo.mixin(this, args);
    this.list = dojo.byId(this.selectorId);
    if(!this.joomfish){
      this.select = dojo.byId("paramsjoomlamenu") ? dojo.byId("paramsjoomlamenu") : dojo.byId('jformparamsmenutypejoomlamenu');
    }else{
      this.select = dojo.byId(this.control+"joomlamenu");
    }
    dojo.destroy(this.select.options[0]);
    dojo.connect(this.select, 'onchange', this, "changeList");
    this.defaultMenu = this.select.options[this.select.selectedIndex].value;
    this.changeList();
  },
  
  changeList: function(e) {
      var type = this.select.options[this.select.selectedIndex].value;
      var node = dojo.create("div");
      node.innerHTML = this.data[type].replace(/option/g,'div');
      dojo.forEach(this.list.childNodes, function(el){
        if(el)
          el.parentNode.removeChild(el);
      });
      
      dojo.forEach(node.childNodes, function(el){
        if(el.nodeName == 'DIV'){
          var opt = document.createElement('OPTION');
          opt.text = el.innerHTML;
          opt.value = dojo.attr(el,'value');
          opt.selected = dojo.attr(el,'selected');
          this.list.options.add(opt);
        }
      }, this);
      if (type!=this.defaultMenu && e && e.currentTarget == this.select) this.list.selectedIndex = 0;   
      //if(e == undefined) this.list.selectedIndex = 0;
  }
});