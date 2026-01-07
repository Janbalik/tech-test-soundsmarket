<?php

/*

|--------------------------------------------------------------------------
| Test Case Configuration
|--------------------------------------------------------------------------
|
|  Extend from TestCase to all tests in Feature folder
|  and use lazy refresh for updating database
|
*/

use Tests\TestCase;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

pest()->extend(TestCase::class)
    ->use(LazilyRefreshDatabase::class)
    ->in('Feature');

/*

|--------------------------------------------------------------------------
| Unit Test Configuration
|--------------------------------------------------------------------------
|
| Extend PHPUnit\Framework\TestCase for unit tests
|
*/

pest()->extend(PHPUnit\Framework\TestCase::class)
    ->in('Unit');
