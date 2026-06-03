<?php

namespace Drupal\Tests\satellite_demo\Functional;

use Drupal\Core\Url;
use Drupal\FunctionalTests\Installer\InstallerTestBase;

/**
 * Tests generation of ice cream.
 *
 * @group satellite
 */
class DemoContentTest extends InstallerTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'satellite';

  /**
   * {@inheritdoc}
   *
   * The profile is set automatically, as this is a distribution.
   */
  protected function setUpProfile() {}

  /**
   * {@inheritdoc}
   *
   * Satellite can only be installed in English.
   */
  protected function setUpLanguage() {}

  /**
   * Override install parameters.
   */
  public function installParameters() {
    $params = parent::installParameters();

    $params['forms']['satellite_module_configure_form'] = [
      'optional_modules' => [
        'automated_cron' => FALSE,
        'dynamic_page_cache' => TRUE,
        'inline_form_errors' => TRUE,
        'page_cache' => TRUE,
        'satellite_development' => FALSE,
        'satellite_news' => FALSE,
      ],
      'satellite_demo' => TRUE,
    ];

    return $params;
  }

  /**
   * {@inheritdoc}
   */
  protected function setUpSite() {

    $edit = $this->translatePostValues($this->parameters['forms']['satellite_module_configure_form']);
    $this->submitForm($edit, $this->translations['Save and continue']);

    parent::setUpSite();
  }

  /**
   * Test that menu migration is successful.
   */
  public function testDemoMenu() {
    $this->runMigration();
    $this->drupalGet('<front>');
    $this->assertSession()->statusCodeEquals(200);
    // cSpell:disable-next-line.
    $this->assertSession()->pageTextContains('About Us');
    $this->assertSession()->pageTextContains('ESNcard');
    $this->assertSession()->pageTextContains('Join us');
    $this->assertSession()->pageTextContains('Volunteering');
  }

  /**
   * Test block spotlight migration is successful.
   */
  public function testSpotlightBlock() {
    $this->runMigration();
    $this->drupalGet('<front>');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains($this->config('system.site')->get('name'));
    $this->assertSession()->linkExists('Read more about us');
  }

  /**
   * Test info block migration is successful.
   */
  public function testInfoBlock() {
    $this->runMigration();
    $this->drupalGet('<front>');
    $this->assertSession()->statusCodeEquals(200);

    $this->assertSession()->pageTextContains('For local friends');
    $this->assertSession()->pageTextContains('For incoming students');
  }

  /**
   * Common code that tests some module settings and executes the migrations.
   */
  private function runMigration() {
    $this->assertEquals(0, \Drupal::state()->get('satellite_demo_migrations_have_run'));
    $url = Url::fromRoute('satellite_demo.execute_migrations');
    $this->drupalGet($url);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Migration successful!');
    $this->assertEquals(1, \Drupal::state()->get('satellite_demo_migrations_have_run'));
  }

}
