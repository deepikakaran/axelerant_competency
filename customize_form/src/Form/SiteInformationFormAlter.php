<?php

namespace Drupal\customize_form\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form\SiteInformationForm;

/**
 * Contains the extra fields for Site Information Form.
 */
class SiteInformationFormAlter extends SiteInformationForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the system site config.
    $site_config = $this->config('system.site');
    // Invoke the parent form.
    $form = parent::buildForm($form, $form_state);

    // Include the additional form field elements.
    $form['site_api_key_details'] = [
      '#type' => 'details',
      '#title' => t('API Key Details'),
      '#open' => 'open',
    ];

    $form['site_api_key_details']['siteapikey'] = [
      '#type' => 'textfield',
      '#title' => t('Site API Key'),
      '#weight' => 1,
      '#placeholder' => t('Add Site API key here...'),
      '#default_value' => $site_config->get('siteapikey') ? $site_config->get('siteapikey') : 'No API Key yet',
      '#description' => t("Provide the API Key in the above field."),
    ];

    // Update the submit button label.
    $form['actions']['submit']['#value'] = t('Update configuration');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Invoke the parent form submit.
    parent::submitForm($form, $form_state);
    // Get the value already stored in the API key field.
    $default_value = $form['site_api_key_details']['siteapikey']['#default_value'];

    // Get the value entered during form submit.
    $api_key = $form_state->getValue('siteapikey');

    // Save only when API key field differs from the default value
    // to avoid triggering of config save on each submit.
    if ($default_value != $api_key) {
      // Set the API key value to config.
      $this->config('system.site')
        ->set('siteapikey', $api_key)
        ->save();

      // Status message to show when values are updated.
      $this->messenger()->addStatus($this->t('The Site API Key has been saved with @api_key', ['@api_key' => $api_key]));
    }
  }

}
