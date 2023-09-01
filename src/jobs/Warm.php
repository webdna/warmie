<?php

namespace webdna\warmie\jobs;

use webdna\warmie\Warmie;

use Craft;
use craft\queue\BaseJob;
use yii\queue\RetryableJobInterface;

/**
 * Warm queue job
 */
class Warm extends BaseJob implements RetryableJobInterface
{
    
    public function getTtr(): int
    {
        return 300;
    }
    
    /**
     * @inheritdoc
     */
    public function canRetry($attempt, $error): bool
    {
        return $attempt < 3;
    }
    
    
    
    function execute($queue): void
    {
        Warmie::getInstance()->warm->all();
        
    }

    protected function defaultDescription(): ?string
    {
        return 'Warming site';
    }
}
