<?php
/**
* CG Scroll - Joomla Module 
* Package			: Joomla 3.10.x - 4.x - 5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
namespace ConseilGouz\Module\CGScroll\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

\defined('_JEXEC') or die;

/**
 * Dispatcher class
 *
 * @since  4.2.0
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    /**
     * Returns the layout data.
     *
     * @return  array
     *
     * @since   4.2.0
     */
    protected function getLayoutData()
    {
        $data = parent::getLayoutData();

        return $data;
    }
}
