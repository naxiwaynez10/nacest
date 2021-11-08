<?php

namespace App;

// use Student;
/* Handles all users requests */
class studentsController
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

    public function index(){
        $data = array();
        $data['title'] = "My profile";
        // $data['student'] = app('students')->get();
        echo app('twig')->render('student-profile.html', $data);
    }

    // public function edit(){
    //     $data = array();
    //     $data['title'] = "Edit My profile";
    //     $data['student'] = app('students')->get();
    //     echo app('twig')->render('edit_profile.html', $data);
    // }


    public function academicHistory(){
        $data = array();
        $data['title'] = "Add Academic History";
        $data['history'] = app('students')->getAcademicHistory();
        echo app('twig')->render('academic_history.html', $data);
    }

 

    public function wassce(){
        $data = array();
        $data['title'] = "Add O'level Results Record";
        $exams = app('students')->getWassceRecords();
        if(isset($exams['exam2'])){
            // There are two sittings
            $data['courses1'] = app('students')->getSubjects($exams['exam1']['matric'], $exams['exam1']['exam_number']);
            $data['courses2'] = app('students')->getSubjects($exams['exam2']['matric'], $exams['exam2']['exam_number']);
        }
        else{
            $data['courses1'] = app('students')->getSubjects($exams['matric'], $exams['exam_number']);
        }
        
        echo app('twig')->render('add_wassce.html', $data);
    }

    

    public function courseReg(){
        $student = app('students')->get();
        $dept = $student['dept'];
        $matric = $student['matric'];
        $sion = date('Y');
        $ses = $sion - 1;
        $session = $ses."/".$sion;
        $data = array();
        $data['reg_courses'] = app('registration')->get($matric, $session);
        $data['title'] = 'Register Courses '.$session;
        $data['courses'] = app('courses')->retrieve($dept, $session);
        echo app('twig')->render('course_reg.html', $data);
    }

    /// Printables

    public function examscard($id = false){

    }

    public function admissionletter($matric = false){

    }

    public function acceptanceform($matric = false){

    }

    public function guarantorform($matric = false){

    }

    public function profile($matric = false){

    }

    public function feespayable(){

    }
}