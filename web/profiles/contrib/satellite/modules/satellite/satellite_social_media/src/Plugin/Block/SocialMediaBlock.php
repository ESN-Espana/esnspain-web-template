<?php

namespace Drupal\satellite_social_media\Plugin\Block;

use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\esn_accounts_api\Entity\Organisation;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "satellite_social_media",
 *   admin_label = @Translation("Social Media"),
 *   category = @Translation("satellite_social_media")
 * )
 */
class SocialMediaBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The organisation entity.
   *
   * @var \Drupal\esn_accounts_api\Entity\Organisation|null
   */
  protected ?Organisation $entity;

  /**
   * The entity type manager interface.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $module_config, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    if ($module_config->get('selected_organisation_code')) {
      $this->entity = current($this->entityTypeManager->getStorage('esn_organisation')
        ->loadByProperties(['code' => $module_config->get('selected_organisation_code')]));
    }
    else {
      $this->entity = NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')->get('satellite_api_accounts.settings'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $media = [];

    if ($this->entity) {
      if ($link = $this->entity->getUrlFacebook()) {
        $name = 'facebook';
        $url_options = [
          'attributes' => [
            'title' => $name,
            'class' => ['social-' . $name],
            'target' => '_blank',
          ],
        ];
        $url = Url::fromUri($link, $url_options);
        $media[] = [
          '#type' => 'link',
          '#title' => $name,
          '#url' => $url,
        ];
      }

      if ($link = $this->entity->getUrlInstagram()) {
        $name = 'instagram';
        $url_options = [
          'attributes' => [
            'title' => $name,
            'class' => ['social-' . $name],
            'target' => '_blank',
          ],
        ];
        $url = Url::fromUri($link, $url_options);
        $media[] = [
          '#type' => 'link',
          '#title' => $name,
          '#url' => $url,
        ];
      }

      if ($link = $this->entity->getUrlTwitter()) {
        $name = 'twitter';
        $url_options = [
          'attributes' => [
            'title' => $name,
            'class' => ['social-' . $name],
            'target' => '_blank',
          ],
        ];
        $url = Url::fromUri($link, $url_options);
        $media[] = [
          '#type' => 'link',
          '#title' => $name,
          '#url' => $url,
        ];
      }

      if ($media) {
        $block['content'] = [
          '#items' => $media,
          '#theme' => 'item_list',
          '#prefix' => '<div id="socialmedia">',
          '#suffix' => '</div>',
          '#attached' => [
            'library' => ['satellite_social_media/social-media-logo'],
          ],
        ];
        return $block;
      }
    }
    return [];
  }

}
