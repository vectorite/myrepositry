
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