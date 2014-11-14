<?php
/**
 * @Last Modified time: 2014-11-14 01:42:16
 */

if (!defined('JSON_PRETTY_PRINT'))
  define('JSON_PRETTY_PRINT', 0);

class JSONUtils {
  public $json = null;
  public $filename = "";

  const INSERT_DVD_SUCCESS       = 0;
  const INSERT_DVD_ALREADY_EXIST = 1;
  const INSERT_DVD_INVALID_DATA  = 2;
  const INSERT_DVD_INVALID_ID    = 3;
  const INSERT_DVD_ERROR         = 4;

  const UPDATE_DVD_SUCCESS       = 0;
  const UPDATE_DVD_NOT_FOUND     = 1;
  const UPDATE_DVD_INVALID_DATA  = 2;
  const UPDATE_DVD_ERROR         = 3;
  
  const DELETE_DVD_ERROR         = 0;
  const DELETE_DVD_NOT_FOUND     = 1;
  const DELETE_DVD_SUCCESS       = 2;


  public function __construct($file) {
    $this->JSONUtils($file);
  }

  public function JSONUtils($file) {
    $this->filename = $file;
    $this->json = json_decode(file_get_contents($file), TRUE);
  }

  public function getAll() {
    return $this->json;
  }

  public function getById($id) {
    if ($id == null) {return null;}

    foreach ($this->json["dvds"] as $dvd) {
      if ($id == $dvd["id"]) {
        return $dvd;
      }
    }

    return null;
  }

  public function getColumnById($id, $column) {
    $dvd = $this->getById($id);
    if ($dvd == null OR $dvd[$column] == null) {
      return null;
    }

    return array($column => $dvd[$column]);
  }

  public function addDvd($dvd) {
    if ($dvd == null)
      return JSONUtils::INSERT_DVD_INVALID_DATA;
    
    if ( ! is_integer($dvd["id"]))
      return JSONUtils::INSERT_DVD_INVALID_ID;
    
    if ( $this->getById($dvd["id"]) != null )
      return JSONUtils::INSERT_DVD_ALREADY_EXIST;

    $this->json["dvds"][] = $dvd;
    $encodedJSON = json_encode($this->json, JSON_PRETTY_PRINT);

    if (file_put_contents($this->filename, $encodedJSON) === FALSE)
      return JSONUtils::INSERT_DVD_ERROR;
    return JSONUtils::INSERT_DVD_SUCCESS;
  }

  public function getIndex($id) {
    $index = 0;
    foreach ($this->json["dvds"] as $dvd) {
      if ($dvd["id"] == $id) break;
      $index++;
    }
    if (isset($this->json["dvds"][$index]))
      return $index;
    return -1;
  }

  public function updateDvd($dvd) {
    if ($dvd == null)
      return JSONUtils::UPDATE_DVD_INVALID_DATA;
    
    $search = $this->getById($dvd["id"]);
    if ( $search == null )
      return JSONUtils::UPDATE_DVD_NOT_FOUND;

    $this->json["dvds"][$this->getIndex($dvd["id"])] = $dvd;
    $encodedJSON = json_encode($this->json, JSON_PRETTY_PRINT);

    if (file_put_contents($this->filename, $encodedJSON) === FALSE)
      return JSONUtils::UPDATE_DVD_ERROR;
    return JSONUtils::UPDATE_DVD_SUCCESS;
  }

  public function deleteDvd($id) {
    $index = $this->getIndex($id);
    if ($index == -1) return JSONUtils::DELETE_DVD_NOT_FOUND;
    
    unset($this->json["dvds"][$index]);
    $encodedJSON = json_encode($this->json, JSON_PRETTY_PRINT);

    if (file_put_contents($this->filename, $encodedJSON) === FALSE)
      return JSONUtils::DELETE_DVD_ERROR;
    return JSONUtils::DELETE_DVD_SUCCESS;
  }

}