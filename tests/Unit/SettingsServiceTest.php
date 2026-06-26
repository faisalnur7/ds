<?php

namespace Tests\Unit;

use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_service_reads_typed_values(): void
    {
        $service = app(SettingsService::class);

        $service->put('flag', true, 'bool');
        $service->put('limit', 12, 'int');
        $service->put('meta', ['a' => 1], 'json');

        $this->assertTrue($service->get('flag'));
        $this->assertSame(12, $service->get('limit'));
        $this->assertSame(['a' => 1], $service->get('meta'));
    }
}
