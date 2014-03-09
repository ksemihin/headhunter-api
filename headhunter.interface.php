<?php


interface HeadHunterApiInterface {

  /**
   * Method for build query string.
   * @return mixed
   *   Full URL for call api service.
   */
  public function buildQuery();

  /**
   *
   * @param $name
   * @return mixed
   */
  public function method($name);
  public function condition($paramName, $value);

  public function execute();
  public function clearAll();
}