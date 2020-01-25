<?php

class TodoTest extends PHPUnit_Framework_TestCase
{
    public function testTodoGet2()
    {
        $pipi = true;
        $this->assertEquals(true, $pipi, "yes");
    }

    public function testTodoGet()
    {
        $tab = array("zelda" => 2);
        $dede = "dede";
        $qa = 1;
        $wa = 2;
        $this->assertEquals($dede, "dede");
        $this->assertArrayHasKey("zelda", $tab);
        $this->assertGreaterThan($qa, $wa, "oui");
    }

    public function testTodoGet222()
    {
        $pipi = true;
        $this->assertEquals(true, $pipi, "yes");
    }


}