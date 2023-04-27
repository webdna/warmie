<?php

namespace webdna\warmie\console\controllers;

use webdna\warmie\Warmie;

use Craft;
use craft\console\Controller;
use yii\console\ExitCode;

/**
 * Warm controller
 */
class WarmController extends Controller
{
    public ?string $section = null;
    public ?string $group = null;
    public ?string $type = null;
    
    public $defaultAction = 'all';

    public function options($actionID): array
    {
        $options = parent::options($actionID);
        switch ($actionID) {
            case 'entries':
                $options[] = 'section';
                break;
            case 'categories':
                $options[] = 'group';
                break;
            case 'products':
                $options[] = 'type';
                break;
        }
        return $options;
    }

    /**
     * warmie/warm command
     */
    public function actionAll(): int
    {
        Warmie::getInstance()->warm->all();
        
        return ExitCode::OK;
    }
    
    public function actionEntries(): int
    {
        Warmie::getInstance()->warm->entries($this->section);
        
        return ExitCode::OK;
    }
    
    public function actionCategories(): int
    {
        Warmie::getInstance()->warm->categories($this->group);
        
        return ExitCode::OK;
    }
    
    public function actionProducts(): int
    {
        Warmie::getInstance()->warm->products($this->type);
        
        return ExitCode::OK;
    }
}
