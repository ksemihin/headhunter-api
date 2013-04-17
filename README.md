headhunter-api
==============

Provide query API interface for HeadHunter (hh.ru) service

## API Manual for service ##
[API HH.RU](http://api.hh.ru) 

## Usage Example ##

```php
// Init HeadHunter API query interface.
$hh_settings = array(
  'service_url' => 'http://api.hh.ru/,
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
