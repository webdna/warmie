<?php

namespace webdna\warmie;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Utilities;
use webdna\warmie\services\Warm;
use webdna\warmie\utilities\Warmie as WarmieAlias;
use yii\log\Logger;
use craft\log\MonologTarget;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LogLevel;
use yii\base\Event;

/**
 * warmie plugin
 *
 * @method static Warmie getInstance()
 * @author webdna <info@webdna.co.uk>
 * @copyright webdna
 * @license https://craftcms.github.io/license/ Craft License
 * @property-read Warm $warm
 */
class Warmie extends Plugin
{
    public string $schemaVersion = '1.0.0';

    public static function config(): array
    {
        return [
            'components' => ['warm' => Warm::class],
        ];
    }

    public function init()
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
            // ...
        });
        
        Craft::getLogger()->dispatcher->targets[] = new MonologTarget([
            'name' => 'warmie',
            'categories' => ['warmie'],
            'logContext' => false,
            'allowLineBreaks' => false,
            'formatter' => new LineFormatter(
                format: "%datetime% %message%\n",
                dateFormat: 'Y-m-d H:i:s',
            ),
        ]);
    }
    
    public static function log(string $message): void
    {
        Craft::getLogger()->log($message, Logger::LEVEL_INFO, 'warmie');
    }
    
    public static function error(string $message): void
    {
        Craft::getLogger()->log($message, Logger::LEVEL_ERROR, 'warmie');
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        Event::on(
            Utilities::class, 
            Utilities::EVENT_REGISTER_UTILITIES, 
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = WarmieAlias::class;
            }
        );
    }
}
