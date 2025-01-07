<?php
    namespace Framework\Middleware;
    use Request;
    use Framework\Session;
    use Framework\Database;
    use UnauthorizedHTTPException;
    use ForbiddenHTTPException;
    class Auth
    {
        protected $session;
        protected $db;
        protected $roles;
        

        public function __construct(array $roles) {
            $dbconfig = require basePath('config/_db.php');
            $this->db = new Database($dbconfig);
            $this->session = Session::getInstance();
            $this->roles = $roles;
        }

        /**
         * Check if user is authenticated
         * 
         * @return bool
         */
        public function isAuthenticated() {
            if ($this->session->has('user')) {
                 $user_data = $this->session->get('user');
                 $params = [
                    'user_id' => $user_data['user_id']
                 ];
                 $user = $this->db->single("SELECT * FROM users WHERE id = :user_id", $params);
                 if ($user) return true;
            }
            return false;
        }


        public function authorize(Request $request) {
            foreach ($this->roles as $role) {
                $this->handle($role);
            }
            return $request->args;
        }

        /**
         * Check if request is authorized
         * 
         * @param string $role
         * @return bool
         */
        public function handle($role)
        {
            if ($role === 'guest' && $this->isAuthenticated()) {
                throw new ForbiddenHTTPException('The user is already logged in');
            } elseif ($role === 'auth' && !$this->isAuthenticated()) {
                throw new UnauthorizedHTTPException('The user needs to be logged in to use this resource');
            }
        }

    }



?>