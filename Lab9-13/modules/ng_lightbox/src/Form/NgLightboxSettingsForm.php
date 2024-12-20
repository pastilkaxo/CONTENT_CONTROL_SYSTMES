<?php

namespace Drupal\ng_lightbox\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ng_lightbox\NgLightbox;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The lightbox settings form.
 */
class NgLightboxSettingsForm extends ConfigFormBase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * An array of Lightbox renderers.
   *
   * @var array
   */
  protected $renderers = [];

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param array $lightbox_renderers
   *   The lightbox renderers.
   */
  public function __construct(ConfigFactoryInterface $config_factory, array $lightbox_renderers) {
    parent::__construct($config_factory);
    $this->renderers = $lightbox_renderers;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->getParameter('ng_lightbox_renderers')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->config = $this->configFactory()->getEditable('ng_lightbox.settings');

    $form['container']['patterns'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Paths'),
      '#default_value' => $this->config->get('patterns'),
      '#description' => $this->t('New line separated paths that must start with a leading slash. Wildcard character is *. E.g. /comment/*/reply.'),
    ];
    $form['container']['default_width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default Width'),
      '#default_value' => $this->config->get('default_width'),
      '#description' => $this->t('The default width for modals opened with NG Lightbox.'),
    ];
    $form['container']['lightbox_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Lightbox Class'),
      '#default_value' => $this->config->get('lightbox_class'),
      '#description' => $this->t('The css custom class for modals opened with NG Lightbox.'),
    ];
    $form['container']['skip_admin_paths'] = [
      '#title' => $this->t('Skip all admin paths'),
      '#type' => 'checkbox',
      '#default_value' => $this->config->get('skip_admin_paths'),
      '#description' => $this->t('This will exclude all admin paths from the lightbox. If you want some paths, see hook_ng_lightbox_ajax_path_alter().'),
    ];
    $form['container']['renderer'] = [
      '#title' => $this->t('Renderer'),
      '#type' => 'select',
      '#default_value' => $this->config->get('renderer') ?: NgLightbox::DEFAULT_MODAL,
      '#description' => $this->t('Select which renderer should be used for the lightbox.'),
      '#options' => $this->renderers,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config
      ->set('patterns', $values['patterns'])
      ->set('default_width', $values['default_width'])
      ->set('lightbox_class', $values['lightbox_class'])
      ->set('skip_admin_paths', $values['skip_admin_paths'])
      ->set('renderer', $values['renderer'])
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ng_lightbox_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [];
  }

}
