<?php

/* user authentication class */

class Auth
{

    function __construct()
    {
        if (isset($_COOKIE['cn_auth_key']) && !isset($_SESSION['user'])) {
            try {
                app('db')->where('auth_key', $_COOKIE['cn_auth_key']);
                $user = app('db')->getOne('users');
                if ($user) {
                    $passwprd_verify = password_verify($user['password'], $_COOKIE['cn_auth_key']);
                    if ($passwprd_verify) {
                        $this->updateLastLogin($user['user_id']);
                        $_SESSION['user'] = $this->user($user['id']);
                        //if(DISABLE_MULTIPLE_SESSIONS){
                        $auth_key = password_hash($user['password'], PASSWORD_DEFAULT);
                        $data = array();
                        $data['auth_key'] = $auth_key;
                        app('db')->where('id', $user['id']);
                        app('db')->update('users', $data);
                        setcookie('cn_auth_key', $auth_key, time() + (86400 * 30), "/");
                        //}
                    }
                }
            } catch (Exception $e) {
                //pass
            }
        }
    }

    // Check whether user is logged in
    public function isAuthenticated()
    {
        if (isset($_SESSION['user'])) {
            return true;
        } else {
            return false;
        }
    }

    // Log in user by email and password
   /*
    public function authenticate($email, $password)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            app('db')->where('email', $email);
            $valid_email = true;
            $valid_username = false;
        } else {
            app('db')->where('username', $email);
            $valid_username = true;
            $valid_email = false;
        }
        if ($valid_email || $valid_username) {
            if ($user = app('db')->getOne('users')) {
                if ($user['status'] == 1) {
                    if ($hybridauth) {
                        $passwprd_verify = true;
                    } else {
                        $passwprd_verify = password_verify($password, $user['password']);
                    }
                    if ($passwprd_verify) {
                        $this->updateLastLogin($user['id']);
                        $_SESSION['user'] = $this->user($user['id']);
                        if ($user['auth_key'] == '') {
                            $auth_key = password_hash($user['password'], PASSWORD_DEFAULT);
                            $data = array();
                            $data['auth_key'] = $auth_key;
                            app('db')->where('id', $user['id']);
                            app('db')->update('users', $data);
                            setcookie('cn_auth_key', $auth_key, time() + (86400 * 30), "/");
                        } else {
                            setcookie('cn_auth_key', $user['auth_key'], time() + (86400 * 30), "/");
                        }
                        return true;
                    } else {
                        //return 'hello';
                        // Wrong Password
                        app('msg')->error(__('Wrong Password!'));
                        return false;
                    }
                } else if ($user['status'] == 3) {
                    // Pending account
                    app('msg')->error(__('Your account is Suspended!'));
                    //app('msg')->info(__('<a href="'.route('resend-activation').'">Resend Activation Email</a>'));
                    return false;
                } else {
                    // Inactive account
                    app('msg')->error(__('Your account is disabled!'));
                    return false;
                }
            } else {
                // Wrong Email
                app('msg')->error(__('Wrong Email or Username!'));
                return false;
            }
        } else {
            app('msg')->error(__('Email or Username is invalid!'));
            return false;
        }
    }
*/

    public function authenticate($user_id, $password)
    {       app('db')->where('user_id', $user_id);
            $user = app('db')->getOne('users');
            if (!$user) {
                return false;
            }
            if (password_verify($password, $user['password'])) {
                // User is ok
                $_SESSION['user'] = $this->user($user['id']);
                if ($user['auth_key'] == '') {
                    $auth_key = password_hash($user['password'], PASSWORD_DEFAULT);
                    $data = array();
                    $data['auth_key'] = $auth_key;
                    app('db')->where('id', $user['id']);
                    app('db')->update('users', $data); 
                    setcookie('cn_auth_key', $auth_key, time() + (86400 * 30), "/");
                } else {
                    setcookie('cn_auth_key', $user['auth_key'], time() + (86400 * 30), "/");
                }
                if($user['last_login'] == ''){
                    exit($user['last_login']);
                } 
                $this->updateLastLogin($user['user_id']);
                return true;
            }
            return false;
    }
    public function login($post_data)
    {
        if ($post_data && array_key_exists('userid', $post_data) && array_key_exists('password', $post_data)) {
            $login = $this->authenticate($post_data['userid'], $post_data['password']);
            if ($login) {
                if (isset($_GET['next'])) {
                    if (filter_var($_GET['next'], FILTER_VALIDATE_URL) != false) {
                        header("Location: " . $_GET['next']);
                    } else {
                       if(app('auth')->user()['role'] == 4){
                        header("Location: " . route('my_student_profile'));
                       }
                       else{
                         header("Location: " . route('dashboard'));
                       }
                       
                    }
                } else {
                    if(app('auth')->user()['role'] == 4){
                        header("Location: " . route('my_student_profile'));
                    }
                    else{
                        header("Location: " . route('dashboard'));
                    }
                    
                }
            } else {
                $msg = array(
                    'message' => 'Wrong username or password'
                );
                return json_response($msg);
                // header("Location: " . route('login'));
            }
        } 
    }
    // load register page
    public function register($post_data)
    {
        if ($post_data && array_key_exists('userid', $post_data) && array_key_exists('password', $post_data) && array_key_exists('email', $post_data)) {
            $username = app('purify')->xss_clean($post_data['userid']);
            $email = app('purify')->xss_clean($post_data['email']);
            $pass = app('purify')->xss_clean($post_data['password']);
            $first_name = app('purify')->xss_clean($post_data['first_name']);
            $last_name = app('purify')->xss_clean($post_data['last_name']);
            $role = app('purify')->xss_clean($post_data['role']);

            if (!empty($email) || !empty($pass) || !empty($username)) {
                // All correct
                $user_id = $this->isAuthenticated() ? app('auth')->user()['id'] : '1';
                $user = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'user_id' => $username,
                    'password' => password_hash($pass, PASSWORD_BCRYPT),
                    'email' => $email,
                    'role' => $role,
                    'date_added' => app('db')->now(),
                    'date_modified' => app('db')->now(),
                    'modified_by' => $user_id
                );
                if (app('db')->insert('users', $user)) {
                    $msg = array();
                    $msg['message'] = 'User added successfully';
                    $msg['status'] = true;
                    return $msg;
                } else {
                    $msg = array();
                    $msg['message'] = 'An error occured';
                    $msg['status'] = false;
                    return $msg;
                }
            } else {
                $msg = array();
                $msg['message'] = 'All fields are required';
                $msg['status'] = false;
                return $msg;
            }
        }
    }




    // Send password reset link with a reset key
    function sendResetPasswordLink($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            app('db')->where('email', $email);
            $user_email_exist = app('db')->getOne('users');
            if ($user_email_exist) {
                $reset_key = uniqid('fc_', true);
                $data = array();
                $data['reset_key'] = $reset_key;
                app('db')->where('email', $email);
                app('db')->update('users', $data);
                $email_data['reset_link'] = route('reset-password') . '?reset_key=' . $reset_key;
                $body = app('twig')->render('emails/password_reset.html', $email_data);
                send_mail($email, SETTINGS['site_name'] . ' Password Reset', $body);
                app('auth')->logIP($email, 3, 'Success');
            } else {
                app('auth')->logIP($email, 3, 'Failed');
            }
            app('msg')->success(__('If the provided email is on our database, We have sent a password reset link.'));
            return [true, __('Email sent!')];
        } else {
            app('msg')->error(__('Email is invalid!'));
            return [false, ''];
        }
    }

    // Reset password if the reset key is valid
    function resetPassword($reset_key, $password)
    {
        app('db')->where('reset_key', $reset_key);
        $user_exist = app('db')->getOne('users');
        if (empty($password)) {
            return [false, __('Empty Password')];
        } elseif (empty($reset_key)) {
            return [false, __('Empty Reset Key')];
        } elseif (!$user_exist) {
            return [false, __('Wrong Reset Key')];
        } else {
            $data = array();
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            $data['reset_key'] = '';
            $data['auth_key'] = null;
            app('db')->where('reset_key', $reset_key);
            app('db')->update('users', $data);
            return [true, __('Password Reseted Successfully')];
        }
    }

    function checkUserName($user_name)
    {
        if(filter_var($user_name, FILTER_VALIDATE_EMAIL)){
            app('db')->where('email', $user_name);
            $msg = array(
                'message' => 'Email already exist',
                'status' => false
            );
        }
        else{
            app('db')->Where('user_id', $user_name);
            $msg = array(
                'message' => 'Username already exist',
                'status' => false
            );
        }
        if (app('db')->getOne('users')) {
            return $msg;
        } else {
            return false;
        }
    }

    function updatePushDevices($post_data, $user_id)
    {
        app('db')->where('user_id', $user_id);
        $user_push_devices = app('db')->get('push_devices');
        if ($user_push_devices) {
            foreach ($user_push_devices as $each_device) {
                $push_device_data = array();
                $push_device_data['perm_group'] = 0;
                $push_device_data['perm_private'] = 0;
                $push_device_data['perm_mentions'] = 0;
                $push_device_data['perm_notice'] = 0;
                if (array_key_exists("perm_group_" . $each_device['id'], $post_data)) {
                    $push_device_data['perm_group'] = 1;
                }

                if (array_key_exists("perm_private_" . $each_device['id'], $post_data)) {
                    $push_device_data['perm_private'] = 1;
                }

                if (array_key_exists("perm_mentions_" . $each_device['id'], $post_data)) {
                    $push_device_data['perm_mentions'] = 1;
                }

                if (array_key_exists("perm_notice_" . $each_device['id'], $post_data)) {
                    $push_device_data['perm_notice'] = 1;
                }
                app('db')->where('id', $each_device['id']);
                app('db')->update('push_devices', $push_device_data);
            }
        }
    }

    function logIP($email, $type, $message)
    {
        if (isset(SETTINGS['enable_ip_logging']) && SETTINGS['enable_ip_logging'] == true) {
            $data = array();
            $data['ip'] = getClientIP();
            $geoip = getGeoIP($data['ip']);
            $data['country'] = $geoip['country_code'];
            $data['email'] = $email;
            $data['type'] = $type;
            $data['message'] = $message;
            $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $data['time'] = app('db')->now();
            app('db')->insert('ip_logs', $data);
        }
    }



    function isIPBlocked()
    {
        if (isset(SETTINGS['enable_ip_blacklist']) && SETTINGS['enable_ip_blacklist'] == true) {
            $ip = getClientIP();
            if ($ip) {
                $blacklist = preg_replace('/\s/', '', SETTINGS['ip_blacklist']);
                $blacklist = explode(',', $blacklist);
                if (isAllowedIp($ip, $blacklist)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }


    // Send password reset link with a reset key
    function sendEmailVerificationLink($user, $activation_key)
    {
        app('db')->where('id', $user);
        $user_data = app('db')->getOne('users');
        if ($user_data) {
            $email_data = array();
            $email_data['activation_link'] = route('activate') . '?activation_key=' . $activation_key;
            $body = app('twig')->render('emails/activate.html', $email_data);
            send_mail($user_data['email'], SETTINGS['site_name'] . ' Activate Your Account', $body);
        }
        app('msg')->success(__('Activation link has been sent! Please check your inbox.'));
        return [true, __('Email sent!')];
    }

    function updateLastLogin($user)
    {
        $data = array();
        $data['last_login'] = app('db')->now();
        app('db')->where('user_id', $user);
        app('db')->update('users', $data);
    }

    public function get_user_by_id($id)
    {
        if ($id) {
            $user_data = array();
            
            app('db')->where('id', $id);
            $user_data = app('db')->getOne('users');
            $user_data['photo'] = false;
            if($user_data['role'] == 4 || $user_data['role'] == 8){
                //Its a student
                app('db')->Where('matric', $user_data['user_id']);
                $student = app('db')->getOne('students');
                $user_data['photo'] = $student['photo'] ? $student['photo'] : false;
            }
            if ($user_data['photo']) {
                $user_data['avatar_url'] = URL . "assets/media/avatars/" . $user_data['photo'];
            } else {
                if ($user_data['role'] == 9) {
                    $user_data['avatar_url'] = URL . "assets/users/img/user.png";
                } else {
                    $user_data['avatar_url'] = URL . "assets/media/letters/" . strtoupper($user_data['first_name'][0]) . '.svg';
                }
            }
            unset($user_data['password']);
            return $user_data;
        }
    }


    // Get user data
    public function user($id = false)
    {
        if ($id) {
            return $this->get_user_by_id($id);
        } else {
            if (isset($_SESSION['user'])) {
                return $_SESSION['user'];
            } else {
                return false;
            }
        }
    }
}
