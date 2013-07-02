var ThemeConfigurator = {};

dojo.declare("ThemeConfigurator", null, {
	constructor: function(args) {
	 dojo.mixin(this,args);
	 var pane = dojo.place(this.themeSelector, 'module-sliders', 'last');
   
   this.tpc = dojo.byId('themeparamcontainer');
   this.themeDetails = dojo.byId('theme-details');
   
   this.selectTheme = dojo.byId('jformparamsthemetheme');
   dojo.connect(this.selectTheme, 'onchange', this, 'changeTheme');
   this.changeTheme();
  },
  
  changeTheme: function(e){
    this.theme = this.selectTheme.options[this.selectTheme.selectedIndex].value;
    if(this.theme == '' || this.theme == 'default') this.theme = 'default2';
    dojo.byId('theme-details').innerHTML = eval('this.themeParams.'+this.theme);
    eval(eval('this.themeScripts.'+this.theme));
  }
  
});
