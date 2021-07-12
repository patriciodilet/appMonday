<?php
/**
 * @package Email class
 *
 * @author Patricio Díaz / Legaltec
 *
 * @email  pdiazl@legaltec.cl
 *   
 */
include_once ("DBConnection.php");
 class EmailTemplate
 {
    private $_id;
    private $_type;
    private $_title;
    private $_content;
    private $_created;
    private $_modified;
    private $_status;

    public function setId($id) {
        $this->_id = $id;
    }

    public function setType($type) {
        $this->_type = $type;
    }

    public function setTitle($title) {
        $this->_title = $title;
    }

    public function setContent($content) {
        $this->_content = $content;
    }

    public function setCreated() 
    {
        $dataTime= date("Y-m-d H:i:s");
        $this->_created = $dataTime;
    }

    public function setModified() 
    {
        $dataTime= date("Y-m-d H:i:s");
        $this->_modified = $dataTime;
    }

    public function setStatus() 
    {
        $this->_status = true; // active
    }

    public function __construct() {
        $this->db = new DBConnection();
        $this->db = $this->db->returnConnection();     
    }

    public function createEmailTemplate() {
        try {
            $sql = 'INSERT into MondayGestionDiaria.EmailTemplate (type, title, content, created, modified, status) VALUES (:type, :title, :content, :created, :modified, :status)';
            $data = [
                'type' => $this->_type,
                'title' => $this->_title,
                'content' => $this->_content,
                'created' => $this->_created,
                'modified' => $this->_modified,
                'status' => $this->_status
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
    public function updateEmailTemplate() {
        try {
		    $sql = "UPDATE MondayGestionDiaria.EmailTemplate SET type=:type, title=:title, content=:content WHERE id=:id";
		    $data = [
                'type' => $this->_type,
                'title' => $this->_title,
                'content' => $this->_content,
			    'id' => $this->_id
			];
			$stmt = $this->db->prepare($sql);
			$stmt->execute($data);
			$status = $stmt->rowCount();
            return $status;
		} catch (Exception $e) {
			die("There's an error in the query!");
		}
    }

    public function getEmailTemplateById() {
    	try {
    		$sql = "SELECT * FROM MondayGestionDiaria.EmailTemplate WHERE id = :id";
		    $stmt = $this->db->prepare($sql);
		    $data = [
		    	'id' => $this->_id
			];
		    $stmt->execute($data);
		    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }

    public function getEmailTemplateData() {
    	try {
    		$sql = "SELECT * FROM MondayGestionDiaria.EmailTemplate";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
		    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }

    public function getEmailTemplate() {
    	try {
    		$sql = "SELECT * FROM MondayGestionDiaria.EmailTemplate WHERE type = :type";
		    $stmt = $this->db->prepare($sql);
		    $data = [
		    	'type' => $this->_type
			];
		    $stmt->execute($data);
		    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }

    
    public function getEmailData($type, $params) {
        $pattern = '[%s]';
        foreach($params as $key=>$val){
            // concatenate pattern & val (space between words)
            $paramsPattern[sprintf($pattern,$key)] = $val;
        }

	    $this->setType($type);
		$dataTemplate = $this->getEmailTemplate();

        $emailContent = strtr($dataTemplate['content'], $paramsPattern);
        $subject = strtr($dataTemplate['title'],$paramsPattern);

        return [
            'emailContent' => $emailContent,
            'subject' => $subject
        ];
    }

    



 }

?>