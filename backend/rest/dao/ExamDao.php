<?php

class ExamDao {

    private $conn;

    /**
     * constructor of dao class
     */
    public function __construct(){
        try {
          /** TODO
           * List parameters such as servername, username, password, schema. Make sure to use appropriate port
           */
          $host = 'localhost';
          $port = '3306';
          $dbName = 'webfinal';
          $username = 'root';
          $password = '';
          
         
          /** TODO
           * Create new connection
           */
          $this->conn = new PDO("mysql:host=$host;port=$port;dbname=$dbName;",$username,$password);
          
          $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          echo "Connected successfully";
        } catch(PDOException $e) {
          echo "Connection failed: " . $e->getMessage();
        }
    }

    /** TODO
     * Implement DAO method used to get customer information
     */
    public function get_customers(){

      $sql = "SELECT c.first_name , c.last_name , c.birth_date
      FROM customers c;" ;
      
      $stmt = $this->conn->prepare($sql);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    /** TODO
     * Implement DAO method used to get customer meals
     */
    public function get_customer_meals($customer_id) {

      $sql = "SELECT c.id , f.name AS food_name , f.brand AS food_brand, m.created_at AS meal_date
      FROM foods f
      JOIN meals m ON f.id = m.food_id
      JOIN customers c ON m.customer_id = c.id
      WHERE c.id = $customer_id" ;
      
      $stmt = $this->conn->prepare($sql);
      $stmt -> execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);


    }

    /** TODO
     * Implement DAO method used to save customer data
     */
    public function add_customer($first_name,$last_name,$birth_date){
      
      $sql = "INSERT INTO customers(first_name,last_name,birth_date) 
      VALUES(:first_name, :last_name, :birth_date)";

      $stmt = $this->conn->prepare($sql);

      $stmt -> bindParam(':first_name',$first_name);
      $stmt -> bindParam(':last_name' , $last_name);
      $stmt -> bindParam(':birth_date',$birth_date);

      $stmt -> execute();

    }

    /** TODO
     * Implement DAO method used to get foods report
     */
    public function get_foods_report(){

      $sql = "SELECT f.name,f.brand,f.image_url AS image,
      MAX(CASE WHEN n.name = 'energy' THEN fn.quantity ELSE 0 END) AS energy,
      MAX(CASE WHEN n.name = 'protein' THEN fn.quantity ELSE 0 END) AS protein,
      MAX(CASE WHEN n.name = 'fat' THEN fn.quantity ELSE 0 END) AS fat,
      MAX(CASE WHEN n.name = 'fiber' THEN fn.quantity ELSE 0 END) AS fiber,
      MAX(CASE WHEN n.name = 'carbs' THEN fn.quantity ELSE 0 END) AS carbs
      FROM foods f
      LEFT JOIN food_nutrients fn ON f.id = fn.food_id
      LEFT JOIN nutrients n ON fn.nutrient_id = n.id
      GROUP BY f.id
      LIMIT 10 OFFSET 0; " ;
      
      $stmt = $this->conn->prepare($sql);
      $stmt -> execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
}
?>
