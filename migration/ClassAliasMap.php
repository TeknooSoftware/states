<?php

/**
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

/**
 * Script to map all old namespace class (from Uni Alteri) to new Teknoo organization namespace
 */

$mapping = array (
    'UniAlteri\\States\\Command\\AbstractCommand' => 'Teknoo\\States\\Command\\AbstractCommand',
    'UniAlteri\\States\\Command\\ClassCreate' => 'Teknoo\\States\\Command\\ClassCreate',
    'UniAlteri\\States\\Command\\ClassInformation' => 'Teknoo\\States\\Command\\ClassInformation',
    'UniAlteri\\States\\Command\\Parser\\AbstractParser' => 'Teknoo\\States\\Command\\Parser\\AbstractParser',
    'UniAlteri\\States\\Command\\Parser\\Exception\\ClassNotFound' => 'Teknoo\\States\\Command\\Parser\\Exception\\ClassNotFound',
    'UniAlteri\\States\\Command\\Parser\\Exception\\UnReadablePath' => 'Teknoo\\States\\Command\\Parser\\Exception\\UnReadablePath',
    'UniAlteri\\States\\Command\\Parser\\Factory' => 'Teknoo\\States\\Command\\Parser\\Factory',
    'UniAlteri\\States\\Command\\Parser\\Proxy' => 'Teknoo\\States\\Command\\Parser\\Proxy',
    'UniAlteri\\States\\Command\\Parser\\State' => 'Teknoo\\States\\Command\\Parser\\State',
    'UniAlteri\\States\\Command\\Parser\\StatedClass' => 'Teknoo\\States\\Command\\Parser\\StatedClass',
    'UniAlteri\\States\\Command\\StateAdd' => 'Teknoo\\States\\Command\\StateAdd',
    'UniAlteri\\States\\Command\\StateList' => 'Teknoo\\States\\Command\\StateList',
    'UniAlteri\\States\\Command\\Writer\\AbstractWriter' => 'Teknoo\\States\\Command\\Writer\\AbstractWriter',
    'UniAlteri\\States\\Command\\Writer\\Factory' => 'Teknoo\\States\\Command\\Writer\\Factory',
    'UniAlteri\\States\\Command\\Writer\\Proxy' => 'Teknoo\\States\\Command\\Writer\\Proxy',
    'UniAlteri\\States\\Command\\Writer\\State' => 'Teknoo\\States\\Command\\Writer\\State',
    'UniAlteri\\States\\DI\\Container' => 'Teknoo\\States\\DI\\Container',
    'UniAlteri\\States\\DI\\ContainerInterface' => 'Teknoo\\States\\DI\\ContainerInterface',
    'UniAlteri\\States\\DI\\Exception\\ClassNotFound' => 'Teknoo\\States\\DI\\Exception\\ClassNotFound',
    'UniAlteri\\States\\DI\\Exception\\IllegalName' => 'Teknoo\\States\\DI\\Exception\\IllegalName',
    'UniAlteri\\States\\DI\\Exception\\IllegalService' => 'Teknoo\\States\\DI\\Exception\\IllegalService',
    'UniAlteri\\States\\DI\\Exception\\InvalidArgument' => 'Teknoo\\States\\DI\\Exception\\InvalidArgument',
    'UniAlteri\\States\\DI\\InjectionClosure' => 'Teknoo\\States\\DI\\InjectionClosure',
    'UniAlteri\\States\\DI\\InjectionClosureInterface' => 'Teknoo\\States\\DI\\InjectionClosureInterface',
    'UniAlteri\\States\\DI\\InjectionClosurePHP56' => 'Teknoo\\States\\DI\\InjectionClosurePHP56',
    'UniAlteri\\States\\Exception\\AvailableSeveralMethodImplementations' => 'Teknoo\\States\\Exception\\AvailableSeveralMethodImplementations',
    'UniAlteri\\States\\Exception\\ClassNotFound' => 'Teknoo\\States\\Exception\\ClassNotFound',
    'UniAlteri\\States\\Exception\\IllegalArgument' => 'Teknoo\\States\\Exception\\IllegalArgument',
    'UniAlteri\\States\\Exception\\IllegalFactory' => 'Teknoo\\States\\Exception\\IllegalFactory',
    'UniAlteri\\States\\Exception\\IllegalName' => 'Teknoo\\States\\Exception\\IllegalName',
    'UniAlteri\\States\\Exception\\IllegalProxy' => 'Teknoo\\States\\Exception\\IllegalProxy',
    'UniAlteri\\States\\Exception\\IllegalService' => 'Teknoo\\States\\Exception\\IllegalService',
    'UniAlteri\\States\\Exception\\IllegalState' => 'Teknoo\\States\\Exception\\IllegalState',
    'UniAlteri\\States\\Exception\\InvalidArgument' => 'Teknoo\\States\\Exception\\InvalidArgument',
    'UniAlteri\\States\\Exception\\MethodNotImplemented' => 'Teknoo\\States\\Exception\\MethodNotImplemented',
    'UniAlteri\\States\\Exception\\Standard' => 'Teknoo\\States\\Exception\\Standard',
    'UniAlteri\\States\\Exception\\StateNotFound' => 'Teknoo\\States\\Exception\\StateNotFound',
    'UniAlteri\\States\\Exception\\UnReadablePath' => 'Teknoo\\States\\Exception\\UnReadablePath',
    'UniAlteri\\States\\Exception\\UnavailableClosure' => 'Teknoo\\States\\Exception\\UnavailableClosure',
    'UniAlteri\\States\\Exception\\UnavailableDIContainer' => 'Teknoo\\States\\Exception\\UnavailableDIContainer',
    'UniAlteri\\States\\Exception\\UnavailableFactory' => 'Teknoo\\States\\Exception\\UnavailableFactory',
    'UniAlteri\\States\\Exception\\UnavailableLoader' => 'Teknoo\\States\\Exception\\UnavailableLoader',
    'UniAlteri\\States\\Exception\\UnavailablePath' => 'Teknoo\\States\\Exception\\UnavailablePath',
    'UniAlteri\\States\\Exception\\UnavailableState' => 'Teknoo\\States\\Exception\\UnavailableState',
    'UniAlteri\\States\\Factory\\Exception\\IllegalFactory' => 'Teknoo\\States\\Factory\\Exception\\IllegalFactory',
    'UniAlteri\\States\\Factory\\Exception\\IllegalProxy' => 'Teknoo\\States\\Factory\\Exception\\IllegalProxy',
    'UniAlteri\\States\\Factory\\Exception\\InvalidArgument' => 'Teknoo\\States\\Factory\\Exception\\InvalidArgument',
    'UniAlteri\\States\\Factory\\Exception\\StateNotFound' => 'Teknoo\\States\\Factory\\Exception\\StateNotFound',
    'UniAlteri\\States\\Factory\\Exception\\UnavailableDIContainer' => 'Teknoo\\States\\Factory\\Exception\\UnavailableDIContainer',
    'UniAlteri\\States\\Factory\\Exception\\UnavailableFactory' => 'Teknoo\\States\\Factory\\Exception\\UnavailableFactory',
    'UniAlteri\\States\\Factory\\Exception\\UnavailableLoader' => 'Teknoo\\States\\Factory\\Exception\\UnavailableLoader',
    'UniAlteri\\States\\Factory\\FactoryInterface' => 'Teknoo\\States\\Factory\\FactoryInterface',
    'UniAlteri\\States\\Factory\\FactoryTrait' => 'Teknoo\\States\\Factory\\FactoryTrait',
    'UniAlteri\\States\\Factory\\Integrated' => 'Teknoo\\States\\Factory\\Integrated',
    'UniAlteri\\States\\Factory\\Standard' => 'Teknoo\\States\\Factory\\Standard',
    'UniAlteri\\States\\Factory\\StandardStartupFactory' => 'Teknoo\\States\\Factory\\StandardStartupFactory',
    'UniAlteri\\States\\Factory\\StartupFactoryInterface' => 'Teknoo\\States\\Factory\\StartupFactoryInterface',
    'UniAlteri\\States\\Loader\\Exception\\IllegalArgument' => 'Teknoo\\States\\Loader\\Exception\\IllegalArgument',
    'UniAlteri\\States\\Loader\\Exception\\IllegalFactory' => 'Teknoo\\States\\Loader\\Exception\\IllegalFactory',
    'UniAlteri\\States\\Loader\\Exception\\IllegalProxy' => 'Teknoo\\States\\Loader\\Exception\\IllegalProxy',
    'UniAlteri\\States\\Loader\\Exception\\IllegalState' => 'Teknoo\\States\\Loader\\Exception\\IllegalState',
    'UniAlteri\\States\\Loader\\Exception\\UnReadablePath' => 'Teknoo\\States\\Loader\\Exception\\UnReadablePath',
    'UniAlteri\\States\\Loader\\Exception\\UnavailableFactory' => 'Teknoo\\States\\Loader\\Exception\\UnavailableFactory',
    'UniAlteri\\States\\Loader\\Exception\\UnavailablePath' => 'Teknoo\\States\\Loader\\Exception\\UnavailablePath',
    'UniAlteri\\States\\Loader\\Exception\\UnavailableState' => 'Teknoo\\States\\Loader\\Exception\\UnavailableState',
    'UniAlteri\\States\\Loader\\FinderComposer' => 'Teknoo\\States\\Loader\\FinderComposer',
    'UniAlteri\\States\\Loader\\FinderComposerIntegrated' => 'Teknoo\\States\\Loader\\FinderComposerIntegrated',
    'UniAlteri\\States\\Loader\\FinderIntegrated' => 'Teknoo\\States\\Loader\\FinderIntegrated',
    'UniAlteri\\States\\Loader\\FinderInterface' => 'Teknoo\\States\\Loader\\FinderInterface',
    'UniAlteri\\States\\Loader\\FinderStandard' => 'Teknoo\\States\\Loader\\FinderStandard',
    'UniAlteri\\States\\Loader\\IncludePathManager' => 'Teknoo\\States\\Loader\\IncludePathManager',
    'UniAlteri\\States\\Loader\\IncludePathManagerInterface' => 'Teknoo\\States\\Loader\\IncludePathManagerInterface',
    'UniAlteri\\States\\Loader\\LoaderComposer' => 'Teknoo\\States\\Loader\\LoaderComposer',
    'UniAlteri\\States\\Loader\\LoaderInterface' => 'Teknoo\\States\\Loader\\LoaderInterface',
    'UniAlteri\\States\\Loader\\LoaderStandard' => 'Teknoo\\States\\Loader\\LoaderStandard',
    'UniAlteri\\States\\ObjectInterface' => 'Teknoo\\States\\ObjectInterface',
    'UniAlteri\\States\\Proxy\\Exception\\AvailableSeveralMethodImplementations' => 'Teknoo\\States\\Proxy\\Exception\\AvailableSeveralMethodImplementations',
    'UniAlteri\\States\\Proxy\\Exception\\IllegalArgument' => 'Teknoo\\States\\Proxy\\Exception\\IllegalArgument',
    'UniAlteri\\States\\Proxy\\Exception\\IllegalFactory' => 'Teknoo\\States\\Proxy\\Exception\\IllegalFactory',
    'UniAlteri\\States\\Proxy\\Exception\\IllegalName' => 'Teknoo\\States\\Proxy\\Exception\\IllegalName',
    'UniAlteri\\States\\Proxy\\Exception\\InvalidArgument' => 'Teknoo\\States\\Proxy\\Exception\\InvalidArgument',
    'UniAlteri\\States\\Proxy\\Exception\\MethodNotImplemented' => 'Teknoo\\States\\Proxy\\Exception\\MethodNotImplemented',
    'UniAlteri\\States\\Proxy\\Exception\\StateNotFound' => 'Teknoo\\States\\Proxy\\Exception\\StateNotFound',
    'UniAlteri\\States\\Proxy\\Exception\\UnavailableClosure' => 'Teknoo\\States\\Proxy\\Exception\\UnavailableClosure',
    'UniAlteri\\States\\Proxy\\Exception\\UnavailableFactory' => 'Teknoo\\States\\Proxy\\Exception\\UnavailableFactory',
    'UniAlteri\\States\\Proxy\\Exception\\UnavailableState' => 'Teknoo\\States\\Proxy\\Exception\\UnavailableState',
    'UniAlteri\\States\\Proxy\\Integrated' => 'Teknoo\\States\\Proxy\\Integrated',
    'UniAlteri\\States\\Proxy\\IntegratedInterface' => 'Teknoo\\States\\Proxy\\IntegratedInterface',
    'UniAlteri\\States\\Proxy\\IntegratedTrait' => 'Teknoo\\States\\Proxy\\IntegratedTrait',
    'UniAlteri\\States\\Proxy\\ProxyInterface' => 'Teknoo\\States\\Proxy\\ProxyInterface',
    'UniAlteri\\States\\Proxy\\ProxyTrait' => 'Teknoo\\States\\Proxy\\ProxyTrait',
    'UniAlteri\\States\\Proxy\\Standard' => 'Teknoo\\States\\Proxy\\Standard',
    'UniAlteri\\States\\States\\AbstractState' => 'Teknoo\\States\\States\\AbstractState',
    'UniAlteri\\States\\States\\Exception\\IllegalProxy' => 'Teknoo\\States\\States\\Exception\\IllegalProxy',
    'UniAlteri\\States\\States\\Exception\\IllegalService' => 'Teknoo\\States\\States\\Exception\\IllegalService',
    'UniAlteri\\States\\States\\Exception\\InvalidArgument' => 'Teknoo\\States\\States\\Exception\\InvalidArgument',
    'UniAlteri\\States\\States\\Exception\\MethodNotImplemented' => 'Teknoo\\States\\States\\Exception\\MethodNotImplemented',
    'UniAlteri\\States\\States\\StateInterface' => 'Teknoo\\States\\States\\StateInterface',
    'UniAlteri\\States\\States\\StateTrait' => 'Teknoo\\States\\States\\StateTrait'
);

foreach ($mapping as $oldClasName => $newClassName) {
    if (!class_exists($oldClasName, false)) {
        class_alias($newClassName, $oldClasName);
    }
}
