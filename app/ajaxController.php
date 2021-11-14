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

/*             AUTHs    */
public function login(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $post_data = app('request')->body;
        $response = app('auth')->login($post_data);
        return $response;
    }
}

public function register(){
    
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && app('auth')->user()['role'] == 1) {
        $post_data = app('request')->body;
        $response = app('auth')->register($post_data);
        return json_response($response);
   }
}

public function checkUserID(){
    $post_data = app('request')->body;
    $test = app('auth')->checkUserName($post_data['username']);
    if(!$test){
        return json_response($test);
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
    $post_data = app('request')->body;
    $post_data['sitting'] = app('purify')->xss_clean($post_data['sitting']);
    $post_data['exams_type'] = app('purify')->xss_clean($post_data['exam_type']);
    $post_data['card_pin'] = app('purify')->xss_clean($post_data['card_pin']);
    $post_data['center'] = app('purify')->xss_clean($post_data['centre']);
    $post_data['exam_number'] = app('purify')->xss_clean($post_data['exam_number']);
    $post_data['exam_date'] = app('purify')->xss_clean($post_data['exam_date']);
    $post_data['subject'] = app('purify')->xss_clean($post_data['subject']);
    $post_data['grade'] = app('purify')->xss_clean($post_data['grade']);
   $post_data['uid'] = app('purify')->xss_clean($post_data['uid']);
    return json_response(app('wassce')->add($post_data));
}

public function deleteWassce(){
    $post_data = app('request')->body;
    $post_data['id'] = app('purify')->xss_clean($post_data['id']);
    return json_response(app('wassce')->delete($post_data['id']));
}

public function application(){
    $post_data = app('request')->body;
    if(!array_key_exists('wassce', $post_data) || empty($post_data['wassce'])){
        return json_response(array("status" => false, "message" => "You've not added your WASSCE results"));
    }
    if($post_data['category'] == 'Military' && (empty($post_data['sn']) || empty($post_data['rank']))){
        return json_response(array("status" => false, "message" => "Please fill in your Military Details"));
    }
    $post_data['first_name'] = app('purify')->xss_clean($post_data['first_name']);
    $post_data['middle_name'] = app('purify')->xss_clean($post_data['middle_name']);
    $post_data['last_name'] = app('purify')->xss_clean($post_data['last_name']);
    $post_data['email'] = app('purify')->xss_clean($post_data['email']);
    $post_data['phone'] = app('purify')->xss_clean($post_data['phone']);
    $post_data['country'] = app('purify')->xss_clean($post_data['country']);
    $post_data['state'] = app('purify')->xss_clean($post_data['state']);
    $post_data['gender'] = app('purify')->xss_clean($post_data['gender']);
    $post_data['category'] = app('purify')->xss_clean($post_data['category']);
    $post_data['sn'] = $post_data['category'] == 'Military' ? NULL : app('purify')->xss_clean($post_data['sn']);
    $post_data['rank'] = $post_data['category'] == 'Military' ? NULL : app('purify')->xss_clean($post_data['rank']);
    $post_data['first_choice'] = app('purify')->xss_clean($post_data['first_choice']);
    $post_data['second_choice'] = app('purify')->xss_clean($post_data['second_choice']);
    $post_data['jamb_reg_no'] = app('purify')->xss_clean($post_data['jamb_reg_no']);
    $post_data['jamb_score'] = app('purify')->xss_clean($post_data['jamb_score']);
    $post_data['sponsor'] = app('purify')->xss_clean($post_data['sponsor']);
    $post_data['sponsor_address'] = app('purify')->xss_clean($post_data['sponsor_address']);
    $post_data['guardian'] = app('purify')->xss_clean($post_data['guardian']);
    $post_data['guardian_address'] = app('purify')->xss_clean($post_data['guardian_address']);
    $post_data['nok'] = app('purify')->xss_clean($post_data['nok']);
    $post_data['nok_address'] = app('purify')->xss_clean($post_data['nok_address']);
    $post_data['app_number'] = empty(app('purify')->xss_clean($post_data['app_number'])) ? app('purify')->xss_clean($post_data['uid']) : app('purify')->xss_clean($post_data['app_number']);

   
    return json_response(app('application')->apply($post_data));
}

}
