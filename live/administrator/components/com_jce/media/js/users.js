/*  
 * JCE Editor                 2.2.8.4
 * @package                 JCE
 * @url                     http://www.joomlacontenteditor.net
 * @copyright               Copyright (C) 2006 - 2012 Ryan Demmer. All rights reserved
 * @license                 GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
 * @date                    15 October 2012
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * NOTE : Javascript files have been compressed for speed and can be uncompressed using http://jsbeautifier.org/
 */
(function($){$.jce.Users={select:function(){var u=[],v,s,o,h;s=window.parent.document.getElementById('users');$('input:checkbox:checked').each(function(){v=$(this).val();if(u=document.getElementById('username_'+v)){h=$.trim(u.innerHTML);if($.jce.Users.check(s,v)){return;}
var li=document.createElement('li');s.appendChild(li);li.innerHTML='<input type="hidden" name="users[]" value="'+v+'" /><label><span class="users-list-delete"></span>'+h+'</label>';}});},check:function(s,v){$.each(s.childNodes,function(i,n){var input=n.firstChild;if(input.value===v){return true;}});return false;}};window.selectUsers=$.jce.Users.select;})(jQuery);