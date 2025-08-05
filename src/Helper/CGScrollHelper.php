<?php
/**
* CG Scroll - Joomla Module 
* Package			: Joomla 4.x/5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
// no direct access
namespace ConseilGouz\Module\CGScroll\Site\Helper;
defined('_JEXEC') or die;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Feed\FeedFactory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route; 
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper; 
use Joomla\Component\Content\Site\Model\ArticlesModel; 
use Joomla\Component\Content\Site\Model\ArticleModel; 
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class CGScrollHelper {
	//------------------------------FEED DISPLAY------------------------------------------//
	public static function getFeed($params) {
		// Module params
		$rssurl	= $params->get('rssurl', '');
		// Get RSS parsed object
		try
		{
			$feed = new FeedFactory;
			$rssDoc = $feed->getFeed($rssurl);
		}
		catch (\RuntimeException $e)
		{
			return Text::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
		}

		if (empty($rssDoc))
		{
			return Text::_('MOD_SF_ERR_FEED_NOT_RETRIEVED');
		}

		if ($rssDoc)
		{
			return $rssDoc;
		}
	}
	//-----------------------One article display------------------------------------------//
	static function getArticle(&$id, $params) {
		// Get an instance of the generic articles model
		// Get an instance of the generic articles model
		$model   = new ArticleModel(array('ignore_request' => true));
        if ($model) {
		// Set application parameters in model
		$app       = Factory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', 1);
		$model->setState('filter.published', 1);
		$model->setState('filter.featured', $params->get('show_front', 1) == 1 ? 'show' : 'hide');

		// Access filter
		$access = ComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = Access::getAuthorisedViewLevels(Factory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));

		// Date filter
		$date_filtering = $params->get('date_filtering', 'off');

		if ($date_filtering !== 'off')
		{
			$model->setState('filter.date_filtering', $date_filtering);
			$model->setState('filter.date_field', $params->get('date_field', 'a.created'));
			$model->setState('filter.start_date_range', $params->get('start_date_range', '1000-01-01 00:00:00'));
			$model->setState('filter.end_date_range', $params->get('end_date_range', '9999-12-31 23:59:59'));
			$model->setState('filter.relative_date', $params->get('relative_date', 30));
		}
		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());
		// Ordering
		$model->setState('list.ordering', 'a.hits');
		$model->setState('list.direction', 'DESC');

		$item = $model->getItem($id);

		$item->slug    = $item->id . ':' . $item->alias;
		$item->catslug = $item->catid . ':' . $item->category_alias;
		if ($access || in_array($item->access, $authorised))
		{
			// We know that user has the privilege to view the article
			$item->link = Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language));
		}
		else
		{
			$item->link = \JRoute::_('index.php?option=com_users&view=login');
		}
		// nettoyage texte + appliquer les plugins "content"
		$app = Factory::getApplication(); // Joomla 4.0
		$item_cls = new \stdClass;
		$item_cls->text = $item->introtext;
		$item_cls->id = $item->id;
		$item_cls->params = $params;
		$app->triggerEvent('onContentPrepare', array ('com_content.article', &$item_cls, &$item_cls->params, 0)); // Joomla 4.0
		$item->introtext = 	$item_cls->text;	
		$item->introtext = $params->get('articleclean', 1) == 1 ? self::cleanIntrotext($item->introtext) : $item->introtext;
		$item_cls = new \stdClass;
		$item_cls->text = $item->fulltext;
		$item_cls->id = $item->id;
		$item_cls->params = $params;
		$app->triggerEvent('onContentPrepare', array ('com_content.article', &$item_cls, &$item_cls->params, 0)); // Joomla 4.0
		$item->fulltext = 	$item_cls->text;	
		$item->fulltext = $params->get('articleclean', 1) == 1 ? self::cleanIntrotext($item->fulltext) : $item->fulltext;
		
		$arr[0] = $item;
        }
        else {
        	$arr = false;
        }
		return $arr;
	}
	//--------------------------------One category----------------------------------------------//
	static function getCategory($id, $params) {
		// Get an instance of the generic articles model
		$articles     = new ArticlesModel(array('ignore_request' => true));
		if ($articles) {
		// Set application parameters in model
		$app       = Factory::getApplication();
		$appParams = $app->getParams();
		$articles->setState('params', $appParams);

		// Set the filters based on the module params
		$articles->setState('list.start', 0);
		$articles->setState('list.limit', (int) $params->get('count', 0));
		$articles->setState('filter.published', 1);

		// Access filter
		$access     = ComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = Access::getAuthorisedViewLevels(Factory::getUser()->get('id'));
		$articles->setState('filter.access', $access);
		$catids = $id;
		$articles->setState('filter.category_id', $catids);		
	
		// Filter by language
		$articles->setState('filter.language', $app->getLanguageFilter());
		$items = $articles->getItems();
		
				// Display options
		$show_date        = $params->get('show_date', 0);
		$show_date_field  = $params->get('show_date_field', 'created');
		$show_date_format = $params->get('show_date_format', 'Y-m-d H:i:s');
		$show_category    = $params->get('show_category', 0);
		$show_hits        = $params->get('show_hits', 0);
		$show_author      = $params->get('show_author', 0);
		$show_introtext   = true;
		$introtext_limit  = $params->get('introtext_limit', 100);

		// Prepare data for display using display options
		foreach ($items as &$item)
		{
			$item->slug    = $item->id . ':' . $item->alias;
			$item->catslug = $item->catid . ':' . $item->category_alias;

			if ($access || in_array($item->access, $authorised))
			{
				$item->link = Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language));
			}
			else
			{
				$menu      = $app->getMenu();
				$menuitems = $menu->getItems('link', 'index.php?option=com_users&view=login');

				if (isset($menuitems[0]))
				{
					$Itemid = $menuitems[0]->id;
				}
				elseif ($app->input->getInt('Itemid') > 0)
				{
					// Use Itemid from requesting page only if there is no existing menu
					$Itemid = $app->input->getInt('Itemid');
				}

				$item->link = Route::_('index.php?option=com_users&view=login&Itemid=' . $Itemid);
			}

			// Used for styling the active article
			$item->displayDate = '';

			if ($show_date)
			{
				$item->displayDate = JHtml::_('date', $item->$show_date_field, $show_date_format);
			}

			if ($item->catid)
			{
				$item->displayCategoryLink = Route::_(RouteHelper::getCategoryRoute($item->catid));
				$item->displayCategoryTitle = $show_category ? '<a href="' . $item->displayCategoryLink . '">' . $item->category_title . '</a>' : '';
			}
			else
			{
				$item->displayCategoryTitle = $show_category ? $item->category_title : '';
			}

			$item->displayHits       = $show_hits ? $item->hits : '';
			$item->displayAuthorName = $show_author ? $item->author : '';

			// nettoyage texte + appliquer les plugins "content"
			$app = Factory::getApplication(); // Joomla 4.0
			$item_cls = new \stdClass;
			$item_cls->text = $item->introtext;
			$item_cls->id = $item->id;
			$item_cls->params = $params;
			$app->triggerEvent('onContentPrepare', array ('com_content.article', &$item_cls, &$item_cls->params, 0)); // Joomla 4.0
			$item->introtext = 	$item_cls->text;	
			$item->introtext = $params->get('articleclean', 1) == 1 ? self::cleanIntrotext($item->introtext) : $item->introtext;
			$item_cls = new \stdClass;
			$item_cls->text = $item->fulltext;
			$item_cls->id = $item->id;
			$item_cls->params = $params;
			$app->triggerEvent('onContentPrepare', array ('com_content.article', &$item_cls, &$item_cls->params, 0)); // Joomla 4.0
			$item->fulltext = 	$item_cls->text;	
			$item->fulltext = $params->get('articleclean', 1) == 1 ? self::cleanIntrotext($item->fulltext) : $item->fulltext;
			$item->displayReadmore  = $item->alternative_readmore;
		}

		return $items;
		}
		else { return false;
		}

	}
	// get tag title & tag alias
	public static function getTags($id) {
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);
		// Construct the query
		$query->select('tags.title as tag, tags.alias as alias ')
			->from('#__tags as tags')
			->where('tags.id = '.$id)
			;
		$db->setQuery($query);
		return $db->loadResult();
	}
	//------------------------------Latest articles of one category------------------------------------------//
	public static function getLatest($id,$params) {
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		// Get an instance of the generic articles model
		$model    = new ArticlesModel(array('ignore_request' => true));
		// Set application parameters in model
		$app       = Factory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);
		
		$show_introtext   = true;
		$introtext_limit  = $params->get('introtext_limit', 100);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));
		$model->setState('filter.published', 1);

		// This module does not use tags data
		$model->setState('load_tags', false);

		// Access filter
		$access     = ComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = Access::getAuthorisedViewLevels(Factory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Categories filter
		$model->setState('filter.category_id', $id);

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());
		// Featured switch
		$featured = $params->get('show_featured', '');

		if ($featured === '')
		{
			$model->setState('filter.featured', 'show');
		}
		elseif ($featured)
		{
			$model->setState('filter.featured', 'only');
		}
		else
		{
			$model->setState('filter.featured', 'hide');
		}

		// Set ordering
		$order_map = array(
			'm_dsc' => 'a.modified DESC, a.created',
			'mc_dsc' => 'CASE WHEN (a.modified = ' . $db->quote($db->getNullDate()) . ') THEN a.created ELSE a.modified END',
			'c_dsc' => 'a.created',
			'p_dsc' => 'a.publish_up',
			'random' => $db->getQuery(true)->Rand(),
		);

		$ordering = ArrayHelper::getValue($order_map, $params->get('ordering'), 'a.publish_up');
		$dir      = 'DESC';

		$model->setState('list.ordering', $ordering);
		$model->setState('list.direction', $dir);

		$items = $model->getItems();

		foreach ($items as &$item)
		{
			$item->slug    = $item->id . ':' . $item->alias;

			/** @deprecated Catslug is deprecated, use catid instead. 4.0 */
			$item->catslug = $item->catid . ':' . $item->category_alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article
				$item->link = Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language));
			}
			else
			{
				$item->link = \JRoute::_('index.php?option=com_users&view=login');
			}
			// nettoyage texte + appliquer les plugins "content"
			$app = Factory::getApplication(); // Joomla 4.0
			$item_cls = new \stdClass;
			$item_cls->text = $item->introtext;
			$item_cls->id = $item->id;
			$item_cls->params = $params;
			$app->triggerEvent('onContentPrepare', array ('com_content.article', &$item_cls, &$item_cls->params, 0)); // Joomla 4.0
			$item->introtext = 	$item_cls->text;	
			$item->introtext = $params->get('articleclean', 1) == 1 ? self::cleanIntrotext($item->introtext) : $item->introtext;
			$item_cls = new \stdClass;
			$item_cls->text = $item->fulltext;
			$item_cls->id = $item->id;
			$item_cls->params = $params;
			$app->triggerEvent('onContentPrepare', array ('com_content.article', &$item_cls, &$item_cls->params, 0)); // Joomla 4.0
			$item->fulltext = 	$item_cls->text;	
			$item->fulltext = $params->get('articleclean', 1) == 1 ? self::cleanIntrotext($item->fulltext) : $item->fulltext;
			$item->displayReadmore  = $item->alternative_readmore;

		}

		return $items;
	}
	
	public static function cleanIntrotext($introtext)
	{
		$introtext = str_replace('<p>', ' ', $introtext);
		$introtext = str_replace('</p>', ' ', $introtext);
		$introtext = strip_tags($introtext, '<a><em><strong><img>');
		$introtext = trim($introtext);
		return $introtext;
	}
	public static function cleanIntrotextNoImage($introtext,$params)
	{
		$introtext = str_replace('<p>', ' ', $introtext);
		$introtext = str_replace('</p>', ' ', $introtext);
		$introtext = strip_tags($introtext, '<a><em><strong><br>');
		$introtext = trim($introtext); 
		return $introtext;
	}
	/* from https://stackoverflow.com/questions/965235/how-can-i-truncate-a-string-to-the-first-20-words-in-php */
	public static function truncateString($string,$limit){
		$stripped_string =strip_tags(trim($string,' '),'<a><strong>'); // if there are HTML or PHP tags
		$string_array =explode(' ',$stripped_string);
		$truncated_array = array_splice($string_array,0,$limit);
		$truncated_string=implode(' ',$truncated_array);
		if (count($truncated_array) < count($string_array)) $truncated_string .= '...<br/>';
		return $truncated_string;
	}
	public static function truncate($html, $maxLength = 0)
	{
		$baseLength = strlen($html);
		$ptString = HTMLHelper::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = false);
		for ($maxLength; $maxLength < $baseLength;)
		{
			$htmlString = HTMLHelper::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = true);
			$htmlStringToPtString = HTMLHelper::_('string.truncate', $htmlString, $maxLength, $noSplit = true, $allowHtml = false);
			if ($ptString == $htmlStringToPtString)
			{
				return $htmlString;
			}
			$diffLength = strlen($ptString) - strlen($htmlStringToPtString);
			$maxLength += $diffLength;
			if ($baseLength <= $maxLength || $diffLength <= 0)
			{
				return $htmlString;
			}
		}
		return $html;
	}
	public static function showDirection($num_sf,$sf_direction) { 
		echo '<span id="toDirection">';
		if ($sf_direction == 1)	{ 
			echo '<span class="icon-dir-down fas"></span>';
			echo '<span id="toDirectionText"> </span>';
			echo '<span class="icon-dir-up fas"></span>';
			echo '<span id="toDirText"> </span>';
		} else {
			echo '<span class="icon-dir-left fas"></span>';
			echo '<span id="toDirectionText"> </span>';
			echo '<span class="icon-dir-right fas"></span>';
			echo '<span id="toDirText"> </span>';
		}
		echo '</span>';
	
	}
	public static function getAjax() {
        $input = Factory::getApplication()->input;
		$id = $input->get('id');
		$module = self::getModuleById($id);
		$params = new Registry($module->params);  		
        $output = '';
		if ($input->get('data') == "param") {
			return self::getParams($id,$params);
		}
		return false;
	}
	private static function getModuleById($id) {
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params')
			->from('#__modules AS m')
			->where('m.id = '.(int)$id);
		$db->setQuery($query);
		return $db->loadObject();
	}
	private static function getParams($id,$params) {
		$sf_type = $params->get('sf_type', 'FEED');		
		if ($sf_type == 'FEED') {
			$count   = $params->get('rssitems',3);
		} else {
			$count   = $params->get('catitems',3);
		}
		$sf_width   = $params->get('sf_width', 200);
		$sf_speed	= $params->get('sf_speed', 2);
		$sf_height	= $params->get('sf_height', 200);
		$sf_pause	= $params->get('sf_pause', 1);
		$sf_direction = $params->get('direction', 0);
		$ret = '{"id":"'.$id.'","speed":"'.$sf_speed.'","pause":"'.$sf_pause.'","height":"'.$sf_height.'","width":"'.$sf_width.'","direction":"'.$sf_direction.'","count":"'.$count.'"}';
		return $ret;		
	}
}