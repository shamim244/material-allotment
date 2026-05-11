<?php

namespace Tests\Feature;

use App\Models\Meter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkerDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worker = User::factory()->create(['role' => 'worker']);
        $this->otherWorker = User::factory()->create(['role' => 'worker']);
    }

    public function test_worker_can_view_assigned_meters_dashboard()
    {
        $meter1 = Meter::factory()->create(['meter_number' => 'M100', 'status' => 'assigned', 'current_worker_id' => $this->worker->id]);
        $meter2 = Meter::factory()->create(['meter_number' => 'M101', 'status' => 'assigned', 'current_worker_id' => $this->worker->id]);
        $unassignedMeter = Meter::factory()->create(['meter_number' => 'M200', 'status' => 'stock']);

        $response = $this->actingAs($this->worker)
            ->get(route('worker.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('M100');
        $response->assertSee('M101');
        $response->assertDontSee('M200');
    }

    public function test_worker_can_update_meter_status_to_installed()
    {
        $meter = Meter::factory()->create(['meter_number' => 'M100', 'status' => 'assigned', 'current_worker_id' => $this->worker->id]);

        $response = $this->actingAs($this->worker)
            ->patch(route('worker.meter.update', $meter), [
                'status' => 'installed',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('meters', [
            'id' => $meter->id,
            'status' => 'installed',
        ]);
        $this->assertDatabaseHas('meter_logs', [
            'meter_id' => $meter->id,
            'action' => 'status_change',
            'user_id' => $this->worker->id,
        ]);
    }

    public function test_worker_can_report_issue()
    {
        $meter = Meter::factory()->create(['meter_number' => 'M100', 'status' => 'assigned', 'current_worker_id' => $this->worker->id]);

        $response = $this->actingAs($this->worker)
            ->patch(route('worker.meter.update', $meter), [
                'status' => 'issue',
                'remark' => 'Broken screen',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('meters', [
            'id' => $meter->id,
            'status' => 'issue',
        ]);
        $this->assertDatabaseHas('meter_logs', [
            'meter_id' => $meter->id,
            'user_id' => $this->worker->id,
            'remark' => 'Changed status from assigned to issue. Broken screen',
        ]);
    }

    public function test_worker_cannot_update_others_meters()
    {
        $meter = Meter::factory()->create(['meter_number' => 'M300', 'status' => 'assigned', 'current_worker_id' => $this->otherWorker->id]);

        $response = $this->actingAs($this->worker)
            ->patch(route('worker.meter.update', $meter), [
                'status' => 'installed',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('meters', [
            'id' => $meter->id,
            'status' => 'assigned', // Should remain unchanged
        ]);
    }
}
