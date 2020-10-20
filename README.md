# lyyti-api-wrapper-php
Wrapper for Lyyti API to make it simpler to use

Currently supported api resources:
```
events                         -> getEvents()
events/{event_id}/participants -> getParticipants($event)
standard_questions             -> getStandardQuestions(?$event)
```

LyytiApi object caches responses for 10 minutes by default. You can configure this behavior in the constructor.
```php
// new LyytiApi(private key, public key, cache enabled boolean, cache lifetime in minutes)
// Example with 5 minute cache lifetime
$lyyti_api = new LyytiApi\Client"private_key", "public_key", true, 5);
```

Basic usage example:

```php
// Init LyytiApi object
$lyyti_api = new LyytiApi\Client("private_key", "public_key");

// Get events from API
$events = $lyyti_api->getEvents();

// Use the events
foreach ($events as $event) {
    $first_event_language = $event->language[0];
    $event_name = $event->name->$first_event_language;
    echo $event_name."\n";
}
```
