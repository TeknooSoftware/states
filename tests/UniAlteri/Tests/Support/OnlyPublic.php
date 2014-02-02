<?php

namespace UniAlteri\Tests\Support;

use \UniAlteri\States\States;

/**
 * Fake class to test state behavior
 * Class OnlyPublic
 * @package UniAlteri\States\States
 */
class OnlyPublic extends States\AbstractState
{
    public function standardMethod1()
    {
    }

    final public function finalMethod2()
    {
    }

    public static function staticMethod3()
    {
    }

    public function standardMethod4()
    {
    }
}