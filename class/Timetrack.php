<?php
/**
 * @package Timetrack class
 *
 * @author Patricio Díaz
 *
 * @email  pdiazl@legaltec.cl
 *   
 */

include("DBConnection.php");
class Timetrack 
{
    protected $db;
    private $_timetrackID;
    private $_timetrack;
	private $_userEmail;
	private $_TPP;
	private $_itemName;
	private $_duration;
	private $_date;
	private $_milestone;
	private $_postText;
	private $_responseText;
	private $_lastResponseId;
	private $_creatorIdResponse;
	private $_creatorIdPost;
    private $_nameBoard;
    private $_isHolyday;

    public function setTimeTrackID($timetrackID) {
        $this->_timetrackID = $timetrackID;
    }

    public function setBoardId($boardId) {
        $this->_boardId = $boardId;
    }

	public function setItemId($itemId) {
        $this->_itemId = $itemId;
    }

	public function setUserId($userId) {
        $this->_userId = $userId;
    }

	public function setUserEmail($userEmail){
		$this->_userEmail = $userEmail;
	}

	public function setTPP($TPP){
		$this->_TPP = $TPP;
	}

	public function setItemName($itemName){
		$this->_itemName = $itemName;
	}

	public function setDuration($duration){
		$this->_duration = gmdate("H:i:s", $duration);
	}
    
	public function setMilestone($milestone){
		$this->_milestone = $milestone;
	}

	public function setPostText($postText) {
		$text =  is_array($postText) ? reset($postText) : $postText;
        $this->_postText = substr($text,0,250);
    }

	public function setResponseText($responseText) {
		$text =  is_array($responseText) ? reset($responseText) : $responseText;
        $this->_responseText = substr($text,0,250);
    }

	public function setLastResponseId($lastResponseId) {
        $this->_lastResponseId = is_array($lastResponseId) ? reset($lastResponseId) : $lastResponseId;
    }

	public function setCreatorIdResponse($creatorIdResponse) {
        $this->_creatorIdResponse = is_array($creatorIdResponse) ? reset($creatorIdResponse) : $creatorIdResponse;
    }

	public function setCreatorIdPost($creatorIdPost) {
        $this->_creatorIdPost = is_array($creatorIdPost) ? reset($creatorIdPost) : $creatorIdPost;
    }

	public function setNameBoard($nameBoard) {
        $this->_nameBoard = $nameBoard;
    }

	public function setIsHoliday($isHolyday) {
        $this->_isHolyday = $isHolyday;
    }
	
	public function setDate(){
		$this->_date = date("d-m-Y");
	}

    public function __construct() {
        $this->db = new DBConnection();
        $this->db = $this->db->returnConnection();
    }

    // create Timetrack
    public function createTimetrack() {
		try {
    		// $sql = 'INSERT INTO MondayGestionDiaria (boardId, itemId, userId, userEmail, TPP, itemName, duration, milestone, date, postText, responseText, lastResponseId, creatorIdResponse, creatorIdPost, nameBoard, isHolyDay)  VALUES (:boardId, :itemId, :userId, :userEmail, :TPP, :itemName, :duration, :milestone, :date, :postText, :responseText, :lastResponseId, :creatorIdResponse, :creatorIdPost, :nameBoard, :isHolyDay)';
    		$sql = 'INSERT INTO MondayGestionDiaria (boardId, itemId, userId, userEmail, TPP, itemName, duration, milestone, date, postText, responseText, lastResponseId, creatorIdResponse, creatorIdPost, nameBoard, isHolyday)  VALUES (:boardId, :itemId, :userId, :userEmail, :TPP, :itemName, :duration, :milestone, :date, :postText, :responseText, :lastResponseId, :creatorIdResponse, :creatorIdPost, :nameBoard, :isHolyday)';
    		$data = [
			    'boardId' => $this->_boardId,
			    'itemId' => $this->_itemId,
			    'userId' => $this->_userId,
			    'userEmail' => $this->_userEmail,
			    'TPP' => $this->_TPP,
			    'itemName' => $this->_itemName,
			    'duration' => $this->_duration,
				'milestone' => $this->_milestone,
			    'date' => $this->_date,
			    'postText' => $this->_postText,
			    'responseText' => $this->_responseText,
			    'lastResponseId' => $this->_lastResponseId,
			    'creatorIdResponse' => $this->_creatorIdResponse,
			    'creatorIdPost' => $this->_creatorIdPost,
			    'nameBoard' => $this->_nameBoard,
			    'isHolyday' => $this->_isHolyday
			];
	    	$stmt = $this->db->prepare($sql);
	    	$stmt->execute($data);
			$status = $stmt->rowCount();
            return $status;

		} catch (Exception $e) {
    		die("There's an error in the query! " . $e);
		}

    }

    // update Timetrack
    public function updateTimetrack() {
        try {
		    $sql = "UPDATE MondayGestionDiaria SET timetrack=:timetrack WHERE id=:timetrackId";
		    $data = [
			    'timetrack' => $this->_timetrack,
                'timetrackId' => $this->_timetrackId,
			];
			$stmt = $this->db->prepare($sql);
			$stmt->execute($data);
			$status = $stmt->rowCount();
            return $status;
		} catch (Exception $e) {
			die("There's an error in the query!");
		}
    }

    // getAll timetrack
    public function getAllTimetrack() {
    	try {
    		$sql = "SELECT * FROM MondayGestionDiaria";
		    $stmt = $this->db->prepare($sql);

		    $stmt->execute();
		    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }

    // get Timetrack
    public function getTimetrack() {
    	try {
    		$sql = "SELECT * FROM MondayGestionDiaria WHERE id=:timetrackId";
		    $stmt = $this->db->prepare($sql);
		    $data = [
		    	'timetrackId' => $this->_timetrackID
			];
		    $stmt->execute($data);
		    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }

    // delete Timetrack
    public function deleteTimetrack() {
    	try {
	    	$sql = "DELETE FROM MondayGestionDiaria WHERE id=:timetrackId";
		    $stmt = $this->db->prepare($sql);
		    $data = [
		    	'timetrackId' => $this->_timetrackID
			];
	    	$stmt->execute($data);
            $status = $stmt->rowCount();
            return $status;
	    } catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }
/*
	function checkHoliday($date){
        if(date('l', strtotime($date)) == 'Saturday'){
            return false;
        //   return "Saturday";
        }else if(date('l', strtotime($date)) == 'Sunday'){
            return true;
        //   return "Sunday";
        }else{
          $receivedDate = date('d M', strtotime($date));
      
          $holiday = array(
            '01 Jan' => 'New Year Day',
            '02 Apr' => 'Viernes Santo',
            '03 Apr' => 'Sabado Santo',
            '01 May' => 'Dia del Trabajador',
            '15 May' => 'Eleccion Alcaldes',
            '16 May' => 'Eleccion Alcaldes',
            '13 Jun' => 'Segunda Vuelta Gobernadores Regionales',
            '28 Jun' => 'San Pedro y San Pablo',
            '16 Jul' => 'Dia de la Virgen del Carmen',
            '18 Jul' => 'Elecciones Primarias Presidenciales',
            '15 Ago' => 'Asuncion de la Virgen', 
            '17 Sep' => 'Feriado Adicional',
            '18 Sep' => 'Independencia Nacional',
            '19 Sep' => 'Dia de las Glorias del Ejercito',
            '11 Oct' => 'Dia de la Raza',
            '31 Oct' => 'Dia de las Iglesias Evangélicas', 
            '01 Nov' => 'Dia de Todos los Santos',
            '21 Nov' => 'Elecciones Presidenciales y Parlamentarias',
            '08 Dec' => 'Inmaculada Concepcion',
            '19 Dec' => 'Segunda Vuelta Elecciones Presidenciales',
            '25 Dec' => 'Navidad',
            '31 Dec' => 'Feriado Bancario'
          );
      
          foreach($holiday as $key => $value){
            if($receivedDate == $key){
                return true;
            //   return $value;
            }
          }
        }
      }
*/

}
?>