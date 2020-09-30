<?php 

use EightshiftLibs\Helpers\Components;

class ComponentsTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testEnsureString()
    {
        $this->assertIsString(Components::ensureString('asdad'));
        $this->assertIsString(Components::ensureString(['aaa', 'bbb']));
    }
}