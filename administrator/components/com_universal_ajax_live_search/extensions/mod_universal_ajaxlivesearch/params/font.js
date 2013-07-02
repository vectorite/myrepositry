var FontConfigurator = {};
dojo.require("dojo.window");

dojo.declare("FontConfigurator", null, {
	constructor: function(args) {
	 dojo.mixin(this,args);
	 this.init();
  },
  
  init: function(){
    dojo.connect(this.changefont, 'onclick', this, 'showSelector');
    //this.showSelector();
  },
  
  showSelector: function(e){
    dojo.stopEvent(e);
    //this.prevHTMLOv = document.documentElement.style.overflow;
    //document.documentElement.style.overflow = 'hidden';
    //this.prevBODYOv = document.body.style.overflow;
    //document.body.style.overflow = 'hidden';
    pos =  dojo.window.getBox();
    
    this.cont = dojo.create('div', { innerHTML: this.h, style: { 
      position: "fixed", 
      top: 0,
      left: 0,
      width: pos.w+'px',
      height: pos.h+'px',
      zIndex: 9999,
      background: 'url("../modules/'+this.module+'/params/images/b30.png") repeat'
    } }, dojo.body());
    
    this.save = dojo.byId('savefont');
    dojo.connect(this.save, 'onclick', this, 'onsave');
    this.cancel = dojo.byId('cancelfont');
    dojo.connect(this.cancel, 'onclick', this, 'oncancel');
    this.closefont = dojo.byId('closefont');
    dojo.connect(this.closefont, 'onclick', this, 'oncancel');
    
    this.type = dojo.byId('fonttype');
    this.alter = dojo.byId('alternativefont');
    this.family = dojo.byId('fontfamily');
    this.fontsize = dojo.byId('fontsize');
    this.fontbold = dojo.byId('fontbold');
    this.fontitalic = dojo.byId('fontitalic');
    this.fontdecoration = dojo.byId('fontdecoration');
    this.fonttransform = dojo.byId('fonttransform');
    this.fontlineheight = dojo.byId('fontlineheight');
    this.fontalign = dojo.byId('fontalign');
    this.preview = dojo.byId('fontpreview');
    this.text = dojo.byId('textpreview');
    
    this.fontcolor = dojo.byId('fontcolor1');
    
    this.previewbg = dojo.byId('previewbg');
    
    this.fontshadow = dojo.byId('fontshadowbox');
    this.fontshadowx = dojo.byId('fontshadowx');
    this.fontshadowy = dojo.byId('fontshadowy');
    this.fontshadowsize = dojo.byId('fontshadowsize');
    this.fontshadowcolor = dojo.byId('fontshadowcolor');
    
    
    
    this.onload();
    if(this.arr){
      this.ChangeSelectByValue(this.type, this.arr[0].replace("*", ""));
      this.swapFamily();
      this.alter.value = this.arr[1].replace("*", ""); 
      this.ChangeSelectByValue(this.family, this.arr[2].replace("*", ""));
      this.fontsize.value = this.arr[3].replace("*", "");
      this.fontbold.checked = this.arr[4].replace("*", "") == 1 ? true : false;
      this.fontitalic.checked = this.arr[5].replace("*", "") == 1 ? true : false;
      this.fontcolor.value = this.arr[6].replace("*", ""); 
      this.fontshadow.checked = this.arr[7].replace("*", "") == 1 ? true : false;
      this.fontshadowx.value = this.arr[8].replace("*", ""); 
      this.fontshadowy.value = this.arr[9].replace("*", ""); 
      this.fontshadowsize.value = this.arr[10].replace("*", ""); 
      this.fontshadowcolor.value = this.arr[11].replace("*", "");
      this.ChangeSelectByValue(this.fontdecoration, this.arr[12].replace("*", ""));
      this.ChangeSelectByValue(this.fonttransform, this.arr[13].replace("*", ""));
      this.fontlineheight.value = this.arr[14].replace("*", ""); 
      if(!this.arr[15]) this.arr[15] = 'inherit';
      this.ChangeSelectByValue(this.fontalign, this.arr[15].replace("*", ""));
      
      
    }else{
      this.swapFamily();
    }
    
    //this.fontcolor.picker = new jscolor.color(this.fontcolor, {position: 'fixed', pickerPosition: 'top'});
    //this.previewbg.picker = new jscolor.color(this.previewbg, {position: 'fixed', pickerPosition: 'top'});
    //this.fontshadowcolor.picker = new jscolor.color(this.fontshadowcolor, {position: 'fixed', pickerPosition: 'top'});
    
    jQuery.fn.jPicker.defaults.images.clientPath="../modules/"+this.module+"/params/jpicker/images/";
    dojo.byId("fontcolor1").alphaSupport=false; 
    jQuery("#fontcolor1").jPicker({window:{expandable: true,alphaSupport: false}});
    dojo.byId("previewbg").alphaSupport=false; 
    jQuery("#previewbg").jPicker({window:{expandable: true,alphaSupport: false}});
    jQuery("#fontshadowcolor").jPicker({window:{expandable:true, alphaSupport:true}});

    dojo.connect(this.type, 'onchange', this, 'swapFamily');
    
    dojo.connect(this.family, 'onchange', this, 'changeFamily');
    dojo.connect(this.family, 'onkeyup', this, 'changeFamily');
    dojo.connect(this.fontsize, 'onkeyup', this, 'changeFamily');
    dojo.connect(this.fontlineheight, 'onkeyup', this, 'changeFamily');
    dojo.connect(this.fontbold, 'onchange', this, 'changeFamily');
    dojo.connect(this.fontitalic, 'onchange', this, 'changeFamily');
    dojo.connect(this.text, 'onkeyup', this, 'changeFamily');
    dojo.connect(this.fontcolor, 'onchange', this, 'changeFamily');
    dojo.connect(this.previewbg, 'onchange', this, 'changeFamily');
    dojo.connect(this.fontdecoration, 'onchange', this, 'changeFamily');
    dojo.connect(this.fonttransform, 'onchange', this, 'changeFamily');
    dojo.connect(this.fontalign, 'onchange', this, 'changeFamily');
    
    dojo.connect(this.fontshadow, 'onchange', this, 'changeFamily');
    dojo.connect(this.fontshadowx, 'onkeyup', this, 'changeFamily');
    dojo.connect(this.fontshadowy, 'onkeyup', this, 'changeFamily');
    dojo.connect(this.fontshadowsize, 'onkeyup', this, 'changeFamily');
    dojo.connect(this.fontshadowcolor, 'onchange', this, 'changeFamily');
    
    this.changeFamily();
  },
  
  oncancel: function(){
    if(this.cont)
      this.cont.parentNode.removeChild(this.cont);
    //document.body.style.overflow = this.prevHTMLOv;
    //document.documentElement.style.overflow = this.prevBODYOv;
  },
  
  onload: function(){
    this.arr = this.hidden.value.split('|') ;
    if(this.arr.length < 15){
      this.arr = null;
    }
  },
  
  onsave: function(){
    if(this.cont){
      var subset = this.type.options[this.type.selectedIndex].value;
      var f = '';
      if(this.family.length > 0){
        f = this.family.options[this.family.selectedIndex].value;
      }
      var decoration = this.fontdecoration.options[this.fontdecoration.selectedIndex].value;
      var transform = this.fonttransform.options[this.fonttransform.selectedIndex].value;
      var align = this.fontalign.options[this.fontalign.selectedIndex].value;
      var ahex = this.fontshadowcolor.color.active.val().ahex;
      var h = '*'+subset+'|*'+this.alter.value+'|*'+f+'|*'+this.fontsize.value+'|*'+(this.fontbold.checked ? 1 : 0)+'|*'+(this.fontitalic.checked ? 1 : 0)+'|*'+this.fontcolor.value+'|*';
      h+=(this.fontshadow.checked ? 1 : 0)+'|*'+this.fontshadowx.value+'|*'+this.fontshadowy.value+'|*'+this.fontshadowsize.value+'|*'+ahex+'|*'+decoration+'|*'+transform+'|*'+this.fontlineheight.value+'|*'+this.fontalign.value;
      this.hidden.value = h;
      this.cont.parentNode.removeChild(this.cont); 
    }
    document.body.style.overflow = this.prevHTMLOv;
    document.documentElement.style.overflow = this.prevBODYOv;
  },
  
  swapFamily: function(){
    var val = this.type.options[this.type.selectedIndex].value;
    if(this.fonts[val] != undefined){
      this.family.length = 0;
      dojo.forEach(this.fonts[val], function(font){
        if(font == '') return;
        dojo.create('option', {value: font, innerHTML: font}, this.family);
      }, this);
      this.family.disabled = false;
      this.changeFamily();
    }else{
      this.family.disabled = true;
      dojo.style(this.preview, 'fontFamily', this.alter.value);
    }
  },
  
  changeFamily: function(){
    var subset = this.type.options[this.type.selectedIndex].value;
    
    var decoration = this.fontdecoration.options[this.fontdecoration.selectedIndex].value;
    dojo.style(this.preview, 'textDecoration', decoration);
    var transform = this.fonttransform.options[this.fonttransform.selectedIndex].value;
    dojo.style(this.preview,'textTransform', transform);
    var align = this.fontalign.options[this.fontalign.selectedIndex].value;
    dojo.style(this.preview,'textAlign', align);
    
    dojo.style(this.preview,'lineHeight', this.fontlineheight);
    
    if(subset == 'LatinExtended'){
      subset = 'latin,latin-ext';
    }else if(subset == 'CyrillicExtended'){
      subset = 'cyrillic,cyrillic-ext';
    }else if(subset == 'GreekExtended'){
      subset = 'greek,greek-ext';
    }
    var ext = ':';
    if(this.fontbold.checked){
      ext+='700';
      dojo.style(this.preview,'fontWeight', 'bold');
    }else{
      ext+='400';
      dojo.style(this.preview,'fontWeight', 'normal');
    }
    if(this.fontitalic.checked){
      ext+='italic';
      dojo.style(this.preview,'fontStyle', 'italic');
    }else{
      dojo.style(this.preview,'fontStyle', 'normal');
    }
    var val = '';
    if(this.family.length > 0){
      val = this.family.options[this.family.selectedIndex].value;
    }
    dojo.create('link', {rel:'stylesheet', type: 'text/css', href: 'https://fonts.googleapis.com/css?family='+val+ext+'&subset='+subset}, dojo.body());
    (val == '') ? val = this.alter.value : val = "'"+val+"', "+this.alter.value;
    dojo.style(this.preview,'fontFamily', val);
    dojo.style(this.preview,'fontSize', this.fontsize.value);
    dojo.style(this.preview, 'color', '#'+this.fontcolor.value);
    dojo.style(this.preview, 'backgroundColor', '#'+this.previewbg.value);
		
    if(this.fontshadow.checked){
      var c = this.fontshadowcolor.color.active.val();
      dojo.style(this.preview,'textShadow', this.fontshadowx.value+' '+this.fontshadowy.value+' '+this.fontshadowsize.value+' rgba('+c.r+','+c.g+','+c.b+','+c.a/255.0+')');
    }else{
      dojo.style(this.preview,'textShadow', '');
    }
    
    dojo.attr(this.preview,'innerHTML', this.text.value);
  },
  
  ChangeSelectByValue: function(ddl, value) {
    for (var i = 0; i < ddl.options.length; i++) {
      if (ddl.options[i].value == value) {
        if (ddl.selectedIndex != i) {
          ddl.selectedIndex = i;
        }
        break;
      }
    }
 }


});
