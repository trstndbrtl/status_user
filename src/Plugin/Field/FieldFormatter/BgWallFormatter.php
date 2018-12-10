<?php

namespace Drupal\status_user\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'bg_wall_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "bg_wall_formatter",
 *   label = @Translation("Default WBg formatter"),
 *   field_types = {
 *     "bg_wall_field"
 *   }
 * )
 */
class BgWallFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = $this->viewValue($item);
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return array
   */
  protected function viewValue(FieldItemInterface $item) {
    
    $position = $item->get('position')->getValue();
    $filter = $item->get('filter')->getValue();
    $filter_opacity = $item->get('filter_opacity')->getValue();
    $font_size = $item->get('font_size')->getValue();
    $bg_color = $item->get('bg_color')->getValue();
    $color = $item->get('color')->getValue();

    return [
      '#theme' => 'bg_field_theme',
      '#position' => $position,
      '#filter' => $filter,
      '#filter_opacity' => $filter_opacity,
      '#font_size' => $font_size,
      '#bg_color' => $bg_color,
      '#color' => $color,
    ];
  }

}