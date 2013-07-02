<?php
/**
 * @package     JCE Fullpage
 * @copyright 	Copyright (C) 2005 - 2010 Ryan Demmer. All rights reserved.
 * @copyright 	Copyright (C) 2010 Moxiecode Systems AB. All rights reserved.
 * @author		Ryan Demmer
 * @author		Moxiecode
 * @license 	http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see licence.txt
 * JCE is free software. This version may have been modified pursuant
 * to the GNU Lesser General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU Lesser General Public License or
 * other free or open source software licenses.
 */
defined( '_JEXEC' ) or die('RESTRICTED');
?>
<fieldset><legend>{#fullpage_dlg.appearance_textprops}</legend>

<table border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td class="column1"><label for="fontface">{#fullpage_dlg.fontface}</label></td>
		<td><select id="fontface" name="fontface"
			onchange="FullPageDialog.changedStyleProp();">
			<option value="">{#not_set}</option>
		</select></td>
	</tr>

	<tr>
		<td class="column1"><label for="fontsize">{#fullpage_dlg.fontsize}</label></td>
		<td><select id="fontsize" name="fontsize"
			onchange="FullPageDialog.changedStyleProp();">
			<option value="">{#not_set}</option>
		</select></td>
	</tr>

	<tr>
		<td class="column1"><label for="textcolor">{#fullpage_dlg.textcolor}</label></td>
		<td>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><input id="textcolor" name="textcolor" type="text" value="" class="color"
					size="9"
					onchange="updateColor('textcolor_pick','textcolor');FullPageDialog.changedStyleProp();" /></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</fieldset>

<fieldset><legend>{#fullpage_dlg.appearance_bgprops}</legend>

<table border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td class="column1"><label for="bgimage">{#fullpage_dlg.bgimage}</label></td>
		<td>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><input id="bgimage" name="bgimage" type="text" value="" class="browser"
					onchange="FullPageDialog.changedStyleProp();" /></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td class="column1"><label for="bgcolor">{#fullpage_dlg.bgcolor}</label></td>
		<td>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><input id="bgcolor" name="bgcolor" type="text" value="" size="9" class="color"
					onchange="updateColor('bgcolor_pick','bgcolor');FullPageDialog.changedStyleProp();" /></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</fieldset>

<fieldset><legend>{#fullpage_dlg.appearance_marginprops}</legend>

<table border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td class="column1"><label for="leftmargin">{#fullpage_dlg.left_margin}</label></td>
		<td><input id="leftmargin" name="leftmargin" type="text" value=""
			onchange="FullPageDialog.changedStyleProp();" /></td>
		<td class="column1"><label for="rightmargin">{#fullpage_dlg.right_margin}</label></td>
		<td><input id="rightmargin" name="rightmargin" type="text" value=""
			onchange="FullPageDialog.changedStyleProp();" /></td>
	</tr>
	<tr>
		<td class="column1"><label for="topmargin">{#fullpage_dlg.top_margin}</label></td>
		<td><input id="topmargin" name="topmargin" type="text" value=""
			onchange="FullPageDialog.changedStyleProp();" /></td>
		<td class="column1"><label for="bottommargin">{#fullpage_dlg.bottom_margin}</label></td>
		<td><input id="bottommargin" name="bottommargin" type="text" value=""
			onchange="FullPageDialog.changedStyleProp();" /></td>
	</tr>
</table>
</fieldset>

<fieldset><legend>{#fullpage_dlg.appearance_linkprops}</legend>

<table border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td class="column1"><label for="link_color">{#fullpage_dlg.link_color}</label></td>
		<td>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><input id="link_color" name="link_color" type="text" class="color" value=""
					size="9"
					onchange="updateColor('link_color_pick','link_color');FullPageDialog.changedStyleProp();" /></td>
			</tr>
		</table>
		</td>

		<td class="column1"><label for="visited_color">{#fullpage_dlg.visited_color}</label></td>
		<td>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><input id="visited_color" name="visited_color" class="color" type="text"
					value="" size="9"
					onchange="updateColor('visited_color_pick','visited_color');FullPageDialog.changedStyleProp();" /></td>
			</tr>
		</table>
		</td>
	</tr>

	<tr>
		<td class="column1"><label for="active_color">{#fullpage_dlg.active_color}</label></td>
		<td>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><input id="active_color" name="active_color" class="color" type="text"
					value="" size="9"
					onchange="updateColor('active_color_pick','active_color');FullPageDialog.changedStyleProp();" /></td>
			</tr>
		</table>
		</td>

		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</table>
</fieldset>

<fieldset><legend>{#fullpage_dlg.appearance_style}</legend>

<table border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td class="column1"><label for="stylesheet">{#fullpage_dlg.stylesheet}</label></td>
		<td>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><input id="stylesheet" name="stylesheet" type="text" class="browser" value="" /></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td class="column1"><label for="style">{#fullpage_dlg.style}</label></td>
		<td><input id="style" name="style" type="text" value=""
			onchange="FullPageDialog.changedStyle();" /></td>
	</tr>
</table>
</fieldset>
