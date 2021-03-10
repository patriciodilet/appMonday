<?php
/**
 * @package PHP Rest API(DBConnection)
 *
 */

// Database Connection
class DBConnection {
    private $_dbHostname = "3.211.203.97";
    private $_dbName = "MondayGestionDiaria";
    private $_dbUsername = "legaltec";
    private $_dbPassword = "wSHjV&nqD8";
    private $_con;

    public function __construct() {
    	try {
        	$this->_con = new PDO("mysql:host=$this->_dbHostname;dbname=$this->_dbName", $this->_dbUsername, $this->_dbPassword);    
        	$this->_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    } catch(PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}

    }
    // return Connection
    public function returnConnection() {
        return $this->_con;
    }
}
?>