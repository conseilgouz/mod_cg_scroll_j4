<?php
/**
* CG Scroll - Joomla Module 
* Version			: 4.2.6 
* Package			: Joomla 3.10.x - 4.x - 5.x
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filter\OutputFilter;
use ConseilGouz\Module\CGScroll\Site\Helper\CGScrollHelper;

JLoader::registerNamespace('ConseilGouz\Module\CGScroll\Site', JPATH_SITE . '/modules/mod_cg_scroll/src', false, false, 'psr4');

if (!empty($feed) && is_string($feed))
{
	echo $feed;
}
else {
	$lang = Factory::getLanguage();
	$myrtl = $params->get('rssrtl');
	$direction = " ";

	if ($lang->isRTL() && $myrtl == 0) {
		$direction = " redirect-rtl";
	}
	// Feed description
	elseif ($lang->isRTL() && $myrtl == 1) {
		$direction = " redirect-ltr";
	}
	elseif ($lang->isRTL() && $myrtl == 2) {
		$direction = " redirect-rtl";
	}
	elseif ($myrtl == 0) {
		$direction = " redirect-ltr";
	}
	elseif ($myrtl == 1) {
		$direction = " redirect-ltr";
	}
	elseif ($myrtl == 2) {
		$direction = " redirect-rtl";
	}
	$sh_button = $params->get('rssupdn', 1);
?>

<div id="cg_scroll_<?php echo $module->id; ?>" class="cg_scroll" data="<?php echo $module->id ?>">
<?php
	if (($module->showtitle == 0) && (!$params->get('rsstitle', 1)) && (!$params->get('rssdesc', 1)) && (!$params->get('rssimage', 1))) {
		$sh_button = 0;
	}
	if ($module->showtitle == 1) {
		CGScrollHelper::showDirection($num_sf,$sf_direction); 
	}
	//$iUrl	= isset($feed->image)	? $feed->image	: null;
	//$iTitle = isset($feed->imagetitle) ? $feed->imagetitle : null;
    $tags_list = $params->get('tags',array());	
	$tags = array();
	if (!is_null($tags_list)) {
		foreach ($tags_list as $key) {
			$tags[]= CGScrollHelper::getTags($key);
		}
	}
	?>
	<div style="direction: <?php echo $rssrtl ? 'rtl' :'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right' :'left'; ?> ! important"  class="cg-scroll" data="<?php echo $module->id ?>">
	<?php
	if (!is_null($feed->title) && $params->get('rsstitle', 1)) {
	?>
		<h2 class="cg-scroll-title <?php echo $direction; ?>">
			<a href="<?php echo str_replace('&', '&amp;', $rssurl); ?>" target="_blank" rel="noopener noreferrer"> 
				<?php echo $feed->title; ?></a>
		</h2>
	<?php
	}
	if ($params->get('rssdesc', 1)) {
		echo $feed->description; 
	}
	if ($params->get('rssimage', 1) && $feed->image) {
		?>
			<img src="<?php echo $feed->image->uri; ?>" alt="<?php echo $feed->image->title; ?>" class="cg-scroll-feed-img"/>
	<?php }
	if (($sh_button == 1) && ($module->showtitle == 0)) { // show up/down button 
			CGScrollHelper::showDirection($num_sf,$sf_direction); 
	}
	?>
		<div id="sfdmarqueecontainer" data="<?php echo $module->id ?>">
		<div id="vmarquee" style="position: absolute;">		

	<!-- Show items -->
		<ul class="cg-scroll-items" >
		<?php 
    	// on change l'ordre d'affichage: plus proche en premier
		if ($params->get('rssdaterev',1) == 0) { 
			$feed = $feed->reverseItems();
		}
	for ($twice = 0; $twice < 2; $twice++) { // 2.3.5 : continuous scroll effect
		for ($i = 0; $i < $params->get('rssitems', 5); $i++)
		{
			if (!$feed->offsetExists($i)) {
				break;
			}
			$text = !empty($feed[$i]->content) ||  !is_null($feed[$i]->content) ? $feed[$i]->content : $feed[$i]->description;
			$ignore = false;
			if (count($tags_list) > 0)	{
			    $ignore = true;
			    foreach ($tags as $tag) {
					$pos = stripos($text, $tag);
				    if ($pos !== false) {
						$ignore = false;
					}
				}
			}
			$uneDate = date('Ymd',strtotime($feed[$i]->publishedDate));
			if (( ($uneDate > date('Ymd') && $params->get('rssdatesup')) || (!($params->get('rssdatesup')) )) && !$ignore){
			?>
			<?php
				$uri = (!empty($feed[$i]->uri) || !is_null($feed[$i]->uri)) ? $feed[$i]->uri : $feed[$i]->guid;
				$uri = substr($uri, 0, 4) != 'http' ? $params->get('rsslink') : $uri;
				$text = !empty($feed[$i]->content) ||  !is_null($feed[$i]->content) ? $feed[$i]->content : $feed[$i]->description;

			?>
				<li>
					<?php // setlocale(LC_TIME, 'fr_FR.utf8','fra');
					    $laDate = date('d/m/Y',strtotime($feed[$i]->publishedDate));
						$letitle = $feed[$i]->title;
						if ($params->get('rsstitlelgth',60) > 0) {
						$letitle = HTMLHelper::_('string.truncate', $feed[$i]->title, $params->get('rsstitlelgth',60), $noSplit = false, $allowHtml = false);
						}
						if (!empty($uri)) : ?>
						<h5 class="feed-link">
						<a href="<?php echo $uri; ?>" target="_blank" rel="noopener noreferrer"> 
						<?php  echo $letitle; ?><?php if ($params->get('rssdatepub')) {  echo '<br>'.$laDate; }?></a></h5>
					<?php else : ?>
						<h5 class="feed-link"><?php  echo $letitle; ?><?php if ($params->get('rssdatepub')) { echo '<br>'.$laDate; } ?></h5>
					<?php  endif; ?>

					<?php if ($params->get('rssitemdesc') && !empty($text)) : ?>
						<div class="feed-item-description">
						<?php
							if ($params->get('rssimage', 1) == 0) {  // 2.2.8
							// Strip the images.
							   $text = OutputFilter::stripImages($text);
                            }
							// $text = JHtml::_('string.truncate', $text, $params->get('word_count'));
                            $text = CGScrollHelper::truncateString($text,$params->get('word_count'));
							echo str_replace('&apos;', "'", $text);
						?>
						</div>
					<?php endif; ?>
				</li>
		<?php }
			} 
	}?>
		</ul>
	</div>
	</div>
	</div>
</div>
	<?php 
}	
	?>

