<?php
/**
* @version		3.0
* @copyright	Copyright (C) 2007-2011 Stephen Brandon
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldModulelist extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $type = 'Modulelist';


	function getInput() {
		$doc = & JFactory::getDocument();
		$doc->addStyleDeclaration( JFormFieldModulelist::makeStyle() );
		$r  = '<div style="float:right; background-color:white; border:1px solid black; padding:3px;margin-bottom:3px;"><a id="mm_toggler" onclick="mm_toggle(this)" rel="false">' . JText::_('MM_BUTTON_SHOW_DISABLED') . '</a></div>';
		$r .= '<div style="clear:both"></div>';
		$r .= '<div id="all-mod-lists">';
		$r .= JFormFieldModulelist::makeList( true, 'title');
		$r .= JFormFieldModulelist::makeList( false, 'title');
		$r .= '</div>';

$r .= '
	<script type="text/javascript" src="../modules/mod_metamod/fields/grid.js"></script>
	
		<script type="text/javascript">
			window.addEvent("domready", function(){ 
				new Grid($("modulelist-enabledonly"));
				new Grid($("modulelist"));
			});
			function mm_toggle( sender ) {
				sender = $(sender);
				if (sender.getProperty("rel") == "false") {
					sender.setProperty("rel","true");
					sender.set("text", "' . JText::_('MM_BUTTON_HIDE_DISABLED') . '");
					$("modulelist-enabledonly").setStyle("display","none");
					$("modulelist").setStyle("display","");
				} else {
					sender.setProperty("rel","false");
					sender.set("text", "' . JText::_('MM_BUTTON_SHOW_DISABLED') . '");
					$("modulelist-enabledonly").setStyle("display","");
					$("modulelist").setStyle("display","none");
				}
			}
		</script>

';
		return $r;
	}
	
	function makeStyle() {
		return '
			table {}
			table#modulelist-enabledonly td, table#modulelist td
				{ padding: 1px 6px 1px 0; }
			tr.r1 { background-color: #e8e8e8; }
			table.modulelist th.sort { padding-right: 0.4em; padding-left:1px; font-size:110%; }
			table.modulelist td.modid { text-align:right; }
			table.modulelist th.modid { text-align:right; }
			table.modulelist th.hover { background-color:#d0ffd0; }
			table.modulelist img { margin: 0; padding: 0;}
			div#all-mod-lists { height:390px; overflow:auto; background-color:white; }
			ul.mm-variables li li { margin-left: 2em; }
			ul.mm-variables ul { margin-bottom: 0.5em; }
			';
	}
	
	function makeList( $enabledonly = true, $order ) {
		$orderby = "title";
		switch( $order ) {
			case "title":
				$orderby = "title";
				break;
			case "titledesc":
				$orderby = "title desc";
				break;
			case "enabled":
				$orderby = "published, title";
				break;
			case "enableddesc":
				$orderby = "published desc, title";
				break;
			case "id":
				$orderby = "id";
				break;
			case "iddesc":
				$orderby = "id desc";
				break;
			case "type":
				$orderby = "module, title";
				break;
			case "typedesc":
				$orderby = "module desc, title";
				break;
		}		
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true); 
		
		$query->select("id, title, module, position, published");
		$query->from("#__modules");
		if ( $enabledonly ) {
			$query->where("published = 1");
		}
		$query->where("client_id = 0");
		$query->order($orderby);

		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		$arrow = '../modules/mod_metamod/images/updown.png';
		$tick  = '../modules/mod_metamod/images/tick.png';
		$cross = '../modules/mod_metamod/images/cross.png';

		$r = '<table id="modulelist' . ($enabledonly ? "-enabledonly" : '') . '" style="' . ($enabledonly ? '' : 'display:none;') .'" 
				class="modulelist" cellpadding="0" cellspacing="0" border="0" width="100%">
		<thead>
		<tr>
		<th class="modid sort" axis="int" nowrap="nowrap">' . JText::_('MM_MODLIST_ID') . '&nbsp;<img src="'. $arrow .'" alt=""  /></th>
		<th class="sort" axis="string" nowrap="nowrap">' . JText::_('MM_MODLIST_NAME') . '&nbsp;<img src="'. $arrow .'" alt=""  /></th>
		<th class="sort" axis="string" nowrap="nowrap">' . JText::_('MM_MODLIST_TYPE') . '&nbsp;<img src="'. $arrow .'" alt=""  /></th>
		<th class="sort" axis="string" nowrap="nowrap">' . JText::_('MM_MODLIST_POSITION') . '&nbsp;<img src="'. $arrow .'" alt=""  /></th>
		<th class="sort" axis="rel" nowrap="nowrap" style="padding-right:0">' . JText::_('MM_ENABLED') . '&nbsp;<img src="'. $arrow .'" alt=""  /></th>
		</tr>
		</thead>
		<tbody>';
		$counter = 0;
		foreach ($options as $o) {
			$counter = abs( --$counter );
			$r .= "<tr class=" . ($counter ? '"r1"' : '"r2"') .">\n";
			$r .= '<td class="modid"><b>'.$o->id.'</b></td>
				<td>'.$o->title.'</td>
				<td>'.$o->module.'</td>
				<td>'.$o->position.'</td>
				<td align="center" rel="' . ($o->published ? 0 : 1) . '"><img src="'.($o->published ? $tick : $cross).'" height="12" alt="#" /></td>';
			$r .= "</tr>\n";
		}
		$r .= "</tbody></table>";
		return $r;
	}
}