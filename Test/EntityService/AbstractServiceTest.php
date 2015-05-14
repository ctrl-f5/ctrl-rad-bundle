<?php

namespace Ctrl\RadBundle\Test\EntityService;

use Ctrl\RadBundle\EntityService\AbstractService;

class AbstractServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    protected function setUp()
    {
        parent::setUp();

        $this->service = $this->getMockForAbstractClass(
            'Ctrl\\RadBundle\\EntityService\\AbstractService',
            array(),
            '',
            false
        );
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->service = null;
    }

    public function test_get_root_alias()
    {
        $this->service->expects($this->any())
            ->method('getEntityClass')
            ->will($this->returnValue('\\Namespace\\MyTestClass'));

        $this->assertEquals("myTestClass", $this->service->getRootAlias());
    }

    public function test_assert_entity_instance_accepts_correct_class()
    {
        $this->service->expects($this->any())
            ->method('getEntityClass')
            ->will($this->returnValue('Ctrl\\RadBundle\\Entity\\User'));

        $entity = $this->getMockForAbstractClass(
            'Ctrl\\RadBundle\\Entity\\User',
            array(),
            '',
            false
        );

        $this->assertNull($this->service->assertEntityInstance($entity));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_assert_entity_instance_rejects_incorrect_class()
    {
        $this->service->expects($this->any())
            ->method('getEntityClass')
            ->will($this->returnValue('Ctrl\\RadBundle\\Entity\\User'));

        $entity = new \stdClass();

        $this->assertNull($this->service->assertEntityInstance($entity));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_assert_entity_instance_rejects_non_object()
    {
        $this->service->expects($this->any())
            ->method('getEntityClass')
            ->will($this->returnValue('Ctrl\\RadBundle\\Entity\\User'));

        $entity = array('id' => 1);

        $this->assertNull($this->service->assertEntityInstance($entity));
    }
}
