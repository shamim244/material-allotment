<?php

namespace Tests\Feature;

use App\Models\InstallationDetail;
use App\Models\Meter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guests cannot access the map page.
     */
    public function test_guest_cannot_view_map_page(): void
    {
        $response = $this->get('/admin/map');
        $response->assertRedirect('/login');
    }

    /**
     * Test that worker role is forbidden from access.
     */
    public function test_worker_cannot_view_map_page(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $response = $this->actingAs($worker)->get('/admin/map');
        $response->assertStatus(403);
    }

    /**
     * Test that contractor role can access.
     */
    public function test_contractor_can_view_map_page(): void
    {
        $contractor = User::factory()->create(['role' => 'contractor']);
        $response = $this->actingAs($contractor)->get('/admin/map');
        $response->assertStatus(200);
        $response->assertSee('Installation Map');
    }

    /**
     * Test filtering installations on map by worker.
     */
    public function test_contractor_can_filter_map_by_worker(): void
    {
        $contractor = User::factory()->create(['role' => 'contractor']);
        $worker1 = User::factory()->create(['role' => 'worker']);
        $worker2 = User::factory()->create(['role' => 'worker']);

        $meter1 = Meter::factory()->create(['status' => 'installed']);
        $meter2 = Meter::factory()->create(['status' => 'installed']);

        $detail1 = InstallationDetail::create([
            'meter_id' => $meter1->id,
            'worker_id' => $worker1->id,
            'install_type' => 'nsc',
            'latitude' => 12.345,
            'longitude' => 67.890,
            'ca_number' => 'CA111',
            'request_number' => 'REQ111',
        ]);

        $detail2 = InstallationDetail::create([
            'meter_id' => $meter2->id,
            'worker_id' => $worker2->id,
            'install_type' => 'nsc',
            'latitude' => 23.456,
            'longitude' => 78.901,
            'ca_number' => 'CA222',
            'request_number' => 'REQ222',
        ]);

        // Filter by worker1
        $response = $this->actingAs($contractor)->get(route('admin.map.index', ['worker_id' => $worker1->id]));

        $response->assertStatus(200);
        $response->assertSee($meter1->meter_number);
        $response->assertDontSee($meter2->meter_number);
    }

    /**
     * Test filtering installations on map by installation type.
     */
    public function test_contractor_can_filter_map_by_type(): void
    {
        $contractor = User::factory()->create(['role' => 'contractor']);
        $worker = User::factory()->create(['role' => 'worker']);

        $meter1 = Meter::factory()->create(['status' => 'installed']);
        $meter2 = Meter::factory()->create(['status' => 'installed']);

        $detail1 = InstallationDetail::create([
            'meter_id' => $meter1->id,
            'worker_id' => $worker->id,
            'install_type' => 'nsc',
            'latitude' => 12.345,
            'longitude' => 67.890,
            'ca_number' => 'CA111',
        ]);

        $detail2 = InstallationDetail::create([
            'meter_id' => $meter2->id,
            'worker_id' => $worker->id,
            'install_type' => 'replacement',
            'latitude' => 23.456,
            'longitude' => 78.901,
            'ca_number' => 'CA222',
        ]);

        // Filter by replacement
        $response = $this->actingAs($contractor)->get(route('admin.map.index', ['install_type' => 'replacement']));

        $response->assertStatus(200);
        $response->assertSee($meter2->meter_number);
        $response->assertDontSee($meter1->meter_number);
    }

    /**
     * Test filtering installations on map by date range.
     */
    public function test_contractor_can_filter_map_by_date(): void
    {
        $contractor = User::factory()->create(['role' => 'contractor']);
        $worker = User::factory()->create(['role' => 'worker']);

        $meter1 = Meter::factory()->create(['status' => 'installed']);
        $meter2 = Meter::factory()->create(['status' => 'installed']);

        $detail1 = InstallationDetail::create([
            'meter_id' => $meter1->id,
            'worker_id' => $worker->id,
            'install_type' => 'nsc',
            'latitude' => 12.345,
            'longitude' => 67.890,
            'ca_number' => 'CA111',
        ]);
        $detail1->created_at = now()->subDays(5);
        $detail1->save();

        $detail2 = InstallationDetail::create([
            'meter_id' => $meter2->id,
            'worker_id' => $worker->id,
            'install_type' => 'nsc',
            'latitude' => 23.456,
            'longitude' => 78.901,
            'ca_number' => 'CA222',
        ]);
        $detail2->created_at = now();
        $detail2->save();

        // Filter with date range (from 3 days ago to today)
        $response = $this->actingAs($contractor)->get(route('admin.map.index', [
            'date_from' => now()->subDays(3)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d')
        ]));

        $response->assertStatus(200);
        $response->assertSee($meter2->meter_number);
        $response->assertDontSee($meter1->meter_number);
    }
}
