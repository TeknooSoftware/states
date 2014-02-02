<?php

namespace UniAlteri\Tests\Support;

use \UniAlteri\States\States;

/**
 * Fake class to test state behavior
 * Class OnlyPublic
 * @package UniAlteri\States\States
 */
class OnlyProtected extends States\AbstractState
{
    protected static function _staticMethod5()
    {
    }

    protected function _standardMethod6($a, $b)
    {
        return $a+$b;
    }

    final protected function _finalMethod7()
    {
    }

    protected function _standardMethod8()
    {
    }
}