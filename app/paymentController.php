<?php

namespace App;

use Auth;

/* Handles all payment requests */
class paymentController
{
    public function make(){
        $request = app('payments')->paynow();

        // echo json_response($request['data']);
       
    }
}