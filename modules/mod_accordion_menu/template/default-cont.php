<?php
/*-------------------------------------------------------------------------
# mod_accordion_menu - Accordion Menu - Offlajn.com
# -------------------------------------------------------------------------
# @ author    Roland Soos
# @ copyright Copyright (C) 2012 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><div id="<?php echo $module->containerinstanceid; ?>" class="<?php echo $params->get('moduleclass_sfx', 0); ?>">
  <div class="<?php echo $module->containerinstanceid; ?>-inner <?php echo $params->get('class_sfx', 0); ?>">
    <?php if($params->get('moduleshowtitle', 0) ): ?>
    <div class="title">
      <h3><?php echo $module->title; ?></h3>
    </div>
    <?php endif; ?>
    <div style="overflow: hidden; position: relative;">
    <?php $menu->render($tmpl); ?>
    </div>
  </div>
</div>