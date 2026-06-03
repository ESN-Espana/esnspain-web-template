<?php

namespace Drupal\esn_accounts_api\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Interface for class Organisation.
 */
interface OrganisationInterface extends ContentEntityInterface {

  /**
   * Get the title of the organisation.
   *
   * @return string
   *   The title of the organisation.
   */
  public function getTitle();

  /**
   * Set the title of the organisation.
   *
   * @param string $title
   *   The title to set.
   *
   * @return $this
   *   The object class.
   */
  public function setTitle($title);

  /**
   * Get the status of the organisation.
   *
   * @return string
   *   The status of the organisation.
   */
  public function getStatus();

  /**
   * Set the status of the organisation.
   *
   * @param string $status
   *   The title to set.
   *
   * @return $this
   *   The object class.
   */
  public function setStatus($status);

  /**
   * Get the code of the organisation.
   *
   * @return string
   *   The code of the organisation.
   */
  public function getCode();

  /**
   * Set the code of the organisation.
   *
   * @param string $code
   *   The code to set.
   *
   * @return $this
   *   The object class.
   */
  public function setCode($code);

  /**
   * Get the country_code of the organisation.
   *
   * @return string
   *   The country_code of the organisation.
   */
  public function getCountryCode();

  /**
   * Set the country_code of the organisation.
   *
   * @param string $country_code
   *   The country_code to set.
   *
   * @return $this
   *   The object class.
   */
  public function setCountryCode($country_code);

  /**
   * Get the last updated value of the organisation.
   *
   * @return string
   *   The last updated value of the organisation.
   */
  public function getLastUpdate();

  /**
   * Set the last updated value of the organisation.
   *
   * @param string $updated
   *   The last updated value to set.
   *
   * @return $this
   *   The object class.
   */
  public function setLastUpdate($updated);

  /**
   * Get the country of the organisation.
   *
   * @return string
   *   The country of the organisation to set.
   */
  public function getCountry();

  /**
   * Set the country of the organisation.
   *
   * @param string $country
   *   The country of the organisation to set.
   *
   * @return $this
   *   The object class.
   */
  public function setCountry($country);

  /**
   * Get the address of the organisation.
   *
   * @return string
   *   The address of the organisation.
   */
  public function getAddress();

  /**
   * Set the address of the organisation.
   *
   * @param string $address
   *   The address of the organisation to set.
   *
   * @return $this
   *   The object class.
   */
  public function setAddress($address);

  /**
   * Get the URL of the Facebook profile of the organisation.
   *
   * @return string
   *   The facebook url of the organisation.
   */
  public function getUrlFacebook();

  /**
   * Set the URL of the Facebook profile of the organisation.
   *
   * @param string $facebook_url
   *   The uri to save.
   *
   * @return $this
   *   The object class.
   */
  public function setUrlFacebook($facebook_url);

  /**
   * Get the URL of the Instagram profile of the organisation.
   *
   * @return string
   *   The instagram url of the organisation.
   */
  public function getUrlInstagram();

  /**
   * Set the URL of the instagram profile of the organisation.
   *
   * @param string $instagram_url
   *   The instagram profile website of the organisation to set.
   *
   * @return $this
   *   The object class.
   */
  public function setUrlInstagram($instagram_url);

  /**
   * Get the URL of the Twitter profile of the organisation.
   *
   * @return string
   *   The twitter url of the organisation.
   */
  public function getUrlTwitter();

  /**
   * Set the URL of the twitter profile of the organisation.
   *
   * @param string $twitter_url
   *   The twitter profile website of the organisation to set.
   *
   * @return $this
   *   The object class.
   */
  public function setUrlTwitter($twitter_url);

  /**
   * Get the video of the organisation.
   *
   * @return string
   *   The video website url of the organisation.
   */
  public function getUrlVideo();

  /**
   * Sets the video of the organisation.
   *
   * @param string $video_url
   *   The video website of the organisation to set.
   *
   * @return $this
   *   The object class.
   */
  public function setUrlVideo($video_url);

  /**
   * Get the URL of the organisation's website.
   *
   * @return string
   *   The website url of the organisation.
   */
  public function getUrlWebsite();

  /**
   * Set the URL of the organisation's website.
   *
   * @param string $website_url
   *   The website uri of the organisation to set.
   *
   * @return $this
   *   The object class.
   */
  public function setUrlWebsite($website_url);

  /**
   * Get the section's state.
   *
   * @return string
   *   The state of the organisation.
   */
  public function getState();

  /**
   * Set the section's state.
   *
   * @param string $state
   *   The current state of the organisation to set.
   *
   * @return $this
   *   The object class.
   */
  public function setState($state);

  /**
   * Get the cities of the section.
   *
   * @return string
   *   The cities of the organisation.
   */
  public function getCities();

  /**
   * Set cities to a section.
   *
   * @param string $cities
   *   The cities of the organisation to set.
   *
   * @return $this
   *   The object class.
   */
  public function setCities($cities);

  /**
   * Get the coordinates of a section.
   *
   * @return string
   *   The location (coordinates) of the organisation.
   */
  public function getLocation();

  /**
   * Set the location of an organisation.
   *
   * @param string $location
   *   The location of the organisation to set.
   *
   * @return $this
   *   The object class.
   */
  public function setLocation($location);

  /**
   * Get the university name the section belongs to (section only).
   *
   * @return string
   *   The university of the organisation.
   */
  public function getUniversityName();

  /**
   * Set a university name (section only).
   *
   * @param string $university_name
   *   The university name of the organisation to set.
   *
   * @return $this
   *   The object class.
   */
  public function setUniversityName($university_name);

  /**
   * Get the university's URL of a section (section only).
   *
   * @return string
   *   The university url of the organisation.
   */
  public function getUrlUniversity();

  /**
   * Set a section's university website (section only).
   *
   * @param string $university_url
   *   The university url of the organisation to set.
   *
   * @return $this
   *   The object class.
   */
  public function setUrlUniversity($university_url);

  /**
   * Get the organisation's logo path in ESN Accounts.
   *
   * @return string
   *   The path to the logo of the organisation.
   */
  public function getRemoteLogoPath();

  /**
   * Get the file entity ID of the saved logo.
   *
   * @return string
   *   ID of file entity or NULL.
   */
  public function getLocalLogo();

  /**
   * Set the organisation's logo local file entity ID.
   *
   * @param int $id
   *   The file entity ID.
   *
   * @return $this|null
   *   The object class.
   */
  public function setLocalLogo($id);

  /**
   * Set the organisation's logo path from ESN Accounts.
   *
   * @param string $path
   *   The path to the logo of the organisation to set.
   *
   * @return $this
   *   The object class.
   */
  public function setLogo($path);

  /**
   * Gets the logo of the organisation from API and saves it locally.
   *
   * @return int|null
   *   Returns the image ID of the logo or NULL.
   */
  public function saveLogo();

  /**
   * Updates the logo.
   *
   * @param string $path
   *   The path to the logo of the organisation to set.
   *
   * @return int|false
   *   Returns the logo id if saved, false otherwise.
   */
  public function updateLogo(string $path);

}
