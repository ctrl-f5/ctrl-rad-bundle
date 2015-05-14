<?php

namespace Ctrl\RadBundle\Test\EntityService\Criteria;

use Ctrl\RadBundle\EntityService\Criteria\Resolver;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $rootAlias
     * @return Resolver
     */
    protected function getResolver($rootAlias)
    {
        return new Resolver($rootAlias);
    }

    public function test_root_alias_set_on_construct()
    {
        $resolver = $this->getResolver("myRootAlias");

        $this->assertEquals('myRootAlias', $resolver->getRootAlias());
    }

    public function test_set_root_alias()
    {
        $resolver = $this->getResolver("myRootAlias");

        $resolver->setRootAlias('newAlias');
        $this->assertEquals('newAlias', $resolver->getRootAlias());
    }

    public function test_resolve_criteria_adds_join()
    {
        $resolver = $this->getMockBuilder('Ctrl\\RadBundle\\EntityService\\Criteria\\Resolver')
            ->setConstructorArgs(array('root'))
            ->setMethods(null)
            ->getMock()
        ;

        $qb = $this->getMockBuilder('Doctrine\\ORM\\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock()
        ;


        $qb->expects($this->once())->method('getDQLPart')->will($this->returnValue(array()));
        $qb->expects($this->once())->method('join')->with(
            $this->equalTo('root.test'),
            $this->equalTo('test')
        );

        $this->assertSame($resolver, $resolver->resolveCriteria($qb, array('test')));
    }

    public function test_resolve_criteria_adds_join_recursively()
    {
        $resolver = $this->getMockBuilder('Ctrl\\RadBundle\\EntityService\\Criteria\\Resolver')
            ->setConstructorArgs(array('root'))
            ->setMethods(null)
            ->getMock()
        ;

        $qb = $this->getMockBuilder('Doctrine\\ORM\\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock()
        ;


        $qb->expects($this->exactly(2))->method('getDQLPart')->will($this->returnValue(array()));

        $qb->expects($this->exactly(3))->method('join');

        $this->assertSame($resolver, $resolver->resolveCriteria($qb, array(
            'test',
            'test.fooBar'
        )));
    }

    public function test_resolve_criteria_adds_where_for_related_property()
    {
        $resolver = $this->getMockBuilder('Ctrl\\RadBundle\\EntityService\\Criteria\\Resolver')
            ->setConstructorArgs(array('root'))
            ->setMethods(null)
            ->getMock()
        ;

        $qb = $this->getMockBuilder('Doctrine\\ORM\\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock()
        ;


        $qb->expects($this->exactly(1))->method('getDQLPart')->will($this->returnValue(array()));

        $qb->expects($this->exactly(1))->method('join')->with('root.fooBar', 'fooBar');

        $qb->expects($this->exactly(1))->method('andWhere')->with('fooBar.id = ?1')->will($this->returnValue($qb));

        $qb->expects($this->exactly(1))->method('setParameter')->with(1, 69)->will($this->returnValue($qb));

        $this->assertSame($resolver, $resolver->resolveCriteria($qb, array(
            'fooBar.id' => 69
        )));
    }
}
