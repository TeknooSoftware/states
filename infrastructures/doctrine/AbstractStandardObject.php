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
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\States\Doctrine;

use Teknoo\States\Proxy\ProxyInterface;

/**
 * Default Stated class implementation with a doctrine object class.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
abstract class AbstractStandardObject implements ProxyInterface
{
    use StandardTrait;

    /**
     * Default constructor used to initialize the stated object with its factory.
     * @throws \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function __construct()
    {
        $this->postLoadDoctrine();
    }

    /**
     * @return array<string>
     */
    protected static function statesListDeclaration(): array
    {
        return [];
    }
}
