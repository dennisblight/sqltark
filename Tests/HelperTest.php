<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SqlTark\Helper;

final class HelperTest extends TestCase
{
    public function testFlatten_scalar()
    {
        $arr = Helper::flatten([1, 2, 3, 4, 5]);
        $this->assertEquals($arr, [1, 2, 3, 4, 5], 'Scalar array test');
    }

    public function testFlatten_shallow()
    {
        $arr = Helper::flatten([1, [2, 3, 4, 5]]);
        $this->assertEquals($arr, [1, 2, 3, 4, 5], 'Shallow array ending');

        $arr = Helper::flatten([[1, 2, 3, 4, 5]]);
        $this->assertEquals($arr, [1, 2, 3, 4, 5], 'Shallow array all');

        $arr = Helper::flatten([[1, 2, 3], 4, 5]);
        $this->assertEquals($arr, [1, 2, 3, 4, 5], 'Shallow array start');

        $arr = Helper::flatten([[1, 2], [3], [4, 5]]);
        $this->assertEquals($arr, [1, 2, 3, 4, 5], 'Shallow array each');
    }

    public function testFlatten_deep()
    {
        $arr = Helper::flatten([[[1, 2], [3, [4], 5]]]);
        $this->assertEquals($arr, [1, 2, 3, 4, 5], 'Deep array test');
    }

    public function testReplaceAll_itShouldKeepItAsIt()
    {
        $callback = function($x) { return $x . ''; };

        $input = '';
        $output = Helper::replaceAll($input, 'any', $callback);
        $this->assertEquals($input, $output);
        
        $input = null;
        $output = Helper::replaceAll($input, 'any', $callback);
        $this->assertEquals($input, $output);
        
        $input = ' ';
        $output = Helper::replaceAll($input, 'any', $callback);
        $this->assertEquals($input, $output);
        
        $input = '  ';
        $output = Helper::replaceAll($input, 'any', $callback);
        $this->assertEquals($input, $output);
        
        $input = '   ';
        $output = Helper::replaceAll($input, 'any', $callback);
        $this->assertEquals($input, $output);
    }

    public function testReplaceAll_replaceBeginning()
    {
        $callback = function() { return '@'; };
        
        $output = Helper::replaceAll('hello', '?', $callback);
        $this->assertEquals('hello', $output);
        
        $output = Helper::replaceAll('?hello', '?', $callback);
        $this->assertEquals('@hello', $output);
        
        $output = Helper::replaceAll('??hello', '?', $callback);
        $this->assertEquals('@@hello', $output);
        
        $output = Helper::replaceAll('?? hello', '?', $callback);
        $this->assertEquals('@@ hello', $output);
        
        $output = Helper::replaceAll('? ? hello', '?', $callback);
        $this->assertEquals('@ @ hello', $output);
        
        $output = Helper::replaceAll(' ? ? hello', '?', $callback);
        $this->assertEquals(' @ @ hello', $output);
    }

    public function testReplaceAll_replaceEnding()
    {
        $callback = function() { return '@'; };
        
        $output = Helper::replaceAll('hello', '?', $callback);
        $this->assertEquals('hello', $output);
        
        $output = Helper::replaceAll('hello?', '?', $callback);
        $this->assertEquals('hello@', $output);
        
        $output = Helper::replaceAll('hello??', '?', $callback);
        $this->assertEquals('hello@@', $output);
        
        $output = Helper::replaceAll('hello?? ', '?', $callback);
        $this->assertEquals('hello@@ ', $output);
        
        $output = Helper::replaceAll('hello? ? ', '?', $callback);
        $this->assertEquals('hello@ @ ', $output);
        
        $output = Helper::replaceAll('hello ? ? ', '?', $callback);
        $this->assertEquals('hello @ @ ', $output);
    }

    public function testReplaceAll_replaceWithPosition()
    {
        $callback = function($x) { return $x; };
        
        $output = Helper::replaceAll('hello', '?', $callback);
        $this->assertEquals('hello', $output);
        
        $output = Helper::replaceAll('hello?', '?', $callback);
        $this->assertEquals('hello0', $output);
        
        $output = Helper::replaceAll('hello??', '?', $callback);
        $this->assertEquals('hello01', $output);
        
        $output = Helper::replaceAll('hello?? ', '?', $callback);
        $this->assertEquals('hello01 ', $output);
        
        $output = Helper::replaceAll('hello? ? ', '?', $callback);
        $this->assertEquals('hello0 1 ', $output);
        
        $output = Helper::replaceAll('hello ? ? ', '?', $callback);
        $this->assertEquals('hello 0 1 ', $output);
    }
}