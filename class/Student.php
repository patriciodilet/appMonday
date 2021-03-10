<?php
/**
 * @package Student class
 *
 * @author TechArise Team
 *
 * @email  info@techarise.com
 *   
 */

include("DBConnection.php");
class Student 
{
    protected $db;
    private $_studentID;
    private $_firstName;
    private $_lastName;
    private $_rollNo;
    private $_className;
    private $_age;

    public function setStudentID($studentID) {
        $this->_studentID = $studentID;
    }
    public function setFirstName($firstName) {
        $this->_firstName = $firstName;
    }
    public function setLastName($firstName) {
        $this->_lastName = $firstName;
    }
    public function setRollNo($rollNo) {
        $this->_rollNo = $rollNo;
    }
    public function setClassName($className) {
        $this->_className = $className;
    }
    public function setAge($age) {
        $this->_age = $age;
    }
    public function setAddress($address) {
        $this->_address = $address;
    }

    public function __construct() {
        $this->db = new DBConnection();
        $this->db = $this->db->returnConnection();
    }

    // create Student
    public function createStudent() {
		try {
    		$sql = 'INSERT INTO student (first_name, last_name, roll_no, class, age, address, status)  VALUES (:first_name, :last_name, :roll_no, :class, :age, :address, :status)';
    		$data = [
			    'first_name' => $this->_firstName,
			    'last_name' => $this->_lastName,
			    'roll_no' => $this->_rollNo,
			    'class' => $this->_className,
			    'age' => $this->_age,
			    'address' => $this->_address,
			    'status' => 1,
			];
	    	$stmt = $this->db->prepare($sql);
	    	$stmt->execute($data);
			$status = $stmt->rowCount();
            return $status;

		} catch (Exception $e) {
    		die("Oh noes! There's an error in the query!");
		}

    }

    // update Student
    public function updateStudent() {
        try {
		    $sql = "UPDATE student SET first_name=:first_name, last_name=:last_name, roll_no=:roll_no, class=:class, age=:age, address=:address, status=:status WHERE id=:student_id";
		    $data = [
			    'first_name' => $this->_firstName,
			    'last_name' => $this->_lastName,
			    'roll_no' => $this->_rollNo,
			    'class' => $this->_className,
			    'age' => $this->_age,
			    'address' => $this->_address,
			    'status' => 1,
			    'student_id' => $this->_studentID
			];
			$stmt = $this->db->prepare($sql);
			$stmt->execute($data);
			$status = $stmt->rowCount();
            return $status;
		} catch (Exception $e) {
			die("Oh noes! There's an error in the query!");
		}
    }
   
    // getAll Student
    public function getAllStudent() {
    	try {
    		$sql = "SELECT * FROM student";
		    $stmt = $this->db->prepare($sql);

		    $stmt->execute();
		    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("Oh noes! There's an error in the query!");
		}
    }

    // get Student
    public function getStudent() {
    	try {
    		$sql = "SELECT * FROM student WHERE id=:student_id";
		    $stmt = $this->db->prepare($sql);
		    $data = [
		    	'student_id' => $this->_studentID
			];
		    $stmt->execute($data);
		    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("Oh noes! There's an error in the query!");
		}
    }

    // delete Student
    public function deleteStudent() {
    	try {
	    	$sql = "DELETE FROM student WHERE id=:student_id";
		    $stmt = $this->db->prepare($sql);
		    $data = [
		    	'student_id' => $this->_studentID
			];
	    	$stmt->execute($data);
            $status = $stmt->rowCount();
            return $status;
	    } catch (Exception $e) {
		    die("Oh noes! There's an error in the query!");
		}
    }


}
?>