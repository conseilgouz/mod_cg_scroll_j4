<?php
/**
* CG Scroll - Joomla Module 
* Version			: 4.3.2
* Package			: Joomla 3.10.x - 4.x - 5.x
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use ConseilGouz\Module\CGScroll\Site\Helper\CGScrollHelper;

JLoader::registerNamespace('ConseilGouz\Module\CGScroll\Site', JPATH_SITE . '/modules/mod_cg_scroll/src', false, false, 'psr4');

?>

<?php
	$lang = Factory::getLanguage();
	$myrtl = $params->get('rssrtl');
	$direction = " ";
	if ($lang->isRTL() && $myrtl == 0) {
		$direction = " redirect-rtl";
	} elseif ($lang->isRTL() && $myrtl == 1) {
		$direction = " redirect-ltr";
	} elseif ($lang->isRTL() && $myrtl == 2) {
		$direction = " redirect-rtl";
	} elseif ($myrtl == 0) {
		$direction = " redirect-ltr";
	} elseif ($myrtl == 1) {
		$direction = " redirect-ltr";
	} elseif ($myrtl == 2) {
		$direction = " redirect-rtl";
	}
	$sh_button = $params->get('rssupdn', 1);
?>
<?php
	if ($article != false)	{
		echo '<div id="cg_scroll_'.$module->id.'" class="cg_scroll" data="'.$module->id.'">';
		// Image handling
		$iUrl	= isset($article->image)	? $article->image	: null;
		$iTitle = isset($article->imagetitle) ? $article->imagetitle : null;
		if (($sh_button == 1) && ($module->showtitle == 1)) { 
			CGScrollHelper::showDirection($num_sf,$sf_direction); 
		}
		?>
	
		<div style="direction: <?php echo $rssrtl ? 'rtl' :'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right' :'left'; ?> ! important"  class="cg-scroll" data="<?php echo $module->id ?>">
		<?php
		if (($sh_button == 1) && ($module->showtitle == 0)) { // show up/down button 
			CGScrollHelper::showDirection($num_sf,$sf_direction); // 2.2.27
		}
		?>
		</div> 
		<div id="sfdmarqueecontainer" data="<?php echo $module->id ?>" >
		<div id="vmarquee" style="position: absolute;">		
		<!-- Show items -->
<?php	
	for ($twice = 0; $twice < 2; $twice++) { // continuous scroll effect
        echo '<ul class="cg-scroll-items-'.$twice.'"';
        if ($sf_direction == 0 ) { 
			echo 'style="width:'.(($sf_width*(sizeof($article)+1))).'px;"'; 
        }
        echo '>'; // end of ul
        for ($i = 0; $i < $params->get('catitems', 5); $i++) { 
		if (isset($article[$i])) {
			$text_type = $params->get( 'text_type', 'both' );
			$text = "";
			if ($text_type=='introtext') {
				$text = $article[$i]->introtext;
			} else if ($text_type=='fulltext') {
				$text = $article[$i]->fulltext;
			} else if ($text_type=='both') {
				$text = $article[$i]->introtext.$article[$i]->fulltext;
			}
			$uneDate = date('d/m/Y',strtotime($article[$i]->modified));
			?>
				<li>
                <?php  
                if ($params->get('articletitle') || $params->get('articledatepub')) { 
                    echo "<a href='".$article[$i]->link."'>";
                    echo "<h5>";
                    if ($params->get('articletitle') == 1) { 
                        echo $article[$i]->title; 
                    } 
                    if ($params->get('articledatepub') == 1) { 
                        echo ' ('.$uneDate.')'; 
                    } 
                    echo "</h5></a>";
                } 
                if ($params->get('articleimg',0) == 1) { // image d'intro
					echo LayoutHelper::render('joomla.content.intro_image', $article[$i]);
				}
				$text = CGScrollHelper::truncate($text, $params->get('char_count',10),false);
				echo str_replace('&apos;', "'", $text);
				?>
				
				</li>
		<?php }
	      }
          echo '</ul>';
		} 
        ?>
		</div>
	</div>
</div>	
	<?php 
	}
?>
