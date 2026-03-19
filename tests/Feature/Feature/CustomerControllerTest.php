<?php

namespace Tests\Feature\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_customer_index(): void
    {
        $this->assertDatabaseCount('customers', 0);
        $response = $this->getJson('/api/customer');
        $response->assertStatus(200);
    }
}
