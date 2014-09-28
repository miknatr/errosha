<?php

namespace Errosha;

class SimpleErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $handler = new SimpleErrorHandler('some file path');
        $this->assertTrue(true);
    }
}
