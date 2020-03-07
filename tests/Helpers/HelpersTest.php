<?php

namespace GetOlympus\Zeus\Utils;

use Behat\Transliterator\Transliterator;
use GetOlympus\Zeus\Utils\Helpers;
use PHPUnit\Framework\TestCase;

/**
 * HelpersTest controller
 *
 * @package Olympus Zeus-Core
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.46
 *
 */

class HelpersTest extends TestCase
{
    public function testBasePathSetup()
    {
        $name = 'an admin bar';

        $this->assertEquals('AnAdminBar', Helpers::toCamelCaseFormat($name));
        $this->assertEquals('an-admin-bar', Helpers::urlize($name));
        $this->assertEquals('anAdminBar', Helpers::toFunctionFormat($name));
    }
}
