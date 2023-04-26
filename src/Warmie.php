<?php

namespace webdna\warmie;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Utilities;
use webdna\warmie\services\Warm;
use webdna\warmie\utilities\Warmie as WarmieAlias;
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
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
        Event::on(Utilities::class, Utilities::EVENT_REGISTER_UTILITY_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = WarmieAlias::class;
        });
    }
}
