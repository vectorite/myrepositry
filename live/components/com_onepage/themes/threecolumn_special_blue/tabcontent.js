
function linkToCustomersTab(loc) {
  
}

function edit_basket(el)
{
 col = getElementsByClassName('op_update_form'); 
 for (var i = 0; i<col.length; i++ )
  {
    col[i].style.display = 'block'; 
  }
 col2 = getElementsByClassName('static_line'); 
 for (var j = 0; j<col2.length; j++ )
 {
   col2[j].style.display = 'none';
 }
 el.innerHTML = ''; 
 //d = document.getElementById('op_update_form'); 
 //d.style.display = 'block';
 return false;
}

function inputclear2(what)
{
 document.getElementById('label_'+what.id).innerHTML = ''; 
 clearMissing(what); 
}

function clearMissing(what)
{
  if (typeof what.options != 'undefined' && (what.options != null))
  id = what.id + '_div'; 
  else
  id = what.id.replace('_field', '_div'); 
  d = document.getElementById(id); 
  if (d != null)
  if (d.className.toString().indexOf('missing')>=0)
    {
	 d.style.color = 'transparent';
     d.className = 'formLabel';
	}

}

function inputclear(what){

  document.getElementById('label_'+what.id).innerHTML = '';
  clearMissing(what);   
  
 
 

  

  if (typeof what.onblur == 'undefined' || what.onblur == null)
  {
  what.onblur = function (evt) {
     inputreset(what);
   };
  }
  else
  {
  if (what.onblur.toString().indexOf('runSS')>=0)
  {
  what.onblur = function (evt) {
     inputreset(what);
	 op_runSS(); 
   };
  }
  else
  if (what.onblur.toString().indexOf('doublemail_')>=0)
  {
  what.onblur = function (evt) {
     doubleEmailCheck(true);
	 inputreset(what);
   };
  
  }
  what.onfocus = function (evt) {
	inputclear2(what); 
  };
  }

}
function clearState(what)
{
 if (what.options != null)
 {
  id = what.id+'_div';
  var d = document.getElementById(id); 
  if (what.options[what.selectedIndex].value != '')
  if (d.className.toString().indexOf('missing')>=0)
    {
	 d.style.color = 'transparent';
     d.className = 'formLabel';
	}
 
 }


}
function inputreset(what)
{
 if (what.value == '')
 {
 document.getElementById('label_'+what.id).innerHTML = document.getElementById('saved_'+what.id).value; 
  if (typeof what.alt != 'undefined' && what.alt != null)
   {
     if (what.alt.indexOf('*')== (what.alt.length-1))
	  {
	    if (typeof what.options != 'undefined' && (what.options != null))
		id = what.id+'_div'; 
		else
	    id = what.id.replace('_field', '_div'); 
		var d = document.getElementById(id); 
		if (d.className.toString().indexOf('missing')<0)
			{
				d.style.color = 'red';
				d.className = 'formLabel missing';
			}
	  }
   }
 }
 else {
  id = what.id.replace('_field', '_div'); 
  var d = document.getElementById(id); 
  if (d.className.toString().indexOf('missing')>=0)
    {
	 if (typeof d.htmlFor != 'undefined' && (d.htmlFor != null))
	 d.style.color = ''; 
	 else
	 d.style.color = 'transparent';
     d.className = 'formLabel';
	}
 }
 
 
}

function clickclear(thisfield, defaulttext) {
if (thisfield.value == defaulttext) {
thisfield.value = "";
}
}

function clickrecall(thisfield, defaulttext) {
if (thisfield.value == "") {
thisfield.value = defaulttext;
}
}

function op_hideFx(id)
{
  d = document.getElementById(id); 
  if (d != null)
   d.style.display = 'none'; 

}

function op_unhideFx(id)
{
	
  d = document.getElementById(id); 
  if (d != null)
   d.style.display = 'block'; 
}

function tabClick(tabid)
{
  
  // ul 
  tab = document.getElementById(tabid);
  ul2 = tab.parentNode;
  ul = ul2.parentNode;
  for (var i = 0; i<ul.childNodes.length; i++)
  {
   ul.childNodes[i].className = ""; 
   for (var j = 0; j<ul.childNodes[i].childNodes.length; j++)
    {
     if (ul.childNodes[i].childNodes[j].className == "selected")
     {
      ul.childNodes[i].childNodes[j].className = "";
     }
    }
   
  }
  // li
  tab.parentNode.className = "selected";
  tab.className = "selected"
  
  var tabcon = document.getElementById(tab.rel);
  var parentn = document.getElementById('tabscontent');
  for (i=0; i<parentn.childNodes.length; i++)
  {
    if (typeof(parentn.childNodes[i].style) != 'undefined')
    if (parentn.childNodes[i].id != tab.rel)
    parentn.childNodes[i].style.display = 'none';
    else parentn.childNodes[i].style.display = 'block';
  }
  return false;
}


function op_login()
{
 
 document.getElementById('opc_option').value = op_com_user;
 //document.adminForm.task.value = op_com_user_task;
 document.getElementById('opc_task').value = op_com_user_task;
 
 document.adminForm.action = op_com_user_action;
 document.adminForm.controller.value = 'user'; 
 document.adminForm.view.value = ''; 
 
 //alert(op_com_user_task+' '+op_com_user_action+' '+op_com_user); 
 //return false;
 if (document.adminForm.username != null)
 document.adminForm.username.value = document.adminForm.username_login.value;
 else
 {
    var usern = document.createElement('input');
    usern.setAttribute('type', 'hidden');
    usern.setAttribute('name', 'username');
    usern.setAttribute('value', document.getElementById('username_login').value);
    document.adminForm.appendChild(usern);
 }
 
 document.adminForm.submit();
 return true;
}


function submitenter(el, e)
{
 var charCode;
    
    if(e && e.which){
        charCode = e.which;
    }else if(window.event){
        e = window.event;
        charCode = e.keyCode;
    }


if (charCode == 13)
   {
   op_login();
   return false;
   }
else
   return true;
}