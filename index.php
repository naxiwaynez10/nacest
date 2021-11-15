<?php
session_start();
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', __DIR__ . DS);

require BASE_PATH.'vendor/autoload.php';
require BASE_PATH.'config/init.php';

if (file_exists(BASE_PATH.'config/settings.php')) {

    // Include shortcut functions used in template htmls.
    require BASE_PATH.'config/template_functions.php';

    // URLs
    

    /* Auth Controller Routes   */
    $route->any('/', 'App\homeController@index')->as('home');
    $route->any('/register', 'App\authController@register')->as('register');
    $route->any('/logout', 'App\homeController@logout')->as('logout');
    $route->any('/forgot-password', 'App\authController@forgot_password')->as('forgot-password');
    $route->any('/reset-password', 'App\authController@reset_password')->as('reset-password');
    $route->any('/activate', 'App\authController@activate')->as('activate');
    $route->any('/resend-activation', 'App\authController@resend_activation')->as('resend-activation');
    
    // Auth ajaxController routes
    $route->any('/ajax/login', 'App\ajaxController@login')->as('ajax_login');
    $route->any('/ajax/check-id', 'App\authController@checkUserID')->as('ajax_checkId');
    $route->any('/ajax/register', 'App\ajaxController@register')->as('ajax_register');

    // Students Controller routes
    $route->any('/student/profile', 'App\studentsController@index')->as('my_student_profile');
    $route->any('/student/history/add', 'App\studentsController@academicHistory')->as('academic_history');
    $route->any('/student/olevel/add', 'App\studentsController@wassce')->as('olevel');
    $route->any('/student/course/reg', 'App\studentsController@courseReg')->as('registercourse');
    $route->any('/student/get/profile', 'App\studentsController@getProfile')->as('getprofile');
    $route->any('/student/regform', 'App\studentsController@getRegForm')->as('getregform');
    $route->any('/student/admisionletter', 'App\studentsController@getadmissionLetter')->as('getadmissionletter');
    $route->any('/student/guarantorform', 'App\studentsController@getGuarantorForm')->as('getguarantorform');
    $route->any('/student/regform', 'App\studentsController@getRegForm')->as('getregform');
    $route->any('/course/register', 'App\studentsController@regcourse')->as('regcourse');
    //Payment
    $route->any('/fees/paynow', 'App\paymentController@make')->as('makepayment');

    
    //Non-users controllers routes
    $route->any('/ajax/application/apply', 'App\ajaxController@application')->as('ajax-submit-application');
    $route->any('/ajax/wassce/add', 'App\ajaxController@addWassce')->as('ajax-add-wassce');
    $route->any('/ajax/wassce/delete', 'App\ajaxController@deleteWassce')->as('ajax-delete-wassce');
    $route->any('/admission/list', 'App\homeController@admissionList')->as('admissionlistout');

    // Students ajaxController Routes
    $route->any('/ajax/course/find', 'App\studentsController@addCourse')->as('findcourse');
    $route->any('/ajax/course/add', 'App\studentsController@addCourse')->as('addcourse');
    $route->any('/ajax/course/remove', 'App\studentsController@removeCourse')->as('removecourse');
    $route->any('/ajax/reg/submit', 'App\studentsController@saveReg')->as('savereg');

    $route->any('/ajax/update/profile', 'App\studentsController@updateInfo')->as('updateprofile');

    // Staff General Routes 
    $route->any('/dashboard', 'App\staffsController@index')->as('dashboard');
    
    // Staffs Controller routes ( HOD )
    $route->any('/department/student/reg', 'App\staffsController@regStatus')->as('checkreg');
    $route->any('/department/student/manage/carryover', 'App\staffsController@manageCO')->as('manageco');
    $route->any('/department/allcourses/{code}', 'App\staffsController@allDepartmentalCourses')->as('alldeptcourses');
    $route->any('/department/semester/courses/{code}', 'App\staffsController@semesterDepartmentalCourses')->as('semesterdeptcourses');
    $route->any('/department/admissionlist/{code}', 'App\staffsController@deptAdmissionList')->as('deptadmissionlist');
    $route->any('/department/studentlist/{code}', 'App\staffsController@deptStudentList')->as('deptstudentslist');
    $route->any('/department/applicationlist/{code}', 'App\staffsController@deptApplicationList')->as('deptapplist');
    $route->any('/department/uploadresults/{code}', 'App\staffsController@resultsUpload')->as('deptresultsupload');
    
    // Staffs ajaxController routes ( HOD )
    $route->any('/ajax/reg/approve', 'App\ajaxController@approveReg')->as('approvereg');
    $route->any('/ajax/reg/achieve', 'App\ajaxController@achieveReg')->as('achievereg');

    $route->any('/ajax/co/add', 'App\ajaxController@addCO')->as('addco');
    $route->any('/ajax/co/remove', 'App\ajaxController@removeCO')->as('removeco');
    // $route->any('/ajax/co/remove', 'App\ajaxController@removeCO')->as('removeco');


    // Staff Controller routes ( DICT )
    $route->any('/allcourses', 'App\staffsController@allCourses')->as('allcourses');
    $route->any('/semester/courses', 'App\staffsController@semesterCourses')->as('semestercourses');
    $route->any('/admissionlist', 'App\staffsController@AdmissionList')->as('admissionlist');
    $route->any('/studentlist/{level}?', 'App\staffsController@StudentList')->as('studentslist');
    $route->any('/applicationlist/{code}?', 'App\staffsController@ApplicationList')->as('applist');

    $route->any('/paymenthistory', 'App\staffsController@paymentHistory')->as('paymenthistory');
    $route->any('/paymentdetails/{code}', 'App\staffsController@paymentDetails')->as('student_payment_history');

    $route->any('/student/profile/{id}', 'App\staffsController@studentProfile')->as('studentprofile');
    $route->any('/student/edit', 'App\staffsController@editStudent')->as('editstudentprofile');

    // Staffs ajaxController routes ( DICT )
    $route->any('/ajax/student/getprofile', 'App\ajaxController@getStudentProfile')->as('getstudentprofile');
    $route->any('/ajax/student/ban', 'App\ajaxController@suspenStudent')->as('suspendstudent');
    $route->any('/ajax/student/add', 'App\ajaxController@addstudent')->as('addstudent');
    $route->any('/ajax/student/updatecourse', 'App\ajaxController@updateCourse')->as('updatecourse');
    $route->any('/ajax/student/changename', 'App\ajaxController@changeName')->as('changename');
    $route->any('/ajax/application/achieve', 'App\ajaxController@achieveReg')->as('achievereg');

    //Resetters
    $route->any('/ajax/reset/registration', 'App\ajaxController@removeCO')->as('resetregistration');
    $route->any('/ajax/reset/password', 'App\ajaxController@removeCO')->as('resetpassword');

    $route->any('/apply', 'App\homeController@application')->as('application');



    

}

$route->end();

?>
