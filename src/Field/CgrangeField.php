<?php
/**
* CG Scroll - Joomla Module
* Version			: 4.3.4
* Package			: Joomla 3.10.x - 4.x - 5.x
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

namespace ConseilGouz\Module\CGScroll\Site\Field;

defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Form\Field\RangeField;
use Joomla\CMS\Factory;
use Joomla\Component\Modules\Administrator\Model\ModuleModel;

class CgrangeField extends RangeField
{
    public $type = 'Cgrange';

    /**
     * Name of the layout being used to render the field
     *
     * @var    string
     * @since  3.7
     */
    protected $layout = 'conseilgouz.cgrange';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   3.2
     */
    protected function getInput()
    {
        /** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->registerAndUseStyle('cgrange', 'media/mod_cg_scroll/css/cgrange.css');
        $wa->registerAndUseScript('cgrange', 'media/mod_cg_scroll/js/cgrange.js');

        return $this->getRenderer($this->layout)->render($this->collectLayoutData());
    }
}
