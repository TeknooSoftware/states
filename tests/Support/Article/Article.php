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
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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

        return;
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
