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
    }

    // main index function to  load homepage
    public function index()
    {
        // if (!app('auth')->isAuthenticated()) {
        //     header("Location: " . route('dashboard'));
        // }

        $data = array();
        // echo app('twig')->render('login.html', $data);
        echo app('twig')->render('dashboard.html', $data);
    }

    // login page
    public function login()
    {
        if (app('request')->method == 'POST') {
            $post_data = app('request')->body;
            $data = app('auth')->login($post_data);
            // echo app('twig')->render('login', $data);
            header("Location: " . route('login'));
        }
       
    }

    public function register(){
        if (app('request')->method == 'POST') {
            $post_data = app('request')->body;
            $data = app('auth')->register($post_data);
            echo app('twig')->render('register.html', $data);
        }
        else {
            if(app('auth')->isAuthenticated()){
                header("Location: ". route('dashboard'));
            }
            $data = array();
            echo app('twig')->render('register.html');
        }
    }

    // log out and destroy sessions
    public function logout()
    {
        session_destroy();
        if (isset($_COOKIE['cn_auth_key'])) {
            unset($_COOKIE['cn_auth_key']);
            setcookie('cn_auth_key', null, -1, '/');
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header("Location: " . route('login'));
        }
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

}
