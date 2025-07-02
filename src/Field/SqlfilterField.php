<?php
/**
* CG Scroll - Joomla Module
* Package			: Joomla 3.10.x - 4.x - 5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

namespace ConseilGouz\Module\CGScroll\Site\Field;

defined('_JEXEC') or die;
use Joomla\CMS\Form\Field\SqlField;
use Joomla\CMS\Factory;
use Joomla\Component\Modules\Administrator\Model\ModuleModel;

class SqlfilterField extends SqlField
{
    public $type = 'Sqlfilter';

    /**
     * Method to check if SQL query contains errors
     * @return  array  The field option objects or empty (if error in query)
     */
    protected function getOptions()
    {
        $app = Factory::getApplication();
        $input = $app->input;
        $moduleid = $input->get('id');
        $model = new ModuleModel(array('ignore_request' => true));
        $module = $model->getItem($moduleid);
        $lang = $module->language;

        $options = array();

        // Initialize some field attributes.
        $key   = $this->keyField;
        $value = $this->valueField;
        $header = $this->header;

        // Get the database object.
        $db = $this->getDatabase();
        try {
            // Set the query and get the result list.
            $this->query = "SELECT a.id as article_id, CONCAT(a.title,' (',cat.title,')') as value FROM #__content as a ";
            $this->query .= " INNER JOIN #__categories cat on a.catid = cat.id";
            if ($lang != '*') {
                $this->query .= " WHERE a.language in ('*','".$lang."') AND a.state = 1";
            }
            $this->query .= " ORDER BY a.title";
            $db->setQuery($this->query);
        } catch (\Exception $e) {
            return $options; // SQL Error : return empty
        }
        try {
            $items = $db->loadObjectlist();
        } catch (\Exception $e) {
            return $options; // SQL Error : return empty
        }
        // No error : execute SQL
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
