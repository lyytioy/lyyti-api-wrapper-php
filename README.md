# Lyyti API PHP wrapper
Wrapper for Lyyti API to make it simpler to use

Currently supported api resources:
```
events                         -> getEvents()
events/{event_id}/participants -> getParticipants($event)
standard_questions             -> getStandardQuestions(?$event)
```

Examples expect you to have imported the namespace by using
```php
use Lyyti\API\v2\Client as LyytiApi;
```

LyytiApi object caches responses for 10 minutes by default. You can configure this behavior in the constructor.
```php
// new LyytiApi(private key, public key, cache enabled boolean, cache lifetime in minutes)
// Example with 5 minute cache lifetime
$lyyti_api = new LyytiApi\Client("private_key", "public_key", true, 5);
```

Responses come as response objects that can contain http status code, data and error.
```php
$lyyti_api = new LyytiApi\Client("private_key", "public_key");
$response = $lyyti_api->getEvents();

// Events list if the request was successful (Dynamic type. In this case type = ?array)
$data = $response->data;
// Http code for the request (type = int)
$http_code = $response->http_code;
// Error text if the request failed (type = ?string)
$error = $response->error;
```

Basic usage example:

```php
// Init LyytiApi object
$lyyti_api = new LyytiApi\Client("private_key", "public_key");

// Get events from API
$events = $lyyti_api->getEvents()->data;

// Use the events
foreach ($events as $event) {
    $first_event_language = $event->language[0];
    $event_name = $event->name->$first_event_language;
    echo $event_name."\n";
}
```
