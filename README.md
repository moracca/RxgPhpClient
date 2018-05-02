# RxgPhpClient

## Setup

require the RxgPhpClient.php file into your project and initialize an instance.

```
  require_once("RxgPhpClient.php");

  #   Initialize a new instance of the Rxg_Curl_Class
  $rxg = new Rxg_Php_Client("hostname.domain.com", "apikey");
  ```

## Usage
API methods take as their first parameter the scaffold name that you wish to query.  
If you are unsure, hover over a hyperlink in the rXg's admin console and look at the URL.  
The scaffold name is listed after "scaffolds" such as `https://rxg.domain.com/admin/scaffolds/{admins}`

####  Create a record
* param1: scaffold (string)
* param2: record attributes (array)
```
  $result = $rxg->create("admins", 
    array(
      "login" => "operator", 
      "password" => '$uperP@ssword',
      "password_confirmation" => '$uperP@ssword',
      "admin_role" => 1
    )
  );
```
####  Retrieve a record
* param1: scaffold (string)
* param2: record_id (integer)
```
  $result = $rxg->show("wan_targets", 1);
```
####  List all records
* param1: scaffold (string)
* param2: search filter (array - optional)
```
  $result = $rxg->search("accounts");
```
####  List records filtered by search params

* param1: scaffold (string)
* param2: search filter (array - optional)
```
  $result = $rxg->search("accounts", array('first_name' => 'Romeo'));
```
####  Update a record
* param1: scaffold (string)
* param2: record_id (integer)
* param2: updated attributes (array)
```
  $result = $rxg->update("accounts", 43, array("first_name" => 'George'));
```
####  Delete a record
* param1: scaffold (string)
* param2: record_id (integer)
```
  $result = $rxg->delete("accounts", 41);
```

## Error Handling
Wrap API calls in try/catch blocks to catch `Rxg_Exception`.

The response code and message are available as methods of the exception object.
```
try {
  $result = $rxg->create("accounts", 
    array(
      "login" => "jsmith", 
      "password" => 'a',
      "password_confirmation" => 'a'
    )
  );
  print_r($result);
} catch (Rxg_Exception $e) {
  print $e->getCode() + "<br>";
  print $e->getMessage();
}

=> 422
=> {"email":["can't be blank","is too short (minimum is 3 characters)","is invalid"],"first_name":["can't be blank","is too short (minimum is 1 character)"],"last_name":["can't be blank","is too short (minimum is 1 character)"]}
```
