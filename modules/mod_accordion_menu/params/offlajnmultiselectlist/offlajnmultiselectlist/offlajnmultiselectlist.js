dojo.declare("OfflajnMultiSelectList", null, {
	constructor: function(args) {
    dojo.mixin(this,args);
    this.lineHeight = 20;
    this.init();
  },
  
  init: function() {
    this.multiselect = dojo.byId('offlajnmultiselect' + this.name);
    if(this.joomla) {
      this.type = this.typeSelectorInit();
    } 
    this.itemscontainer = dojo.create('div', {'class': 'multiselectitems', 'innerHTML': this.data[this.type]}, this.multiselect);
    if(dojo.isIE == 7) dojo.style(this.multiselect, 'width', dojo.position(this.itemscontainer).w + 10 + 'px'); //IE7 width bug
    this.getList();
    this.hidden = dojo.byId(this.name);
    this.hidden.options = this.options;
    this.hidden.listobj = this;
    
    this.setSize(); 
    dojo.connect(this.itemscontainer, 'onclick', this, 'selectItem');
    this.setSelected();   
    if(this.height) {            
      this.scrollbar = new OfflajnScroller({
        'extraClass': 'multi-select',
        'selectbox': this.multiselect,
        'content': this.itemscontainer,
        'scrollspeed' : 30
      });
      if(this.list.length <= this.height - 2) this.scrollbar.hideScrollBtn();
    }
  },
  
  getList: function() {
    this.list = dojo.query('.multiselectitem', this.itemscontainer);
    this.list.forEach(function(el, i){
      if(this.type != 'simple') {
        el.i = 0;
        if(i) {
          el.i = this.ids[this.type][i-1];
        }
      } else {
          el.i = this.ids[this.type][i];
      }
    },this);
  },
  
  setSize: function() {
    dojo.style(this.multiselect, {
	   'height': this.height * this.lineHeight +  'px'
	});
  },
    
  setSelected: function() {
    var arr = this.hidden.value.split('|');
    if ((arr.length < this.list.length-1 && arr.length != 0) || this.type == 'simple') {
      dojo.forEach(arr, function(item, i){
        this.list.forEach(function(el, j){
          if (el.i == item){
            dojo.addClass(this.list[j], 'selected');
            if(this.mode == 2) 
              this.hidden.selectedIndex = el.i; 
          }
        }, this);
      }, this);
    } else {
        dojo.addClass(this.list[0], 'selected');
    }
  },
  
  selectItem: function(e) {
    if(this.mode == 1 && e.target.i == 0){
      this.allItemSelection();
    }else{
      if(this.mode == 1 && dojo.hasClass(this.list[0], 'selected')) {
        this.list.forEach(function(el, i){
          dojo.removeClass(el, 'selected');
        },this);
        if(dojo.hasClass(e.target, 'selected')) {
          dojo.removeClass(e.target, 'selected');
        } else {
          dojo.addClass(e.target, 'selected');
        }        
      }else if(this.mode == 2){
        this.list.forEach(function(el, i){
          dojo.removeClass(el, 'selected');
        },this);
        dojo.addClass(e.target, 'selected');
        this.hidden.selectedIndex = e.target.i;
      }else{
        if(dojo.hasClass(e.target, 'selected')) {
          dojo.removeClass(e.target, 'selected');
        } else {
          dojo.addClass(e.target, 'selected');
        }
      }
    }
    this.getValues(0);
  },

  allItemSelection: function() {
    this.list.forEach(function(el, i){
      dojo.removeClass(el, 'selected');
    },this);
    
    dojo.addClass(this.list[0], 'selected');
  },
  
  getValues: function(mode) {
    var val = 0;
    this.list.forEach(function(el, i){
      if (dojo.hasClass(el, 'selected') || mode == 1) {
        (val) ? val += '|' + el.i : val = el.i;
      }
    },this);
    if(val != this.hidden.value){
      this.hidden.value = val;
      //this.fireEvent(this.hidden, 'change');
    }
     this.fireEvent(this.hidden, 'change');
  },
  
  /*
  *Menutypeselector
  */
  
  typeSelectorInit: function() {
    var ts = this.name.replace('joomlamenutype', 'joomlamenu');
    this.typeselector = dojo.byId(ts);
    dojo.connect(this.typeselector, 'onchange', this, 'changeMenuItems');
    return this.typeselector.value;
  },    
  
  changeMenuItems: function() {
    this.type = this.typeselector.value;    
    this.itemscontainer.innerHTML = this.data[this.type];
    this.setSize();
    this.getList();
    this.hidden.value = '';
    this.scrollbar.scrollReInit();
    if(this.list.length <= this.height - 2) this.scrollbar.hideScrollBtn();
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
  }
 
});