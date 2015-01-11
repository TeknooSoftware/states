<?php
/**
 * Created by PhpStorm.
 * Author : Richard Déloge, richard@uni-alteri.fr, www.uni-alteri.fr
 * Date: 11/01/15
 * Time: 13:27
 */

namespace Acme\TraitFactory;

use UniAlteri\States\Proxy\ProxyInterface;
use UniAlteri\States\Proxy\ProxyTrait;

class TraitProxy implements ProxyInterface
{
    use ProxyTrait;
}