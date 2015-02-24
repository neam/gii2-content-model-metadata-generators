<?php

use \neam\yii_content_model_metadata_generators\model_trait\Generator;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\model\Generator $generator
 * @var stdClass $itemType item type metadata
 */

echo "<?php\n";
?>

/**
 * This is the content model metadata trait for item type "<?= $itemType->model_class ?>".
 */
trait <?= $traitName . "\n" ?>
{

<?php foreach ($traits as $trait): ?>
    use <?= "$trait;\n" ?>
<?php endforeach; ?>

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [];

<?php // MIXIN_HAS_MANY_HANDSONTABLE_INPUT ?>

<?php if (isset($mixins[Generator::MIXIN_HAS_MANY_HANDSONTABLE_INPUT])): $attributes = $mixins[Generator::MIXIN_HAS_MANY_HANDSONTABLE_INPUT]; ?>

        $behaviors['HasManyHandsontableInputBehavior'] = array(
            'class' => '\neam\yii_relations_ui_core\behaviors\HasManyHandsontableInputBehavior',
            'attributes' => array(
<?php foreach ($attributes as $attribute): ?>
                <?= "'$attribute',\n" ?>
<?php endforeach; ?>
            ),
        );

<?php endif; ?>

<?php // MIXIN_I18N_ATTRIBUTE_MESSAGES ?>

<?php if (isset($mixins[Generator::MIXIN_I18N_ATTRIBUTE_MESSAGES])): $attributes = $mixins[Generator::MIXIN_I18N_ATTRIBUTE_MESSAGES]; ?>

        $behaviors['i18n-attribute-messages'] = array(
            'class' => 'I18nAttributeMessagesBehavior',
            'translationAttributes' => array(
<?php foreach ($attributes as $attribute): ?>
                <?= "'$attribute',\n" ?>
<?php endforeach; ?>
            ),
            'languageSuffixes' => LanguageHelper::getCodes(),
            'behaviorKey' => 'i18n-attribute-messages',
            'displayedMessageSourceComponent' => 'displayedMessages',
            'editedMessageSourceComponent' => 'editedMessages',
        );

<?php endif; ?>

<?php // MIXIN_I18N_COLUMNS_ATTRIBUTE ?>
<?php // MIXIN_I18N_COLUMNS_RELATION ?>

<?php if (isset($mixins[Generator::MIXIN_I18N_COLUMNS_ATTRIBUTE])): $attributes = $mixins[Generator::MIXIN_I18N_COLUMNS_ATTRIBUTE]; ?>

        // List of model attributes and relations to make multilingual using yii-i18n-columns
        $i18nColumns = DataModel::i18nColumns();

        $i18nColumnsMap = array_intersect_key($i18nColumns['attributes'], $classNames);
        $i18nColumnsMultilingualRelationsMap = array_intersect_key($i18nColumns['relations'], $classNames);

        if (!empty($i18nColumnsMap)) {
            $behaviors['i18n-columns'] = array(
                'class' => 'I18nColumnsBehavior',
                'translationAttributes' => array_values($i18nColumnsMap)[0],
            );
        }

        if (!empty($i18nColumnsMultilingualRelationsMap)) {
            $behaviors['i18n-columns']['multilingualRelations'] = array_values($i18nColumnsMultilingualRelationsMap)[0];
        }

<?php endif; ?>

<?php // MIXIN_OWNABLE ?>

<?php if (isset($mixins[Generator::MIXIN_OWNABLE])): ?>

        $behaviors['owner-behavior'] = array(
            'class' => 'OwnerBehavior',
        );

        // Do not attach owner-behavior when running unit/functional tests - TODO: remove this silly workaround
        if (defined('TESTING')) {
            unset($behaviors['owner-behavior']);
        }

<?php endif; ?>

<?php // MIXIN_PERMALINKABLE_FILES ?>

<?php if (isset($mixins[Generator::MIXIN_PERMALINKABLE_FILES])): $attributes = $mixins[Generator::MIXIN_PERMALINKABLE_FILES]; ?>

        // Routable files
        $permalinkableFiles = array_intersect_key(DataModel::permalinkableFiles(), $classNames);
        if (!empty($permalinkableFiles)) {
            // Just grab the values for the first defined class name in the list.
            $fileRouteAttributeRefs = array_values($permalinkableFiles)[0];
            $behaviors['HasManyHandsontableInputBehavior']['attributes'][] = 'fileRoutes';
            $behaviors['PermalinkableItemBehavior'] = array(
                'class' => '\neam\yii_permalinkable_items_core\behaviors\PermalinkableItemBehavior',
                'relation' => 'fileRoutes',
                'routeClass' => 'FileRoute',
                'fileRouteAttributeRefs' => $fileRouteAttributeRefs,
            );
        }

<?php endif; ?>

<?php // MIXIN_PERMALINKABLE_ITEM ?>

<?php if (isset($mixins[Generator::MIXIN_PERMALINKABLE_ITEM])): ?>

        // Routable items
        $permalinkableItems = array_intersect_key(DataModel::permalinkableItems(), $classNames);
        if (!empty($permalinkableItems)) {
            $behaviors['HasManyHandsontableInputBehavior']['attributes'][] = 'routes';
            $behaviors['PermalinkableItemBehavior'] = array(
                'class' => '\neam\yii_permalinkable_items_core\behaviors\PermalinkableItemBehavior',
                'relation' => 'routes',
                'routeClass' => 'Route',
                'fileRouteAttributeRefs' => array(),
            );
        }

<?php endif; ?>

<?php // MIXIN_RESTRICTED_ACCESS ?>

<?php if (isset($mixins[Generator::MIXIN_RESTRICTED_ACCESS])): ?>

        $behaviors[] = static::MIXIN_RESTRICTED_ACCESS;

        $behaviors['RestrictedAccessBehavior'] = array(
            'class' => '\RestrictedAccessBehavior',
        );

<?php endif; ?>

<?php // MIXIN_RELATIONAL_GRAPH_DB ?>

<?php if (isset($mixins[Generator::MIXIN_RELATIONAL_GRAPH_DB])): ?>

        $behaviors['relational-graph-db'] = array(
            'class' => 'dna.vendor.neam.yii-relational-graph-db.behaviors.RelatedNodesBehavior',
        );

<?php endif; ?>

<?php // MIXIN_RELATED_ITEMS_SIR_TREVOR_UI ?>

<?php if (isset($mixins[Generator::MIXIN_RELATED_ITEMS_SIR_TREVOR_UI])): $attributes = $mixins[Generator::MIXIN_RELATED_ITEMS_SIR_TREVOR_UI]; ?>

        $behaviors['related-items-sir-trevor-ui-behavior'] = array(
            'class' => 'dna.vendor.neam.yii-relational-graph-db.behaviors.RelatedNodesSirTrevorUiBehavior',
            'attributes' => array(
                "related" => array(
                    "ordered" => true,
                    "relation" => "related",
                    "ModelClass" => "*",
                ),
            )
        );

        // Temporary - TODO: Refactor
        if (isset($this->exercises)) {
            $behaviors['related-items-sir-trevor-ui-behavior']['attributes']['exercises'] = array(
                "ordered" => true,
                "relation" => "exercises",
                "ModelClass" => "Exercise",
            );
        }
        if (isset($this->videos)) {
            $behaviors['related-items-sir-trevor-ui-behavior']['attributes']['videos'] = array(
                "ordered" => true,
                "relation" => "videos",
                "ModelClass" => "Video",
            );
        }
        if (isset($this->snapshots)) {
            $behaviors['related-items-sir-trevor-ui-behavior']['attributes']['snapshots'] = array(
                "ordered" => true,
                "relation" => "snapshots",
                "ModelClass" => "Snapshot",
            );
        }
        if (isset($this->dataArticles)) {
            $behaviors['related-items-sir-trevor-ui-behavior']['attributes']['dataArticles'] = array(
                "ordered" => true,
                "relation" => "dataArticles",
                "ModelClass" => "DataArticle",
            );
        }

<?php endif; ?>

<?php // MIXIN_QA_STATE ?>

<?php if (isset($mixins[Generator::MIXIN_QA_STATE])): ?>



<?php endif; ?>

        return array_merge(
            parent::behaviors(), $behaviors
        );

    }

    /**
     * @inheritdoc
     */
    public function relations()
    {
        return array_merge(
            parent::relations(), array(
<?php foreach ($relations as $name => $relation): ?>
                <?= "'$name' => $relation,\n" ?>
<?php endforeach; ?>
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(), array(
<?php foreach ($rules as $rule): ?>
                <?= "$rule,\n" ?>
<?php endforeach; ?>
            )
        );
    }

    /**
     * Define status-dependent fields
     * @return array
     */
    public function statusRequirements()
    {
        return array(
<?php foreach ($statusRequirements as $statusRequirementRef => $statusRequirement): ?>
            '<?= "$statusRequirementRef" ?>' => array(
    <?php foreach ($statusRequirement as $attribute): ?>
                <?= "'$attribute',\n" ?>
    <?php endforeach; ?>
            ),
<?php endforeach; ?>
        );
    }

    /**
     * Define step-dependent fields
     * @return array
     */
    public function flowSteps()
    {
        return array(
<?php foreach ($flowSteps as $flowStepRef => $flowStep): ?>
            '<?= "$flowStepRef" ?>' => array(
    <?php foreach ($flowStep as $attribute): ?>
                <?= "'$attribute',\n" ?>
    <?php endforeach; ?>
            ),
<?php endforeach; ?>
        );
    }

    public function flowStepCaptions()
    {
        return array(
<?php foreach ($flowStepCaptions as $name=>$flowStepCaption): ?>
            <?= "'$name' => " . $generator->generateString($flowStepCaption) . ",\n" ?>
<?php endforeach; ?>
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(), array(
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return array_merge(
            parent::attributeHints(), array(
<?php foreach ($hints as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
            )
        );
    }

}
