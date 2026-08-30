<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\Models\TestUser;

class AdminTableTraitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_json_response_with_basic_query(): void
    {
        TestUser::create([
            'name'      => 'علی',
            'email'     => 'ali@example.com',
            'password'  => bcrypt('password'),
            'is_active' => true,
        ]);

        TestUser::create([
            'name'      => 'حسن',
            'email'     => 'hasan@example.com',
            'password'  => bcrypt('password'),
            'is_active' => false,
        ]);

        $request  = Request::create('/users.json', 'GET');
        $response = TestUser::adminTable($request, [
            'search'   => ['name', 'email'],
            'filters'  => ['is_active'],
            'sortable' => ['name', 'email', 'created_at'],
        ]);

        $json = $response->getData(true);

        $this->assertTrue($json['success']);
        $this->assertCount(2, $json['data']);
        $this->assertEquals(2, $json['total']);
        $this->assertEquals(1, $json['current_page']);
        $this->assertEquals(1, $json['last_page']);
    }

    /** @test */
    public function it_searches_across_multiple_columns(): void
    {
        TestUser::create(['name' => 'علی', 'email' => 'ali@example.com', 'password' => bcrypt('pass')]);
        TestUser::create(['name' => 'حسن', 'email' => 'hasan@example.com', 'password' => bcrypt('pass')]);

        $request  = Request::create('/users.json', 'GET', ['search' => 'علی']);
        $response = TestUser::adminTable($request, ['search' => ['name', 'email']]);

        $json = $response->getData(true);

        $this->assertTrue($json['success']);
        $this->assertCount(1, $json['data']);
        $this->assertEquals('علی', $json['data'][0]['name']);
    }

    /** @test */
    public function it_searches_by_email(): void
    {
        TestUser::create(['name' => 'علی', 'email' => 'ali@test.com', 'password' => bcrypt('pass')]);
        TestUser::create(['name' => 'حسن', 'email' => 'hasan@test.com', 'password' => bcrypt('pass')]);

        $request  = Request::create('/users.json', 'GET', ['search' => 'ali@test']);
        $response = TestUser::adminTable($request, ['search' => ['name', 'email']]);

        $json = $response->getData(true);
        $this->assertCount(1, $json['data']);
        $this->assertEquals('ali@test.com', $json['data'][0]['email']);
    }

    /** @test */
    public function it_filters_by_is_active(): void
    {
        TestUser::create(['name' => 'A', 'email' => 'a@test.com', 'password' => bcrypt('pass'), 'is_active' => true]);
        TestUser::create(['name' => 'B', 'email' => 'b@test.com', 'password' => bcrypt('pass'), 'is_active' => false]);

        $request  = Request::create('/users.json', 'GET', ['is_active' => 1]);
        $response = TestUser::adminTable($request, ['filters' => ['is_active']]);

        $json = $response->getData(true);
        $this->assertCount(1, $json['data']);
        $this->assertEquals(1, $json['data'][0]['is_active']);
    }

    /** @test */
    public function it_sorts_by_column(): void
    {
        TestUser::create(['name' => 'Zebra', 'email' => 'z@test.com', 'password' => bcrypt('pass')]);
        TestUser::create(['name' => 'Alpha', 'email' => 'a@test.com', 'password' => bcrypt('pass')]);

        $request  = Request::create('/users.json', 'GET', ['sort' => 'name', 'direction' => 'asc']);
        $response = TestUser::adminTable($request, ['sortable' => ['name']]);

        $json = $response->getData(true);
        $this->assertEquals('Alpha', $json['data'][0]['name']);
        $this->assertEquals('Zebra', $json['data'][1]['name']);
    }

    /** @test */
    public function it_sorts_descending(): void
    {
        TestUser::create(['name' => 'Alpha', 'email' => 'a@test.com', 'password' => bcrypt('pass')]);
        TestUser::create(['name' => 'Zebra', 'email' => 'z@test.com', 'password' => bcrypt('pass')]);

        $request  = Request::create('/users.json', 'GET', ['sort' => 'name', 'direction' => 'DESC']);
        $response = TestUser::adminTable($request, ['sortable' => ['name']]);

        $json = $response->getData(true);
        $this->assertEquals('Zebra', $json['data'][0]['name']);
        $this->assertEquals('Alpha', $json['data'][1]['name']);
    }

    /** @test */
    public function it_blocks_unauthorized_sort_columns(): void
    {
        TestUser::create(['name' => 'A', 'email' => 'a@test.com', 'password' => bcrypt('pass')]);
        TestUser::create(['name' => 'B', 'email' => 'b@test.com', 'password' => bcrypt('pass')]);

        $request  = Request::create('/users.json', 'GET', [
            'sort'      => 'password',
            'direction' => 'asc',
        ]);
        $response = TestUser::adminTable($request, ['sortable' => ['name', 'email']]);

        $json = $response->getData(true);
        $this->assertTrue($json['success']);
        $this->assertCount(2, $json['data']);
    }

    /** @test */
    public function it_sorts_by_default_column_when_no_sort_provided(): void
    {
        TestUser::create(['name' => 'A', 'email' => 'a@test.com', 'password' => bcrypt('pass')]);
        TestUser::create(['name' => 'B', 'email' => 'b@test.com', 'password' => bcrypt('pass')]);

        $request  = Request::create('/users.json', 'GET');
        $response = TestUser::adminTable($request);

        $json = $response->getData(true);
        $this->assertGreaterThan($json['data'][1]['id'], $json['data'][0]['id']);
    }

    /** @test */
    public function it_paginates_results(): void
    {
        for ($i = 0; $i < 25; $i++) {
            TestUser::create([
                'name'     => "User {$i}",
                'email'    => "user{$i}@test.com",
                'password' => bcrypt('pass'),
            ]);
        }

        $request  = Request::create('/users.json', 'GET', ['per_page' => 10]);
        $response = TestUser::adminTable($request);

        $json = $response->getData(true);
        $this->assertCount(10, $json['data']);
        $this->assertEquals(25, $json['total']);
        $this->assertEquals(3, $json['last_page']);
        $this->assertEquals(1, $json['current_page']);
        $this->assertEquals(1, $json['from']);
        $this->assertEquals(10, $json['to']);
    }

    /** @test */
    public function it_caps_per_page_at_100(): void
    {
        for ($i = 0; $i < 120; $i++) {
            TestUser::create([
                'name'     => "User {$i}",
                'email'    => "user{$i}@test.com",
                'password' => bcrypt('pass'),
            ]);
        }

        $request  = Request::create('/users.json', 'GET', ['per_page' => 500]);
        $response = TestUser::adminTable($request);

        $json = $response->getData(true);
        $this->assertCount(100, $json['data']);
        $this->assertEquals(100, $json['per_page']);
    }

    /** @test */
    public function it_uses_admin_transform_when_available(): void
    {
        TestUser::create([
            'name'     => 'Test User',
            'email'    => 'test@test.com',
            'password' => bcrypt('pass'),
        ]);

        $request  = Request::create('/users.json', 'GET');
        $response = TestUser::adminTable($request);

        $json = $response->getData(true);
        $this->assertTrue($json['success']);
        $this->assertArrayHasKey('id', $json['data'][0]);
        $this->assertArrayHasKey('name', $json['data'][0]);
        $this->assertArrayHasKey('email', $json['data'][0]);
        $this->assertArrayHasKey('is_active', $json['data'][0]);
    }

    /** @test */
    public function it_uses_custom_transform_callback(): void
    {
        TestUser::create([
            'name'     => 'Callback User',
            'email'    => 'cb@test.com',
            'password' => bcrypt('pass'),
        ]);

        $request  = Request::create('/users.json', 'GET');
        $response = TestUser::adminTable($request, [], function ($user) {
            return ['full_label' => "{$user->name} ({$user->email})"];
        });

        $json = $response->getData(true);
        $this->assertArrayHasKey('full_label', $json['data'][0]);
        $this->assertEquals('Callback User (cb@test.com)', $json['data'][0]['full_label']);
    }

    /** @test */
    public function it_combines_search_filter_and_sort(): void
    {
        TestUser::create(['name' => 'Ali', 'email' => 'ali@test.com', 'password' => bcrypt('pass'), 'is_active' => true]);
        TestUser::create(['name' => 'Hassan', 'email' => 'hassan@test.com', 'password' => bcrypt('pass'), 'is_active' => true]);
        TestUser::create(['name' => 'Ali B', 'email' => 'ali2@test.com', 'password' => bcrypt('pass'), 'is_active' => false]);

        $request = Request::create('/users.json', 'GET', [
            'search'    => 'Ali',
            'is_active' => 1,
            'sort'      => 'name',
            'direction' => 'asc',
        ]);
        $response = TestUser::adminTable($request, [
            'search'   => ['name', 'email'],
            'filters'  => ['is_active'],
            'sortable' => ['name'],
        ]);

        $json = $response->getData(true);
        $this->assertCount(1, $json['data']);
        $this->assertEquals('Ali', $json['data'][0]['name']);
    }

    /** @test */
    public function it_returns_empty_when_no_match(): void
    {
        TestUser::create(['name' => 'Ali', 'email' => 'ali@test.com', 'password' => bcrypt('pass')]);

        $request  = Request::create('/users.json', 'GET', ['search' => 'nonexistent']);
        $response = TestUser::adminTable($request, ['search' => ['name', 'email']]);

        $json = $response->getData(true);
        $this->assertTrue($json['success']);
        $this->assertCount(0, $json['data']);
        $this->assertEquals(0, $json['total']);
    }

    /** @test */
    public function it_handles_second_page(): void
    {
        for ($i = 0; $i < 15; $i++) {
            TestUser::create([
                'name'     => "User {$i}",
                'email'    => "user{$i}@test.com",
                'password' => bcrypt('pass'),
            ]);
        }

        $this->assertEquals(15, TestUser::count());

        $request  = Request::create('/users.json', 'GET', ['per_page' => 10, 'page' => 2]);
        $response = TestUser::adminTable($request);

        $json = $response->getData(true);
        $this->assertCount(5, $json['data']);
        $this->assertEquals(2, $json['current_page']);
        $this->assertEquals(11, $json['from']);
        $this->assertEquals(15, $json['to']);
    }
}
