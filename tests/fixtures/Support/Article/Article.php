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

namespace Teknoo\Tests\Support\Article;

use Teknoo\States\Proxy;
use Teknoo\Tests\Support\Article\Article\Archived;
use Teknoo\Tests\Support\Article\Article\Draft;
use Teknoo\Tests\Support\Article\Article\Extended;
use Teknoo\Tests\Support\Article\Article\Promoted;
use Teknoo\Tests\Support\Article\Article\Published;
use Teknoo\Tests\Support\Article\Article\StateDefault;

/**
 * Proxy Article
 * Proxy class of the stated class Article
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class Article implements Proxy\ProxyInterface
{
    use Proxy\ProxyTrait;

    protected static function statesListDeclaration(): array
    {
        return [
            Archived::class,
            Draft::class,
            Extended::class,
            Promoted::class,
            Published::class,
            StateDefault::class,
        ];
    }

    /**
     * Get an article's attribute.
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function getAttribute($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
    }

    /**
     * Update an article's attribute.
     *
     * @param string $name
     */
    public function setAttribute($name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * To initialize this article with some data.
     */
    public function __construct(protected array $data = [])
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeStateProxy();

        //If the article is published, load the state Published, else load the state Draft
        if (false === $this->isPublished()) {
            $this->enableState(Draft::class);
        } else {
            $this->enableState(Published::class);
        }
    }
}
