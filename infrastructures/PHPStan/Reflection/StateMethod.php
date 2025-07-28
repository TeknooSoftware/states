<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\States\PHPStan\Reflection;

use PHPStan\BetterReflection\Reflection\Adapter\ReflectionFunction;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionMethod;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionParameter;
use PHPStan\Internal\DeprecatedAttributeHelper;
use PHPStan\Reflection\Assertions;
use PHPStan\Reflection\AttributeReflection;
use PHPStan\Reflection\AttributeReflectionFactory;
use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ExtendedFunctionVariant;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Reflection\ExtendedParametersAcceptor;
use PHPStan\Reflection\InitializerExprContext;
use PHPStan\Reflection\InitializerExprTypeResolver;
use PHPStan\Reflection\MethodPrototypeReflection;
use PHPStan\Reflection\MissingMethodFromReflectionException;
use PHPStan\Reflection\Php\PhpParameterReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\MixedType;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use PHPStan\Type\TypehintHelper;
use ReflectionException;
use ReflectionType;
use Teknoo\States\PHPStan\Contracts\Reflection\AttributeReflectionFactoryInterface;
use Teknoo\States\PHPStan\Contracts\Reflection\InitializerExprTypeResolverInterface;

use function array_map;

/**
 * To provide a PHPStan reflection for state's methode in a stated class.
 *
 * State's method are cloure binded dynamically to the parent scope, So the APIs Reflection provides a
 * `ReflectionFunction` instead of `ReflectionMethod` and lost method's status about visibility (private/public),
 * static etc.. *
 *
 * This class provide a valid PHPStan reflection for these method from the PHPStan API Reflection on the closure
 * and the closure factory. A fallback to native API Reflection is provided when PHPSTan's Reflection is not available
 * for states classes (disable by AST Visitor of States to avoid anothers false positives)
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class StateMethod implements ExtendedMethodReflection
{
    /** @var list<ExtendedFunctionVariant>|null */
    private ?array $variants = null;

    /** @var list<PhpParameterReflection>|null */
    private ?array $parameters = null;

    private ?Type $returnType = null;

    private ?Type $nativeReturnType = null;

    /**
     * @param list<AttributeReflection> $attributes
     */
    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
        private readonly InitializerExprTypeResolver|InitializerExprTypeResolverInterface $initializerExprTypeResolver,
        private readonly AttributeReflectionFactory|AttributeReflectionFactoryInterface $attributeReflectionFactory,
        private readonly ReflectionMethod $factoryReflection,
        private readonly ReflectionFunction $closureReflection,
        private readonly ClassReflection $declaringClass,
        private readonly ?Type $phpDocReturnType,
        private readonly ?Type $phpDocThrowType,
        private readonly ?Type $selfOutType,
        private readonly Assertions $asserts,
        private readonly TemplateTypeMap $templateTypeMap,
        private readonly ?bool $isPure,
        private readonly array $attributes,
        private readonly bool $acceptsNamedArguments,
    ) {
    }

    public function getName(): string
    {
        return $this->factoryReflection->getName();
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->declaringClass;
    }

    public function getPrototype(): ClassMemberReflection
    {
        try {
            $prototypeMethod = $this->factoryReflection->getPrototype();
            $declaringClassName = $prototypeMethod->getDeclaringClass()->getName();

            $prototypeDeclaringClass = $this->declaringClass->getAncestorWithClassName($declaringClassName);

            if (!$prototypeDeclaringClass instanceof ClassReflection) {
                $prototypeDeclaringClass = $this->reflectionProvider->getClass($declaringClassName);
            }

            $tentativeReturnType = null;
            if (($trt = $prototypeMethod->getTentativeReturnType()) instanceof ReflectionType) {
                $tentativeReturnType = TypehintHelper::decideTypeFromReflection(
                    reflectionType: $trt,
                    selfClass: $prototypeDeclaringClass,
                );
            }

            return new MethodPrototypeReflection(
                name: $prototypeMethod->getName(),
                declaringClass: $prototypeDeclaringClass,
                isStatic: $prototypeMethod->isStatic(),
                isPrivate: $prototypeMethod->isPrivate(),
                isPublic: $prototypeMethod->isPublic(),
                isAbstract: $prototypeMethod->isAbstract(),
                isInternal: $prototypeMethod->isInternal(),
                variants: $prototypeDeclaringClass->getNativeMethod($prototypeMethod->getName())->getVariants(),
                tentativeReturnType: $tentativeReturnType,
            );
        } catch (ReflectionException | ShouldNotHappenException | MissingMethodFromReflectionException) {
            return $this;
        }
    }

    public function isStatic(): bool
    {
        return false;
    }

    public function isPrivate(): bool
    {
        return $this->factoryReflection->isPrivate();
    }

    public function isPublic(): bool
    {
        return $this->factoryReflection->isPublic();
    }

    public function isFinal(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function isFinalByKeyword(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function isInternal(): TrinaryLogic
    {
        return TrinaryLogic::createFromBoolean($this->factoryReflection->isInternal());
    }

    public function isDeprecated(): TrinaryLogic
    {
        return TrinaryLogic::createFromBoolean($this->factoryReflection->isDeprecated());
    }

    public function getDeprecatedDescription(): ?string
    {
        if ($this->factoryReflection->isDeprecated()) {
            $attributes = $this->factoryReflection->getBetterReflection()->getAttributes();
            return DeprecatedAttributeHelper::getDeprecatedDescription($attributes);
        }

        return null;
    }

    public function isVariadic(): bool
    {
        return $this->closureReflection->isVariadic();
    }

    public function getDocComment(): ?string
    {
        $docComment = $this->factoryReflection->getDocComment();
        if (false === $docComment) {
            return null;
        }

        return $docComment;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getThrowType(): ?Type
    {
        return $this->phpDocThrowType;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function hasSideEffects(): TrinaryLogic
    {
        if ($this->getReturnType()->isVoid()->yes()) {
            return TrinaryLogic::createYes();
        }

        if (!$this->isPure()->maybe()) {
            return $this->isPure();
        }

        if (new ThisType($this->getDeclaringClass())->isSuperTypeOf($this->getReturnType())->yes()) {
            return TrinaryLogic::createYes();
        }

        return TrinaryLogic::createMaybe();
    }

    public function isPure(): TrinaryLogic
    {
        if (null === $this->isPure) {
            return TrinaryLogic::createMaybe();
        }

        return TrinaryLogic::createFromBoolean($this->isPure);
    }

    public function getSelfOutType(): ?Type
    {
        return $this->selfOutType;
    }

    public function returnsByReference(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    /**
     * @return list<ExtendedParametersAcceptor>
     * @throws ShouldNotHappenException
     */
    public function getVariants(): array
    {
        if (null === $this->variants) {
            $this->variants = [
                new ExtendedFunctionVariant(
                    templateTypeMap: $this->templateTypeMap,
                    resolvedTemplateTypeMap: null,
                    parameters: $this->getParameters(),
                    isVariadic: $this->isVariadic(),
                    returnType: $this->getReturnType(),
                    phpDocReturnType: $this->getPhpDocReturnType(),
                    nativeReturnType: $this->getNativeReturnType()
                )
            ];
        }

        return $this->variants;
    }

    /**
     * @throws ShouldNotHappenException
     */
    public function getOnlyVariant(): ExtendedParametersAcceptor
    {
        return $this->getVariants()[0];
    }

    public function getNamedArgumentsVariants(): ?array
    {
        return null;
    }

    public function acceptsNamedArguments(): TrinaryLogic
    {
        return TrinaryLogic::createFromBoolean(
            $this->declaringClass->acceptsNamedArguments() && $this->acceptsNamedArguments
        );
    }

    public function getAsserts(): Assertions
    {
        return $this->asserts;
    }

    public function isBuiltin(): TrinaryLogic|bool
    {
        return TrinaryLogic::createFromBoolean($this->factoryReflection->isInternal());
    }

    public function isAbstract(): bool
    {
        return false;
    }

    /**
     * @return list<PhpParameterReflection>
     */
    private function getParameters(): array
    {
        if ($this->parameters === null) {
            $this->parameters = array_map(
                fn (ReflectionParameter $reflection): PhpParameterReflection => new PhpParameterReflection(
                    $this->initializerExprTypeResolver,
                    $reflection,
                    null,
                    $this->getDeclaringClass(),
                    null,
                    TrinaryLogic::createMaybe(),
                    null,
                    $this->attributeReflectionFactory->fromNativeReflection(
                        $reflection->getAttributes(),
                        InitializerExprContext::fromReflectionParameter($reflection)
                    )
                ),
                $this->closureReflection->getParameters()
            );
        }

        return $this->parameters ?? [];
    }

    /**
     * @throws ShouldNotHappenException
     */
    private function getNativeReturnType(): Type
    {
        if (!$this->nativeReturnType instanceof Type) {
            $this->nativeReturnType = TypehintHelper::decideTypeFromReflection(
                reflectionType: $this->closureReflection->getReturnType(),
                selfClass: $this->declaringClass
            );
        }

        return $this->nativeReturnType;
    }

    private function getPhpDocReturnType(): Type
    {
        return $this->phpDocReturnType ?? new MixedType();
    }

    /**
     * @throws ShouldNotHappenException
     */
    private function getReturnType(): Type
    {
        if (!$this->returnType instanceof Type) {
            $returnType = $this->closureReflection->getReturnType();

            $this->returnType = TypehintHelper::decideTypeFromReflection(
                reflectionType: $returnType,
                phpDocType: $this->phpDocReturnType,
                selfClass: $this->declaringClass,
            );
        }

        return $this->returnType;
    }
}
