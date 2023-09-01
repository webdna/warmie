<?php

namespace webdna\warmie\controllers;

use webdna\warmie\Warmie;
use webdna\warmie\jobs\Warm as WarmJob;

use Craft;
use craft\helpers\App;
use craft\helpers\StringHelper;
use craft\web\Controller;
use craft\web\View;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use craft\helpers\Queue;

/**
 * Warm service
 */
class CacheController extends Controller
{
	protected array|bool|int $allowAnonymous = true;
	
	public function actionWarm(): Response
	{
		$job = new WarmJob();
		Queue::push($job);
		
		Craft::$app->getSession()->setNotice('Warming started');
		
		return $this->redirectToPostedUrl();
	}
}
