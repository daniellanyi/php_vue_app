<?php 
    namespace App\Controllers;

    use BadRequestHTTPException;
    use BaseBodyValidation;
    use BaseRoute;
    use ConflictHTTPException;
    use Framework\Database;
    use Framework\Mailer;
    use Exception;
    use ForbiddenHTTPException;
    use Framework\Middleware\Auth;
    use Framework\Session;
    use GoneHTTPException;
    use HTTPException;
    use NotFoundHTTPException;
    use UnauthorizedHTTPException;
    use Throwable;
    
    


    class LoginBody extends BaseBodyValidation {
        public string $email;
        public string $password;
        public function __construct($data)
        {
            $this->registerValidator('email', ['Validators', 'validateEmail'], 'email');
            BaseBodyValidation::__construct($data);
        }
    }

    class EmailBody extends BaseBodyValidation {
        public string $email;

        public function __construct($data)
        {
            $this->registerValidator('email', ['Validators', 'validateEmail'], 'email');
            BaseBodyValidation::__construct($data);
        }
    }

    class VerifyEmailBody extends BaseBodyValidation {
        public string $code;
    }
    class CompleteSignUpBody extends BaseBodyValidation {
        public string $password;
        public string $username;
    }
    class ChangeUserPasswordBody extends BaseBodyValidation {
        public string $password;
    }


    class AuthController extends BaseRoute
    {

        private static $instance = null;

        public static function getRouter() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance->router;
        }

        protected $db;
        protected $mailer;
        protected $session;
        public function __construct()
        {
            
            $dbconfig = require basePath('config/_db.php');
            $mailerconfig = require basePath('config/_mailer.php');
            $this->db = new Database($dbconfig);
            $this->session = Session::getInstance();
            $this->mailer = new Mailer($mailerconfig);

            parent::__construct();
            $this->init();
        }

        public function init() {
            $this->router->get('/test/{id}', [$this, 'test']);
            $this->router->get('/session', [$this, 'checkUserSession']);
            $this->router->get('/resend-verification-code', [$this, 'resendCode'], [[new Auth(['guest']), 'authorize']]);
            $this->router->post('/signup', [$this, 'signUp'],[[new Auth(['guest']), 'authorize']]);
            $this->router->post('/verify-email', [$this, 'verifyEmail'], [[new Auth(['guest']), 'authorize']]);
            $this->router->post('/complete-signup', [$this, 'completeSignup'], [[new Auth(['guest']), 'authorize']]);
            $this->router->post('/login', [$this, 'login'],  [[new Auth(['guest']), 'authorize']]);
            $this->router->post('/logout', [$this, 'logout'],  [[new Auth(['auth']), 'authorize']]);
            $this->router->post('/forgot-password', [$this, 'forgotPassword'], [[new Auth(['guest']), 'authorize']]);
            $this->router->post('/change-password', [$this, 'changeUserPassword'], [[new Auth(['guest']), 'authorize']]);
        }

        
        public function test(int $id)
        {
            
            $message = 'Hello Worol ';

            echo $message . $id;
        }

        public function verifyEmail(VerifyEmailBody $data) {
            if (!$this->session->has('registration_details')) {
                throw new ForbiddenHTTPException('Register your details');
            }
            if (!password_verify($data->code, $this->session->get('email_verif_code'))) {
                throw new UnauthorizedHTTPException('Code verification failed');
            }
            if (!$this->session->has('email_verif_code_expiry') || $this->session->get('email_verif_code_expiry') < time()) {
                throw new GoneHTTPException('Code expired');
            };
            $this->session->set('validated', true);
            $this->session->clear('email_verif_code');
            $this->session->clear('email_verif_code_expiry');
            HTTPResponse(200, 'Email verified');
            return;
            
        }

        public function createUserSession($user, $responseCode) {
            $expiry = time() + 3600;
            $this->session->reset($expiry);
            $CSRFToken = bin2hex(random_bytes(32));
            $this->session->set('user', [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
              ]);
            $this->session->set('CSRFToken', $CSRFToken);
            $this->session->set('expiresAt', $expiry);
            self::sendSessionData($user, $expiry, $responseCode, $CSRFToken);

        }

        public function createUser($user) {
            try {
                $params = [
                    'email' => $user['email'],     
                    'username' => $user['username'],        
                    'password' => password_hash($user['password'], PASSWORD_DEFAULT), 
                ];
                $this->db->query('INSERT INTO users (email, username, hashed_password) VALUES (:email, :username, :password)', $params);
                return $this->db->conn->lastInsertId();
            } catch (Exception $e) {
                error_log($e);
            }
        }

        public function sendSessionData($user, $expiry, $statusCode, $CSRFToken) {
            JSONResponse($statusCode, [
                'userId' => $user['id'],
                'username' => $user['username'],
                'userEmail' => $user['email'],
                'sessionExpiry' => $expiry,
                'CSRFToken'=> $CSRFToken
            ]);

        }

        public function checkUserSession() {
            $user = $this->session->get('user');
            if (!$user) {
                error_log($user);
                throw new UnauthorizedHTTPException('Client has no session');
            }
            $expiry = $this->session->get('expiresAt');;
            $CSRFToken = bin2hex(random_bytes(32));
            $this->session->set('CSRFToken', $CSRFToken);
            self::sendSessionData($user, $expiry, 200, $CSRFToken);

        }


        

        public function completeSignup(CompleteSignUpBody $data) {
            if (!$this->session->has('validated'))
            {
                throw new UnauthorizedHTTPException('User\'s email isn\'t verified');
            }

            $user_details = $this->session->get('registration_details');
            if (self::email_exists($user_details['email'])) {
                throw new ConflictHTTPException('Email already exists');
            }

            $user_details['password'] = $data->password;
            $user_details['username'] = $data->username;

            try {
                $user_id = self::createUser($user_details);
            } catch (Exception $e) {
                error_log($e);
            }

            self::createUserSession([
                'id' => $user_id,
                'email' => $user_details['email'],
                'username' => $user_details['username']
            ], 201);
        }

        

        public function changeUserPassword(ChangeUserPasswordBody $data) {
            
            if (!$this->session->has('validated'))
            {
                throw new UnauthorizedHTTPException('User\'s email isn\'t verified');
            }
            $user_details = $this->session->get('registration_details');
            $params = [
                'email'=>$user_details['email']
            ];
            

            $user = $this->db->single("SELECT * FROM users WHERE email = :email", $params);
            
            $user_id = $user['id'];
            $params = [
                'user_id'=>$user_id,
                'hashed_password'=> password_hash($data->password, PASSWORD_DEFAULT)
            ];
            $this->db->query('UPDATE users SET hashed_password=:hashed_password WHERE id=:user_id', $params);
            
            self::createUserSession([
                'id' => $user_id,
                'email' => $user_details['email'],
                'username' => $user['username']
            ], 200);
        }

        

        public function signUp(EmailBody $data) {
            error_log($data->email);
            if (self::email_exists($data->email)) {
                throw new ConflictHTTPException('Email already exists');
            }
            
            
            $this->session->set('registration_details', [
                'email' => $data->email,
            ]);
            
            try {
                
                self::sendVerificationEmail($data->email);
            } catch (Throwable $e) {
                throw new HTTPException(500, 'Email could not be sent', $e->getMessage());
            }
            JSONResponse(200, [
                'email'=>$data->email]);
        }


        public function forgotPassword(EmailBody $data)
        {
            
            if (!self::email_exists($data->email)) {
                HTTPResponse(409, 'Email doesn\'t exists');
                return;
            }
            $this->session->set('registration_details', [
                'email' => $data->email,
            ]);
            self::sendVerificationEmail($data->email);
            
            JSONResponse(200, [
                'email'=>$data->email]);
        }


        public function email_exists($email) {
            $params = [
                'email'=> $email
            ];
            $user_record = $this->db->single("SELECT * FROM users WHERE email = :email", $params);
            if ($user_record) { 
                return true;
            }
            return false;
        }


        public function resendCode() {
            if (!$this->session->has('registration_details')) {
                throw new BadRequestHTTPException('Register your details');
            }
            $this->session->clear('email_verif_code');
            $this->session->clear('email_verif_code_expiry');
            $data = $this->session->get('registration_details');
            self::sendVerificationEmail($data['email']);
        }

        public function login(LoginBody $data) {
            $params = [
                'email' => $data->email
            ];
            
            $user = $this->db->single("SELECT * FROM users WHERE email = :email", $params);
                
           
            if (!$user) {
                throw new UnauthorizedHTTPException('Invalid credentials');
                error_log($user);
            };

            if (password_verify($data->password, $user['hashed_password'])) {
                
                self::createUserSession([
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'username' => $user['username']
                ], 200);
            } else {
                throw new UnauthorizedHTTPException('Invalid credentials');
            }

        }

        public function sendVerificationEmail($recipientEmail) {
            
            $subject = "Verification code for your email";
            $code = self::generateCode(5);
            $hashed_code = password_hash($code, PASSWORD_BCRYPT);
            $expiry = time() + 30;
            $this->session->set('email_verif_code', $hashed_code);
            $this->session->set('email_verif_code_expiry', $expiry);
            $body = loadEmailTemplate('emailVerification',
                ['verification_code' => $code]
            );
            $this->mailer->addAddress($recipientEmail);
            $this->mailer->addContent($subject, $body);
            
            try {
                $this->mailer->sendEmail();
            } catch (Throwable $e) {
                error_log($e);
                throw new Exception($e->getMessage());
            }
        }

        public function logout() {
            $user = $this->session->get('user');
            if (!$user) {
                throw new UnauthorizedHTTPException('Client has no session');
            }
            $this->session->reset();
            HTTPResponse(200, 'Successfully logged out');
        }

        private function generateCode($length) {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = '';
            
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
            
            return $code;
        }

        
        }
?>