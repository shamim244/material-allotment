<?php

namespace Tests\Feature;

use App\Models\Meter;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contractor = User::factory()->create(['role' => 'contractor']);
        $this->worker = User::factory()->create(['role' => 'worker']);
    }

    public function test_worker_can_request_return()
    {
        $meter = Meter::factory()->create([
            'meter_number' => 'M900',
            'status' => 'assigned',
            'current_worker_id' => $this->worker->id,
        ]);

        $response = $this->actingAs($this->worker)
            ->post(route('return.store'), [
                'meter_id' => $meter->id,
                'reason' => 'Unused',
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('return_requests', [
            'meter_id' => $meter->id,
            'worker_id' => $this->worker->id,
            'reason' => 'Unused',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('meters', [
            'id' => $meter->id,
            'status' => 'return_pending',
        ]);
    }

    public function test_contractor_can_approve_return()
    {
        $meter = Meter::factory()->create([
            'status' => 'return_pending',
            'current_worker_id' => $this->worker->id,
        ]);

        $returnRequest = ReturnRequest::create([
            'meter_id' => $meter->id,
            'worker_id' => $this->worker->id,
            'reason' => 'Defective',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->contractor)
            ->patch(route('returns.update', $returnRequest->id), [
                'status' => 'approved',
                'admin_remark' => 'OK',
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('return_requests', [
            'id' => $returnRequest->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('meters', [
            'id' => $meter->id,
            'status' => 'returned',
            'current_worker_id' => null,
        ]);
    }

    public function test_contractor_can_reject_return()
    {
        $meter = Meter::factory()->create([
            'status' => 'return_pending',
            'current_worker_id' => $this->worker->id,
        ]);

        $returnRequest = ReturnRequest::create([
            'meter_id' => $meter->id,
            'worker_id' => $this->worker->id,
            'reason' => 'Wrong reason',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->contractor)
            ->patch(route('returns.update', $returnRequest->id), [
                'status' => 'rejected',
                'admin_remark' => 'Keep it',
            ]);

        $this->assertDatabaseHas('return_requests', [
            'id' => $returnRequest->id,
            'status' => 'rejected',
        ]);

        $this->assertDatabaseHas('meters', [
            'id' => $meter->id,
            'status' => 'assigned', // Should revert to assigned
        ]);
    }
}
