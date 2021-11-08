<?php

namespace App;


class adminController
{

    function __construct()
    {
        // Verify CSFR
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (app('csfr')->verifyToken(SECRET_KEY)) {
                header('HTTP/1.0 403 Forbidden');
                exit();
            }
        }

        // if (!app('auth')->isAuthenticated()) {
        //     header("Location: " . route('login') . '?next=' . route('dashboard'));
        // }
    }
    // Dashboard
    // function index()
    // {
    //     if (!app('auth')->isAuthenticated()) {
    //         header("Location: " . route('login'));
    //     }
    //     $data = array();
    //     echo app('twig')->render('index.html', $data);
    // }

    public function profile (){
        $data = array();
        // echo app('twig')->render('login.html', $data);
        echo app('twig')->render('student-profile.html', $data);
    }
}
