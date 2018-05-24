<?php

namespace Drupal\thomas_more_icecream;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;

class OrderManager {

  protected $connection;
  protected $time;
  public function __construct(Connection $connection, TimeInterface $time) {
    $this->connection = $connection;
    $this->time = $time;
  }
  // Order wordt toegevoegd aan de databank
  public function addOrder(array $order) {

    $this->connection->insert('thomas_more_icecream')
      ->fields([
        'bestelling' => $order['order'],
        'extra' => $order['extra'],
        'time_clicked' => $this->time->getRequestTime(),
      ])->execute()
    ;
  }

  //vraagt op hoeveel er wordt bespeld voor een bepaalde keuze
  public function getOrders(string $bestelling) {
    $query = $this->connection->select('thomas_more_icecream', 't');
    $query->condition('t.bestelling', $bestelling);
    return (int) $query->countQuery()->execute()->fetchField();
  }

}
