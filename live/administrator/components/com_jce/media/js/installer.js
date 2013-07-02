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
(function($){$.jce.Installer={init:function(options){$('#tabs').tabs();$('button#install_button').button({icons:{primary:'icon-install'}});$('button.install_uninstall').button({icons:{primary:'icon-remove'}}).click(function(e){if($('div#tabs input:checkbox:checked').length){$(this).addClass('ui-state-loading');$('input[name="task"]').val('remove');$('form[name="adminForm"]').submit();}
e.preventDefault();});}};})(jQuery);