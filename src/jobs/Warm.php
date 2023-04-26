<?php

namespace webdna\warmie\jobs;

use Craft;
use craft\queue\BaseJob;

/**
 * Warm queue job
 */
class Warm extends BaseJob
{
    function execute($queue): void
    {
        // ...
    }

    protected function defaultDescription(): ?string
    {
        return null;
    }
}
