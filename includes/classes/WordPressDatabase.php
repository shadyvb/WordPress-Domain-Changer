<?php
class WordPressDatabase extends mysqli
{

  private $table_prefix = "wp_";

  public function __construct($host, $username, $password, $database, $table_prefix) {
    parent::__construct($host, $username, $password, $database);
    $this->table_prefix = $this->setTablePrefix($table_prefix);
  }

  public function getConnectError() {
    return mysqli_connect_error();
  }

  public function getTablePrefix() {
    return $this->table_prefix;
  }

  public function setTablePrefix($string) {
    return $this->table_prefix = $this->escape_string($string);
  }

}