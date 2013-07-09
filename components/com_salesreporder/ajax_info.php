<?php

define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

if (file_exists(dirname(__FILE__) . '/defines.php')) {
	include_once dirname(__FILE__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
	define('JPATH_BASE', dirname(dirname(dirname(__FILE__))));
	require_once JPATH_BASE.'/includes/defines.php';
}

require_once JPATH_BASE.'/includes/framework.php';

JDEBUG ? $_PROFILER->mark('afterLoad') : null;

$app = JFactory::getApplication('site');
$app->initialise();

	 $db = JFactory::getDBO();
	 $post = JRequest::get('post');
	 
	 if(!empty($post['mycountry_id']))
		 {
			$mystate = "select virtuemart_state_id,state_name from #__virtuemart_states where virtuemart_country_id = ".$post['mycountry_id'];
			$db->setQuery($mystate);
			$state_list_name = $db->loadObjectList();
?>
			<select required="" name="virtuemart_state_id" size="1" id="virtuemart_state_id" class="inputbox multiple" aria-invalid="false" onchange="myuserregistrationValidate1();">
				<option value="">-- Select --</option>
				<?php foreach($state_list_name as $mystatelist) : ?>
						<option value="<?php echo $mystatelist->virtuemart_state_id?>"<?php if($mystatelist->virtuemart_state_id==$user_info['virtuemart_state_id']) echo "selected='selected'" ?>><?php echo $mystatelist->state_name; ?></option>
				<?php endforeach; ?>
			</select>

		<?php } else {

	 if($post['state_id'] != '10')
		{	
			$sql = 'SELECT COUNT(*) FROM #__virtuemart_zipcode WHERE  zipcode = "'.$post['zipcode'].'"';				
			$db->setQuery($sql);
			$zipcode_id_count = $db->loadResult();
			
			if($zipcode_id_count > 0)
			 {
				echo "notcorrect"; die;
			 }
			else
			 {
				echo "correct"; die;
			 }
		}
	  else	
		{	
			$sql = 'SELECT id FROM #__virtuemart_zipcode WHERE virtuemart_state_id="'.$post['state_id'].'" AND zipcode = "'.$post['zipcode'].'"';				
			$db->setQuery($sql);
			$zipcode_id = $db->loadObject();
			if($zipcode_id > 0)
			{
				echo "correct"; die;
			}
			else
			{
				echo "notcorrect"; die;
			}
		}
	}

		
		