<?php

/**
 * Class HeadHunterApi
 * Provide simple Query interface for Head Hunter service.
 */

class HeadHunterApi {

  public $service_url = '';
  public $format = 'json';
  public $version = '1';
  public $debug_mode = NULL;
  private $method_map = array();
  private $param = array();
  private $query = '';
  private $response = array();


  function __construct($param = array()) {
    foreach($param as $name=>$value) {
      $this->{$name} = $value;
    }
  }

  function __destructor() {
    $this->clearAll();
  }

  /**
   * Generation query url.
   */
  private function buildQuery() {
    $url = $this->service_url;
    $url .= $this->version . '/';
    $url .= $this->format . '/';
    $url .= implode('/', $this->method_map) . '?' . http_build_query($this->param);
    $this->query = $url;
  }


  /**
   * Check necessary params for query.
   */
  private function check() {
    if (   empty($this->method_map)
        || empty($this->param)
        || empty($this->service_url)
    ) {
      throw new Exception('Param or method map is empty');
    }
  }

  /**
   * Set method for query.
   * @param $name (string) Method name.
   * @return $this  Current object.
   */
  public function method($name) {
    $this->method_map[] = $name;
    return $this;
  }


  /**
   * Service call with query.
   */
  public function execute() {
    $this->check();
    $this->buildQuery();
    $response = json_decode(file_get_contents($this->query));
    $this->response = !empty($response) ? $response : NULL;
  }


  /**
   * Return Total count items for current result.
   * @return mixed
   * @throws Exception
   */
  public function rowTotalCount() {
    if (is_null($this->response)) {
      throw new Exception('Response in NULL');
    }
    return $this->response->found;
  }


  /**
   * Return vacancy count for current query.
   * @return int
   * @throws Exception
   */
  public function rowResultVacancyCount() {
    if (is_null($this->response)) {
      throw new Exception('Response is NULL');
    }
    return count($this->response->vacancies);
  }


  /**
   * Get Count for current result.
   * @return int
   * @throws Exception
   */
  public function getResultAllCount() {
    if (is_null($this->response)) {
      throw new Exception('Response is NULL');
    }
    return count($this->response);
  }


  /**
   * Helper debug method.
   * @return $this
   */
  public function dvm($full = FALSE) {
    if (!$this->debug_mode) {
      throw new Exception('Try to call debug method in not debug mode.');
    }
    if (empty($this->response)) {
      $this->execute();
    }
    var_dump(array(
      'Query' => $this->query,
      'Method' => $this->method_map,
      'Param' => $this->param,
      'Response' => $full ? $this->response: '{EMPTY IN NOT FULL MODE} Row count ~' . $this->rowTotalCount(),
    ));

  }


  /**
   * Return all vacancies for current response.
   * @return array
   */
  public function getVacancyResult() {
    return $this->response->vacancies;
  }


  /**
   * Get all result per current query.
   * @return array
   */
  public function getAllResult() {
    return $this->response;
  }


  /**
   * Set condition to query.
   * @param $paramName
   * @param $value
   * @return $this
   */
  public function condition($paramName, $value) {
    $this->param[$paramName] = $value;
    return $this;
  }


  /**
   * Set limit for query result.
   * @param $items
   * @return $this
   */
  public function limit($items) {
    $this->param['items'] = $items;
    return $this;
  }


  /**
   * Set page number.
   * @param $number
   * @return $this
   */
  public function page($number) {
    $this->param['page'] = $number;
    return $this;
  }


  /**
   * Helper method for clear all input data.
   */
  public function clearAll() {
    $this->param = array();
    $this->method_map = array();
    $this->response = NULL;
    $this->query = '';
  }
}
