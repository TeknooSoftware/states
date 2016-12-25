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
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\Tests\Support\Article;

use Teknoo\States\Proxy;
use Teknoo\Tests\Support\Article\States\Archived;
use Teknoo\Tests\Support\Article\States\Draft;
use Teknoo\Tests\Support\Article\States\Extended;
use Teknoo\Tests\Support\Article\States\Promoted;
use Teknoo\Tests\Support\Article\States\Published;
use Teknoo\Tests\Support\Article\States\StateDefault;

/**
 * Proxy Article
 * Proxy class of the stated class Article
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Article extends Proxy\Standard
{
    /**
     * Article's data.
     *
     * @var array
     */
    protected $data = array();

    public static function statesListDeclaration(): array
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
     * @param mixed  $value
     */
    public function setAttribute($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * To initialize this article with some data.
     *
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->data = $data;
        parent::__construct();
        //If the article is published, load the state Published, else load the state Draft
        if (false === $this->isPublished()) {
            $this->enableState(Draft::class);
        } else {
            $this->enableState(Published::class);
        }
    }
}
