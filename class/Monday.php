<?php
/**
 * @package Email class
 *
 * @author Patricio Díaz / Legaltec
 *
 * @email  pdiazl@legaltec.cl
 *   
 */
class Monday {

    private $_token;
    private $_query;
    
    // public function setQuery($query) {
    //     $this->_query = $query;
    // }

    // public function __construct() {
    // 	try {
            
    //     	$this->_token = $configApp['tokenMonday'];
	//     } catch(PDOException $e) {
	// 		echo "Failed: " . $e->getMessage();
	// 	}

    // }
  
    public function getMondayData($query) {
        try {
            $configApp = include('../class/ConfigApp.php');
            $token = $configApp['tokenMonday'];
	        $apiUrl = $configApp['apiUrl'];

	        $headers = ['Content-Type: application/json', 'Authorization: ' . $token];

	        $data = @file_get_contents($apiUrl, false, stream_context_create([
		        'http' => [
		        'method' => 'POST',
		        'header' => $headers,
		        'content' => json_encode(['query' => $query]),
		        ]
	        ]));

	        $result = json_decode($data, true);
	        return $result;
        } catch (Exception $e) {
            die("There's an error in the query! " . $e);
        }
    
    }

}



?>