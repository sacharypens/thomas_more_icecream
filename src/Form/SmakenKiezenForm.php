<?php

namespace Drupal\thomas_more_icecream\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SmakenKiezenForm extends FormBase {

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
    return 'thomas_more_icecream_smaken_kiezen_form';
  }

  // opbouwen van de form
  public function buildForm(array $form, FormStateInterface $form_state) {
    $smaken = $this->state->get('thomas_more_icecream.smaken',['test']);

    $form['toevoegen'] = [
      '#type' => 'textfield',
      '#title' => 'Smaak',
      '#default_value' =>  "",
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Opslaan',
      '#button_type' => 'primary',
    ];


    $form['smaak'] = array(
      '#type' => 'table',
      '#header' => array(
        $this
          ->t('Smaak'),
        $this
          ->t(''),
      ),
    );
    for ($i = 0; $i <= count($smaken)-1; $i++) {
      $form['smaak'][$i]['#attributes'] = array(
        'class' => array(
          'foo',
          'baz',
        ),
      );
      $form['smaak'][$i]['naam'] = array(
        '#type' => 'markup',
        '#markup' => $smaken[$i],
      );
      $form['smaak'][$i]['button'] = array(
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
  // Verwijderen van een smaak
  public function verwijder(array &$form, FormStateInterface $form_state) {
    $id = $form_state->getTriggeringElement()['#name'];

    $smaken = $this->state->get('thomas_more_icecream.smaken',[]);
    array_splice($smaken,$id-1,1);
    $this->state->set('thomas_more_icecream.smaken',$smaken);
    drupal_set_message('De smaak is succesvol verwijdert');
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Toevoegen van nieuwe smaken in de array en een melding tonen
    $smaken = $this->state->get('thomas_more_icecream.smaken',[]);
    array_push($smaken, $form_state->getValue('toevoegen'));
    $this->state->set('thomas_more_icecream.smaken',$smaken);
    drupal_set_message('De smaak is succesvol opgeslagen');
  }

}
