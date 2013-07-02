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
defined( '_JEXEC' ) or die('ERROR_403');
?>
<fieldset><legend>{#fullpage_dlg.meta_props}</legend>

<table border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td class="nowrap"><label for="metatitle">{#fullpage_dlg.meta_title}</label>&nbsp;</td>
		<td><input type="text" id="metatitle" name="metatitle" value=""
			class="mceFocus" /></td>
	</tr>
	<tr>
		<td class="nowrap"><label for="metakeywords">{#fullpage_dlg.meta_keywords}</label>&nbsp;</td>
		<td><textarea id="metakeywords" name="metakeywords" rows="4"></textarea></td>
	</tr>
	<tr>
		<td class="nowrap"><label for="metadescription">{#fullpage_dlg.meta_description}</label>&nbsp;</td>
		<td><textarea id="metadescription" name="metadescription" rows="4"></textarea></td>
	</tr>
	<tr>
		<td class="nowrap"><label for="metaauthor">{#fullpage_dlg.author}</label>&nbsp;</td>
		<td><input type="text" id="metaauthor" name="metaauthor" value="" /></td>
	</tr>
	<tr>
		<td class="nowrap"><label for="metacopyright">{#fullpage_dlg.copyright}</label>&nbsp;</td>
		<td><input type="text" id="metacopyright" name="metacopyright"
			value="" /></td>
	</tr>
	<tr>
		<td class="nowrap"><label for="metarobots">{#fullpage_dlg.meta_robots}</label>&nbsp;</td>
		<td><select id="metarobots" name="metarobots">
			<option value="">{#not_set}</option>
			<option value="index,follow">{#fullpage_dlg.meta_index_follow}</option>
			<option value="index,nofollow">{#fullpage_dlg.meta_index_nofollow}</option>
			<option value="noindex,follow">{#fullpage_dlg.meta_noindex_follow}</option>
			<option value="noindex,nofollow">{#fullpage_dlg.meta_noindex_nofollow}</option>
		</select></td>
	</tr>
</table>
</fieldset>

<fieldset><legend>{#fullpage_dlg.langprops}</legend>

<table border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td class="column1"><label for="docencoding">{#fullpage_dlg.encoding}</label></td>
		<td><select id="docencoding" name="docencoding">
			<option value="">{#not_set}</option>
		</select></td>
	</tr>
	<tr>
		<td class="nowrap"><label for="doctype">{#fullpage_dlg.doctypes}</label>&nbsp;</td>
		<td><select id="doctype" name="doctype">
			<option value="">{#not_set}</option>
		</select></td>
	</tr>
	<tr>
		<td class="nowrap"><label for="langcode">{#fullpage_dlg.langcode}</label>&nbsp;</td>
		<td><input type="text" id="langcode" name="langcode" value="" /></td>
	</tr>
	<tr>
		<td class="column1"><label for="langdir">{#fullpage_dlg.langdir}</label></td>
		<td><select id="langdir" name="langdir">
			<option value="">{#not_set}</option>
			<option value="ltr">{#fullpage_dlg.ltr}</option>
			<option value="rtl">{#fullpage_dlg.rtl}</option>
		</select></td>
	</tr>
	<tr>
		<td class="nowrap"><label for="xml_pi">{#fullpage_dlg.xml_pi}</label>&nbsp;</td>
		<td><input type="checkbox" id="xml_pi" name="xml_pi" class="checkbox" /></td>
	</tr>
</table>
</fieldset>
