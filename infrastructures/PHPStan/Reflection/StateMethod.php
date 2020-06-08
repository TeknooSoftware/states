<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\PHPStan\Reflection;

use PHPStan\Reflection\Php\BuiltinMethodReflection;
use PHPStan\TrinaryLogic;

/**
 * To provide a PHPStan reflection for state's methode in a stated class.
 * State's method are cloure binded dynamically to the parent scope, So the PHP's API Reflection provides a
 * \ReflectionFunction instead of \ReflectionMethod and lost method's status about visibilty (private/public),
 * static etc..
 * This class provide a valid PHPStan reflection for these method from the API Reflection on the closure
 * and the closure factory.
 *
 * @see http://php.net/manual/en/class.arrayaccess.php
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class StateMethod implements BuiltinMethodReflection
{
    private \ReflectionMethod $factoryReflection;
    
    private \ReflectionFunction $closureReflection;

    public function __construct(\ReflectionMethod $factoryReflection, \ReflectionFunction $closureReflection)
    {
        $this->factoryReflection = $factoryReflection;
        $this->closureReflection = $closureReflection;
    }

    public function getName(): string
    {
        return $this->factoryReflection->getName();
    }

    public function getReflection(): ?\ReflectionMethod
    {
        return $this->factoryReflection;
    }

    /**
     * @return string|false
     */
    public function getFileName()
    {
        return $this->factoryReflection->getFileName();
    }

    public function getDeclaringClass(): \ReflectionClass
    {
        $reflection = $this->closureReflection->getClosureScopeClass();

        if (!$reflection instanceof \ReflectionClass) {
            throw new \RuntimeException("Reflection class is not available for the closure");
        }

        return $reflection;
    }

    /**
     * @return int|false
     */
    public function getStartLine()
    {
        return $this->closureReflection->getStartLine();
    }

    /**
     * @return int|false
     */
    public function getEndLine()
    {
        return $this->closureReflection->getEndLine();
    }

    /**
     * @return string|null
     */
    public function getDocComment(): ?string
    {
        $doc = $this->factoryReflection->getDocComment();

        if (false === $doc) {
            return null;
        }

        return $doc;
    }

    public function isStatic(): bool
    {
        return $this->factoryReflection->isStatic();
    }

    public function isPrivate(): bool
    {
        return $this->factoryReflection->isPrivate();
    }

    public function isPublic(): bool
    {
        return $this->factoryReflection->isPublic();
    }

    public function getPrototype(): BuiltinMethodReflection
    {
        return $this;
    }

    public function isDeprecated(): TrinaryLogic
    {
        return TrinaryLogic::createFromBoolean($this->factoryReflection->isDeprecated());
    }

    public function isFinal(): bool
    {
        return $this->factoryReflection->isFinal();
    }

    public function isInternal(): bool
    {
        return $this->factoryReflection->isInternal();
    }

    public function isAbstract(): bool
    {
        return $this->factoryReflection->isAbstract();
    }

    public function isVariadic(): bool
    {
        return $this->closureReflection->isVariadic();
    }

    public function getReturnType(): ?\ReflectionType
    {
        return $this->closureReflection->getReturnType();
    }

    /**
     * @return \ReflectionParameter[]
     */
    public function getParameters(): array
    {
        return $this->closureReflection->getParameters();
    }
}
