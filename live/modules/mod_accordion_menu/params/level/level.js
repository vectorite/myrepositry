var ThemeLevel = {};

dojo.declare("ThemeLevel", null, {
	constructor: function(args){
    dojo.mixin(this, args);
    dojo.style(this.el, 'position', 'relative');
    dojo.style(this.el, 'width', '100%');
    
    for (var k in this.values) {
      if((/^level[0-9]*/).test(k)){
          var formel = document.adminForm[this.control+"["+k+"]"];
          
          if(formel && formel.length){
            if(formel[0].nodeName == "INPUT"){
              for(var i=0; i<formel.length; i++){
                if(formel[i].value == this.values[k]){
                  formel[i].checked = true;
                }
              }
            }else if(formel[0].nodeName == "OPTION"){
              for(var i=0; i<formel.length; i++){
                if(formel[i].value == this.values[k]){
                  formel.selectedIndex = formel[i].index;
                }
              }
            }
          }else{
            try{
              var e = dojo.byId(this.id+k);
              e.value = this.values[k];
              if(e.color){
                e.color.active.val('ahex', e.value);
              }
              e.onchange();
            }catch(e){
            };
         }
      }
    }
    
    this.showedRemoveBtn = null;
    this.loadLevels();
    this.addAddLevelBtn();
    this.addRemoveLevelBtn();
  },
  
  loadLevels: function(){
    this.levels = dojo.query('.legend', this.el);
    dojo.forEach(this.levels, function(el){
      if(el.opener) dojo.disconnect(el.opener);
      var openerEl = dojo.query('h3', el)[0];
      openerEl.animated = dojo.query('.content', el)[0];
      el.opener = dojo.connect(openerEl, 'onclick', function(e){
        var opener = e.currentTarget;
        var el = e.currentTarget.animated;
        dojo.style(el, 'overflow', 'hidden');
        var h = parseInt(dojo.position(el).h);
        if(h == 0){
          dojo.addClass(opener, 'jpane-toggler-down');
          dojo.addClass(opener, 'pane-toggler-down');
          h = parseInt(dojo.position(dojo.query('table', el)[0] || dojo.query('fieldset', el)[0]).h);
        }else{
          dojo.removeClass(opener, 'jpane-toggler-down');
          dojo.removeClass(opener, 'pane-toggler-down');
          h=0;
        }
        dojo.animateProperty({
          node: el,
          properties: {
              height: h
          }
        }).play();
      });
    });
  },
  
  addRemoveLevelBtn: function(){
    this.removeLevelBtn = dojo.create('div', {'class' : 'removeBtn', innerHTML: '<div><div>REMOVE LEVEL</div></div>'}, this.el);
    this.showRemoveLevelBtn();
    dojo.connect(this.removeLevelBtn, 'onclick', this, 'removeLevel');
  },
  
  showRemoveLevelBtn: function(){
    if(this.levels.length <= 1){
      dojo.removeClass(this.removeLevelBtn, 'removeBtnShow');
      return;
    }
    dojo.addClass(this.removeLevelBtn, 'removeBtnShow');
  },
  
  addAddLevelBtn: function(){
    this.addLevelBtn = dojo.create('div', {'class' : 'addBtn', innerHTML: '<div><div>ADD LEVEL</div></div>'}, this.el);
    dojo.connect(this.addLevelBtn, 'onclick', this, 'addLevel');
  },
  
  removeLevel: function(e){
    this.levels[this.levels.length-1].parentNode.removeChild(this.levels[this.levels.length-1]);
    this.loadLevels();
    this.showRemoveLevelBtn();
  },
  
  addLevel: function(e){
    var lastEl = this.levels[this.levels.length-1];
    var html = this.render.replace(/\[x\]/g,this.levels.length+1);
    dojo.create('div', {'innerHTML' : html}, lastEl, 'after');
    this.loadLevels();
    this.showRemoveLevelBtn();
    eval(this.scripts.replace(/\[x\]/g,this.levels.length));
  }
});
