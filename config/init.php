<?php

// Route Init o handle all URLs
$app = System\App::instance();
$app->request = System\Request::instance();
$app->route	= System\Route::instance($app->request);
$route = $app->route;

// SECRET_KEY will be used to create csrf tokens, You can change this with your own random hash
define('SECRET_KEY', '4vm4t0fers5s1ulojfp78f9s9c');


define('DISABLE_MULTIPLE_SESSIONS', false);

$settings = array();

// Include utils functions
require_once(BASE_PATH.'utils/utils.php');

// Check the script is installed, then init the database
if (file_exists(BASE_PATH.'config/settings.php')) {

    // Include main settings file
    require_once(BASE_PATH.'config/settings.php');

    if (!defined('URL')) {
        // Generate URL
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $script = $_SERVER['SCRIPT_NAME'];
        $parent = dirname($script);
        if (stripos($uri, $script) !== false) {
            $path = substr($uri, strlen($script));
        } elseif (stripos($uri, $parent) !== false) {
            $path = substr($uri, strlen($parent));
        } else {
            $path = $uri;
        }
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';
        $protocol = $secure ? 'https' : 'http';
        $hostname = str_replace('/:(.*)$/', "", $_SERVER['HTTP_HOST']);
        $servername = empty($_SERVER['SERVER_NAME']) ? $hostname : $_SERVER['SERVER_NAME'];
        $fullurl = strtolower($protocol . '://' . $servername) .  str_replace($path, '/', parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
        $last_two_chars = substr($fullurl, -2);
        if($last_two_chars == '//'){
           $fullurl = substr($fullurl, 0, -1);
        }
        define('URL', $fullurl );
    }

    // Init database with settings
    app()->db = new MysqliDb (
            Array (
                'host' => DB_HOST,
                'username' => DB_USER,
                'password' => DB_PASSWORD,
                'db'=> DB_NAME,
                'port' => 3306,
                'prefix' => DB_PREFIX,
                'charset' => 'utf8mb4')
            );

    // Site Settings init
    $settings['animate_css'] = true;
    $settings['nice_scroll'] = false;
    $settings['template_cache'] = false;

    if (ini_get('post_max_size')) {
        $post_max_size = (int)(str_replace('M', '', ini_get('post_max_size')) * 1024 * 1024);
        $upload_max_filesize = (int)(str_replace('M', '', ini_get('upload_max_filesize')) * 1024 * 1024);
        $settings['post_max_size'] = $post_max_size>$upload_max_filesize?$upload_max_filesize:$post_max_size;
    }else{
        $settings['post_max_size'] = 500000;
    }


    $site_settings = app()->db->get('settings');
    foreach ($site_settings as $each_settings) {
        $settings[$each_settings['name']] = $each_settings['value'];
    }

    define('SETTINGS', $settings);

    // Timezone
    date_default_timezone_set(isset(SETTINGS['timezone'])?SETTINGS['timezone']:'Asia/Colombo');
    app()->db->rawQuery('SET time_zone=?', Array (date('P')));

    // Template Init
    $loader = new \Twig\Loader\FilesystemLoader(['templates', 'assets']);

    app()->twig = new \Twig\Environment($loader);

    // Auth
    require_once('classes/Auth.php');
    app()->auth = new Auth();
}
// Admin
require_once('classes/Students.php');
app()->students = new Students();

require_once('classes/Courses.php');
app()->courses = new Courses();

require_once('classes/Application.php');
app()->application = new Application();

require_once('classes/Wassce.php');
app()->wassce = new Wassce();

require_once('classes/Registration.php');
app()->registeration = new Registration();

require_once('classes/Payments.php');
app()->payments = new Payments();

require_once('classes/Departments.php');
app()->departments = new Departments();

// Messages
app()->msg = new \Plasticbrain\FlashMessages\FlashMessages();

// Upload
$image_size = array();
$image_size['logo']['width'] = "130";
$image_size['logo']['height'] = "30";
$image_size['small_logo']['width'] = "40";
$image_size['small_logo']['height'] = "40";
$image_size['favicon']['width'] = "32";
$image_size['favicon']['height'] = "32";
define('IMAGE_SIZE', $image_size);
require_once('classes/Upload.php');
require_once('classes/Resize.php');




// PHP Mailer for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
$mail = new PHPMailer(true);
if (isset($settings['email_smtp']) && $settings['email_smtp'] == true) {
    $mail->isSMTP();
    $mail->SMTPAuth   = true;
    $mail->Host       = array_key_exists("email_host", $settings) ? $settings['email_host'] : "";
    $mail->Username   = array_key_exists("email_username", $settings) ? $settings['email_username'] : "";
    $mail->Password   = array_key_exists("email_password", $settings) ? $settings['email_password'] : "";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = array_key_exists("email_port", $settings) ? $settings['email_port'] : 587;
    // email for non ssl connections
    $mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
        )
    );
}
$mail->From = array_key_exists("email_from_address", $settings) ? $settings['email_from_address'] : "chatnet@".$_SERVER['HTTP_HOST'];
$mail->FromName = array_key_exists("email_from_name", $settings) ? $settings['email_from_name'] : "ChatNet";
app()->mail = $mail;

// XSS Protection
use voku\helper\AntiXSS;
app()->purify = new AntiXSS();

// CSRF Protection
require_once('classes/Csrf.php');
app()->csfr = new Csrf();


// Bad Words Filter
use mofodojodino\ProfanityFilter\Check;

if (defined('SETTINGS')) {
    //Set language
    if (isset($_COOKIE['lang'])) {
        app()->lang = json_decode($_COOKIE['lang'],true);
    }else if(isset(SETTINGS['default_lang'])){
        app('db')->where('code',SETTINGS['default_lang']);
        $reqlang = app('db')->getOne('languages');
        app()->lang = $reqlang;
        $reqlang_json = json_encode($reqlang, true);
        setcookie('lang', $reqlang_json, time() + (86400 * 100), "/");
    }else{
        $reqlang = array('code'=>'en', 'name'=>'English', 'country'=> 'us', 'direction'=> 'ltr');
        app()->lang = $reqlang;
        $reqlang_json = json_encode($reqlang, true);
        setcookie('lang', $reqlang_json, time() + (86400 * 100), "/");
    }
    $lang_file = BASE_PATH.'lang'.DS.app()->lang['code'].'.php';
    if (file_exists($lang_file)) {
        if ($lang_terms = file_get_contents($lang_file)) {
            //var_dump(unserialize($lang_terms));
            define('LANG_TERMS', unserialize($lang_terms));
        }
    }
    // Set Fonts
    if (isset(app()->lang['google_font_family'])) {
        app()->google_font_family = app()->lang['google_font_family'];
    }else if(isset(SETTINGS['google_font_family']) && !empty(SETTINGS['google_font_family'])){
        app()->google_font_family = SETTINGS['google_font_family'];
    }else{
        app()->google_font_family = 'Poppins';
    }

    // Set theme
    if (isset($_COOKIE['theme'])) {
        app()->theme = $_COOKIE['theme'];
    }else if(isset(SETTINGS['theme'])){
        app()->theme = SETTINGS['theme'];
    }else{
        app()->theme = 'default';
    }

    // Init profanity
    if (isset(SETTINGS['bad_words']) and SETTINGS['bad_words']) {
        $badWords = explode(', ', SETTINGS['bad_words']);
    }else{
        $badWords = array();
    }
    app()->profanity = new Check($badWords);

    // View as for Admins
    if(isset($_GET['view-as'])){
        $_SESSION['view-as'] = app('auth')->get_user_by_id($_GET['view-as']);
    }
}



?>
