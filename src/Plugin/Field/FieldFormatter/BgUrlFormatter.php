<?php

namespace Drupal\status_user\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\image\ImageStyleStorageInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Url;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Render\RendererInterface;
use Drupal\media\MediaInterface;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\status_user\SerializedData;


/**
 * Plugin implementation of the 'bg_url_default' formatter.
 *
 * @FieldFormatter(
 *   id = "bg_url_default",
 *   label = @Translation("Bg image url formatter"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */

class BgUrlFormatter extends ImageFormatter {

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The link generator.
   *
   * @var \Drupal\Core\Utility\LinkGeneratorInterface
   */
  protected $linkGenerator;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The image style entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $imageStyleStorage;

  /**
   * Constructs an MediaThumbnailFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings settings.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Utility\LinkGeneratorInterface $link_generator
   *   The link generator service.
   * @param \Drupal\image\ImageStyleStorageInterface $image_style_storage
   *   The image style entity storage handler.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, AccountInterface $current_user, LinkGeneratorInterface $link_generator, ImageStyleStorageInterface $image_style_storage, RendererInterface $renderer) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $current_user, $image_style_storage);
    $this->currentUser = $current_user;
    $this->linkGenerator = $link_generator;
    $this->imageStyleStorage = $image_style_storage;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('current_user'),
      $container->get('link_generator'),
      $container->get('entity_type.manager')->getStorage('image_style'),
      $container->get('renderer')
    );
  }

    /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'image_style' => '',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();

    $image_styles = image_style_options(FALSE);
    // Unset possible 'No defined styles' option.
    unset($image_styles['']);
    // Styles could be lost because of enabled/disabled modules that defines
    // their styles in code.
    $image_style_setting = $this->getSetting('image_style');
    if (isset($image_styles[$image_style_setting])) {
      $summary[] = t('URL for Image style: @style', array('@style' => $image_styles[$image_style_setting]));
    }
    else {
      $summary[] = t('Original image');
    }

    return $summary;
  }


  /**
   * {@inheritdoc}
   *
   * This has to be overridden because FileFormatterBase expects $item to be
   * of type \Drupal\file\Plugin\Field\FieldType\FileItem and calls
   * isDisplayed() which is not in FieldItemInterface.
   */
  protected function needsEntityLoad(EntityReferenceItem $item) {
    return !$item->hasNewEntity();
  }

    /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $image_styles = image_style_options(FALSE);
    $element['image_style'] = array(
      '#title' => t('Image style'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('image_style'),
      '#empty_option' => t('None (original image)'),
      '#options' => $image_styles,
      '#description' => array(
        '#markup' => $this->linkGenerator->generate($this->t('Configure Image Styles'), new Url('entity.image_style.collection')),
        '#access' => $this->currentUser->hasPermission('administer image styles'),
      ),
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    $media_items = $this->getEntitiesToView($items, $langcode);

    if (!empty($media_items)) {
      
      $image_style_setting = $this->getSetting('image_style');
  
      // Collect cache tags to be added for each item in the field.
      $cache_tags = array();
      if (!empty($image_style_setting)) {
        $image_style = $this->imageStyleStorage->load($image_style_setting);
        $cache_tags = $image_style->getCacheTags();
      }
  
      foreach ($media_items as $delta => $media) {
  
        if ($media->hasField('field_media_image') && $media->field_media_image->getValue() && $media->field_media_image->getValue()[0]) {
          $file = $media->field_media_image->referencedEntities()[0];
          $image_uri = $file->getFileUri();
          $url = Url::fromUri(file_create_url($image_uri));
  
          $cache_tags = Cache::mergeTags($cache_tags, $file->getCacheTags());
  
          if ($image_style_setting) {
            $url_link = ImageStyle::load($image_style_setting)->buildUrl($file->getFileUri());
            $element[$delta] = [
              '#markup' => $url_link,
              '#cache' => array(
                'tags' => $cache_tags,
              ),
            ];
          }else{
            $element[$delta] = [
              '#markup' => $url->getUri(),
              '#cache' => array(
                'tags' => $cache_tags,
              ),
            ];
          }
  
        }
      }
    }

    return $element;
  }

   /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    // This formatter is only available for entity types that reference
    // media items.
    return ($field_definition->getFieldStorageDefinition()->getSetting('target_type') == 'media');
  }

}