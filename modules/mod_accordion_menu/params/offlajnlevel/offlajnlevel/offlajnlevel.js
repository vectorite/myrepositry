dojo.require("dojo.cookie");

dojo.declare("ThemeLevel", null, {
	constructor: function(args){
    dojo.mixin(this, args);
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
              formel.value = this.values[k];
              if(formel.color){
                formel.color.active.val('ahex', formel.value);
              }
              this.fireEvent(formel, 'change');
            }catch(e){
            };
         }
      }
    }
    
    this.showedRemoveBtn = null;
    this.num = 0;
    this.states = new Array();
    this.loadLevels();
    this.addAddLevelBtn();
    this.addRemoveLevelBtn();
    this.loadStates();
    if(this.version == "15") {
      dojo.removeClass(this.el.parentNode.parentNode, 'blue');
      dojo.addClass(this.el.parentNode.parentNode, 'levelgroup');
    } else {
      dojo.removeClass(this.el.parentNode, 'blue');
      dojo.addClass(this.el.parentNode, 'levelgroup');
    }
  },
  
  fireEvent: function(element,event){
    if ((document.createEventObject && !dojo.isIE) || (document.createEventObject && dojo.isIE && dojo.isIE < 9)){
      var evt = document.createEventObject();
      return element.fireEvent('on'+event,evt);
    }else{
      var evt = document.createEvent("HTMLEvents");
      evt.initEvent(event, true, true );
      return !element.dispatchEvent(evt);
    }
  },
    
  loadLevels: function(){
    this.num = 0;
    this.levels = dojo.query('.legend', this.el);
    dojo.forEach(this.levels, function(el){
      //this.states[this.num] = 0;
      if(el.opener) dojo.disconnect(el.opener);
      var openerEl = dojo.query('h3', el)[0];
      openerEl.animated = dojo.query('.content', el)[0];
      dojo.attr(openerEl.animated, 'id', 'offlajnlevelpanel-' + this.num);
      el.opener = dojo.connect(openerEl, 'onclick', this, 'openClose');
      this.num++;
    }, this);
  },
  
  openClose: function(e) {
    var opener = e.currentTarget;
        var el = e.currentTarget.animated;
        dojo.style(el, 'overflow', 'hidden');
        var h = parseInt(dojo.position(el).h);
        var id = parseInt(el.id.replace('offlajnlevelpanel-', ''));
        if(h == 0){
          dojo.addClass(opener, 'jpane-toggler-down');
          dojo.addClass(opener, 'pane-toggler-down');
          this.states[id] = 1;
          dojo.cookie('offlajnlevels', dojo.toJson(this.states), { expires: 15 });
          h = parseInt(dojo.position(dojo.query('table', el)[0] || dojo.query('fieldset', el)[0]).h);
        }else{
          dojo.removeClass(opener, 'jpane-toggler-down');
          dojo.removeClass(opener, 'pane-toggler-down');
          this.states[id] = 0;
          dojo.cookie('offlajnlevels', dojo.toJson(this.states), { expires: 15 });
          h=0;
        }
        dojo.animateProperty({
          node: el,
          properties: {
              height: h
          },
          onEnd: function() {if(h) dojo.style(this.node, 'height', 'auto');}
        }).play();
  },
  
  loadStates: function() {
    var states = new Array();
    var states = dojo.fromJson(dojo.cookie('offlajnlevels'));
    dojo.forEach(states, function(el, i){
      if(el) {
          this.states[i] = el;
          var opener = dojo.byId('offlajnlevelpanel-' + i);
          if(opener){
            var h = parseInt(dojo.position(dojo.query('table', opener)[0] || dojo.query('fieldset', opener)[0]).h);
            dojo.style(opener, 'height', 'auto');
          }
        }
    }, this);
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
