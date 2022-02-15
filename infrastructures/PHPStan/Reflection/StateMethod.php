<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
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
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionType;
use RuntimeException;
use Teknoo\States\State\StateInterface;

/**
 * To provide a PHPStan reflection for state's methode in a stated class.
 *
 * State's method are cloure binded dynamically to the parent scope, So the PHP's API Reflection provides a
 * `\ReflectionFunction` instead of `\ReflectionMethod` and lost method's status about visibility (private/public),
 * static etc..
 *
 * This class provide a valid PHPStan reflection for these method from the API Reflection on the closure
 * and the closure factory.
 *
 * @see http://php.net/manual/en/class.arrayaccess.php
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class StateMethod implements BuiltinMethodReflection
{
    public function __construct(
        private ReflectionMethod $factoryReflection,
        private ReflectionFunction $closureReflection,
    ) {
    }

    public function getName(): string
    {
        return $this->factoryReflection->getName();
    }

    public function getReflection(): ?ReflectionMethod
    {
        return $this->factoryReflection;
    }

    public function getFileName(): ?string
    {
        if (empty($fileName = $this->factoryReflection->getFileName())) {
            return null;
        }

        return $fileName;
    }

    /**
     * @return ReflectionClass<StateInterface>
     */
    public function getDeclaringClass(): ReflectionClass
    {
        $reflection = $this->closureReflection->getClosureScopeClass();

        if (!$reflection instanceof ReflectionClass) {
            throw new RuntimeException("Reflection class is not available for the closure");
        }

        return $reflection;
    }

    public function getStartLine(): ?int
    {
        if (empty($startLine = $this->closureReflection->getStartLine())) {
            return null;
        }

        return $startLine;
    }

    public function getEndLine(): ?int
    {
        if (empty($endLine = $this->closureReflection->getEndLine())) {
            return null;
        }

        return $endLine;
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

    public function getReturnType(): ?ReflectionType
    {
        return $this->closureReflection->getReturnType();
    }

    public function getTentativeReturnType(): ?ReflectionType
    {
        return null;
    }

    /**
     * @return ReflectionParameter[]
     */
    public function getParameters(): array
    {
        return $this->closureReflection->getParameters();
    }
}
