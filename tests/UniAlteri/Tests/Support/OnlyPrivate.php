<?php

namespace UniAlteri\Tests\Support;

use \UniAlteri\States\States;

/**
 * Fake class to test state behavior
 * Class OnlyPublic
 * @package UniAlteri\States\States
 */
class OnlyPrivate extends States\StateAbstract
{
    final private function _finalMethod9()
    {
    }

    private function _standardMethod10()
    {
    }

    final private function _finalMethod11()
    {
    }

    private static function _staticMethod12()
    {
    }
}