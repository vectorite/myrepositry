/*
* This part handles Javascript functions of configurator view
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

function sp_toggleD(showId)
{
 var s = document.getElementById("language_selector");
 if (s!=null)
 if (s.options != null)
 {
  for (var i=0; i<s.options.length; i++)
  {
   var val = s.options[i].value;
   var c = document.getElementById("lang_"+val+"_table");
   if (c!=null)
   if (c.style != null)
   if (c.style.display !=null)
   {
    if (s.options[i].selected)
    {
     c.style.display = '';
    } else
     c.style.display = 'none';
   }
  }
 }
}
function clearArticle(name)
{
  document.getElementById(name+'_id').value='';
  document.getElementById(name+'_name').value='';
  return true; 
}
function addnew()
{
 var html = document.getElementById("comeshere");
 var oldVars = [];
 if (html != null)
 {
  htmlOrig = html.innerHTML;
  op_next++;
  myId = "_new_"+op_next;
  
  for (var i = 1; i<op_next; i++)
  {
   oldVars[i] = [];
   var h = document.getElementById('hidepsid__new_'+i);
   oldVars[i]['ship'] = getMultiple(h);
   var h1 = document.getElementById('hidep__new_'+i);
   oldVars[i]['hp'] = getMultiple(h1);
   var h2 = document.getElementById('hidepdef__new_'+i);
   oldVars[i]['def'] = getMultiple(h2);
  }
  html.innerHTML += html1+myId+html2+myId+html21+myId+html3+myId+html31+myId+html4+myId+html41+myId+html5;
  for (var i = 1; i<op_next; i++)
  {
   var h = document.getElementById('hidepsid__new_'+i);
   setMultiple(h, oldVars[i]['ship']);
   var h1 = document.getElementById('hidep__new_'+i);
   setMultiple(h1, oldVars[i]['hp']);
   var h2 = document.getElementById('hidepdef__new_'+i);
   setMultiple(h2, oldVars[i]['def']);

  }
 }
 return false;
}

function getMultiple(ob)
{
 var arSelected = new Array();
 for (var i = 0; i<ob.options.length; i++)
 if (ob.options[i].selected)
  arSelected.push(i);
 return arSelected;
}

function setMultiple(ob, arSelected)
{
 for (var i = 0; i<arSelected.length; i++)
  ob.options[arSelected[i]].selected = true;
}
function op_langedit(lang)
{
 var lc = document.getElementById("op_"+lang+"_changed");
 if (lc != null)
 lc.value = 'yes';
}
function submitbutton2(task)
{

 var d = document.getElementById('task');
 d.value = task;
 document.adminForm.submit();
 return true;
}



function op_runAjax()
{
return true;  
	
	
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
     xmlhttp2=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
	xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
    }
    if (xmlhttp2!=null)
    {
    var url = op_ajaxurl+"index.php?option=com_onepage&view=config&format=raw";
	var query = ""; 
    xmlhttp2.open("POST", url, true);
    
    //Send the proper header information along with the request
    xmlhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp2.setRequestHeader("Content-length", query.length);
    xmlhttp2.setRequestHeader("Connection", "close");
    xmlhttp2.onreadystatechange= op_get_geo_response ;
    
	
	
    xmlhttp2.send(query); 
    
    
	}
 
}


function op_get_geo_response()
{
  
  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
    {
    var resp = xmlhttp2.responseText;
    if (resp != null) 
    {
	 d = document.getElementById('opc_language_editor').innerHTML = resp; 
     // CODE COMES HERE
    
    }
	}
	if (xmlhttp2.status != 200) 
	 {
	  
	 }
    return true;
}