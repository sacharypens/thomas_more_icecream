<?php

namespace Drupal\thomas_more_icecream\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\State\StateInterface;

/**
 * Defines a icecream menu block.
 *
 * @Block(
 *  id = "thomas_more_icecream_block",
 *  admin_label = @Translation("Icecream"),
 * )
 */
class icecreamBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\thomas_more_icecream\Form\bestellingForm');

    return $form;
  }
}