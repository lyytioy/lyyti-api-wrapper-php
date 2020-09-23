# lyyti-api-wrapper-php
Wrapper for Lyyti API to make it simpler to use

Basic usage example:

```php
// Init LyytiApi object
$lyyti_api = new LyytiApi("private_key", "public_key");

// Get events from API
$events = $lyyti_api->getEvents();

// Use the events
foreach ($events as $event) {
    $first_event_language = $event->language[0];
    $event_name = $event->name->$first_event_language;
    echo $event_name."\n";
}
```
