<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SqlTark\Component\AdHocTableFromClause;
use SqlTark\Component\ComponentType;
use SqlTark\Component\FromClause;
use SqlTark\Component\QueryFromClause;
use SqlTark\Query\Query;

class QueryFromTest extends TestCase
{
    public function testFrom()
    {
        $q = new Query('table');

        $component = $q->getOneComponent(ComponentType::From);
        $this->assertInstanceOf(FromClause::class, $component);
    }

    public function testFrom_alias()
    {
        $q = new Query('table AS t1');

        /** @var FromClause */
        $component = $q->getOneComponent(ComponentType::From);
        $this->assertEquals('t1', $component->getAlias());
    }
    
    public function testFrom_multipleCallShouldReplace()
    {
        $q = new Query('table');

        $q->from('table2')->from('table3');

        /** @var FromClause $component */
        $component = $q->getOneComponent(ComponentType::From);
        $this->assertInstanceOf(FromClause::class, $component);

        $this->assertEquals('table3', $component->getTable());
    }

    public function testFromQuery()
    {
        $q = new Query;
        $q2 = new Query('table');
        $q3 = new Query('table');

        $q->from($q2, 't2');
        $component = $q->getOneComponent(ComponentType::From);
        $this->assertInstanceOf(FromClause::class, $component);

        $q->from($q3->alias('t3'));
        $component = $q->getOneComponent(ComponentType::From);
        $this->assertInstanceOf(FromClause::class, $component);

        $q->from(function($q) {
            return $q->from('table')->alias('t4');
        });
        $component = $q->getOneComponent(ComponentType::From);
        $this->assertInstanceOf(FromClause::class, $component);
    }

    public function testFromAdHoc()
    {
        $q = new Query;
        $q->fromAdHoc('t2', [
            ['c1' => 'one', 'c1' => 'two', 'c1' => 'three', ]
        ]);

        /** @var AdHocTableFromClause $component */
        $component = $q->getOneComponent(ComponentType::From);
        $this->assertInstanceOf(AdHocTableFromClause::class, $component);
    }

    public function testFromAdHoc_throwWhenDifferent()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage("Array values count must same with columns count.");

        $q = new Query;
        $q->fromAdHoc('t2', ['c1', 'c2', 'c3'], [[1, 2, 3], [1, 2, 3, 4], []]);

        /** @var AdHocTableFromClause $component */
        $component = $q->getOneComponent(ComponentType::From);
        $this->assertInstanceOf(AdHocTableFromClause::class, $component);

        $q->fromAdHoc('t2', [
            ['c1' => 'one', 'c1' => 'two', 'c1' => 'three'],
            [1, 2, 3],
            [1, 2, 3, 4],
            [],
        ]);
        
        $this->assertInstanceOf(AdHocTableFromClause::class, $component);
    }
}