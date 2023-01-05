<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SqlTark\Compiler\MySqlCompiler;
use SqlTark\Query\Query;
use SqlTark\Expressions;

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
        $query->select(new Expressions\Literal('col1'), new Expressions\Literal('col2'), new Expressions\Literal('col3'));
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

        $q = new Query('t2');

        $query = new Query('t1');
        $query->where('c1', '=', 'val');
        $query->where('c1', '!=', 'val');
        $query->where('c1', '<', 'val');
        $query->where('c1', '>', 'val');
        $query->where('c1', 'LIKE', 'val');

        $expected = "SELECT * FROM `t1` WHERE `c1` = 'val' AND `c1` != 'val' AND `c1` < 'val' AND `c1` > 'val' AND `c1` LIKE 'val'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test various operator');

        $query = new Query('t1');
        $query->where('c1', 1);
        $query->where('c1', 1.01);
        $query->where('c1', true);
        $query->where('c1', null);
        $query->where('c1', 'null');

        $expected = "SELECT * FROM `t1` WHERE `c1` = 1 AND `c1` = 1.01 AND `c1` = TRUE AND `c1` = NULL AND `c1` = 'null'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test chaining and');

        $query = new Query('t1');
        $query->orWhere('c1', 1);
        $query->orWhere('c1', 1.01);
        $query->orWhere('c1', true);
        $query->orWhere('c1', null);
        $query->orWhere('c1', 'null');

        $expected = "SELECT * FROM `t1` WHERE `c1` = 1 OR `c1` = 1.01 OR `c1` = TRUE OR `c1` = NULL OR `c1` = 'null'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test chaining or');

        $query = new Query('t1');
        $query->whereNot('c1', 1);
        $query->whereNot('c1', 1.01);
        $query->whereNot('c1', true);
        $query->whereNot('c1', null);
        $query->whereNot('c1', 'null');

        $expected = "SELECT * FROM `t1` WHERE NOT (`c1` = 1) AND NOT (`c1` = 1.01) AND NOT (`c1` = TRUE) AND NOT (`c1` = NULL) AND NOT (`c1` = 'null')";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test chaining not');

        $query = new Query('t1');
        $query->where('t1.c1', $q);

        $expected = "SELECT * FROM `t1` WHERE `t1`.`c1` = (SELECT * FROM `t2`)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test column against query');

        $query = new Query('t1');
        $query->where('t1.c1', function($q) { return $q->from('t2'); });

        $expected = "SELECT * FROM `t1` WHERE `t1`.`c1` = (SELECT * FROM `t2`)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test column against closure');

        $query = new Query('t1');
        $query->where(1, 'val');
        $query->where(1.01, 'val');
        $query->where(true, 'val');
        $query->where(new Expressions\Literal('val'), 'val');

        $expected = "SELECT * FROM `t1` WHERE 1 = 'val' AND 1.01 = 'val' AND TRUE = 'val' AND 'val' = 'val'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test literal against literal');

        $query = new Query('t1');
        $query->where(1, new Expressions\Column('val'));
        $query->where(1.01, new Expressions\Column('val'));
        $query->where(true, new Expressions\Column('val'));
        $query->where(new Expressions\Literal('val'), new Expressions\Column('t1.val'));

        $expected = "SELECT * FROM `t1` WHERE 1 = `val` AND 1.01 = `val` AND TRUE = `val` AND 'val' = `t1`.`val`";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test literal against column');

        $query = new Query('t1');
        $query->where($q, $q);

        $expected = "SELECT * FROM `t1` WHERE (SELECT * FROM `t2`) = (SELECT * FROM `t2`)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test query against query');

        $query = new Query('t1');
        $query->where($q, function($q) { return $q->from('tbl'); });

        $expected = "SELECT * FROM `t1` WHERE (SELECT * FROM `t2`) = (SELECT * FROM `tbl`)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test query against closure');

        $query = new Query('t1');
        $query->where(function($q) { return $q->from('tbl'); }, $q);

        $expected = "SELECT * FROM `t1` WHERE (SELECT * FROM `tbl`) = (SELECT * FROM `t2`)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test closure against query');

        $query = new Query('t1');
        $query->where(function($q) { return $q->from('tbl'); }, function($q) { return $q->from('tbl'); });

        $expected = "SELECT * FROM `t1` WHERE (SELECT * FROM `tbl`) = (SELECT * FROM `tbl`)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test closure against closure');
    }

    public function testQuery_whereIn()
    {
        $compiler = new MySqlCompiler;

        $q = new Query('t2');
        $fn = function($q) { return $q->from('tbl'); };
        
        // Test various modifier
        $query = new Query('t1');
        $query->whereIn('c1', [1, 2.02, true]);
        $query->whereNotIn('c1', [null, 'str', 1]);
        $query->orWhereIn('c1', [2.02, true, null]);
        $query->orWhereNotIn('c1', ['str', 1, 2.02]);

        $expected = "SELECT * FROM `t1` WHERE `c1` IN (1, 2.02, TRUE) AND `c1` NOT IN (NULL, 'str', 1) OR `c1` IN (2.02, TRUE, NULL) OR `c1` NOT IN ('str', 1, 2.02)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test various modifier');
        
        $query = new Query('t1');
        $query->whereIn('c1', $q);

        $expected = "SELECT * FROM `t1` WHERE `c1` IN (SELECT * FROM `t2`)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test against query');
        
        $query = new Query('t1');
        $query->whereIn('c1', $fn);

        $expected = "SELECT * FROM `t1` WHERE `c1` IN (SELECT * FROM `tbl`)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test against closure');
        
        $query = new Query('t1');
        $query->whereIn(1, [1, 2.02]);
        $query->whereIn(1.01, [true, null]);
        $query->whereIn(true, ['5', new Expressions\Column('x')]);
        $query->whereIn(null, $q);
        $query->whereIn(new Expressions\Literal('str'), $fn);

        $expected = "SELECT * FROM `t1` WHERE 1 IN (1, 2.02) AND 1.01 IN (TRUE, NULL) AND TRUE IN ('5', `x`) AND NULL IN (SELECT * FROM `t2`) AND 'str' IN (SELECT * FROM `tbl`)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test literal in');
        
        $query = new Query('t1');
        $query->whereIn($q, [1, 2.02]);
        $query->whereIn($q, [true, null]);
        $query->whereIn($q, ['5', new Expressions\Column('x')]);
        $query->whereIn($q, $q);
        $query->whereIn($q, $fn);

        $expected = "SELECT * FROM `t1` WHERE (SELECT * FROM `t2`) IN (1, 2.02) AND (SELECT * FROM `t2`) IN (TRUE, NULL) AND (SELECT * FROM `t2`) IN ('5', `x`) AND (SELECT * FROM `t2`) IN (SELECT * FROM `t2`) AND (SELECT * FROM `t2`) IN (SELECT * FROM `tbl`)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test query in');
        
        $query = new Query('t1');
        $query->whereIn($fn, [1, 2.02]);
        $query->whereIn($fn, [true, null]);
        $query->whereIn($fn, ['5', new Expressions\Column('x')]);
        $query->whereIn($fn, $q);
        $query->whereIn($fn, $fn);

        $expected = "SELECT * FROM `t1` WHERE (SELECT * FROM `tbl`) IN (1, 2.02) AND (SELECT * FROM `tbl`) IN (TRUE, NULL) AND (SELECT * FROM `tbl`) IN ('5', `x`) AND (SELECT * FROM `tbl`) IN (SELECT * FROM `t2`) AND (SELECT * FROM `tbl`) IN (SELECT * FROM `tbl`)";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test closure in');
    }

    public function testQuery_whereLike()
    {
        $compiler = new MySqlCompiler;

        $q = new Query('t2');
        $fn = function($q) { return $q->from('tbl'); };
        
        // Test various modifier
        $query = new Query('t1');
        $query->whereLike('c1', 'asdf');
        $query->whereNotLike('c1', 'asdf%');
        $query->orWhereLike('c1', '%asdf');
        $query->orWhereNotLike('c1', '%asdf%');

        $expected = "SELECT * FROM `t1` WHERE `c1` LIKE 'asdf' AND `c1` NOT LIKE 'asdf%' OR `c1` LIKE '%asdf' OR `c1` NOT LIKE '%asdf%'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test various modifier');
        
        $query = new Query('t1');
        $query->whereLike(1, 'asdf');
        $query->whereLike(1.1, 'asdf');
        $query->whereLike(true, 'asdf');
        $query->whereLike(null, 'asdf');
        $query->whereLike(new Expressions\Literal('asdf'), 'asdf');
        $query->whereLike($q, 'asdf');
        $query->whereLike($fn, 'asdf');

        $expected = "SELECT * FROM `t1` WHERE 1 LIKE 'asdf' AND 1.1 LIKE 'asdf' AND TRUE LIKE 'asdf' AND NULL LIKE 'asdf' AND 'asdf' LIKE 'asdf' AND (SELECT * FROM `t2`) LIKE 'asdf' AND (SELECT * FROM `tbl`) LIKE 'asdf'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test literal like');
        
        $query = new Query('t1');
        $query->whereLike('c1', 'asdf', true);

        $expected = "SELECT * FROM `t1` WHERE `c1` LIKE BINARY 'asdf'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test case sensitive');
        
        $query = new Query('t1');
        $query->whereLike('c1', 'asdf', false, '!');

        $expected = "SELECT * FROM `t1` WHERE `c1` LIKE 'asdf' ESCAPE '!'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test escape');
    }

    public function testQuery_whereStarts()
    {
        $compiler = new MySqlCompiler;

        $q = new Query('t2');
        $fn = function($q) { return $q->from('tbl'); };
        
        // Test various modifier
        $query = new Query('t1');
        $query->whereStarts('c1', 'asdf');
        $query->whereNotStarts('c1', 'asdf%');
        $query->orWhereStarts('c1', '%asdf');
        $query->orWhereNotStarts('c1', '%asdf%');

        $expected = "SELECT * FROM `t1` WHERE `c1` LIKE 'asdf%' AND `c1` NOT LIKE 'asdf\%%' OR `c1` LIKE '\%asdf%' OR `c1` NOT LIKE '\%asdf\%%'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test various modifier');
        
        $query = new Query('t1');
        $query->whereStarts(1, 'asdf');
        $query->whereStarts(1.1, 'asdf');
        $query->whereStarts(true, 'asdf');
        $query->whereStarts(null, 'asdf');
        $query->whereStarts(new Expressions\Literal('asdf'), 'asdf');
        $query->whereStarts($q, 'asdf');
        $query->whereStarts($fn, 'asdf');

        $expected = "SELECT * FROM `t1` WHERE 1 LIKE 'asdf%' AND 1.1 LIKE 'asdf%' AND TRUE LIKE 'asdf%' AND NULL LIKE 'asdf%' AND 'asdf' LIKE 'asdf%' AND (SELECT * FROM `t2`) LIKE 'asdf%' AND (SELECT * FROM `tbl`) LIKE 'asdf%'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test literal like');
        
        $query = new Query('t1');
        $query->whereStarts('c1', 'asdf', true);

        $expected = "SELECT * FROM `t1` WHERE `c1` LIKE BINARY 'asdf%'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test case sensitive');
        
        $query = new Query('t1');
        $query->whereStarts('c1', 'asdf%', false, '!');

        $expected = "SELECT * FROM `t1` WHERE `c1` LIKE 'asdf!%%' ESCAPE '!'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test escape');
    }

    public function testQuery_whereEnds()
    {
        $compiler = new MySqlCompiler;

        $q = new Query('t2');
        $fn = function($q) { return $q->from('tbl'); };
        
        // Test various modifier
        $query = new Query('t1');
        $query->whereEnds('c1', 'asdf');
        $query->whereNotEnds('c1', 'asdf%');
        $query->orWhereEnds('c1', '%asdf');
        $query->orWhereNotEnds('c1', '%asdf%');

        $expected = "SELECT * FROM `t1` WHERE `c1` LIKE '%asdf' AND `c1` NOT LIKE '%asdf\%' OR `c1` LIKE '%\%asdf' OR `c1` NOT LIKE '%\%asdf\%'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test various modifier');
        
        $query = new Query('t1');
        $query->whereEnds(1, 'asdf');
        $query->whereEnds(1.1, 'asdf');
        $query->whereEnds(true, 'asdf');
        $query->whereEnds(null, 'asdf');
        $query->whereEnds(new Expressions\Literal('asdf'), 'asdf');
        $query->whereEnds($q, 'asdf');
        $query->whereEnds($fn, 'asdf');

        $expected = "SELECT * FROM `t1` WHERE 1 LIKE '%asdf' AND 1.1 LIKE '%asdf' AND TRUE LIKE '%asdf' AND NULL LIKE '%asdf' AND 'asdf' LIKE '%asdf' AND (SELECT * FROM `t2`) LIKE '%asdf' AND (SELECT * FROM `tbl`) LIKE '%asdf'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test literal like');
        
        $query = new Query('t1');
        $query->whereEnds('c1', 'asdf', true);

        $expected = "SELECT * FROM `t1` WHERE `c1` LIKE BINARY '%asdf'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test case sensitive');
        
        $query = new Query('t1');
        $query->whereEnds('c1', '%asdf', false, '!');

        $expected = "SELECT * FROM `t1` WHERE `c1` LIKE '%!%asdf' ESCAPE '!'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test escape');
    }

    public function testQuery_whereContains()
    {
        $compiler = new MySqlCompiler;

        $q = new Query('t2');
        $fn = function($q) { return $q->from('tbl'); };
        
        // Test various modifier
        $query = new Query('t1');
        $query->whereContains('c1', 'asdf');
        $query->whereNotContains('c1', 'asdf%');
        $query->orWhereContains('c1', '%asdf');
        $query->orWhereNotContains('c1', '%asdf%');

        $expected = "SELECT * FROM `t1` WHERE `c1` LIKE '%asdf%' AND `c1` NOT LIKE '%asdf\%%' OR `c1` LIKE '%\%asdf%' OR `c1` NOT LIKE '%\%asdf\%%'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test various modifier');
        
        $query = new Query('t1');
        $query->whereContains(1, 'asdf');
        $query->whereContains(1.1, 'asdf');
        $query->whereContains(true, 'asdf');
        $query->whereContains(null, 'asdf');
        $query->whereContains(new Expressions\Literal('asdf'), 'asdf');
        $query->whereContains($q, 'asdf');
        $query->whereContains($fn, 'asdf');

        $expected = "SELECT * FROM `t1` WHERE 1 LIKE '%asdf%' AND 1.1 LIKE '%asdf%' AND TRUE LIKE '%asdf%' AND NULL LIKE '%asdf%' AND 'asdf' LIKE '%asdf%' AND (SELECT * FROM `t2`) LIKE '%asdf%' AND (SELECT * FROM `tbl`) LIKE '%asdf%'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test literal like');
        
        $query = new Query('t1');
        $query->whereContains('c1', 'asdf', true);

        $expected = "SELECT * FROM `t1` WHERE `c1` LIKE BINARY '%asdf%'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test case sensitive');
        
        $query = new Query('t1');
        $query->whereContains('c1', '%asdf%', false, '!');

        $expected = "SELECT * FROM `t1` WHERE `c1` LIKE '%!%asdf!%%' ESCAPE '!'";
        $compiled = $compiler->compileQuery($query);
        $this->assertEquals($expected, $compiled, 'Test escape');
    }

    public function testQuery_join()
    {
        $compiler = new MySqlCompiler;

        $q = new Query('t1');
        $q->join('t2', 't1.id', 't2.id');

        $expected = "SELECT * FROM `t1` JOIN `t2` ON `t1`.`id` = `t2`.`id`";
        $compiled = $compiler->compileQuery($q);
        $this->assertEquals($expected, $compiled, 'Test Join');

        $q = new Query('t1');
        $q->leftJoin('t2', 't1.id', 't2.id');

        $expected = "SELECT * FROM `t1` LEFT JOIN `t2` ON `t1`.`id` = `t2`.`id`";
        $compiled = $compiler->compileQuery($q);
        $this->assertEquals($expected, $compiled, 'Test Left Join');

        $q = new Query('t1');
        $q->rightJoin('t2', 't1.id', 't2.id');

        $expected = "SELECT * FROM `t1` RIGHT JOIN `t2` ON `t1`.`id` = `t2`.`id`";
        $compiled = $compiler->compileQuery($q);
        $this->assertEquals($expected, $compiled, 'Test Right Join');

        $q = new Query('t1');
        $q->innerJoin('t2', 't1.id', 't2.id');

        $expected = "SELECT * FROM `t1` INNER JOIN `t2` ON `t1`.`id` = `t2`.`id`";
        $compiled = $compiler->compileQuery($q);
        $this->assertEquals($expected, $compiled, 'Test Inner Join');

        $q = new Query('t1');
        $q->outerJoin('t2', 't1.id', 't2.id');

        $expected = "SELECT * FROM `t1` OUTER JOIN `t2` ON `t1`.`id` = `t2`.`id`";
        $compiled = $compiler->compileQuery($q);
        $this->assertEquals($expected, $compiled, 'Test Outer Join');
    }

    public function testQuery_multipleConditionJoin()
    {
        $compiler = new MySqlCompiler;

        $q = new Query('t1');
        $q->leftJoin(function($j) {
            return $j->from('t2 AS a')
                ->on('t1.id', 't2.id')
                ->on('t1.period', 202212)
                ->on('t1.name', Expressions::literal('zay'))
            ;
        });
        $q->whereNull('t2.id');

        $expected = "SELECT * FROM `t1` LEFT JOIN `t2` AS `a` ON `t1`.`id` = `t2`.`id` AND `t1`.`period` = 202212 AND `t1`.`name` = 'zay' WHERE `t2`.`id` IS NULL";
        $compiled = $compiler->compileQuery($q);
        $this->assertEquals($expected, $compiled, 'Test Join');

        $q = new Query('t1');
        $q->leftJoin('t2 AS a', function($j) {
            return $j->on('t1.id', 't2.id')
                ->on('t1.period', 202212)
                ->on('t1.name', Expressions::literal('zay'))
            ;
        });
        $q->whereNull('t2.id');

        $expected = "SELECT * FROM `t1` LEFT JOIN `t2` AS `a` ON `t1`.`id` = `t2`.`id` AND `t1`.`period` = 202212 AND `t1`.`name` = 'zay' WHERE `t2`.`id` IS NULL";
        $compiled = $compiler->compileQuery($q);
        $this->assertEquals($expected, $compiled, 'Test Join');
    }

    public function testInsertQuery()
    {
        $compiler = new MySqlCompiler;

        $query = new Query('t1');
        $insertQuery = $query->asInsert([
            'col1' => 1,
            'col2' => 1.01,
            'col3' => null,
            'col4' => false,
            'col5' => 'string',
        ]);

        $expected = "INSERT INTO `t1` (`col1`, `col2`, `col3`, `col4`, `col5`) VALUES (1, 1.01, NULL, FALSE, 'string')";
        $compiled = $compiler->compileInsertQuery($insertQuery);
        $this->assertEquals($expected, $compiled);

        $query = new Query('t1');
        $insertQuery = $query->asInsert(
            ['col1', 'col2', 'col3', 'col4', 'col5'],
            [
                [1, 1.01, NULL, FALSE, 'string'],
                [1, 1.01, NULL, FALSE, 'string'],
                [1, 1.01, NULL, FALSE, 'string'],
            ]
        );

        $expected = "INSERT INTO `t1` (`col1`, `col2`, `col3`, `col4`, `col5`) VALUES (1, 1.01, NULL, FALSE, 'string'), (1, 1.01, NULL, FALSE, 'string'), (1, 1.01, NULL, FALSE, 'string')";
        $compiled = $compiler->compileInsertQuery($insertQuery);
        $this->assertEquals($expected, $compiled);

        $query = new Query('t1');
        $insertQuery = $query->asInsert(
            [
                ['col1' => 1, 'col2' => 1.01, 'col3' => NULL, 'col4' => FALSE, 'col5' => 'string'],
                ['col1' => 1, 'col2' => 1.01, 'col3' => NULL, 'col4' => FALSE, 'col5' => 'string'],
                ['col1' => 1, 'col2' => 1.01, 'col3' => NULL, 'col4' => FALSE, 'col5' => 'string'],
            ]
        );

        $expected = "INSERT INTO `t1` (`col1`, `col2`, `col3`, `col4`, `col5`) VALUES (1, 1.01, NULL, FALSE, 'string'), (1, 1.01, NULL, FALSE, 'string'), (1, 1.01, NULL, FALSE, 'string')";
        $compiled = $compiler->compileInsertQuery($insertQuery);
        $this->assertEquals($expected, $compiled);
    }

    public function testInsertQuery_fromQuery()
    {
        $compiler = new MySqlCompiler;
        $q = new Query('t2');

        $query = new Query('t1');
        $insertQuery = $query->asInsertWithQuery($q);

        $expected = "INSERT INTO `t1` SELECT * FROM `t2`";
        $compiled = $compiler->compileInsertQuery($insertQuery);
        $this->assertEquals($expected, $compiled);

        $query = new Query('t1');
        $insertQuery = $query->asInsertWithQuery($q, ['c1', 'c2', 'c3']);

        $expected = "INSERT INTO `t1` (`c1`, `c2`, `c3`) SELECT * FROM `t2`";
        $compiled = $compiler->compileInsertQuery($insertQuery);
        $this->assertEquals($expected, $compiled);
    }

    public function testUpdateQuery()
    {
        $compiler = new MySqlCompiler;
        $q = new Query('t2');

        $query = new Query('t1');
        $updateQuery = $query->asUpdate([
            'col1' => 'str',
            'col2' => 1,
            'col3' => 1.1,
            'col4' => true,
            'col5' => null,
        ]);

        $expected = "UPDATE `t1` SET `col1` = 'str', `col2` = 1, `col3` = 1.1, `col4` = TRUE, `col5` = NULL";
        $compiled = $compiler->compileUpdateQuery($updateQuery);
        $this->assertEquals($expected, $compiled);

        $query = new Query('t1');
        $updateQuery = $query->asUpdate((object) [
            'col1' => 'str',
            'col2' => 1,
            'col3' => 1.1,
            'col4' => true,
            'col5' => null,
        ]);

        $expected = "UPDATE `t1` SET `col1` = 'str', `col2` = 1, `col3` = 1.1, `col4` = TRUE, `col5` = NULL";
        $compiled = $compiler->compileUpdateQuery($updateQuery);
        $this->assertEquals($expected, $compiled);

        $query = new Query('t1');
        $updateQuery = $query->where('col1', 'str')->limit(1)->asUpdate([
            'col1' => 'str',
            'col2' => 1,
            'col3' => 1.1,
            'col4' => true,
            'col5' => null,
        ]);

        $expected = "UPDATE `t1` SET `col1` = 'str', `col2` = 1, `col3` = 1.1, `col4` = TRUE, `col5` = NULL WHERE `col1` = 'str' LIMIT 1";
        $compiled = $compiler->compileUpdateQuery($updateQuery);
        $this->assertEquals($expected, $compiled);
    }

    public function testDeleteQuery()
    {
        
        $compiler = new MySqlCompiler;
        $q = new Query('t2');

        $query = new Query('t1');
        $deleteQuery = $query->asDelete();

        $expected = "DELETE FROM `t1`";
        $compiled = $compiler->compileDeleteQuery($deleteQuery);
        $this->assertEquals($expected, $compiled);

        $query = new Query('t1');
        $deleteQuery = $query->asDelete()->where('col1', 'str')->limit(1);

        $expected = "DELETE FROM `t1` WHERE `col1` = 'str' LIMIT 1";
        $compiled = $compiler->compileDeleteQuery($deleteQuery);
        $this->assertEquals($expected, $compiled);
    }

    public function testQuery_combine()
    {
        $compiler = new MySqlCompiler;
        $q = new Query('t2');
        $fn = function($q) { return $q->from('tbl'); };

        $query = new Query('t1');
        $query->union($q);
        $query->unionAll($fn);
        $query->except($q);
        $query->exceptAll($fn);
        $query->intersect($q);
        $query->intersectAll($fn);

        $compiled = $compiler->compileQuery($query);
        $expected = 'SELECT * FROM `t1` UNION SELECT * FROM `t2` UNION ALL SELECT * FROM `tbl` EXCEPT SELECT * FROM `t2` EXCEPT ALL SELECT * FROM `tbl` INTERSECT SELECT * FROM `t2` INTERSECT ALL SELECT * FROM `tbl`';
        $this->assertEquals($expected, $compiled);
    }

    public function testQuery_cte()
    {
        $compiler = new MySqlCompiler;
        $q = new Query('t2');
        $fn = function($q) { return $q->from('tbl'); };

        $query = new Query('t1');
        $query->with($q, '_cte1_');
        $query->with($fn, '_cte2_');
        $query->with($q->alias('_cte3_'));

        $compiled = $compiler->compileQuery($query);
        $expected = 'WITH _cte1_ AS (SELECT * FROM `t2`), _cte2_ AS (SELECT * FROM `tbl`), _cte3_ AS (SELECT * FROM `t2`) SELECT * FROM `t1`';
        $this->assertEquals($expected, $compiled);
    }
}
