<?php
/*
 *  Akeeba Backup Lazy Scheduling
 *  Copyright (C) 2010-2013  Nicholas K. Dionysopoulos / AkeebaBackup.com
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

$app = JFactory::getApplication();
if (!$app->isSite())
{
	/**
	 * I have been telling you for eighteen months to disable the plugin. If you
	 * have not been listening to me, now you'll HAVE to.
	 */
	$app->enqueueMessage('The System - Akeeba Backup Lazy Scheduling plugin has been deprecated since May 2011. We warned you that it would be removed in a later version. That "later version"? It is NOW! In order to get rid of this message please go to Extensions, Manage Plugins and UNPUBLISH this plugin.', 'warning');
}