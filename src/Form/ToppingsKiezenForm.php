<?php

namespace Drupal\thomas_more_icecream\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ToppingsKiezenForm extends FormBase {

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
    return 'thomas_more_icecream_toppings_kiezen_form';
  }
  // opbouwen van de form
  public function buildForm(array $form, FormStateInterface $form_state) {
    $toppings = $this->state->get('thomas_more_icecream.toppings',['test']);

    $form['toevoegen'] = [
      '#type' => 'textfield',
      '#title' => 'Topping',
      '#default_value' =>  "",
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Opslaan',
      '#button_type' => 'primary',
    ];


    $form['topping'] = array(
      '#type' => 'table',
      '#header' => array(
        $this
          ->t('Topping'),
        $this
          ->t(''),
      ),
    );
    for ($i = 0; $i <= count($toppings)-1; $i++) {
      $form['topping'][$i]['#attributes'] = array(
        'class' => array(
          'foo',
          'baz',
        ),
      );
      $form['topping'][$i]['naam'] = array(
        '#type' => 'markup',
        '#markup' => $toppings[$i],
      );
      $form['topping'][$i]['button'] = array(
        '#type' => 'submit',
        '#value' => 'verwijder',
        '#name' => $i+1,
        '#submit' => [
          [$this,'verwijder'],
        ]
      );
    }




    return $form;
  }
  // Verwijderen van een topping
  public function verwijder(array &$form, FormStateInterface $form_state) {
    $id = $form_state->getTriggeringElement()['#name'];

    $toppings = $this->state->get('thomas_more_icecream.toppings',[]);
    array_splice($toppings,$id-1,1);
    $this->state->set('thomas_more_icecream.toppings',$toppings);
    drupal_set_message('De smaak is succesvol verwijdert');
  }

  // Toevoegen van nieuwe toppings in de array en een melding tonen
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $toppings = $this->state->get('thomas_more_icecream.toppings',[]);
    array_push($toppings, $form_state->getValue('toevoegen'));
    $this->state->set('thomas_more_icecream.toppings',$toppings);
    drupal_set_message('De smaak is succesvol opgeslagen');
  }

}
