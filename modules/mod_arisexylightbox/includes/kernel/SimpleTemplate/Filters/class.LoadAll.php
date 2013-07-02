<?php
/*
 * ARI Framework Lite
 *
 * @package		ARI Framework Lite
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

AriKernel::import('SimpleTemplate.Filters.Truncate');
AriKernel::import('SimpleTemplate.Filters.Replace');
AriKernel::import('SimpleTemplate.Filters.HtmlTruncate');
AriKernel::import('SimpleTemplate.Filters.RestoreTags');
AriKernel::import('SimpleTemplate.Filters.StripTags');
AriKernel::import('SimpleTemplate.Filters.UpperCase');
AriKernel::import('SimpleTemplate.Filters.LowerCase');
AriKernel::import('SimpleTemplate.Filters.Empty');
AriKernel::import('SimpleTemplate.Filters.Format');
AriKernel::import('SimpleTemplate.Filters.Filter');
AriKernel::import('SimpleTemplate.Filters.DBQuote');
AriKernel::import('SimpleTemplate.Filters.DateFormat');
AriKernel::import('SimpleTemplate.Filters.NumberFormat');
?>