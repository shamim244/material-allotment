<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Meter;
use App\Models\MeterGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contractor = User::factory()->create(['role' => 'contractor']);
        $this->worker = User::factory()->create(['role' => 'worker']);
    }

    public function test_contractor_can_assign_group_to_worker()
    {
        $group = MeterGroup::create(['name' => 'Test Box', 'type' => 'box']);
        $meters = Meter::factory()->count(5)->create(['group_id' => $group->id, 'status' => 'stock']);

        $response = $this->actingAs($this->contractor)
            ->post(route('assignments.store'), [
                'worker_id' => $this->worker->id,
                'assignment_type' => 'group',
                'group_id' => $group->id,
            ]);

        $response->assertSessionHasNoErrors();
        
        foreach ($meters as $meter) {
            $this->assertDatabaseHas('meters', [
                'id' => $meter->id,
                'status' => 'assigned',
                'current_worker_id' => $this->worker->id,
            ]);
            
            $this->assertDatabaseHas('assignments', [
                'meter_id' => $meter->id,
                'worker_id' => $this->worker->id,
            ]);
        }
    }

    public function test_contractor_can_assign_individual_meters()
    {
        $meter1 = Meter::create(['meter_number' => 'M100', 'status' => 'stock']);
        $meter2 = Meter::create(['meter_number' => 'M101', 'status' => 'stock']);

        $response = $this->actingAs($this->contractor)
            ->post(route('assignments.store'), [
                'worker_id' => $this->worker->id,
                'assignment_type' => 'individual',
                'individual_meter_ids' => [$meter1->id, $meter2->id],
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('meters', ['meter_number' => 'M100', 'status' => 'assigned', 'current_worker_id' => $this->worker->id]);
        $this->assertDatabaseHas('meters', ['meter_number' => 'M101', 'status' => 'assigned', 'current_worker_id' => $this->worker->id]);
    }
}
