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
}
