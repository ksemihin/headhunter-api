<?php

/**
 * Class HeadHunterApi2
 * Provide simple Query interface for Head Hunter service api version 2.
 */
class HeadHunterApi2 extends HeadHunterApi {
    
    public $version = null;
    public $format = null;
    public $user_agent = "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13";

    /**
     * The factory method to get an api object by version number
     * 
     * @param array $param
     * @param integer $version
     * @return \HeadHunterApi2|\HeadHunterApi
     */
    public static function getByVersion($param = array(), $version = 2) {
        return $version === 2
            ? new HeadHunterApi2($param)
            : new HeadHunterApi($param);
    }
    
    /**
     * Getting response from the server
     * 
     * @return mixed
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
    
    /** @deprecated */
    public function limit($items) {
        return $this;
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
     * Return all vacancies for current response.
     * @return array
     */
    public function getVacancyResult() {
        return $this->response->items;
    }
 }

/**
 * Class HeadHunterApi
 * Provide simple Query interface for Head Hunter service.
 */
class HeadHunterApi {

    public $service_url = '';
    public $format = 'json';
    public $version = '1';
    public $debug_mode = NULL;
    protected $method_map = array();
    protected $param = array();
    protected $query = '';
    protected $response = array();

    function __construct($param = array()) {
        foreach ($param as $name => $value) {
            $this->{$name} = $value;
        }
    }

    function __destructor() {
        $this->clearAll();
    }

    /**
     * Generation query url.
     */
    protected function buildQuery() {
        $url = $this->service_url;
        $segment = array();
        if(!is_null($this->version))
            $segment[] = $this->version;
        if(!is_null($this->format))
            $segment[] = $this->format;
        $segments = array_merge($segment, $this->method_map);
        $url .= implode('/', $segments) . '?' . http_build_query($this->param);
        $this->query = $url;
    }

    /**
     * Check necessary params for query.
     */
    protected function check() {
        if (empty($this->method_map)
                || empty($this->param)
                || empty($this->service_url)
        ) {
            throw new Exception('Param or method map is empty');
        }
    }
    
    /**
     * Getting response from the server
     * 
     * @return mixed
     */
    protected function getContents() {
        return file_get_contents($this->query);
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
        $response = json_decode($this->getContents());
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
            'Response' => $full ? $this->response : '{EMPTY IN NOT FULL MODE} Row count ~' . $this->rowTotalCount(),
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