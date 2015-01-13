<?php
/**
 * Created by PhpStorm.
 * Author : Richard Déloge, richard@uni-alteri.fr, www.uni-alteri.fr
 * Date: 11/01/15
 * Time: 23:04
 */

namespace Acme\GoodState\States;

use UniAlteri\States\States\StateInterface;
use UniAlteri\States\States\StateTrait;

class StateWithTrait implements StateInterface
{
    use StateTrait;
}