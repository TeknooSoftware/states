<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\States\Doctrine;

use ProxyManager\Proxy\LazyLoadingInterface;
use SensitiveParameter;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;
use Throwable;

/**
 * Trait adapt standard proxies to doctrine.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
trait StandardTrait
{
    use ProxyTrait {
        __call as private __callTrait;
    }

    /**
     * Doctrine does not call the construction and create a new instance without it.
     * This callback reinitialize proxy.
     *
     * @throws \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function postLoadDoctrine(): ProxyInterface
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeStateProxy();
        //Select good state
        $this->updateStates();

        return $this;
    }

    public function updateStates(): ProxyInterface
    {
        return $this;
    }

    private function alterVisibilityScopeLimit(int $limit): int
    {
        return $limit + 1;
    }

    /**
     * @param array<mixed> $arguments
     * @throws Throwable
     */
    public function __call(string $methodName, #[SensitiveParameter] array $arguments): mixed
    {
        if ($this instanceof LazyLoadingInterface && !$this->isProxyInitialized()) {
            $this->initializeProxy();
        }

        return $this->__callTrait($methodName, $arguments);
    }
}
