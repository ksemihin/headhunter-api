headhunter-api
==============

Provide query API interface for HeadHunter (hh.ru) service

## API Manual for service ##
[API HH.RU](http://api.hh.ru) 

Added new api interface support

## New API usege example ##

```php
// Init HeadHunter API query interface.
$hh_settings = array(
  'service_url' => 'https://api.hh.ru/',
  'debug_mode' => FALSE,
);
```

Example for this query:  https://api.hh.ru/vacancies?text=developer&area=16&page=1&per_page=5

```php

// default is a new version of API

$hh = HeadHunterApi2::getByVersion($hh_settings);
$hh->method("vacancies");
$hh->condition("text", "developer")
        ->condition("area", 16)
        ->page(1)
        ->per_page(5)
        ->execute();
$result = $hh->getVacancyResult();

/*
$methods = array("vacancies");
$params = array(
    "text" => "developer",
    "area" => 16,
    "page" => 1,
    "per_page" => 5);
$fullResult = $hh->get($methods, $params);
*/

print_r($result);
foreach($result as $row) {
    $vacancy = $hh->getVacancy($row->id);
    print_r($vacancy);
}

// regions

$areas = $hh->getAreas();
$area = $hh->getArea($areaId);
```

## Old API usage Example ##

```php
// Init HeadHunter API query interface.
$hh_settings = array(
  'service_url' => 'http://api.hh.ru/',
  'debug_mode' => FALSE,
);
```

Example for this query: http://api.hh.ru/1/json/vacancy/search/?region=113&field=13&specialization=471&items=10&page=0 

```php
$hh = new HeadHunterApi($hh_settings);
$hh->method('vacancy')->method('search');
$hh->condition('region', 113)
  ->condition('field', 13)
  ->condition('specializations', 471)
  ->limit(10)
  ->page(0)
  ->execute();

print_r($hh->getVacancyResult());

```
