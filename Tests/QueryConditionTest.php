<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SqlTark\Expressions;
use SqlTark\Query\Condition;
use SqlTark\Query\Join;
use SqlTark\Query\Query;

final class QueryConditionTest extends TestCase
{
    public function testQuery_where_oneParameter()
    {
        $this->expectNotToPerformAssertions();

        $query = new Query();

        $query->where([
            'column1' => 'string',
            'column2' => 1,
            'column3' => 1.01,
            'column4' => true,
            'column5' => null,
        ]);

        $query->or()->where([
            1 => 'string',
            2 => 1,
            3 => 1.01,
            4 => true,
            5 => null,
        ]);

        $query->or()->where([
            1.01 => 'string',
            2.01 => 1,
            3.01 => 1.01,
            4.01 => true,
            5.01 => null,
        ]);

        $query->or()->where([
            true => 'string',
            false => 1,
        ]);

        $query->or()->where([
            true => 1.01,
            false => true,
        ]);

        $query->or()->where([
            true => null,
        ]);

        $query->or()->where([
            null => 'string',
        ]);

        $query->or()->where([
            null => 1,
        ]);

        $query->or()->where([
            null => 1.01,
        ]);

        $query->or()->where([
            null => true,
        ]);

        $query->or()->where([
            null => null,
        ]);
    }

    public function testQuery_where_oneParameter_expectError()
    {
        $query = new Query();

        $cases = [
            'string',
            1,
            1.01,
            true,
            null,
            new \DateTime(),
            (object) [],
            Expressions::literal(1),
            Expressions::column('column1'),
            new Query('table1'),
            function ($query) {
                return $query->from('table2');
            },
        ];

        $count = 0;
        foreach ($cases as $item) {
            try {
                $query->where($item);
            } catch (InvalidArgumentException $ex) {
                $count++;
            }
        }

        $this->assertEquals($count, count($cases));
    }

    public function testQuery_where_twoParameters()
    {
        $this->expectNotToPerformAssertions();

        $query = new Query();

        $query->where('column1', 'column2');
        $query->where(2, 2);
        $query->where(2.02, 2.02);
        $query->where(true, true);
        $query->where(null, null);

        $query->where(new \DateTime(), new \DateTime());
        $query->where(Expressions::literal(1), Expressions::literal(2));
        $query->where(Expressions::column('column1'), Expressions::column('column2'));
        $query->where(new Query('table1'), new Query('table2'));
        $query->where(function ($q) {
            return $q->from('table1');
        }, function ($q) {
            return $q->from('table1');
        });
    }

    public function testQuery_where_twoParameters_expectError()
    {
        $query = new Query();

        $params = [
            [1, 2, 3],
            new stdClass,
            function () {
            },
            new Condition,
            new Join,
        ];

        $count = 0;
        foreach ($params as $item) {
            try {
                $query->where($item, $item);
            } catch (InvalidArgumentException $ex) {
                $count++;
            }
        }

        $this->assertEquals($count, count($params));
    }

    public function testQuery_where_threeParameters()
    {
        $this->expectNotToPerformAssertions();

        $query = new Query();

        $query->where('column1', '=', 'column2');
        $query->where(2, '=', 2);
        $query->where(2.02, '=', 2.02);
        $query->where(true, '=', true);
        $query->where(null, '=', null);

        $query->where(new \DateTime(), '=', new \DateTime());
        $query->where(Expressions::literal(1), '=', Expressions::literal(2));
        $query->where(Expressions::column('column1'), '=', Expressions::column('column2'));
        $query->where(new Query('table1'), '=', new Query('table2'));
        $query->where(function ($q) {
            return $q->from('table1');
        }, '=', function ($q) {
            return $q->from('table1');
        });
    }

    public function testQuery_whereIn()
    {
        $this->expectNotToPerformAssertions();
        
        $query = new Query('table1');

        $q2 = new Query('table2');

        $query->whereIn('col1', [1, 2, 3]);
        $query->whereIn('col2', ['s1', 's2', 's3']);
        $query->whereIn('col3', [1.01, 2.02, 3.03]);
        $query->whereIn('col4', [true, false, null]);
        $query->whereIn('col5', [true, false, null]);
        $query->whereIn('col5', $q2);
    }

    public function testQuery_whereIn_expectError()
    {
        $query = new Query('table1');
        
        $params = [
            new stdClass,
            [],
            [new Query('t2')]
        ];

        $count = 0;
        foreach ($params as $item) {
            try {
                $query->where($item, $item);
            } catch (InvalidArgumentException $ex) {
                $count++;
            }
        }

        $this->assertEquals($count, count($params));
    }
}
