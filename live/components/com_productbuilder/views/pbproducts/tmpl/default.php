<?php
/**
* VM product builder component
* @version $Id: pbproducts/default.php 2.0 2012-22-3 22:22 sakisTerz $
* @package VM product builder front-end
* @subpackage views
* @author Sakis Terzis (sakis@breakDesigns.net)
* @copyright	Copyright (C)2008- 2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
* see administrator/components/com_catfiltering/COPYING.txt
*/

defined( '_JEXEC' ) or die( 'Restricted Access');

$bund_counter=count($this->items);
$counter=1;
$total_count=1;
$bund_per_row=$this->params->get('pbproducts_per_row',2);?>


<div class="pbproducts<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; 
	

foreach($this->items as $item){
	$this->item=$item;
    if($counter==1){?>
        <div id="product_list" style="width: 100%; float: none;">
         <?php  }

    echo $this->loadTemplate('pbproduct');    
    if($counter==$bund_per_row || $total_count==$bund_counter){
          $counter=0;?>
          <div style="clear:both;"></div>
          </div>
		<?php }
		$counter++;
        $total_count++;
}

if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) :?>
		<div class="pagination">
						<?php  if ($this->params->def('show_pagination_results', 1)) : ?>
						<p class="counter">
								<?php echo $this->pagination->getPagesCounter(); ?>
						</p>

				<?php endif; ?>
				<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
<?php  endif; ?>
</div>