<?php
/**
 * @version		$Id: config.php 221 2011-06-11 17:30:33Z happy_noodle_boy $
 * @package      JCE
 * @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
 * @author		Ryan Demmer
 * @license      GNU/GPL
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
class WFEmotionsPluginConfig
{
	public function getConfig(&$settings)
	{
		// Get JContentEditor instance
		$wf 	= WFEditor::getInstance();

		$smilies = 'smiley-confused.gif,smiley-cool.gif,smiley-cry.gif,smiley-eek.gif,smiley-embarassed.gif,smiley-evil.gif,smiley-laughing.gif,smiley-mad.gif,smiley-neutral.gif,smiley-roll.gif,smiley-sad.gif,smiley-surprised.gif,smiley-tongue_out.gif,smiley-wink.gif,smiley-yell.gif,smiley-smile.gif';

		$settings['emotions_smilies'] 	= $wf->getParam('emotions.smilies');
		$settings['emotions_url'] 		= $wf->getParam('emotions.url');
	}
}
?>