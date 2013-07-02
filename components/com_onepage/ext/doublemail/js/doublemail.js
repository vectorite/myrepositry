/*
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

 function doublemail_checkMail()
{
 
 msg = document.getElementById('email2_info'); 
 if (!doubleEmailCheck())
     msg.style.display = 'block';
   else 
  msg.style.display = 'none';
  
  return true;
}

function doubleEmailCheck(useAlert)
{
 e1 = document.getElementById('email_field'); 
 e2 = document.getElementById('email2_field');
 msg = document.getElementById('email2_info'); 
 if (e1 !== null && e2 != null)
 {
   if (e1.value != e2.value)
   {
     if (useAlert != null && useAlert == true)
     {
       msg_txt = msg.innerHTML; 
       alert(msg_txt);
     }
     return false;
   }
   else 
 {
  return true;
 }
 } 
  return true;
}