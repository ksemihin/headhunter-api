<?php


class HeadHunterApi implements HeadHunterApiInterface {

  protected  $service_url = 'http://api.hh.ru';

  protected $method_map = array();
  protected $param = array();
  protected $query = '';
  protected $response = array();
  protected $callback = array();

  public $format = 'json';
  public $debug_mode = NULL;
  public $user_agent = "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13";


  function __construct($param = array()) {
    if (empty($param)) {
      foreach ($param as $name => $value) {
        $this->{$name} = $value;
      }
    }
    return $this;
  }

  function __destructor() {
    $this->clearAll();
  }

  /**
   * Generate URL-encoded query string
   * @return mixed|void
   */
  public function buildQuery() {
    $url = $this->service_url . '/';
    $segment = array();
    if(!is_null($this->format))
      $segment[] = $this->format;
    $segments = array_merge($segment, $this->method_map);
    $url .= implode('/', $segments) . '?' . http_build_query($this->param);
    return $this->query = $url;
  }

  /**
   * Set the number of elements on a page, but not more than 500
   *
   * @param integer $number
   * @return \HeadHunterApi2
   */
  public function per_page($number) {
    $number = (int) $number;
    if($number < 1)
      $number = 1;
    if($number > 500)
      $number = 500;
    $this->param["per_page"] = $number;
    return $this;
  }

  /**
   * Helper method for call one time method.
   * @param array $methods
   *   Method name.
   * @param array $params
   *   Condition string.
   * @return array
   */
  public function get($methods = array(), $params = array()) {
    $this->clearAll();
    foreach ((array) $methods as $method) {
      $this->method ($method);
    }
    foreach ((array) $params as $paramName => $value) {
      $this->condition($paramName, $value);
    }
    $this->execute();
    return $this->getResponse();
  }

  /**
   * Get all result per current query.
   * @return array
   */
  public function getResponse() {
    return $this->response;
  }

  /**
   * Add
   * @param $name
   * @return $this|mixed
   */
  public function method($name) {
    $this->method_map[] = $name;
    return $this;
  }

  /**
   * Add condition for query.
   * @param $paramName
   * @param $value
   * @return $this
   */
  public function condition($paramName, $value) {
    $this->param[$paramName] = $value;
    return $this;
  }

  /**
   * Execute query.
   * @return mixed|null
   */
  public function execute() {
    $this->executeCallback('pre_execute');
    $this->check();
    $this->buildQuery();
    $response = json_decode($this->getContents());
    $this->response = !empty($response) ? $response : NULL;
    $this->executeCallback('after_execute');
    return $this->response;
  }

  /**
   * Get content from remote server.
   * @return string
   */
  protected function getContents() {
    $opts = array(
      'http' => array(
        'method' => "GET",
        'header' => $this->user_agent . "\r\n"
      )
    );
    $context = stream_context_create($opts);
    return file_get_contents($this->query, false, $context);
  }

  /**
   * Clear all data.
   */
  public function clearAll() {
    $this->param = array();
    $this->method_map = array();
    $this->response = NULL;
    $this->query = '';
  }

  /**
   * Check necessary params for query.
   */
  protected function check() {
    if (empty($this->method_map)
      || empty($this->service_url)
    ) {
      throw new Exception('Param or method map is empty');
    }
  }

  /**
   * Add callback function.
   * @param $func_name
   *   Function name. (string)
   * @param string $group
   *   Group callbacks name (string)
   * @return $this
   */
  public function callback($func_name, $group = 'after_execute') {
    $this->callback[$group][] = $func_name;
    return $this;
  }

  /**
   * Call custom function by group.
   * @param $group
   *   Group name (string)
   */
  private function executeCallback($group) {
    foreach($this->callback[$group] as $func_name) {
      if (function_exists($func_name)) {
        call_user_func($func_name, $this);
      }
    }
  }
}