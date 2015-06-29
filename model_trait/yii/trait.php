<?php

use \neam\gii2_content_model_metadata_generators\model_trait\Generator;

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

<?php // MIXIN_HAS_MANY_HANDSONTABLE_INPUT 

if (isset($mixins[Generator::MIXIN_HAS_MANY_HANDSONTABLE_INPUT])): $attributes = $mixins[Generator::MIXIN_HAS_MANY_HANDSONTABLE_INPUT]; ?>
        $behaviors['HasManyHandsontableInputBehavior'] = array(
            'class' => '\neam\yii_relations_ui_core\behaviors\HasManyHandsontableInputBehavior',
            'attributes' => array(
<?php foreach ($attributes as $attribute): ?>
                <?= "'$attribute',\n" ?>
<?php endforeach; ?>
            ),
        );

<?php endif; 

// MIXIN_I18N_ATTRIBUTE_MESSAGES 

if (isset($mixins[Generator::MIXIN_I18N_ATTRIBUTE_MESSAGES])): $attributes = $mixins[Generator::MIXIN_I18N_ATTRIBUTE_MESSAGES]; ?>
        // Model attributes and relations to make multilingual using yii-i18n-attribute-messages
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

<?php endif;

// MIXIN_I18N_COLUMNS_ATTRIBUTE
// MIXIN_I18N_COLUMNS_RELATION

if (isset($mixins[Generator::MIXIN_I18N_COLUMNS_ATTRIBUTE]) || isset($mixins[Generator::MIXIN_I18N_COLUMNS_RELATION])):
    $attributes = !empty($mixins[Generator::MIXIN_I18N_COLUMNS_ATTRIBUTE]) ? $mixins[Generator::MIXIN_I18N_COLUMNS_ATTRIBUTE] : [];
    $multilingualRelations = !empty($mixins[Generator::MIXIN_I18N_COLUMNS_RELATION]) ? $mixins[Generator::MIXIN_I18N_COLUMNS_RELATION] : [];
    ?>
        // Model attributes and relations to make multilingual using yii-i18n-columns
        $behaviors['i18n-columns'] = array(
            'class' => 'I18nColumnsBehavior',
            'translationAttributes' => array(
<?php foreach ($attributes as $attribute): ?>
                <?= "'$attribute',\n" ?>
<?php endforeach; ?>
            ),
            'multilingualRelations' => array(
<?php foreach ($multilingualRelations as $relation => $db_column): ?>
                <?= "'$relation' => '$db_column',\n" ?>
<?php endforeach; ?>
            ),
        );

<?php endif;

// MIXIN_OWNABLE 

if (isset($mixins[Generator::MIXIN_OWNABLE])): ?>
        $behaviors['owner-behavior'] = array(
            'class' => 'OwnerBehavior',
        );

        // Do not attach owner-behavior when running unit/functional tests - TODO: remove this silly workaround
        if (defined('TESTING')) {
            unset($behaviors['owner-behavior']);
        }

<?php endif;

// MIXIN_PERMALINKABLE_FILES 

if (isset($mixins[Generator::MIXIN_PERMALINKABLE_FILES])): $attributes = $mixins[Generator::MIXIN_PERMALINKABLE_FILES]; ?>
        // Permalinkable/routable files
        $behaviors['permalinkable-files'] = array(
            'class' => '\neam\yii_permalinkable_items_core\behaviors\PermalinkableItemBehavior',
            'relation' => 'fileRoutes',
            'routeClass' => 'FileRoute',
            'fileRouteAttributeRefs' => array(
<?php foreach ($attributes as $attribute): ?>
                <?= "'$attribute',\n" ?>
<?php endforeach; ?>
            ),
        );

<?php endif;

// MIXIN_PERMALINKABLE_ITEM 

if (isset($mixins[Generator::MIXIN_PERMALINKABLE_ITEM])): ?>
        // Permalinkable/routable items
        $behaviors['permalinkable-item'] = array(
            'class' => '\neam\yii_permalinkable_items_core\behaviors\PermalinkableItemBehavior',
            'relation' => 'routes',
            'routeClass' => 'Route',
            'fileRouteAttributeRefs' => array(),
        );

<?php endif;

// MIXIN_RESTRICTED_ACCESS 

if (isset($mixins[Generator::MIXIN_RESTRICTED_ACCESS])): ?>
        $behaviors['RestrictedAccessBehavior'] = array(
            'class' => '\RestrictedAccessBehavior',
        );

<?php endif;

// MIXIN_RELATIONAL_GRAPH_DB 

if (isset($mixins[Generator::MIXIN_RELATIONAL_GRAPH_DB])): ?>
        $behaviors['relational-graph-db'] = array(
            'class' => 'dna.vendor.neam.yii-relational-graph-db.behaviors.RelatedNodesBehavior',
        );

<?php endif;

// MIXIN_RELATED_ITEMS_SIR_TREVOR_UI 

if (isset($mixins[Generator::MIXIN_RELATED_ITEMS_SIR_TREVOR_UI])): $attributes = $mixins[Generator::MIXIN_RELATED_ITEMS_SIR_TREVOR_UI]; ?>
        $behaviors['related-items-sir-trevor-ui-behavior'] = array(
            'class' => 'dna.vendor.neam.yii-relational-graph-db.behaviors.RelatedNodesSirTrevorUiBehavior',
            'attributes' => array(),
        );

<?php foreach ($attributes as $relationName => $constraint): ?>
        $behaviors['related-items-sir-trevor-ui-behavior']['attributes']['<?= "$relationName" ?>'] = array(
            "ordered" => true,
            "relation" => "<?= "$relationName" ?>",
            "ModelClass" => "<?= "$constraint" ?>",
        );

<?php endforeach; ?>
<?php endif;

// MIXIN_QA_STATE 

if (isset($mixins[Generator::MIXIN_QA_STATE])): ?>
        $behaviors['qa-state'] = array(
            'class' => 'QaStateBehavior',
            'scenarios' => array_merge(
                MetaData::qaStateCoreScenarios(),
                LanguageHelper::qaStateTranslateScenarios()
            ),
        );

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
            parent::relations(),
<?php foreach ($relations as $relation): ?>
            <?= "$relation,\n" ?>
<?php endforeach; ?>
            array()
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
<?php foreach ($rules as $rule): ?>
            <?= "$rule,\n" ?>
<?php endforeach; ?>
            array()
        );
    }

<?php if ($itemType->attributes): ?>
    /**
     * Define item type attributes
     * @return array
     */
    public function itemTypeAttributes()
    {
        return array(
<?php foreach ($itemType->attributes as $attribute): ?>
            '<?= "$attribute->ref" ?>' => array(
                'ref' => <?= "'{$attribute->ref}',\n" ?>
                'type' => <?= "'{$attribute->attributeType->ref}',\n" ?>
            ),
<?php endforeach; ?>
        );
    }
<?php endif; ?>

<?php if ($itemType->is_preparable): ?>
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
<?php endif; ?>

<?php if ($itemType->is_workflow_item): ?>
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
<?php endif; ?>

<?php if ($itemType->is_workflow_item): ?>
    public function flowStepCaptions()
    {
        return array(
<?php foreach ($flowStepCaptions as $name=>$flowStepCaption): ?>
            <?= "'$name' => " . $generator->generateString($flowStepCaption) . ",\n" ?>
<?php endforeach; ?>
        );
    }
<?php endif; ?>

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
