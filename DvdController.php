<?php
/**
 * @Last Modified time: 2014-11-14 14:32:44
 */

require 'JSONUtils.php';

ini_set("register_globals", 0);

class DvdController {

  private $JSONUtils;

  /**
   * init is the first method called
   */
  public function init() {
    $this->JSONUtils = new JSONUtils("database.json");
  }

  // public function authorize() {
  //   return (isset($_GET["key"]) && $_GET["key"] == "hello");
  // }

  /**
     * Returns a JSON string object to the browser when hitting the root of the domain
     * @url GET /
     */
  public function main() {
    $return = array(
      "status"  => "0",
      "message" => "Go to /dvds"
    );
    return $return;
  }

  /**
   * Gets a list of all the dvds
   * @url GET /dvds
   */
  public function getAllDvds() {
    return $this->JSONUtils->getAll();
  }

  /**
   * Gets the dvd by id
   * @url GET /dvds/$id
   */
  public function getDvd($id = null) {
    $dvd = $this->JSONUtils->getById($id);
    
    if ($dvd == null)
      return array("status" => 0, "message" => "ID not found");

    return $dvd;
  }

  /**
   * Gets the dvd by id
   * @url GET /dvds/$id/$column
   */
  public function getDvdProperty($id = null, $column = null) {
    $prop = $this->JSONUtils->getColumnById($id, $column);
    
    if ($prop == null)
      return array("status" => 0, "message" => "Invalid input");

    return $prop;
  }

  /**
   * @url POST /dvds/$id
   */
  public function addDvd($id, $data) {
    $columns = array("id", "title", "artist", "country", "company", "price", "year");
    $return  = array("status" => 1, "message" => "");
    
    $dvd = (array)$data;
    foreach ($columns as $column) {
      if ( ! isset($dvd[$column])) {
        $return["status"] = 0;
        $return["message"] = "'$column' is undefined!";
        break;
      }
    }

    if ($dvd["id"] != $id) {
      $return["status"] = 0;
      $return["message"] = "The ID:{$dvd["id"]} does not match $id!";
    }

    if ($return["status"] != 0) {
      $dvd["id"] = intval($dvd["id"], 10);

      switch ($this->JSONUtils->addDvd($dvd)) {
        case JSONUtils::INSERT_DVD_INVALID_DATA:
          $return["status"]  = "0";
          $return["message"] = "The data is not valid";
          break;

        case JSONUtils::INSERT_DVD_INVALID_ID:
          $return["status"]  = "0";
          $return["message"] = "The ID is not a valid integer";
          break;
        
        case JSONUtils::INSERT_DVD_ALREADY_EXIST:
          $return["status"]  = "0";
          $return["message"] = "The ID already exist in the database, use PUT to update it";
          break;

        case JSONUtils::INSERT_DVD_ERROR;
          $return["status"]  = "0";
          $return["message"] = "Error writing to the database";
          break;

        case JSONUtils::INSERT_DVD_SUCCESS:
          $return["status"]  = "1";
          $return["message"] = "DVD '". $dvd["title"] ."' added successfully";
          break;

        default:
          $return["status"]  = "0";
          $return["message"] = "Unknown error";
          break;
      }
    }

    return $return;
  }

  /**
   * @url PUT /dvds/$id
   */

  public function updateDvd($id, $data) {
    $columns = array("id", "title", "artist", "country", "company", "price", "year");
    $return  = array("status" => 1, "message" => "");

    $dvd = (array)$data;

    foreach ($columns as $column) {
      if ( ! isset($dvd[$column])) {
        $return["status"] = 0;
        $return["message"] = "'$column' is undefined!";
        break;
      }
    }

    if ($dvd["id"] != $id) {
      $return["status"] = 0;
      $return["message"] = "The ID:{$dvd["id"]} does not match $id!";
    }

    if ($return["status"] != 0) {
      $dvd["id"] = intval($dvd["id"], 10);

      switch ($this->JSONUtils->updateDvd($dvd)) {
        case JSONUtils::UPDATE_DVD_INVALID_DATA:
          $return["status"]  = "0";
          $return["message"] = "The data is not valid";
          break;

        case JSONUtils::UPDATE_DVD_NOT_FOUND:
          $return["status"]  = "0";
          $return["message"] = "The ID " . $dvd["id"] . " was not found";
          break;

        case JSONUtils::UPDATE_DVD_ERROR;
          $return["status"]  = "0";
          $return["message"] = "Error writing to the database";
          break;

        case JSONUtils::UPDATE_DVD_SUCCESS:
          $return["status"]  = "1";
          $return["message"] = "DVD '". $dvd["title"] ."' updated successfully";
          break;
        default:
          $return["status"]  = "0";
          $return["message"] = "Unknown error";
          break;
      }
    }

    return $return;
  }

  /**
   * @url DELETE /dvds/$id
   */
  public function deleteDvd($id) {
    switch ($this->JSONUtils->deleteDvd($id)) {
      case JSONUtils::DELETE_DVD_SUCCESS:
        $return["status"]  = "1";
        $return["message"] = "DVD $id deleted successfully";
        break;
      case JSONUtils::DELETE_DVD_NOT_FOUND:
        $return["status"]  = "0";
        $return["message"] = "ID:$id not found!";
        break;
      case JSONUtils::DELETE_DVD_ERROR:
        $return["status"]  = "0";
        $return["message"] = "Error while deleting DVD with id $id !";
        break;
      default:
        $return["status"]  = "0";
        $return["message"] = "Unknown error";
        break;
    }

    return $return;
  }

}