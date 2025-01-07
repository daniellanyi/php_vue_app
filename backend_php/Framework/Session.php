<?php 
    
    namespace Framework;

use Exception;
use PDO;
    class Session
    {
        private static $instance = null;
        private $db;

        

        public function __construct() {
            
            
            
            $config = require \basePath('config/_db.php');
            
            $this->db = new Database($config);
            
            session_set_save_handler(
                [$this, "_open"],
                [$this, "_close"],
                [$this, "_read"],
                [$this, "_write"],
                [$this, "_destroy"],
                [$this, "_gc"]
            );
           
            
        }


        /**
         * Open
         * 
         * 
         */
        public function _open() {
            // If successful
            if ($this->db) {
                
                // Return True
                return true;
            }
            // Return False
            return false;
        }


        /**
         * Close
         */
        public function _close() {
            //PDO doesn't have an explicit close method.
            return true;
        }

        /**
         * Read
         */
        public function _read($id) {
            $params = [
                'id'=> $id
            ];
            
            $row = $this->db->query("SELECT data FROM sessions WHERE id = :id", $params)->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $data = json_decode($row["data"]);
                if (!empty($data)) {
                    return $data;
                }
            }
            return '';
            
            
        }


        /**
         * Write
         */
        public function _write($id, $data) {
            // Create time stamp
            $access = date("Y-m-d H:i:s", time());
            
            
            $data = json_encode($data); // Assuming data is an array or object
            

            $params = [
                'id'=> $id,
                'access'=>$access,
                'data'=>$data
            ];
            // Set query
            $result = $this->db->query("REPLACE INTO sessions VALUES (:id, :access, :data)", $params);

            
            if ($result) {
                return true;
            }

            return false;
        }


        /**
         * Destroy
         */
        public function _destroy($id) {

            $params = [
                'id'=>$id
            ];
            // Set query
            $result =  $this->db->query("DELETE FROM sessions WHERE id = :id", $params);

           

            // Attempt execution
            // If successful
            if ($result) {
                // Return True
                return true;
            }

            // Return False
            return false;
        }

        /**
         * Garbage Collection
         */
        public function _gc($max) {
            $max = (int) $max;
            // Calculate what is to be deemed old
            $old = time() - $max;

            $params = [
                'old'=> $old
            ];
            // Set query
            $result = $this->db->query("DELETE * FROM sessions WHERE access < :old", $params);

            // Attempt execution
            if ($result) {
                // Return True
                return true;
            }

            // Return False
            return false;
        }


        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Start the session
         * 
         * @return void
         */
        public function start()
        {
            if ((self::has('expiresAt')) && time() > (self::get('expiresAt'))) {
                $expiry = time() + 3600;
                self::reset($expiry);
            }
            
            if (session_status() == PHP_SESSION_NONE) {
                $expiry = time() + 3600;
                self::set('expiresAt', $expiry);
                session_start();
            }
           
        }
        /**
         * Set a session key/value pair
         * 
         * @param string $key
         * @param mixed $value
         * @return void
         */
        public function set($key, $value)
        {
            $_SESSION[$key] = $value;
        }

        /**
         * Get a session value by the key
         * 
         * @param string $key
         * @param mixed $default
         * @return mixed
         */
        public function get($key, $default = null)
        {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
        }

        /**
         * Check if session key exists
         * 
         * @param string $key
         * @return bool
         */
        public function has($key)
        {
            return isset($_SESSION[$key]);
        }

        /** 
         * Clear session by key
         * 
         * @param string $key
         * @return void
         */

        public function reset(int | null $expiry = null) {
            if ($expiry === null) {
                $expiry = time() + 3600;
            }
            session_unset();
            session_regenerate_id(true);
            self::set('expiresAt', $expiry);
        }

        public function clear($key)
        {
            if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            }
        }
    }




?>