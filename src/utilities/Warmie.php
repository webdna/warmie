<?php

namespace webdna\warmie\utilities;

use Craft;
use craft\base\Utility;

/**
 * Warmie utility
 */
class Warmie extends Utility
{
    public static function displayName(): string
    {
        return Craft::t('warmie', 'Warmie');
    }

    static function id(): string
    {
        return 'warmie';
    }

    public static function iconPath(): ?string
    {
        return null;
        
        $iconPath = Craft::getAlias('@putyourlightson/blitz/icon-mask.svg');
        
        if (!is_string($iconPath)) {
            return null;
        }
        
        return $iconPath;
    }

    static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('warmie/_utility', [
            
        ]);
    }
}
