<?php

namespace webdna\warmie\services;

use Craft;
use craft\elements\Entry;
use craft\elements\Category;
use craft\commerce\elements\Product;
use craft\helpers\Console;
use craft\helpers\ElementHelper;
use craft\helpers\UrlHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use yii\base\Component;

/**
 * Warm service
 */
class Warm extends Component
{
    public function getUrls()
    {
        $urls = [];
        
        foreach (Entry::find()->collect() as $element) {
            if ($element->url && !ElementHelper::isDraftOrRevision($element)) {
                $urls[] = $element->url;
            }
        }
        
        foreach (Category::find()->collect() as $element) {
            if ($element->url && !ElementHelper::isDraftOrRevision($element)) {
                $urls[] = $element->url;
            }
        }
        
        if (Craft::$app->getPlugins()->isPluginEnabled('commerce')) {
            foreach (Product::find()->collect() as $element) {
                if ($element->url && !ElementHelper::isDraftOrRevision($element)) {
                    $urls[] = $element->url;
                }
            }
        }
        
        return $urls;
    }
    
    public function warmUrls(array $urls)
    {
        $client = new Client([
            'base_uri' => UrlHelper::baseSiteUrl(),
        ]);
        
        $request = function($urls) use ($client) {
            foreach ($urls as $url) {
                yield new Request('GET', $url, ['http_errors' => false]);
            }
        };
        
        $pool = new Pool($client, $request($urls), [
            'concurrency' => 5,
            'fulfilled' => function (Response $response, $index) use ($urls) {
                $code = $response->getStatusCode();
                $output = "%y$urls[$index] : ".($code == 200 ? "%g" : "%r")." $code%n";
                Console::output(Console::renderColoredString($output));
            },
            'rejected' => function ($exception, $index) use ($urls) {
                if ($exception->getResponse()) {
                    $code = $exception->getResponse()->getStatusCode();
                } else {
                    $code = 500;
                }
                $output = "%y$urls[$index] : %r$code%n";
                Console::output(Console::renderColoredString($output));
            },
        ]);
        
        $promise = $pool->promise();
        $promise->wait();
    }
}
