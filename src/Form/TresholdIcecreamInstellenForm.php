<?php

namespace Drupal\thomas_more_icecream\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TresholdIcecreamInstellenForm extends FormBase {

  // Dependency Injection
  protected $state;
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state')
    );
  }
  public function getFormId() {
    return 'thomas_more_icecream_treshold_icecream_instellen_form';
  }

  // aanmaken form
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')->getEditable('thomas_more_icecream.config');

    $form['wafels'] = [
      '#type' => 'number',
      '#title' => 'Wafels',
      '#default_value' =>  $config->get('wafels'),
    ];
    $form['ijsjes'] = [
      '#type' => 'number',
      '#title' => 'Ijsjes',
      '#default_value' =>  $config->get('ijsjes'),
    ];


    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Opslaan',
      '#button_type' => 'primary',
    ];

    return $form;
  }

  // knop wordt verzonden
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')->getEditable('thomas_more_icecream.config');
    //aanpassen van de threshold
    $config->set('wafels', $form_state->getValue('wafels'))->save();
    $config->set('ijsjes', $form_state->getValue('ijsjes'))->save();

    //tellers worden gereset
    $config->set('tellerIjsjes', 0)->save();
    $config->set('tellerWafels', 0)->save();
    drupal_set_message('De tresholds zijn succesvol opgeslagen en tellers zijn gereset');
  }
}
