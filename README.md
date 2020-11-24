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

Client memory caches responses for 10 minutes by default (does not persist if the Client object is deallocated).
You can configure this behavior by passing Cache object to the Client.
```php
// new LyytiApi(private key, public key, cache)
// Example with 5 minute cache that is stored in a file to make it persistent
$lyyti_api = new LyytiApi\Client("private_key", "public_key", new LyytiApi\Cache(1, "cachefile.json"));
```

Responses come as Response objects that contain http status code and depending if the API request succeeded data and error.
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
