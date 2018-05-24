<?php

namespace Drupal\thomas_more_icecream\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\thomas_more_icecream\OrderManager;

class bestellingForm extends FormBase {

  // Dependency Injection van de state en van orderManager
  protected $state;
  protected $orderManager;
  public function __construct(StateInterface $state, OrderManager $orderManager) {
    $this->orderManager = $orderManager;
    $this->state = $state;
  }
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state'),
      $container->get('thomas_more_icecream.order_manager')

    );
  }

  public function getFormId() {
    return 'thomas_more_icecream_order_form';
  }

  // Aanmaken form
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['keuze'] = [
      '#type' => 'radios',
      '#title' => 'Wat wil je eten?',
      '#options' => [
        'icecream' => 'icecream',
        'wafels' => 'wafels',

      ],
    ];
    // Array key => value maken
    $smaken = [];
    foreach($this->state->get('thomas_more_icecream.smaken') as $smaak) {
      $smaken[$smaak] = $smaak;
    }
    $form['smaak'] = [
      '#type' => 'radios',
      '#title' => 'Welke smaak?',
      '#options' => $smaken,
      '#states' => [
        'visible' => [
          ':input[name="keuze"]' =>
            [
              'value' => 'icecream',
            ],
        ],
      ],
    ];
    // Array key => value maken
    $toppings = [];
    foreach($this->state->get('thomas_more_icecream.toppings') as $topping) {
      $toppings[$topping] = $topping;
    }
    $form['topping'] = [
      '#type' => 'checkboxes',
      '#title' => 'Welke topping wil je op je wafel?',
      '#options' => $toppings,
      '#states' => [
        'visible' => [
          ':input[name="keuze"]' =>
            [
              'value' => 'wafels',
            ],
        ],
      ],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Bestellen',
      '#button_type' => 'primary',
    ];
    return $form;
  }

  //Bij het drukken van "bestellen"
  public function submitForm(array &$form, FormStateInterface $form_state) {

    //aanmaken variables
    $config = \Drupal::service('config.factory')
      ->getEditable('thomas_more_icecream.config');
    $aantalBestellingen = 0;
    $threshold = 0;
    $keuze = $form_state->getValue('keuze');

    // Controleert wat er wordt besteld en maakt een array aan
    if ($keuze == 'icecream') {
      $order = [
        'order' => 'icecream',
        'extra' => $form_state->getValue('smaak'),
      ];
    }
    else {
      $currentToppings = "";
      foreach ($form_state->getValue('topping') as $topping) {
        if (!empty($topping)) {
          $currentToppings .= $topping . ';';
        }
      }
      $order = [
        'order' => 'wafel',
        'extra' => $currentToppings,
      ];
    }

    // database aanpassen
    $this->orderManager->addOrder($order);

    // teller wijzigen en bericht tonen en wanneer nodig een mail verzenden
    if ($keuze == 'icecream') {
      $config->set('tellerIjsjes', $config->get('tellerIjsjes') + 1)->save();
      $aantalBestellingen = $config->get('tellerIjsjes');
      $threshold = $config->get('ijsjes');
    }
    else {
      $config->set('tellerWafels', $config->get('tellerWafels') + 1)->save();
      $aantalBestellingen = $config->get('tellerWafels');;
      $threshold = $config->get('wafels');
    }

    if ($aantalBestellingen < $threshold) {
      $aantalNodig = $threshold - $aantalBestellingen;
      drupal_set_message('Bestelling opgeslagen. er zijn nog ' . $aantalNodig . ' bestellingen nodig.', 'warning');
    }
    else {
      drupal_set_message('De treshhold is bereikt. De bestelling is onderweg.');
      $this->sendMail($keuze);
      if ($keuze == 'icecream') {
        $config->set('tellerIjsjes', 0)->save();
      }
      else {
        $config->set('tellerWafels', 0)->save();
      }
    }

  }

  // mail verzenden
  public function sendMail(string $keuze) {
    // aanmaken variable
    $config = \Drupal::service('config.factory')
      ->getEditable('thomas_more_icecream.config');
    //bericht samenstellen
    $bericht = "beste\n\nDe volgende artikels zijn besteld:\n";
    if($keuze == 'icecream'){
      $bericht .= 'er zijn ' . $config->get('tellerIjsjes') . ' ' . $keuze . ' besteld.';
    } else {
      $bericht .= 'er zijn ' . $config->get('tellerWafels') . ' ' . $keuze . ' besteld.';
    }
    $bericht .= "\nGelieve deze te leveren.\n\nMet vriendelijke groeten\nSacha en Nick";


    //verzenden mail
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'thomas_more_icecream';
    $key = 'create_article';
    $to = "jeroen.tubex@intracto.com";
    $params['message'] = $bericht;
    $params['node_title'] = "bestelling van " . $keuze;
    $send = TRUE;
    $result = $mailManager->mail($module, $key, $to, "nl", $params, NULL, $send);
  }
}