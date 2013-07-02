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
if($item->fib){
  $this->stack[] = $item->parent;
  $this->level = count($this->stack);
}
if($this->up){
  while($this->level > $item->level){
?>
</dl></dd>
<?php
    array_pop($this->stack);
    $this->level = count($this->stack);
  }
  $this->up = false;
}

$classes = array('level'.$this->level, 'off-nav-'.$item->id, ($item->p ? "parent" : "notparent"), ($item->opened ? "opened" : ""), ($item->active ? "active" : ""));
if(isset($this->openedlevels[$this->level]) && $item->p){
  $classes[] = 'opened forceopened';
}
if($item->fib){
  $classes[] = 'first';
}
if($item->lib){
  $classes[] = 'last';
}
$classes = implode(' ', $classes);
if($item->fib):
?>
<dl <?php if($this->level == 1): ?>id="offlajn-accordion-<?php echo $this->_module->id ?>-<?php echo $this->level ?>"<?php endif; ?> class="level<?php echo $this->level ?>">
<?php endif; ?>
  <dt class="<?php echo $classes ?>">
    <span class="outer">
      <span class="inner">
        <?php echo $item->nname; ?>
      </span>
    </span>
  </dt>
  <dd class="<?php echo $classes ?>">
    <?php if($item->p): $this->renderItem(); else: ?>
  </dd>
  <?php endif; ?>
<?php
if($item->lib):
  $this->up = true;
?>
<?php if($item->level == 1): ?>
</dl>
<?php endif; ?>
<?php endif; ?>