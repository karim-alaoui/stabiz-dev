<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use function PHPUnit\Framework\assertTrue;

/**
 * Testing for different helper functions in helper.php
 * Class HelperFn
 * @package Tests\Unit
 */
class HelperFn extends TestCase
{
    /** @noinspection PhpNonStrictObjectEqualityInspection */
    public function test_db_bool_value_fn()
    {
        $this->assertTrue(db_bool_val(true) == DB::raw('true'));
        $this->assertTrue(db_bool_val(false) == DB::raw('false'));
        $this->assertTrue(db_bool_val(null) == DB::raw('null'));
    }

    public function test_bool_convert()
    {
        assertTrue(bool_convert('true') === true);
        assertTrue(bool_convert('1') === true);
        assertTrue(bool_convert('on') === true);
        assertTrue(bool_convert(true) === true);
        assertTrue(bool_convert(false) === false);
        assertTrue(bool_convert('false') === false);
        assertTrue(bool_convert('random value') === false);
    }

    public function test_format_date()
    {
        $format = config('other.jp_date_format_php');
        $now = now()->format($format);
        assertTrue($now == format_date($now));
    }
}
