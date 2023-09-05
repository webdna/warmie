<?php

namespace webdna\warmie\services;

use webdna\warmie\Warmie;

use Craft;
use craft\elements\Entry;
use craft\elements\Category;
use craft\commerce\elements\Product;
use craft\helpers\ArrayHelper;
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
    public function all()
    {
        $urls = [];
        
        $urls = ArrayHelper::Merge(
            $urls, 
            $this->entryUrls(), 
            $this->categoryUrls(), 
            $this->productUrls()
        );
        
        $this->warm($urls);
    }
    
    public function entries($section=null)
    {
        $this->warm($this->entryUrls($section));
    }
    
    public function categories($group=null)
    {
        $this->warm($this->categoryUrls($group));
    }
    
    public function products($type=null)
    {
        $this->warm($this->productUrls($type));
    }
    
    
    
    
    private function warm(array $urls)
    {
        $client = new Client([
            'base_uri' => UrlHelper::baseSiteUrl(),
        ]);
        
        $total = count($urls);
        $count = 1;
        
        $request = function($urls) use ($client) {
            foreach ($urls as $url) {
                yield new Request('GET', $url, ['http_errors' => false]);
            }
        };
        
        $pool = new Pool($client, $request($urls), [
            'concurrency' => App::env('CONCURRENT_REQUESTS', 3),
            'fulfilled' => function (Response $response, $index) use ($urls, $total, &$count) {
                $code = $response->getStatusCode();
                $output = "%y[$count/$total] $urls[$index] : ".($code == 200 ? "%g" : "%r")." $code%n";
                Console::output(Console::renderColoredString($output));
                if ($code == 200) {
                    Warmie::log("$urls[$index] : $code");
                } else {
                    Warmie::error("$urls[$index] : $code");
                }
                $count++;
            },
            'rejected' => function ($exception, $index) use ($urls, $total, &$count) {
                if ($exception->getResponse()) {
                    $code = $exception->getResponse()->getStatusCode();
                } else {
                    $code = 500;
                }
                $output = "%y[$count/$total] $urls[$index] : %r$code%n";
                Console::output(Console::renderColoredString($output));
                Warmie::error("$urls[$index] : $code");
                $count++;
            },
        ]);
        
        $promise = $pool->promise();
        $promise->wait();
    }
    
    private function entryUrls($section=null)
    {
        $urls = [];
        
        foreach (Entry::find()->section($section)->collect() as $element) {
            if ($element->url && !ElementHelper::isDraftOrRevision($element)) {
                $urls[] = $element->url;
            }
        }
        
        return $urls;
    }
    
    private function categoryUrls($group=null)
    {
        $urls = [];
        
        foreach (Category::find()->group($group)->collect() as $element) {
            if ($element->url && !ElementHelper::isDraftOrRevision($element)) {
                $urls[] = $element->url;
            }
        }
        
        return $urls;
    }
    
    private function productUrls($type=null)
    {
        $urls = [];
        
        if (Craft::$app->getPlugins()->isPluginEnabled('commerce')) {
            foreach (Product::find()->type($type)->collect() as $element) {
                if ($element->url && !ElementHelper::isDraftOrRevision($element)) {
                    $urls[] = $element->url;
                }
            }
        }
        
        return $urls;
    }
}
