<?php
/**
 * @var yii\web\View $this
 * @var yii\gii\generators\model\Generator $generator
 * @var stdClass[] $itemTypes item types metadata
 */

echo "<?php\n";
?>

/**
 * Content model metadata helper class for enumerating item types
 */
class <?= $helperName . "\n" ?>
{

    static public function exists($modelClass)
    {
        return in_array($modelClass, array_keys(static::all()));
    }

    static public function label($modelClass, $n)
    {

        $choiceFormat = static::choiceFormatLabel($modelClass);
        return Yii::t('app', $choiceFormat, $n);

    }

    static public function choiceFormatLabel($modelClass)
    {

        $labels = static::choiceFormatLabels();
        if (!isset($labels[$modelClass])) {
            throw new ItemTypeLabelMissingException();
        }
        return $labels[$modelClass];

    }

    static public function where($attribute)
    {

        return static::byBooleanAttribute()[$attribute];

    }

    /**
     *
     */
    static public function all()
    {
        return [
<?php foreach ($itemTypes as $itemType): ?>
            '<?= $itemType->model_class; ?>' => '<?= $itemType->table; ?>',
<?php endforeach; ?>
        ];
    }

    /**
     *
     */
    static public function byBooleanAttribute()
    {
        return [
<?php foreach ($itemTypesByBooleanAttributes as $attribute=>$matchingItemTypes): ?>
            '<?= $attribute ?>' => array(
    <?php foreach ($matchingItemTypes as $itemType): ?>
            '<?= $itemType->model_class; ?>' => '<?= $itemType->table; ?>',
    <?php endforeach; ?>
            ),
<?php endforeach; ?>
        ];
    }

    /**
     * Frontend UI labels for the models
     * @return array
     */
    static public function choiceFormatLabels()
    {
        return [
<?php foreach ($itemTypes as $itemType): ?>
            '<?= $itemType->model_class; ?>' => '<?= $itemType->title_choice_format; ?>',
<?php endforeach; ?>
        ];
    }

    /**
     * Slugs for the model classes
     * Used when setting semantic routes
     * @return array
     */
    static public function modelSlugs()
    {
        return [
<?php foreach ($itemTypes as $itemType): ?>
            '<?= $itemType->model_class; ?>' => '<?= $itemType->slug; ?>',
<?php endforeach; ?>
        ];
    }

}

class ItemTypeLabelMissingException extends Exception {

}
