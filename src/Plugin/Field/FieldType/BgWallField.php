<?php

namespace Drupal\status_interface\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'bg_wall_field' field type.
 *
 * @FieldType(
 *   id = "bg_wall_field",
 *   label = @Translation("WBackground"),
 *   description = @Translation("Field for background status"),
 *   default_widget = "bg_wall_widget",
 *   default_formatter = "bg_wall_formatter"
 * )
 */
class BgWallField extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'position' => [
          'type' => 'varchar',
          'length' => 15,
        ],
        'filter' => [
          'type' => 'int',
          'length' => 2,
        ],
        'filter_opacity' => [
          'type' => 'int',
          'length' => 2,
        ],
        'font_size' => [
          'type' => 'int',
          'length' => 3,
        ],
        'bg_color' => [
          'type' => 'varchar',
          'length' => 20,
        ],
        'color' => [
          'type' => 'varchar',
          'length' => 20,
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
        'position' => 'center center',
        'filter' => 0,
        'filter_opacity' => 0,
        'font_size' => 22,
        'bg_color' => "transparent",
        'color' => "0,0,0",
      ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties['position'] = DataDefinition::create('string')
      ->setLabel(t('Bg position'));

    $properties['filter'] = DataDefinition::create('integer')
      ->setLabel(t('Filter'));
    
    $properties['filter_opacity'] = DataDefinition::create('integer')
      ->setLabel(t('Filter opacity'));
    
    $properties['font_size'] = DataDefinition::create('integer')
      ->setLabel(t('Font size'));

    $properties['bg_color'] = DataDefinition::create('string')
      ->setLabel(t('Background color'));
    
    $properties['color'] = DataDefinition::create('string')
      ->setLabel(t('Color'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $isEmpty = 
      empty($this->get('position')->getValue()) &&
      empty($this->get('filter')->getValue()) &&
      empty($this->get('filter_opacity')->getValue()) &&
      empty($this->get('font_size')->getValue()) &&
      empty($this->get('bg_color')->getValue()) &&
      empty($this->get('color')->getValue());
    return $isEmpty;
  }
}