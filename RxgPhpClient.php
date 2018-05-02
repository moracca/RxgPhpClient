<?php

class Rxg_Php_Client {

  require_once('Rxg_Exception.php');

  var $endpointUrl;
  var $apiKey;
  var $curl_handler;
  
  var $defaults = array(
      CURLOPT_HEADER => 0,
      // CURLOPT_FRESH_CONNECT => 1,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_SSL_VERIFYPEER => false,  // ssl fix
      CURLOPT_SSL_VERIFYHOST => false // ssl fix
    );
  
  //constructor saves the values
  function __construct($hostname, $key) {
    $this->endpointUrl="https://$hostname/admin/scaffolds/";
    $this->apiKey=$key;
  }

  /**
  * Performs a GET request using the PHP cURL library.  Assumes response is JSON
  *
  * @param string   $url      The URL to retrieve
  * 
  * @throws Rxg_Exception the response is not a 2XX code12.108.53.2
  * @return $result array
  */ 
  function get($url) {
    $curl_handler = curl_init();
    $params = array("api_key" => $this->apiKey);
    $options = array(CURLOPT_URL => $url."?".http_build_query($params));
    curl_setopt_array($curl_handler, ($this->defaults + $options));
    
    $result = curl_exec($curl_handler);
    if ($result === false) {
      throw new Rxg_Exception(curl_error($curl_handler), $responseCode);
    }
    
    $responseCode = curl_getinfo($curl_handler, CURLINFO_RESPONSE_CODE);
    # Make sure the request was successful, ie - the response code begins with 2 
    if (substr((string)$responseCode, 0, 1) != "2") {
      throw new Rxg_Exception($result, $responseCode);
    } else {
      $jsonResponse = json_decode($result, true);
      return $jsonResponse;
    }
  }

  /**
  * Performs a POST request using the PHP cURL library.  Assumes response is JSON
  *
  * @param string   $url      The URL to post to
  * @param array    $body   The (optional) array containing the POST body
  * 
  * @throws Rxg_Exception the response is not a 2XX code12.108.53.2
  * @return $result array
  */ 
  function post($url, $body = array()) {
    $curl_handler = curl_init();
    $params = array("api_key" => $this->apiKey);
    $options = array(
      CURLOPT_URL => $url."?".http_build_query($params),
      CURLOPT_POST => count($body),
      # preg_replace remove PHP's array indexes to properly pass a simple array of values
      CURLOPT_POSTFIELDS => preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', http_build_query($body))
    );
    curl_setopt_array($curl_handler, ($this->defaults + $options));
    
    $result = curl_exec($curl_handler);

    if ($result === false) {
      throw new Rxg_Exception(curl_error($curl_handler), 1);
    }
    $responseCode = curl_getinfo($curl_handler, CURLINFO_RESPONSE_CODE);
    # Make sure the request was successful, ie - the response code begins with 2 
    if (substr((string)$responseCode, 0, 1) != "2") {
      throw new Rxg_Exception($result, $responseCode);
    } else {
      $jsonResponse = json_decode($result, true);
      return $jsonResponse;
    }
    
  }

  /**
  * Creates a record in the given scaffold
  *
  * @param string   $scaffold   The scaffold to post to
  * @param array    $record     The array containing the required record attribute params
  * 
  * @return $result array       The array containing the created record's attributes
  */ 
  function create($scaffold, $record) {
    $result = $this->post($this->endpointUrl.$scaffold."/create.json", array("record" => $record));
    return $result;
  }

  /**
  * Lists all record in the given scaffold.  
  * Note that "list" is a reserved function name in PHP
  *
  * @param string   $scaffold       The scaffold to post to
  * @param array    $search_params  Optional array containing a set of record attributes to
  *                                 be used to filter the list
  * 
  * @return $result array           The multi-dimensional array of records matching the search
  */ 
  function search($scaffold, $search_params = array()) {
    $result = $this->post($this->endpointUrl.$scaffold."/index.json", $search_params);
    return $result;
  }

  /**
  * Retrieve a specific record in the given scaffold by ID.  
  * Note that "list" is a reserved function name in PHP
  *
  * @param string   $scaffold   The scaffold to get the record from
  * @param integer  $id         The ID of the record to retrieve
  * 
  * @return $result array       The array containing the specified record's attribues 
  */ 
  function show($scaffold, $id) {
    $result = $this->get($this->endpointUrl.$scaffold."/".$id.".json");
    return $result;
  }

  /**
  * Retrieve a specific record in the given scaffold by ID.  
  * Note that "list" is a reserved function name in PHP
  *
  * @param string   $scaffold   The scaffold to post the update to
  * @param integer  $id         The ID of the record to update
  * @param array    $record     The array of updated attribute records to be applied to the record
  * 
  * @return $result array       The array containing the updated record's attribues 
  */ 
  function update($scaffold, $id, $record) {
    $result = $this->post($this->endpointUrl.$scaffold."/update/".$id.".json", array("record" => $record));
    return $result;
  }

  /**
  * Retrieve a specific record in the given scaffold by ID.  
  * Note that "list" is a reserved function name in PHP
  *
  * @param string   $scaffold   The scaffold to get the record from
  * @param integer  $id         The ID of the record to destroy
  * 
  * @return $result boolean     True if the record was deleted successfully.
  */ 
  function destroy($scaffold, $id) {
    $result = $this->post($this->endpointUrl.$scaffold."/destroy/".$id.".json");
    # the response to a delete request has a blank body, and json_decodes to NULL
    if ($result === NULL) {
      return true;
    } else {
      return false;
    }
  }

}

?>
