<?php

namespace Drupal\status_interface\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'bg_wall_widget' widget.
 *
 * @FieldWidget(
 *   id = "bg_wall_widget",
 *   label = @Translation("Bg Wall widget"),
 *   field_types = {
 *     "bg_wall_field"
 *   }
 * )
 */
class BgWallWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'fieldset_state' => 'closed',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['fieldset_state'] = [
      '#type' => 'select',
      '#title' => t('Fieldset default state'),
      '#options' => [
        'open' => t('Open'),
        'closed' => t('Closed')
      ],
      '#default_value' => $this->getSetting('fieldset_state'),
      '#description' => t('The default state of the fieldset which contains the two plate fields: open or closed')
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = t('Fieldset state: @state', ['@state' => $this->getSetting('fieldset_state')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['details'] = [
      '#type' => 'details',
      '#title' => $element['#title'],
      '#open' => $this->getSetting('fieldset_state') == 'open' ? TRUE : FALSE,
      '#description' => $element['#description'],
    ] + $element;

    $element['details']['position'] = [
      '#title' => t('Bg position'),
      '#default_value' => isset($items[$delta]->bg_position) ? $items[$delta]->bg_position : 'center center',
      '#type' => 'textfield',
      '#required' => FALSE,
    ];

    $element['details']['filter'] = [
      '#type' => 'radios',
      '#title' => t('Active filter'),
      '#options' => [0 => t('Desactive'), 1 => t('Active')],
      '#default_value' => isset($items[$delta]->filter) ? $items[$delta]->filter : 0,
      '#required' => FALSE,
    ];

    $element['details']['filter_opacity'] = [
      '#type' => 'select',
      '#title' => t('Filter Opacity'),
      '#options' => [
        1 => t('.1'),
        2 => t('.2'),
        3 => t('.3'),
        4 => t('.4'),
        5 => t('.5'),
        6 => t('.6'),
        7 => t('.7'),
        8 => t('.8'),
        9 => t('.9'),
      ],
      '#required' => FALSE,
      '#empty_option' => t('-select-'),
      '#default_value' => isset($items[$delta]->filter_opacity) ? $items[$delta]->filter_opacity : 6,
    ];

    $element['details']['font_size'] = [
      '#type' => 'select',
      '#title' => t('Font size'),
      '#options' => [
        15 => t('15'),
        18 => t('18'),
        22 => t('22'),
        26 => t('26'),
        30 => t('30'),
        34 => t('34'),
        40 => t('40'),
        50 => t('50'),
        60 => t('60'),
        70 => t('70'),
        80 => t('80'),
        100 => t('100'),
        120 => t('120'),
        140 => t('140'),
      ],
      '#required' => FALSE,
      '#empty_option' => t('-select-'),
      '#default_value' => isset($items[$delta]->font_size) ? $items[$delta]->font_size : 22,
    ];

    $element['details']['bg_color'] = [
      '#title' => t('Bg color'),
      '#default_value' => isset($items[$delta]->bg_color) ? $items[$delta]->bg_color : '255,255,255',
      '#type' => 'textfield',
      '#required' => FALSE,
    ];

    $element['details']['color'] = [
      '#title' => t('Color'),
      '#default_value' => isset($items[$delta]->color) ? $items[$delta]->color : '0,0,0',
      '#type' => 'textfield',
      '#required' => FALSE,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$value) {
      $value['position'] = $value['details']['position'];
      $value['filter'] = $value['details']['filter'];
      $value['filter_opacity'] = $value['details']['filter_opacity'];
      $value['font_size'] = $value['details']['font_size'];
      $value['bg_color'] = $value['details']['bg_color'];
      $value['color'] = $value['details']['color'];
      unset($value['details']);
    }

    return $values;
  }
}