<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SqlTark\Compiler\MySqlCompiler;
use SqlTark\Query\Query;

use function SqlTark\Expressions\literal;

final class CompilerTest extends TestCase
{
    public function testQuery_from()
    {
        $compiler = new MySqlCompiler;

        $query = new Query();
        $compiled = $compiler->compileQuery($query);
        $expected = "SELECT *";
        $this->assertEquals($expected, $compiled);

        $query = new Query("table1");
        $compiled = $compiler->compileQuery($query);
        $expected = "SELECT * FROM `table1`";
        $this->assertEquals($expected, $compiled);

        $query = new Query("table1 AS t1");
        $compiled = $compiler->compileQuery($query);
        $expected = "SELECT * FROM `table1` AS `t1`";
        $this->assertEquals($expected, $compiled);
    }

    public function testQuery_fromQuery()
    {
        $compiler = new MySqlCompiler;

        $q1 = new Query('table2');

        $query = new Query();
        $query->from($q1, 't2');
        $compiled = $compiler->compileQuery($query);
        $expected = "SELECT * FROM (SELECT * FROM `table2`) AS t2";
        $this->assertEquals($expected, $compiled);

        $query = new Query();
        $query->from(function($q) { return $q->from('t2'); }, 't2');
        $compiled = $compiler->compileQuery($query);
        $expected = "SELECT * FROM (SELECT * FROM `t2`) AS t2";
        $this->assertEquals($expected, $compiled);
    }

    public function testQuery_fromAdHoc()
    {
        $compiler = new MySqlCompiler;

        $query = new Query();
        $query->fromAdHoc('ad_hoc', ['c1','c2','c3'], [[1, 'str1', 1.01], [2, 'str2', 2.02], [3, 'str3', 3.03]]);
        $compiled = $compiler->compileQuery($query);
        $expected = "SELECT * FROM (SELECT 1 AS `c1`, 'str1' AS `c2`, 1.01 AS `c3` UNION ALL SELECT 2, 'str2', 2.02 UNION ALL SELECT 3, 'str3', 3.03) AS `ad_hoc`";
        $this->assertEquals($expected, $compiled);
        
        $query = new Query();
        $query->fromAdHoc('ad_hoc', [
            ['c1' => 1, 'c2' => 'str1', 'c3' => 1.01],
            ['c1' => 2, 'c2' => 'str2', 'c3' => 2.02],
            ['c1' => 3, 'c2' => 'str3', 'c3' => 3.03]
        ]);
        $compiled = $compiler->compileQuery($query);
        $expected = "SELECT * FROM (SELECT 1 AS `c1`, 'str1' AS `c2`, 1.01 AS `c3` UNION ALL SELECT 2, 'str2', 2.02 UNION ALL SELECT 3, 'str3', 3.03) AS `ad_hoc`";
        $this->assertEquals($expected, $compiled);
    }

    public function testQuery_select()
    {
        $compiler = new MySqlCompiler;

        $query = new Query();
        $query->select(1, 2, 3);
        $expected = "SELECT 1, 2, 3";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);

        $query = new Query();
        $query->select(1.01, 2.02, 3.03);
        $expected = "SELECT 1.01, 2.02, 3.03";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);

        $query = new Query();
        $query->select(TRUE, FALSE, NULL);
        $expected = "SELECT TRUE, FALSE, NULL";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);

        $query = new Query();
        $query->select('col1', 'col2', 'col3');
        $expected = "SELECT `col1`, `col2`, `col3`";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);

        $query = new Query();
        $query->select('t.col1', 't.col2', 't.col3');
        $expected = "SELECT `t`.`col1`, `t`.`col2`, `t`.`col3`";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);

        $query = new Query();
        $query->select('t.col1 AS c1', 't.col2 AS c2', 't.col3 AS c3');
        $expected = "SELECT `t`.`col1` AS `c1`, `t`.`col2` AS `c2`, `t`.`col3` AS `c3`";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);

        $query = new Query();
        $query->select(literal('col1'), literal('col2'), literal('col3'));
        $expected = "SELECT 'col1', 'col2', 'col3'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);
    }

    public function testQuery_selectSubQuery()
    {
        $compiler = new MySqlCompiler;

        $q2 = new Query('t2');

        $query = new Query();
        $query->select($q2, 1, 2, 3);
        $expected = "SELECT (SELECT * FROM `t2`), 1, 2, 3";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);

        $query = new Query();
        $q2->alias('asdf');
        $query->select($q2);
        $expected = "SELECT (SELECT * FROM `t2`) AS `asdf`";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);
    }

    public function testQuery_groupBy()
    {
        $compiler = new MySqlCompiler;

        $query = new Query('table1');
        $query->groupBy('col1', 'col2', 'col3');
        $expected = "SELECT * FROM `table1` GROUP BY `col1`, `col2`, `col3`";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);
    }

    public function testQuery_orderBy()
    {
        $compiler = new MySqlCompiler;

        $query = new Query('table1');
        $query->orderBy('col1', 'col2', 'col3');
        $expected = "SELECT * FROM `table1` ORDER BY `col1` ASC, `col2` ASC, `col3` ASC";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);

        $query = new Query('table1');
        $query->orderByDesc('col1', 'col2', 'col3');
        $expected = "SELECT * FROM `table1` ORDER BY `col1` DESC, `col2` DESC, `col3` DESC";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);

        $query = new Query('table1');
        $query->orderBy('col1');
        $query->orderByDesc('col2');
        $query->orderByRandom();
        $expected = "SELECT * FROM `table1` ORDER BY `col1` ASC, `col2` DESC, RAND()";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);
    }

    public function testQuery_paging()
    {
        $compiler = new MySqlCompiler;

        $query = new Query('t1');
        $query->limit(100);
        $expected = "SELECT * FROM `t1` LIMIT 100";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);

        $query = new Query('t1');
        $query->limit(100);
        $query->offset(123);
        $expected = "SELECT * FROM `t1` LIMIT 123, 100";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);

        $query = new Query('t1');
        $query->offset(321);
        $expected = "SELECT * FROM `t1` LIMIT 321, 18446744073709551615";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);
    }

    public function testQuery_where()
    {
        $compiler = new MySqlCompiler;

        $query = new Query('t1');
        $q = new Query('t2');

        $query->where('c1', 1);
        $query->orWhere('c1', 1.01);
        $query->whereNot('c1', true);
        $query->orWhereNot('c1', null);

        $expected = "SELECT * FROM `t1` WHERE `c1` = 1 OR `c1` = 1.01 AND NOT (`c1` = TRUE) OR NOT (`c1` = NULL)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);
        
        $query = new Query('t1');
        $query->where('c1', 1);
        $query->where('c1', 2);
        $query->orWhere('c1', 3);
        $query->whereNot('c1', 4);
        $query->orWhereNot('c1', 5);

        $expected = "SELECT * FROM `t1` WHERE `c1` = 1 AND `c1` = 2 OR `c1` = 3 AND NOT (`c1` = 4) OR NOT (`c1` = 5)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);
        
        $query = new Query('t1');
        $query->where('c1', 1.01);
        $query->where('c1', 2.02);
        $query->orWhere('c1', 3.03);
        $query->whereNot('c1', 4.04);
        $query->orWhereNot('c1', 5.05);

        $expected = "SELECT * FROM `t1` WHERE `c1` = 1.01 AND `c1` = 2.02 OR `c1` = 3.03 AND NOT (`c1` = 4.04) OR NOT (`c1` = 5.05)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);
        
        $query = new Query('t1');
        $query->where('c1', false);
        $query->where('c1', true);
        $query->orWhere('c1', false);
        $query->whereNot('c1', true);
        $query->orWhereNot('c1', false);

        $expected = "SELECT * FROM `t1` WHERE `c1` = FALSE AND `c1` = TRUE OR `c1` = FALSE AND NOT (`c1` = TRUE) OR NOT (`c1` = FALSE)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);
        
        $query = new Query('t1');
        $query->where('c1', null);
        $query->where('c1', null);
        $query->orWhere('c1', null);
        $query->whereNot('c1', null);
        $query->orWhereNot('c1', null);

        $expected = "SELECT * FROM `t1` WHERE `c1` = NULL AND `c1` = NULL OR `c1` = NULL AND NOT (`c1` = NULL) OR NOT (`c1` = NULL)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);
        
        $query = new Query('t1');
        $query->where('c1', $q);
        $query->where('c1', $q);
        $query->orWhere('c1', $q);
        $query->whereNot('c1', $q);
        $query->orWhereNot('c1', $q);

        $expected = "SELECT * FROM `t1` WHERE `c1` = (SELECT * FROM `t2`) AND `c1` = (SELECT * FROM `t2`) OR `c1` = (SELECT * FROM `t2`) AND NOT (`c1` = (SELECT * FROM `t2`)) OR NOT (`c1` = (SELECT * FROM `t2`))";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);
        
        $query = new Query('t1');
        $query->where('c1', function($q) { return $q->from('t3'); });
        $query->where('c1', function($q) { return $q->from('t3'); });
        $query->orWhere('c1', function($q) { return $q->from('t3'); });
        $query->whereNot('c1', function($q) { return $q->from('t3'); });
        $query->orWhereNot('c1', function($q) { return $q->from('t3'); });

        $expected = "SELECT * FROM `t1` WHERE `c1` = (SELECT * FROM `t3`) AND `c1` = (SELECT * FROM `t3`) OR `c1` = (SELECT * FROM `t3`) AND NOT (`c1` = (SELECT * FROM `t3`)) OR NOT (`c1` = (SELECT * FROM `t3`))";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled);
    }
}
