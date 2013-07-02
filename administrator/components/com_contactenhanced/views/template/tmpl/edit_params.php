<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     	Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
$app		= &JFactory::getApplication();

$fieldSets = $this->form->getFieldsets('params');
foreach ($fieldSets as $name => $fieldSet) :
	echo JHtml::_('sliders.panel',JText::_($fieldSet->label), $name.'-params');
	if (isset($fieldSet->description) && trim($fieldSet->description)) :
		echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
	endif;
	?>
	<fieldset class="panelform" >
		<ul class="adminformlist">
			<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<li><?php echo $field->label; ?>
				<?php echo $field->input; ?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
<?php endforeach; 

echo JHtml::_('sliders.panel',JText::_('CE_TPL_AVAILABLE_SYNTAXES'), 'template-params');
?>
<table class="admintable"  style="width:100%;padding:0 5px 0 10px">
				<tr>
					<td valign="top" class="key">
						{name}
					</td>
					<td valign="top">
						[<?php echo JText::_( "The User's name" ); ?>]
					</td>
				
				</tr><tr>
					<td valign="top" class="key">
						{email}
					</td>
					<td valign="top">
						[<?php echo JText::_( "The User's email" ); ?>]
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{subject}
					</td>
					<td valign="top">
						[<?php echo JText::_( "The subject of the message" ); ?>]
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						{site_url}
					</td>
					<td valign="top">
						<?php echo JURI::root(); ?>
					</td>
				</tr><tr>
					<td valign="top" class="key">
				
						{site_name}
					</td>
					<td valign="top">
						<?php echo $app->getCfg('sitename'); ?>
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{site_link}
					</td>
					<td valign="top">
						<?php 
						$site_link	= JHTML::_('link', JURI::root(),$app->getCfg('sitename')); 
						echo $site_link; ?>
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_url}
					</td>
					<td valign="top">
						[<?php echo JText::_( "URL of the contact" ); ?>]
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_link}
					</td>
					<td valign="top">
						[<?php echo JText::_( "Link to the contact" ); ?>]		
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						{custom_fields}
					</td>
					<td valign="top">
						[<?php echo JText::_( "Displays all custom fields" ); ?>	]
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{enquiry}
					</td>
					<td valign="top">
						<?php echo JText::sprintf( "COM_CONTACTENHANCED_MAILENQUIRY", $site_link ).'<br />User Name < useremail@domain.com >'; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						{system_info}
					</td>
					<td valign="top">
						<?php echo ceHelper::getSystemInfo($this->params);?>
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{timestamp}
					</td>
					<td valign="top">
						<?php 
						
						jimport('joomla.utilities.date');
						$tz	= new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
					//	echo ceHelper::print_r($tz); exit;
						$date = new JDate(time());
						$date->setTimezone($tz);
				
						echo $date->format(JText::_('DATE_FORMAT_LC2'),true);
						?>
					</td>
				</tr>
			
				<tr>
					<td valign="top" class="key">
						{contact_id}
					</td>
					<td valign="top">
						[<?php echo JText::_( "The contact's id" ); ?>]
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_name}
					</td><td valign="top">
						[[<?php echo JText::_( "Contact Name" ); ?>]
					</td>
				</tr><tr>
					<td valign="top" class="key">
				
						{contact_con_position}
					</td>
					<td valign="top">
						[<?php echo JText::_( "Contact Position" ); ?>]
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_address}
					</td>
					<td valign="top">
						[<?php echo JText::_( "Contact's Mail address" ); ?>]
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_suburb}
					</td>
					<td valign="top">
						
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_state}
					</td>
					<td valign="top">
				
						
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_country}
					</td>
					<td valign="top">
						
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_postcode}
					</td>
				
					<td valign="top">
						
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_telephone}
					</td>
					<td valign="top">
						
					</td>
				</tr><tr>
					<td valign="top" class="key">
				
						{contact_fax}
					</td>
					<td valign="top">
						
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_misc}
					</td>
					<td valign="top">
						
					</td>
				
				</tr><tr>
					<td valign="top" class="key">
						{contact_sidebar}
					</td>
					<td valign="top">
						
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_image}
					</td>
					<td valign="top">
						[<?php echo JText::_( "The file name of the contact's image" ); ?>]
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_email_to}
					</td>
					<td valign="top">
						[<?php echo JText::_( "The contact's email" ); ?>]
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						{contact_mobile}
					</td>
					<td valign="top">
						
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_webpage}
					</td>
					<td valign="top">
						
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_lat}
					</td>
					<td valign="top">
						[<?php echo JText::_( "Latitude of the contact's address" ); ?>]
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_lng}
					</td>
					<td valign="top">
						[<?php echo JText::_( "longitude of the contact's address" ); ?>]
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						{contact_extra_field_1}
					</td>
					<td valign="top">
						[<?php echo JText::_( "The content of the Contact 'Extra field 1'" ); ?>]
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_extra_field_2}
					</td>
					<td valign="top">
				
						
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_extra_field_3}
					</td>
					<td valign="top">
						
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_extra_field_4}
					</td>
				
					<td valign="top">
						
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{contact_extra_field_5}
					</td>
					<td valign="top">
						
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						{contact_category_name}
					</td>
				
					<td valign="top">
						
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						{DATE_FORMAT_LC}
					</td>
					<td valign="top">
						<?php echo $date->format(JText::_('DATE_FORMAT_LC'),true); ?>
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{DATE_FORMAT_LC1}
					</td>
					<td valign="top">
						<?php echo $date->format(JText::_('DATE_FORMAT_LC1'),true); ?>
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{DATE_FORMAT_LC2}
					</td>
					<td valign="top">
					<?php echo $date->format(JText::_('DATE_FORMAT_LC2'),true); ?>	
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{DATE_FORMAT_LC3}
					</td>
					<td valign="top">
						<?php echo $date->format(JText::_('DATE_FORMAT_LC3'),true); ?>
					</td>
				</tr><tr>
					<td valign="top" class="key">
						{DATE_FORMAT_LC4}
					</td>
					<td valign="top">
						<?php echo $date->format(JText::_('DATE_FORMAT_LC4'),true); ?>
					</td>
				</tr>
				</table>