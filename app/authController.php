<?php

namespace App;

use Auth;

class authController{
    function __construct(){
        if(app('auth')->isAuthenticated() && app('auth')->user()['role'] != 8){
            if(app('auth')->user()['role'] == 4){
                header('Location: '.route('my_student_profile'));
            }
            if(app('auth')->user()['role'] < 9){
                if(app('auth')->user()['role'] !=1){
                    header('Location: '.route('dashboard'));
                }
                
            }
            
        }
       if (isset($_GET['view-as'])) {
            $url =  "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('Location: ' . strtok($url, '?'));
        } 
    }

    public function reset_password(){
        $data = array();
        $data['title'] = "Sign in to your profile";
        echo app('twig')->render('login.html', $data);
    }

    public function register(){
        // if(app('auth')->user()['role'] != 1) header('Location: '.route('login'));
        $data = array();
        $data['title'] = "Add a new User";
        echo app('twig')->render('register.html', $data);
    }

    public function logout(){
        app('auth')->logout();
    }

    public function changePassword(){

    }

    // public function reset_password(){

    // }

}