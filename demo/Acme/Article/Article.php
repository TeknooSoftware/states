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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
namespace demo\Acme\Article;

use demo\Acme\Article\States\Archived;
use demo\Acme\Article\States\Draft;
use demo\Acme\Article\States\Extended;
use demo\Acme\Article\States\Promoted;
use demo\Acme\Article\States\Published;
use demo\Acme\Article\States\StateDefault;
use Teknoo\States\Proxy;

/**
 * Proxy Article
 * Proxy class of the stated class Article.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class Article extends Proxy\Standard
{
    protected static function statesListDeclaration(): array
    {
        return [
            Archived::class,
            Draft::class,
            Extended::class,
            Promoted::class,
            Published::class,
            StateDefault::class
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

        return null;
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
     *
     * @param array $data
     */
    public function __construct(protected $data = [])
    {
        parent::__construct();
        //If the article is published, load the state Published, else load the state Draft
        if (false === $this->isPublished()) {
            $this->enableState(Draft::class);
        } else {
            $this->enableState(Published::class);
        }
    }
}
