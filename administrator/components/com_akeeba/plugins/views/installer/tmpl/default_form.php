<?php // no direct access
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.3
 */

defined( '_JEXEC' ) or die();

JHtml::_('behavior.framework');
?>

<?php if(version_compare(JVERSION, '3.0', 'lt')): ?>
<script type="text/javascript" language="javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.getElementById('adminForm');

		// do field validation
		if (form.install_package.value == ""){
			alert("<?php echo JText::_('COM_INSTALLER_MSG_INSTALL_PLEASE_SELECT_A_PACKAGE', true); ?>");
		} else {
			form.installtype.value = 'upload';
			form.submit();
		}
	}

	Joomla.submitbutton3 = function(pressbutton) {
		var form = document.getElementById('adminForm');

		// do field validation
		if (form.install_directory.value == ""){
			alert("<?php echo JText::_('COM_INSTALLER_MSG_INSTALL_PLEASE_SELECT_A_DIRECTORY', true); ?>");
		} else {
			form.installtype.value = 'folder';
			form.submit();
		}
	}

	Joomla.submitbutton4 = function(pressbutton) {
		var form = document.getElementById('adminForm');

		// do field validation
		if (form.install_url.value == "" || form.install_url.value == "http://"){
			alert("<?php echo JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL', true); ?>");
		} else {
			form.installtype.value = 'url';
			form.submit();
		}
	}
	
	function showWhatsthis() {
		if($('whatsthis').getStyle('display') == 'block') {
			$('whatsthis').setStyle('display','none');
		} else {
			$('whatsthis').setStyle('display','block');
		}
	}
</script>
<?php else: ?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.getElementById('adminForm');

		// do field validation
		if (form.install_package.value == ""){
			alert("<?php echo JText::_('COM_INSTALLER_MSG_INSTALL_PLEASE_SELECT_A_PACKAGE', true); ?>");
		} else {
			form.installtype.value = 'upload';
			form.submit();
		}
	}

	Joomla.submitbutton3 = function(pressbutton) {
		var form = document.getElementById('adminForm');

		// do field validation
		if (form.install_directory.value == ""){
			alert("<?php echo JText::_('COM_INSTALLER_MSG_INSTALL_PLEASE_SELECT_A_DIRECTORY', true); ?>");
		} else {
			form.installtype.value = 'folder';
			form.submit();
		}
	}

	Joomla.submitbutton4 = function(pressbutton) {
		var form = document.getElementById('adminForm');

		// do field validation
		if (form.install_url.value == "" || form.install_url.value == "http://"){
			alert("<?php echo JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL', true); ?>");
		} else {
			form.installtype.value = 'url';
			form.submit();
		}
	}
</script>
<?php endif; ?>

<div id="installer-install">
<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_akeeba&view=installer');?>" method="post" name="adminForm" id="adminForm">
<?php if(version_compare(JVERSION, '3.0', 'lt')): ?>
	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>
	<div class="width-70 fltlft">
		<fieldset class="uploadform">
			<legend><?php echo JText::_('COM_INSTALLER_UPLOAD_PACKAGE_FILE'); ?></legend>
			<label for="install_package"><?php echo JText::_('COM_INSTALLER_PACKAGE_FILE'); ?></label>
			<input class="input_box" id="install_package" name="install_package" type="file" size="57" />
			<input class="button" type="button" value="<?php echo JText::_('COM_INSTALLER_UPLOAD_AND_INSTALL'); ?>" onclick="Joomla.submitbutton()" />
		</fieldset>
		<div class="clr"></div>
		<fieldset class="uploadform">
			<legend><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_DIRECTORY'); ?></legend>
			<label for="install_directory"><?php echo JText::_('COM_INSTALLER_INSTALL_DIRECTORY'); ?></label>
			<input type="text" id="install_directory" name="install_directory" class="input_box" size="70" value="<?php echo $this->state->get('install.directory'); ?>" />			<input type="button" class="button" value="<?php echo JText::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton3()" />
		</fieldset>
		<div class="clr"></div>
		<fieldset class="uploadform">
			<legend><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_URL'); ?></legend>
			<label for="install_url"><?php echo JText::_('COM_INSTALLER_INSTALL_URL'); ?></label>
			<input type="text" id="install_url" name="install_url" class="input_box" size="70" value="http://" />
			<input type="button" class="button" value="<?php echo JText::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton4()" />
		</fieldset>
		<input type="hidden" name="type" value="" />
		<input type="hidden" name="installtype" value="upload" />
		<input type="hidden" name="task" value="doInstall" />
		<input type="hidden" name="<?php echo JFactory::getSession()->getToken()?>" value="1" />
	</div>
<?php else: ?>
  	<?php if(!empty( $this->sidebar)): ?>
      <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
      </div>  
      <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
    <?php endif;?>
  	
  	<!-- Render messages set by extension install scripts here -->
  	<?php if ($this->showMessage) : ?>
  	<?php echo $this->loadTemplate('message'); ?>
  	<?php endif; ?>
  	
  	<ul class="nav nav-tabs">
  		<li class="active"><a href="#upload" data-toggle="tab"><?php echo JText::_('COM_INSTALLER_UPLOAD_PACKAGE_FILE'); ?></a></li>
  		<li><a href="#directory" data-toggle="tab"><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_DIRECTORY'); ?></a></li>
  		<li><a href="#url" data-toggle="tab"><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_URL'); ?></a></li>
  		<?php if ($this->ftp) : ?>
  			<li><a href="#ftp" data-toggle="tab"><?php echo JText::_('COM_INSTALLER_INSTALL_FTP'); ?></a></li>
  		<?php endif; ?>
  	</ul>
  	<div class="tab-content">
  		<div class="tab-pane active" id="upload">
  			<fieldset class="uploadform">
  				<legend><?php echo JText::_('COM_INSTALLER_UPLOAD_PACKAGE_FILE'); ?></legend>
  				<div class="control-group">
  					<label for="install_package" class="control-label"><?php echo JText::_('COM_INSTALLER_PACKAGE_FILE'); ?></label>
  					<div class="controls">
  						<input class="input_box" id="install_package" name="install_package" type="file" size="57" />
  					</div>
  				</div>
  				<div class="form-actions">
  					<input class="btn btn-primary" type="button" value="<?php echo JText::_('COM_INSTALLER_UPLOAD_AND_INSTALL'); ?>" onclick="Joomla.submitbutton()" />
  				</div>
  			</fieldset>
  		</div>
  		<div class="tab-pane" id="directory">
  			<fieldset class="uploadform">
  				<legend><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_DIRECTORY'); ?></legend>
  				<div class="control-group">
  					<label for="install_directory" class="control-label"><?php echo JText::_('COM_INSTALLER_INSTALL_DIRECTORY'); ?></label>
  					<div class="controls">
  						<input type="text" id="install_directory" name="install_directory" class="span5 input_box" size="70" value="<?php echo $this->state->get('install.directory'); ?>" />
  					</div>
  				</div>
  				<div class="form-actions">
  					<input type="button" class="btn btn-primary" value="<?php echo JText::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton3()" />
  				</div>
  			</fieldset>
  		</div>
  		<div class="tab-pane" id="url">
  			<fieldset class="uploadform">
  				<legend><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_URL'); ?></legend>
  				<div class="control-group">
  					<label for="install_url" class="control-label"><?php echo JText::_('COM_INSTALLER_INSTALL_URL'); ?></label>
  					<div class="controls">
  						<input type="text" id="install_url" name="install_url" class="span5 input_box" size="70" value="http://" />
  					</div>
  				</div>
  				<div class="form-actions">
  					<input type="button" class="btn btn-primary" value="<?php echo JText::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton4()" />
  				</div>
  			</fieldset>
  		</div>
  		<?php if ($this->ftp) : ?>
  		<div class="tab-pane" id="ftp">
  			<?php echo $this->loadTemplate('ftp'); ?>
  		</div>
  		<?php endif; ?>
  	<input type="hidden" name="type" value="" />
  	<input type="hidden" name="installtype" value="upload" />
  	<input type="hidden" name="task" value="doInstall" />
  	<?php echo JHtml::_('form.token'); ?>
  	
  	</div>
<?php endif; ?>
</form>

<?php if(version_compare(JVERSION, '3.0', 'gt')): ?>
<div class="row-fluid">
	<div class="span10">
		<p class="well">
			<img src="../media/com_akeeba/icons/akeeba-16.png" align="bottom" />
			<em>
				<?php echo JText::_('INSTALLER_ENHANCEDBY');?> <a href="https://www.AkeebaBackup.com">Akeeba Backup</a>
			</em>
			&bull;
			<a href="index.php?option=com_installer&skipsrp=1"><?php echo JText::_('INSTALLER_SWITCHTOREGULAR') ?></a>
		</p>

		<div class="alert">
			<h4><?php echo JText::_('INSTALLER_WHATSTHIS') ?></h4>
			<?php echo JText::_('INSTALLER_WHATSTHIS_TEXT') ?>
		</div>
	</div>
</div>
<?php else: ?>
<div class="width-70 fltlft">
<p>
	<img src="../media/com_akeeba/icons/akeeba-16.png" align="bottom" />
	<em>
		<?php echo JText::_('INSTALLER_ENHANCEDBY');?> <a href="https://www.AkeebaBackup.com">Akeeba Backup</a>
	</em>
	&bull;
	<a href="index.php?option=com_installer&skipsrp=1"><?php echo JText::_('INSTALLER_SWITCHTOREGULAR') ?></a>
	&bull;
	<a href="javascript:showWhatsthis()"><?php echo JText::_('INSTALLER_WHATSTHIS') ?></a>
</p>

<fieldset id="whatsthis" style="display:none">
	<legend><?php echo JText::_('INSTALLER_WHATSTHIS') ?></legend>
	<?php echo JText::_('INSTALLER_WHATSTHIS_TEXT') ?>
</fieldset>

</div>
<?php endif; ?>
</div>