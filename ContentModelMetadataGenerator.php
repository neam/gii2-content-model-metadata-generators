<?php
/**
 * @link http://neamlabs.com/
 * @copyright Copyright (c) 2015 Neam AB
 */

namespace neam\yii_content_model_metadata_generators;

use yii\base\ErrorException;
use yii\helpers\Json;
use Yii;

abstract class ContentModelMetadataGenerator extends \yii\gii\Generator
{

    public $ns;

    /**
     * @var null string
     */
    public $jsonPathAlias = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['jsonPathAlias'], 'required'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'jsonPathAlias' => 'Path alias to content-model-metadata.json',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(
            parent::hints(),
            [
                'jsonPathAlias' => 'This json file should contain metadata about the item types and attributes for the current content model',
            ]
        );
    }

    public function getContentModelMetadata()
    {

        $path = Yii::getAlias($this->jsonPathAlias);
        $json = file_get_contents($path);
        $cmm = Json::decode($json, false);
        if (empty($cmm)) {
            throw new ErrorException("Content model metadata json was found empty");
        }
        return $cmm;

    }

}