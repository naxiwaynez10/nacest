<?php
namespace App;

class staffsController
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

        if(app('auth')->isAuthenticated() && app('auth')->user()['role'] == 4){
            header("Location: " . route('my_student_profile'));
        }
        elseif(!app('auth')->isAuthenticated()){
            header("Location: " . route('index'));
        }
        
        if (isset($_GET['view-as'])) {
            $url =  "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('Location: ' . strtok($url, '?'));
        }
    }

    public function index(){
        $data = array();
        $data['title'] = "Staff Dashboard";
        $data['faculty_count'] = app('departments')->countFaculty();
        $data['dept_count'] = app('departments')->count();
        $data['military_students_count'] = count(app('students')->getAdmissionList(false, false, false, "Military"));
        $data['military_admission_count'] = count(app('students')->getAdmissionList(false, false, false, "Military", true));
        // $data['civilian_admission_count'] = count(app('students')->getAdmissionList(false, false, false, "Civilian"));
        $data['students_count'] = app('students')->getStudentsCount();
        echo app('twig')->render('dashboard.html', $data);
    }

    //For HODs
    public function regStatus(){
        $data = array();
        $user = app('auth')->user();
       if($user['role'] == 2){ 
            $dept = app('department')->getName($user['dept']);
            $session = get_current_session();
            $data['students'] = app('registration')->getStudents($dept, false, $session);
            $data['title'] = "Registered Students for the department of ".$dept;
            $data['department'] = $dept;
            $data['code'] = $user['dept'];
            $data['session'] = $session;
            echo app('twig')->render('dept_reg.html', $data);
        }
    }



    public function manageCO(){

    }

    public function allDepartmentalCourses(){
        $data = array();
        $user = app('auth')->user();
        $dept = app('department')->getName($user['dept']);
        $data['title'] = "All Courses for the department of ".$dept;
        $course = app('departments')->getCourses($dept, false, false);
        $data['code'] = $user['dept'];
        $data['courses'] = $course;
    }

    public function semesterDepartmentalCourses(){
        $data = array();
        $user = app('auth')->user();
        $dept = app('department')->getName($user['dept']);
        $data['title'] = "All Courses for the department of ".$dept;
        $course = app('departments')->getCourses($dept, false, 1);
        $data['code'] = $user['dept'];
        $data['courses'] = $course;
    }

    public function deptAdmissionList(){
        $data = array();
        $user = app('auth')->user();
        $session = get_current_session();
        $dept = app('department')->getName($user['dept']);
        $data['title'] = "Admission List for the department of ".$dept;
        $list = app('students')->getAdmissionList($user['dept'], $session);
        $data['code'] = $user['dept'];
        $data['list'] = $list;
        echo app('twig')->render('dept_admission_list.html', $data);
    }

    public function deptStudentList(){
        $data = array();
        $user = app('auth')->user();
        $session = get_current_session();
        $dept = app('department')->getName($user['dept']);
        $data['title'] = "List of Students in the department of ".$dept;
        $list = app('students')->getStudentsInDepartment($user['dept'], false, $session);
        $data['code'] = $user['dept'];
        $data['students'] = $list;
        echo app('twig')->render('dapt-student-list.html', $data);
    }

    // public function deptApplicationList(){
    //     $data = array();
    //     $user = app('auth')->user();
    //     $session = get_current_session();
    //     $dept = app('department')->getName($user['dept']);
    //     $data['title'] = "List of Application for the department of ".$dept;
    //     $list = app('students')->getStudentsInDepartment($user['dept'], $session);
    //     $data['code'] = $user['dept'];
    //     $data['courses'] = $list;
    //     echo app('twig')->render('application_list.html', $data);
    // }

    public function resultsUpload(){

    }

    // End of HODs

    // For DICT

    public function allCourses(){
        $data = array();
        $user = app('auth')->user();
        if($user['role'] != 1) return;
        $data['title'] = "All courses in the School";
        $list = app('courses')->getAll();
        $data['courses'] = $list;
        echo app('twig')->render('all_courses.html', $data);
    }

    // public function semesterCourses(){
    //     $data = array();
    //     $user = app('auth')->user();
    //     if($user['role'] != 1) return;
    //     $data['title'] = "All courses in the School";
    //     $list = app('courses')->getAll();
    //     $data['courses'] = $list;
    //     echo app('twig')->render('all_courses.html', $data);
    // }

    public function AdmissionList(){
        $data = array();
        $session = get_current_session();
        $data['title'] = "All Admission List";
        $list = app('students')->getAdmissionList(false, $session);
        $data['list'] = $list;
        echo app('twig')->render('admission_list.html', $data);
    }

    public function StudentList($level){
        $data = array();
        $session = get_current_session();
        $data['title'] = "List of Students in the school";
        if($level){
            $list = app('students')->getAll(false, $level, $session);
        }
        else{
            $list = app('students')->getAll(false, false, $session);
        }
        $data['students'] = $list;
        $data['level'] = $level;
        echo app('twig')->render('students-list.html', $data);
    }

    public function ApplicationList($code){
        $data = array();
        $data['title'] = "All Application List";
        $data['Applications'] = app('application')->getAll();
        echo app('twig')->render('application_list.html', $data);
    }

    public function paymentHistory(){
        $data = array();
        $data['title'] = "All Payment History";
        $data['payments'] = app('payments')->getHistory();
        echo app('twig')->render('payment_history.html', $data);
    }

    public function paymentDetails($code){

    }

    public function studentProfile($id){
        $data = array();
        $data['title'] = "Student's Profile";
        $data['student'] = app('students')->get($id);
        echo app('twig')->render('student_profile.html', $data);
    }

    public function editStudent(){
        $data = array();
        $data['title'] = "Edit Students Profile";
        echo app('twig')->render('edit_student.html', $data);
    }
    
}