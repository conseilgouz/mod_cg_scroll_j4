<?php

/**
* CG Scroll - Joomla Module 
* Package			: Joomla 4.x - 5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The article news module service provider.
 *
 * @since  4.2.0
 */
return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.2.0
     */
    public function register(Container $container)
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\ConseilGouz\\Module\\CGScroll'));
        $container->registerServiceProvider(new HelperFactory('\\ConseilGouz\\Module\\CGScroll\\Site\\Helper'));

        $container->registerServiceProvider(new Module());
    }
};
