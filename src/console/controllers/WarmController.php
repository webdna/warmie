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
    public $defaultAction = 'index';

    public function options($actionID): array
    {
        $options = parent::options($actionID);
        switch ($actionID) {
            case 'index':
                // $options[] = '...';
                break;
        }
        return $options;
    }

    /**
     * warmie/warm command
     */
    public function actionIndex(): int
    {
        $urls = Warmie::getInstance()->warm->getUrls();
            
        Warmie::getInstance()->warm->warmUrls($urls);
        
        return ExitCode::OK;
    }
}
