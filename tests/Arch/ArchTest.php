<?php

declare(strict_types=1);

arch('app uses strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('modules use strict types when present')
    ->expect('Modules')
    ->toUseStrictTypes();

arch('no debugging functions are left in source')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->not->toBeUsed();
