<?php
namespace App;

/* This class is handling all the ajax requests */

class ajaxController{

function __construct() {
    // Verify CSFR
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(! app('csfr')->verifyToken(SECRET_KEY) ){
            header('HTTP/1.0 403 Forbidden');
            exit();
        }
    }
    
}

/* FOR STUDENTS AREA */

public function updateRecord(){

}

public function addHistory(){

}
public function removeCourse(){

}
public function addCourse(){


}
public function addWassce(){

}



}
