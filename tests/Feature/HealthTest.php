<?php

declare(strict_types=1);

use function Pest\Laravel\get;

it('returns health status payload', function (): void {
    get('/.well-known/petora-health')
        ->assertStatus(200)
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('checks.app', true);
});
