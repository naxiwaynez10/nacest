<?php

namespace App;

use Auth;

/* This class is handling all the requests in the fornt end*/

class homeController
{

    function __construct()
    {
        // Verify CSFR
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!app('csfr')->verifyToken(SECRET_KEY)) {
                header('HTTP/1.0 403 Forbidden');
                exit();
            }
        }

        if (isset($_GET['view-as'])) {
            $url =  "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('Location: ' . strtok($url, '?'));
        }

        if (app('auth')->isAuthenticated() && app('auth')->user()['role'] == 4) {
            header("Location: " . route('my_student_profile'));
        }
        if (app('auth')->isAuthenticated() && app('auth')->user()['role'] == 2) {
            header("Location: " . route('dashboard'));
        }
    }

    // main index function to  load homepage
    public function index()
    {
        $data = array();
        echo app('twig')->render('index.html', $data);
    }

    public function application(){
        $data = array();
        $data['title'] = 'Start your online Application';
        $data['uid'] = randomPassword('1234567890', 8);
        echo app('twig')->render('application_form.html', $data);
    }

    // log out and destroy sessions
    public function logout()
    {
        session_destroy();
        if (isset($_COOKIE['cn_auth_key'])) {
            unset($_COOKIE['cn_auth_key']);
            setcookie('cn_auth_key', null, -1, '/');
        }
        header("Location: " . route('home'));
    }

    // load forget password page
    public function forgot_password()
    {
        if (app('request')->method == 'POST') {
            $data = array();
            $data['title'] = 'Forget Password - ' . SETTINGS['site_name'];
            $post_data = app('request')->body;
            if ($post_data && array_key_exists("email", $post_data)) {
                app('auth')->sendResetPasswordLink($post_data['email']);
            }
            echo app('twig')->render('forgot_password.html', $data);
        } else {
            $data = array();
            $data['title'] = 'Forget Password - ' . SETTINGS['site_name'];
            echo app('twig')->render('forgot_password.html', $data);
        }
    }

    // load reset password page
    public function reset_password()
    {
        if (app('request')->method == 'POST') {
            $data = array();
            $data['title'] = 'Reset Password - ' . SETTINGS['site_name'];
            $post_data = app('request')->body;
            if ($post_data && array_key_exists("password", $post_data) && array_key_exists("reset_key", $post_data)) {
                $reset_key = app('purify')->xss_clean($_GET['reset_key']);
                $validate_data = clean_and_validate("password", $post_data['password']);
                $password = $validate_data[0];
                $valid = true;
                $message = '<ul>';
                if (!$validate_data[1][0]) {
                    $valid = false;
                    foreach ($validate_data[1][1]['password'] as $each_error) {
                        $message .= "<li>" . $each_error . "</li>";
                    }
                }
                $message .= "</ul>";

                if ($valid) {
                    $reset = app('auth')->resetPassword($reset_key, $password);
                    if ($reset[0]) {
                        app('msg')->success($reset[1]);
                        header("Location: " . route('login'));
                    } else {
                        app('msg')->error($reset[1]);
                        if (isset($_GET['reset_key'])) {
                            $value = app('purify')->xss_clean($_GET['reset_key']);
                            $data['reset_key'] = $value;
                        }
                        echo app('twig')->render('reset_password.html', $data);
                    }
                } else {
                    app('msg')->error($message);
                    echo app('twig')->render('reset_password.html', $data);
                }
            }
        } else {
            $data = array();
            $data['title'] = 'Reset Password - ' . SETTINGS['site_name'];
            if (isset($_GET['reset_key'])) {
                $value = app('purify')->xss_clean($_GET['reset_key']);
                $data['reset_key'] = $value;
            }
            echo app('twig')->render('reset_password.html', $data);
        }
    }

    public function getAdmissionList(){
        $session = get_current_session();
        $data = array();
        $data['title'] = 'Current Admission List';
        $data['admission'] = app('students')->getAdmissionList(false, false, $session, false,true);
        echo app('twig')->render('admission_list.html', $data);
    }

}
