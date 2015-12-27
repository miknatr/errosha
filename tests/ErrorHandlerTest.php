<?php

namespace Errosha;

class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $handler = new ErrorHandler();
        $this->assertTrue(true);
    }
}
