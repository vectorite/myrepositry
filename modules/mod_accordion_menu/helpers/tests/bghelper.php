<?php
/*-------------------------------------------------------------------------
# mod_accordion_menu - Accordion Menu - Offlajn.com
# -------------------------------------------------------------------------
# @ author    Roland Soos
# @ copyright Copyright (C) 2012 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
require_once(dirname(__FILE__).'/../bg.php');

  $generator = new OfflajnBgHelper();
  
  //$result = $generator->generateBackground("FF00006b");
  $result = $generator->generateGradientBackground("1-ABCC3D-597305", 1, 300);
  
  echo $result;
?>
<html>
  <head>
    <title>background-color generator</title>
  </head>
  <body>
    <div style="<?php echo $result; ?> height: 300px; width: 300px;">
    
    </div>
  </body>
</html>