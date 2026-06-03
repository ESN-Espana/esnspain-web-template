<?php

namespace Drupal\satellite_user\Plugin\OpenIDConnectClient;

use Drupal\Core\Form\FormStateInterface;
use Drupal\openid_connect\Plugin\OpenIDConnectClient\OpenIDConnectGenericClient;

/**
 * ESN Accounts OpenID Connect client.
 *
 * Used primarily to login with 'ESN Accounts' into this platform.
 *
 * @OpenIDConnectClient(
 *   id = "esn-accounts",
 *   label = @Translation("ESN Accounts OpenID")
 * )
 */
class OpenIDConnectEsnAccountsClient extends OpenIDConnectGenericClient {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'issuer_url' => '',
      'authorization_endpoint' => 'https://accounts.esn.org/oauth/authorize',
      'token_endpoint' => 'https://accounts.esn.org/oauth/token',
      'userinfo_endpoint' => 'https://accounts.esn.org/oauth/v1/userinfo',
      'end_session_endpoint' => 'https://accounts.esn.org/user/logout',
      'scopes' => ['oauth2_access_to_profile_information'],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['disclaimer'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#prefix' => '<br>',
      '#value' => $this->t(
        "Overwrite the client ID and secret through the settings.php file. The path of your settings.php file should be <pre>sites/default/settings.php</pre><br>This is an example of how you can alter them: <br><pre>@example</pre>",
        [
          '@example' => "\$config['openid_connect.client.esn_accounts']['settings']['client_id'] = '<your client ID>';
\$config['openid_connect.client.esn_accounts']['settings']['client_secret'] = '<your client secret>';",
        ]
      ),
      '#attributes' => [
        'class' => 'alert alert-primary',
      ],
      '#weight' => -100,
    ];

    $form['client_id']['#required'] = FALSE;
    $form['client_id']['#disabled'] = TRUE;
    $form['client_secret']['#required'] = FALSE;
    $form['client_secret']['#disabled'] = TRUE;
    return $form;
  }

}
