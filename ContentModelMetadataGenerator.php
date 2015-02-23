<?php
/**
 * @link http://neamlabs.com/
 * @copyright Copyright (c) 2015 Neam AB
 */

namespace neam\yii_content_model_metadata_generators;

class ContentModelMetadataGenerator extends \yii\gii\generators\model\Generator
{

    /**
     * @var null string
     */
    public $jsonPath = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['jsonPath'], 'safe'],
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
                'jsonPath' => 'Path to content-model-metadata.json',
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
                'jsonPath' => 'This json file should contain metadata about the item types and attributes for the current content model',
            ]
        );
    }



}