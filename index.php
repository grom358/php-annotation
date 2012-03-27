<?php
require_once 'simpletest/autorun.php';

class Test extends UnitTestCase {
    function testFailure() {
        $this->assertTrue(false);
    }
}
