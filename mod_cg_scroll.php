<?php
/**
* CG Scroll - Joomla Module 
* Version			: 4.3.0
* Package			: Joomla 3.10.x - 4.x - 5.x
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;
use ConseilGouz\Module\CGScroll\Site\Helper\CGScrollHelper;

JLoader::registerNamespace('ConseilGouz\Module\CGScroll\Site', JPATH_SITE . '/modules/mod_cg_scroll/src', false, false, 'psr4');

$document 		= Factory::getDocument();
$baseurl 		= URI::base();
$modulefield	= ''.URI::base(true).'/media/'.$module->module.'/';

//Get this module id
$nummod_sf		= $module->id;
$num_sf		= 'mod'.$nummod_sf;

$sf_type = $params->get('sf_type', 'FEED');
$sf_height	= $params->get('sf_height', 200);
$sf_width   = $params->get('sf_width', 200);
if ($sf_type == 'FEED') {
	$rssitems   = $params->get('rssitems',3);
} else {
	$rssitems   = $params->get('catitems',3);
}
$sf_delay	= $params->get('sf_delay', 1);
$sf_speed	= $params->get('sf_speed', 2);
$sf_pause	= $params->get('sf_pause', 1);
$sf_w_img	= $params->get('sf_w_img', '100'); 
$rssurl		= $params->get('rssurl', '');
$rssrtl		= $params->get('rssrtl', 0);
$sf_direction = $params->get('direction', 0);
$sf_delay	= $sf_delay * 1000;
$sf_w_img   = str_replace('%','',$sf_w_img);   
$sf_wimg_responsive=(100 - $sf_w_img)/2;
$sf_slowdown = $params->get('sf_extraslow', 0);

if ($sf_w_img>='55'): $margin_item_image='padding: 0 '.$sf_wimg_responsive.'% ;'; endif; 
if ($sf_w_img<='54'): $margin_item_image="margin-right:5px; float:left;"; endif;         

$j = new Version();
$version=substr($j->getShortVersion(), 0,1); 
if ($version < "4") { // Joomla 3.x
	$document->addStyleSheet("//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css");
	$document->addStyleSheet($modulefield.'css/scroll_j3.css');
	$document->addScript($modulefield.'js/scroll.js');
} else {
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = Factory::getDocument()->getWebAssetManager();
	$wa->registerAndUseStyle('scroll','media/'.$module->module.'/css/scroll.css');
    if ((bool)Factory::getConfig()->get('debug')) { // Mode debug
        $document->addScript($modulefield.'js/scroll.js'); 
    } else {
        $wa->registerAndUseScript('scroll','media/'.$module->module.'/js/scroll.js');
    }
}
if ($sf_type == 'FEED') {
	$feed = CGScrollHelper::getFeed($params);
	$count = $params->get('rssitems', 5);
	if (is_array($feed) && ($count > count($feed)))  { $count = count($feed);}
	$document->addScriptOptions('mod_cg_scroll_'.$module->id, 
                array('id' => $module->id,'speed' => $sf_speed, 'pause' => $sf_pause, 'height' => $sf_height, 'width' => $sf_width, 'direction' => $sf_direction,'count'=> $count,'delay'=>$sf_delay,'slowdown'=>$sf_slowdown));
	require ModuleHelper::getLayoutPath('mod_cg_scroll', 'defaultFeed');
} elseif ($sf_type == 'CATEGORY') {
	$category = $params->get('category_id',1);
	$article = CGScrollHelper::getCategory($category,$params);
	$count = $params->get('catitems', 5);
	if ($count > count($article))  { $count = count($article);}
	$document->addScriptOptions($module->module.'_'.$module->id, 
                array('id' => $module->id,'speed' => $sf_speed, 'pause' => $sf_pause, 'height' => $sf_height, 'width' => $sf_width, 'direction' => $sf_direction,'count'=> $count,'delay'=>$sf_delay,'slowdown'=>$sf_slowdown));
	require ModuleHelper::getLayoutPath($module->module, 'defaultArticle');
} elseif ($sf_type == 'LATEST') {
	$categories = $params->get('categories_id');
	$article = CGScrollHelper::getLatest($categories,$params);
	$count = $params->get('catitems', 5);
	if ($count > count($article))  { $count = count($article);}
	$document->addScriptOptions($module->module.'_'.$module->id, 
               array('id' => $module->id,'speed' => $sf_speed, 'pause' => $sf_pause, 'height' => $sf_height, 'width' => $sf_width, 'direction' => $sf_direction,'count'=> $count,'delay'=>$sf_delay,'slowdown'=>$sf_slowdown));
	require ModuleHelper::getLayoutPath($module->module, 'defaultArticle');
} else {
	$articleId = $params->get('article_id',1);
	$article = CGScrollHelper::getArticle($articleId,$params);
	$document->addScriptOptions($module->module.'_'.$module->id, 
                array('id' => $module->id,'speed' => $sf_speed, 'pause' => $sf_pause, 'height' => $sf_height, 'width' => $sf_width, 'direction' => $sf_direction,'count'=> 1,'delay'=>$sf_delay,'slowdown'=>$sf_slowdown));
	require ModuleHelper::getLayoutPath($module->module, 'defaultArticle');
}
?>