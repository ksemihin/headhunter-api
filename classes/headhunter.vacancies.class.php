<?php

/**
 * @file
 *   Provide methods for work with vacancies on hh.ru service.
 */

class HeadHunterVacancies extends HeadHunterApi {


  public function getVacanciesList() {
    $this->method('vacancies');
    return $this->execute();
  }

  public function getVacancyById($vacancy_id) {
    $this->method('vacancies');
    $this->method($vacancy_id);
    $this->execute();
  }
}