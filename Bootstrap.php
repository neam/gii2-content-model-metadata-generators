<?php
/**
 * @link http://neamlabs.com/
 * @copyright Copyright (c) 2015 Neam AB
 */

namespace neam\yii_content_model_metadata_generators;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 * @package neam\yii_content_model_metadata_generators
 * @author Fredrik Wollsén <fredrik@neam.se>
 */
class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if ($app->hasModule('gii')) {

            if (!isset($app->getModule('gii')->generators['content-model-metadata-model-trait'])) {
                $app->getModule('gii')->generators['content-model-metadata-model-trait'] = 'neam\yii_content_model_metadata_generators\model_trait\Generator';
            }
            if (!isset($app->getModule('gii')->generators['content-model-metadata-helper'])) {
                $app->getModule('gii')->generators['content-model-metadata-helper'] = 'neam\yii_content_model_metadata_generators\helper\Generator';
            }
        }
    }
}