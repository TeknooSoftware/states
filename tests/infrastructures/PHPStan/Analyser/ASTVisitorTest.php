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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\PHPStan\Analyser;

use PHPStan\Parser\Parser;
use PHPStan\Reflection\ReflectionProvider;
use PHPUnit\Framework\TestCase;
use Teknoo\States\PHPStan\Analyser\ASTVisitor;

/**
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers      \Teknoo\States\PHPStan\Analyser\ASTVisitor
 */
class ASTVisitorTest extends TestCase
{
    private ?ReflectionProvider $reflectionProvider = null;

    private ?Parser $parser = null;

    private function getReflectionProviderMock(): ReflectionProvider
    {
        if (!$this->reflectionProvider instanceof ReflectionProvider) {
            $this->reflectionProvider = $this->createMock(ReflectionProvider::class);
        }

        return $this->reflectionProvider;
    }

    private function getParserMock(): Parser
    {
        if (!$this->parser instanceof Parser) {
            $this->parser = $this->createMock(Parser::class);
        }

        return $this->parser;
    }

    public function buildVisitor(): ASTVisitor
    {
        return new ASTVisitor(
            $this->getReflectionProviderMock(),
            $this->getParserMock()
        );
    }

    public function testLeaveNodeWithNonClassNode()
    {

    }

    public function testLeaveNodeWithNonStatedClassNode()
    {

    }

    public function testLeaveNodeWithStateClassNode()
    {

    }

    public function testLeaveNodeWithProxyClassNodeWithoutState()
    {

    }

    public function testLeaveNodeWithProxyClassNodeWithStateAlreadyFetched()
    {

    }

    public function testLeaveNodeWithProxyClassNodeWithStateNotAlreadyFetched()
    {

    }

    public function testLeaveNodeWithProxyClassNodeWithInheritance()
    {

    }
}