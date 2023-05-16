<?php

namespace Tests;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\App;

/**
 * Class TestCase
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        /**
         * If even by mistake, you ever end up running test on production server,
         * it will delete all the data in the database because .env.testing won't be
         * there most probably and no testing database will be there. It will use the main
         * database where all the previous data will be erased the moment you run the test
         * because all the tests use RefreshDatabase trait.
         * To prevent such an accidental disaster, this is added.
         * -----------------------------------------------
         * DON'T DELETE THIS EVEN BY MISTAKE
         * -----------------------------------------------
         */
        if (!App::environment(['local', 'testing'])) {
            throw new Exception('Can not run test on env apart from local, testing');
        }
    }
}
