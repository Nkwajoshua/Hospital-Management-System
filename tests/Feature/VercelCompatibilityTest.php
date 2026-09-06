<?php

namespace Tests\Feature;

use JsonException;
use Tests\TestCase;

class VercelCompatibilityTest extends TestCase
{
    /** @throws JsonException */
    public function test_serverless_entrypoint_and_runtime_configuration_are_present(): void
    {
        $this->assertFileExists(base_path('api/index.php'));
        $this->assertFileExists(base_path('vercel.json'));
        $this->assertFileExists(base_path('.env.vercel.example'));

        $config = json_decode(
            file_get_contents(base_path('vercel.json')),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $this->assertSame('vercel-php@0.7.4', $config['functions']['api/index.php']['runtime']);
        $this->assertSame('/api/index.php', $config['routes'][3]['dest']);

        $environment = file_get_contents(base_path('.env.vercel.example'));
        $this->assertStringContainsString('SESSION_DRIVER=cookie', $environment);
        $this->assertStringContainsString('CACHE_STORE=array', $environment);
        $this->assertStringContainsString('VIEW_COMPILED_PATH=/tmp/views', $environment);
        $this->assertStringContainsString('LOG_CHANNEL=stderr', $environment);
    }
}
