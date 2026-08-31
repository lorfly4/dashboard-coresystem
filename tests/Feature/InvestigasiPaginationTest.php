<?php

namespace Tests\Feature;

use App\Http\Controllers\InvestigasiController;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class InvestigasiPaginationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_data_returns_paginated_results_with_ten_per_page(): void
    {
        $payload = [
            ['id' => 1],
            ['id' => 2],
        ];

        $paginator = new LengthAwarePaginator(
            $payload,
            2,
            10,
            1,
            ['path' => '/pra-registrasi/data']
        );

        $mock = Mockery::mock('alias:App\\Models\\Investigasi');
        $mock->shouldReceive('with')->once()->with([
            'asuransi',
            'jenisClaim',
            'investigator',
        ])->andReturnSelf();
        $mock->shouldReceive('latest')->once()->andReturnSelf();
        $mock->shouldReceive('paginate')->once()->with(10)->andReturn($paginator);

        $response = (new InvestigasiController())->data(new \Illuminate\Http\Request());

        $this->assertSame(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertTrue($json['success']);
        $this->assertCount(2, $json['data']);
        $this->assertSame(10, $json['pagination']['per_page']);
    }
}
