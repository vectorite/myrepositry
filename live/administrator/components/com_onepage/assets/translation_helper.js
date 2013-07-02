/*
* This function is ran by AJAX
* 
*/

function op_runSST(el, command)
{

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
    username = document.getElementById('nickname').value; 

    if (command == 'update')
    {
     translation = op_escape(el.value); 
     var query = 'translation_var='+el.name+'&translation='+translation+'&nickname='+username+'&command=update';
    }
    else
    {
     //el.onclick = "";
     var query = 'command=generatefile&nickname='+username;
    }
    var url = op_secureurl+"";
    xmlhttp2.open("POST", url, true);
    
    //Send the proper header information along with the request
    xmlhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp2.setRequestHeader("Content-length", query.length);
    xmlhttp2.setRequestHeader("Connection", "close");
    xmlhttp2.onreadystatechange= op_get_SST_response ;
    
    xmlhttp2.send(query); 
    
    
 }
 return false;
}

  function op_escape(str)
  {
   if ((typeof(str) != 'undefined') && (str != null))
   {
     x = str.split("&").join("%26");
     return x;
   }
   else 
   return "";
  }
  
  
function op_get_SST_response()
{

  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
    {
    // here is the response from request
    var resp = xmlhttp2.responseText;
    if (resp != null) 
    {
        
		var a = resp.split('>><<');
		if (a.length > 0 && (a[0].indexOf('hash')>=0))
		{
		 id = a[0]; 
		 ai = id.indexOf('hash');
		 id = id.substr(ai); 
		 var delfo = document.getElementById(id);
		 if (delfo != null) 
		 {
		 delfo.innerHTML = a[1];
		 if (id.indexOf('generate')>=0)
		 {
		   delfo.href = a[2]; 
		   delfo.onlick = "return true;";
		 }
		 }
		 else 
		  {
		    
		  }
		}
		else 
		{
		 document.getElementById('resp_msg').innerHTML = resp + document.getElementById('resp_msg').innerHTML; 
		 
		}
       
    }
    
  } 
  else
  {
    if (typeof console != 'undefined')
	if (console != null)
	 console.log(xmlhttp2);
  }  
}


