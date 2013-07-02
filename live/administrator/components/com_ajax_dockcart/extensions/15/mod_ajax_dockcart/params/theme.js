var ThemeConfigurator = {};

dojo.declare("ThemeConfigurator", null, {
  constructor: function(args) {
    dojo.mixin(this,args);
    var pane = null;
    if(!this.joomfish){
      pane = dojo.place(this.themeSelector, 'menu-pane', 'last');
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
      pane = dojo.place(this.themeSelector, el, 'last');
      if(this.control == 'defaultvalue_params'){
        dojo.style(pane, 'display', 'none');
      }
      
    }
    
    this.themeDetails = dojo.byId(this.control+'theme-details');
    
    this.selectTheme = dojo.byId(this.control+'theme');
    dojo.connect(this.selectTheme, 'onchange', this, 'changeTheme');
    this.changeTheme();
  },
  
  changeTheme: function(e){
    this.theme = this.selectTheme.options[this.selectTheme.selectedIndex].value;
    if(this.theme == '' || this.theme == 'default') this.theme = 'default2';
    this.themeDetails.innerHTML = eval('this.themeParams.'+this.theme);
    eval(eval('this.themeScripts.'+this.theme));
  }
  
});
