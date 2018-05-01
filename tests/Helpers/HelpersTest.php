<?php

namespace GetOlympus\Zeus\Helpers\Controller;

use PHPUnit\Framework\TestCase;
use GetOlympus\Zeus\Helpers\Controller\Helpers;
use Behat\Transliterator\Transliterator;

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
