<?php

namespace GetOlympus\Zeus\Common\Controller;

use PHPUnit\Framework\TestCase;
use GetOlympus\Zeus\Common\Controller\Common;
use Behat\Transliterator\Transliterator;

/**
 * CommonTest controller
 *
 * @package Olympus Zeus-Core
 * @author Achraf Chouk <achrafchouk@gmail.com>
 * @since 0.0.46
 *
 */

class CommonTest extends TestCase
{
    public function testBasePathSetup()
    {
        $name = 'an admin bar';

        $this->assertEquals('AnAdminBar', Common::toCamelCaseFormat($name));
        $this->assertEquals('an-admin-bar', Common::urlize($name));
        $this->assertEquals('anAdminBar', Common::toFunctionFormat($name));
    }
}
