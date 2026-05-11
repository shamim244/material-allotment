<?php

namespace Tests\Feature;

use App\Models\Meter;
use App\Models\MeterGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'contractor']);
    }

    // ---- Box Import Preview ----

    public function test_box_preview_returns_meter_list()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('stock.import.box.preview'), [
                'start_meter_number' => 'C1350151',
            ]);

        $response->assertOk();
        $response->assertJsonCount(5, 'meters');
        $response->assertJsonPath('meters.0.number', 'C1350151');
        $response->assertJsonPath('meters.4.number', 'C1350155');
        $response->assertJsonPath('meters.0.exists', false);
    }

    public function test_box_preview_detects_existing_meters()
    {
        // Pre-import some meters
        Meter::create(['meter_number' => 'C1350151', 'status' => 'stock']);
        Meter::create(['meter_number' => 'C1350153', 'status' => 'stock']);

        $response = $this->actingAs($this->user)
            ->postJson(route('stock.import.box.preview'), [
                'start_meter_number' => 'C1350151',
            ]);

        $response->assertOk();
        $response->assertJsonPath('meters.0.exists', true);  // C1350151 exists
        $response->assertJsonPath('meters.1.exists', false); // C1350152 new
        $response->assertJsonPath('meters.2.exists', true);  // C1350153 exists
        $response->assertJsonPath('meters.3.exists', false); // C1350154 new
        $response->assertJsonPath('meters.4.exists', false); // C1350155 new
        $response->assertJsonPath('all_exist', false);
    }

    public function test_box_preview_rejects_invalid_start()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('stock.import.box.preview'), [
                'start_meter_number' => 'C1350152', // Invalid: ends in 2
            ]);

        $response->assertStatus(422);
    }

    // ---- Box Import Store ----

    public function test_box_import_partial_box()
    {
        // Pre-import 2 meters
        Meter::create(['meter_number' => 'C1350151', 'status' => 'stock']);
        Meter::create(['meter_number' => 'C1350153', 'status' => 'stock']);

        $response = $this->actingAs($this->user)
            ->post(route('stock.import.box'), [
                'box_start' => 'C1350151',
                'selected_meters' => ['C1350152', 'C1350154', 'C1350155'],
            ]);

        $response->assertSessionHasNoErrors();
        // Should now have 5 total meters (2 pre-existing + 3 new)
        $this->assertCount(5, Meter::all());
        $this->assertDatabaseHas('meters', ['meter_number' => 'C1350152']);
        $this->assertDatabaseHas('meters', ['meter_number' => 'C1350154']);
        $this->assertDatabaseHas('meters', ['meter_number' => 'C1350155']);
        $this->assertDatabaseHas('meter_groups', ['type' => 'box']);
    }

    public function test_box_import_full_new_box()
    {
        $response = $this->actingAs($this->user)
            ->post(route('stock.import.box'), [
                'box_start' => 'C1350156',
                'selected_meters' => ['C1350156', 'C1350157', 'C1350158', 'C1350159', 'C1350160'],
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertCount(5, Meter::all());
        $this->assertDatabaseHas('meters', ['meter_number' => 'C1350156']);
        $this->assertDatabaseHas('meters', ['meter_number' => 'C1350160']);
    }

    // ---- Normal Import ----

    public function test_normal_import_saves_individual_meters()
    {
        $response = $this->actingAs($this->user)
            ->post(route('stock.import.normal'), [
                'meter_numbers' => 'C1350140, C1350143, C170195',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertCount(3, Meter::all());
        $this->assertDatabaseHas('meters', ['meter_number' => 'C1350140']);
        $this->assertDatabaseHas('meters', ['meter_number' => 'C1350143']);
        $this->assertDatabaseHas('meters', ['meter_number' => 'C170195']);
    }

    // ---- Bulk/Large Import ----

    public function test_large_import_aligned_grouping()
    {
        // Range 151-160 (valid starts): 151-155 = group, 156-160 = group
        $response = $this->actingAs($this->user)
            ->post(route('stock.import.large'), [
                'start_meter' => 'C1350151',
                'end_meter' => 'C1350160',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertCount(10, Meter::all());
        $this->assertCount(2, MeterGroup::where('type', 'large')->get());

        // ALL meters should be in a group
        $this->assertEquals(0, Meter::whereNull('group_id')->count());
    }

    public function test_large_import_misaligned_start()
    {
        // C1350150 → last digit 0 → belongs to box C1350146-C1350150
        // C1350151-155 → box group, C1350156-160 → box group
        // ALL meters in groups — 3 groups total
        $response = $this->actingAs($this->user)
            ->post(route('stock.import.large'), [
                'start_meter' => 'C1350150',
                'end_meter' => 'C1350160',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertCount(11, Meter::all());

        // C1350150 should be IN a group (box C1350146-C1350150)
        $meter150 = Meter::where('meter_number', 'C1350150')->first();
        $this->assertNotNull($meter150->group_id);

        // 3 groups total: (150), (151-155), (156-160)
        $this->assertCount(3, MeterGroup::where('type', 'large')->get());
        // No standalone meters
        $this->assertEquals(0, Meter::whereNull('group_id')->count());
    }

    public function test_large_import_misaligned_end()
    {
        // C1350151-155 = group, C1350156-160 = group, C1350161-162 in box C1350161-165 (partial)
        // ALL meters in groups — 3 groups
        $response = $this->actingAs($this->user)
            ->post(route('stock.import.large'), [
                'start_meter' => 'C1350151',
                'end_meter' => 'C1350162',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertCount(12, Meter::all());
        // 3 groups: (151-155), (156-160), (161-162 partial box)
        $this->assertCount(3, MeterGroup::where('type', 'large')->get());

        // No standalone meters
        $this->assertEquals(0, Meter::whereNull('group_id')->count());

        // C1350161, C1350162 should be in a group
        $this->assertNotNull(Meter::where('meter_number', 'C1350161')->first()->group_id);
        $this->assertNotNull(Meter::where('meter_number', 'C1350162')->first()->group_id);
    }

    public function test_large_import_validates_range()
    {
        $response = $this->actingAs($this->user)
            ->post(route('stock.import.large'), [
                'start_meter' => 'C1350160',
                'end_meter' => 'C1350150', // End before start
            ]);

        $response->assertSessionHasErrors('start_meter');
    }
}
