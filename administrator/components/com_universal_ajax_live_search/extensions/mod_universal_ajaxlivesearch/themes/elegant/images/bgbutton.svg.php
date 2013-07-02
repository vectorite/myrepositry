<?php 
/*------------------------------------------------------------------------
# mod_universal_ajaxlivesearch - Universal AJAX Live Search 
# ------------------------------------------------------------------------
# author    Janos Biro 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; ?>
<svg xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:svg="http://www.w3.org/2000/svg"
    xmlns="http://www.w3.org/2000/svg"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    width="100%"
    height="100%"
    version="1.0"
    >
	<style type="text/css">
		.start {
			stop-color: #<?php echo $gradient[0]; ?>;
			stop-opacity: 1;
		}
		.end
		{
			stop-color: #<?php echo $gradient[1]; ?>;
			stop-opacity: 1;
		}
	</style>
	<defs>
        <linearGradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" class="start"/>
            <stop offset="100%" class="end"/>
        </linearGradient>
    </defs>
    <rect x="0" y="0" rx="5" ry="5" fill="url(#gradient)" width="100%" height="100%" />
    <rect x="-10" y="0" rx="0" ry="0" fill="url(#gradient)" width="100%" height="100%" />
</svg>