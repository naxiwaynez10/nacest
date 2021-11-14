<?php
class Students 
{
    public function get($matric = false, $id = false){
        if($matric){
            app('db')->where('matric', $matric);
        }
        elseif($id){
            app('db')->where('id', $id);
        }
        else{
            app('db')->where('matric', app('auth')->user()['user_id']);
        }
        return app('db')->getOne('students');
    }

    public function getAll($code, $level, $session){
        if($level){
            app('db')->where('level', $level);
            app('db')->orWhere('level', $level."1");
            app('db')->orWhere('level', $level."2");
        }
        if($code){
            app('db')->where('dept', $code);
            app('db')->orWhere('faculty', $code);
        }
        if($session){
            app('db')->where('session', $session);
        }
       return app('db')->get('students');
    }

    public function getStudentsCount($dept = false, $faculty = false, $session = false, $level = false){
        if($dept){
            app('db')->where('dept', $dept);
        }
        if($faculty){
            app('db')->where('faculty', $faculty);
        }
        if($session){
            app('db')->where('session', $session);
        }
        if($level){
            app('db')->where('level', $level);
        }
        app('db')->where('status', 1);
        $students = app('db')->get('students');
        return app('db')->count;
    }

    
    public function getStudentsInFaculty($code){
        app('db')->where('faculty', $code);
       return app('db')->get('students');
    }

    public function getStudentsInFacultyCount($code){
        return count($this->getStudentsInFaculty($code));
    }


    public function getAdmissionList($dept, $faculty, $session, $type, $c = false){
        if($c){
            app('db')->orwhere('level', 'PRE-ND');
            app('db')->orWhere('level', 'ND1');
            app('db')->orWhere('level', 'PRE-HND');
            app('db')->orWhere('level', 'HND1');
            app('db')->orderBy('dept', 'DESC');
        }
        
        if($dept){
            app('db')->where('dept', $dept);
        }
        if($type){
            app('db')->where('category', $type);
        }
        if($faculty){
            app('db')->where('faculty', $faculty);
        }
        if($session){
            app('db')->where('session', $session);
        }
        return app('db')->get('students');
    }
    public function exists($matric, $dept){
        if($dept){
            app('db')->where('dept', $dept);
        }
        app('db')->where('matric', $matric);
        if(app('db')->getOne('students')){
            return true;
        }
        return false;
    }

    public function suspend($matric){
        app('db')->where('user_id');
        if(app('db')->update('users', array('status'=>2))){
            return true;
        }
        return false;
    }

    public function getStatus($matric){

    }

    public function getResult($matric, $session){

    }

    public function updateRecord($record){
        if(array_key_exists('matric', $record)){
            app('db')->where('matric', $record['matric']);
            if(app('db')->update('users', $record)){
                return true;
            }
            return false;
        }
    }

    public function addCO($data){
        if(array_key_exists('matric', $data)){
            if(app('db')->insert('COs', $data)){
                return true;
            }
            return false;
        }
    }

    public function removeCO($data){
        if(array_key_exists('matric', $data)){
            app('db')->where('matric', $data['matric']);
            app('db')->where('course', $data['course']);
            app('db')->where('session', $data['session']);
            if(app('db')->delete('COs')){
                return true;
            }
            return false;
        }
    }

    public function generateMatric($dept, $level){
        $year = date("y");
        app('db')->where('dept', $dept);
        app('db')->where('level', $level);
        $st = app('db')->get('students');
        $num = $st->count + 1;
        $matric = "NICEST/".strtoupper($dept)."/".$level.$year."/".$num;
        return $matric;
    }

    public function getDept($matric){
        $students = $this->get($matric);
        return app('department')->getName($students['dept']);
    }

    public function hasRegistered($matric){

    }

    public function registered($dept, $course){
        if($dept){
            app('db')->where('dept', $dept);
        }
        app('db')->where('course', $course);
        return app('db')->get('course_reg');
    }

    public function delete($matric){
        app('db')->where('user_id', $matric);
        if(app('db')->delete('users')){
            return true;
        }
        return false;
    }

    public function save($matric = false, $id = false, $data){
        $update = false;
        if($matric || $id){
            if($matric){
                app('db')->where('matric', $matric);
                $update = app('db')->update('students', $data);
            }
            elseif($id){
                app('db')->where('id', $id);
                $update = app('db')->update('students', $data);
            }

            if($update){
                return true;
            }
            else{
                return false;
            }
        }
        $data['matric'] = $this->generateMatric($data['dept'], $data['level']);
        if(app('db')->insert('students', $data)){
            return true;
        }
        return false;
        
    }

    public function promote($matric){
        app('db')->where('matric', $matric);
        $user = app('db')->getOne('students');
        $stage = false;
        if($user){
            //User exists
            if($user['level'] == "PRE-ND"){
                $stage = app('db')->update('students', array('level'=> "ND1"));
            }
            else if($user['level'] == "ND1"){
                $stage = app('db')->update('students', array('level'=> "ND2"));
            }
            
            else if($user['level'] == "PRE-HND"){
                $stage = app('db')->update('students', array('level'=> "HND1"));
            }
            else if($user['level'] == "HND1"){
                $stage = app('db')->update('students', array('level'=> "HND2"));
            }

            if($stage){
                return true;
            }
            return false;
        }
        return false;
        
    }

    public function getWassceRecords($matric = false){
        $matric = "48274544";
        if(!$matric){
            $student = app('students')->get();
            $matric = $student['matric'];
        }
        // if(preg_match($matric, "_")){
        //     // Its a matric number
        //     app('db')->where('matric', $matric);
        // }
        else{
            app('db')->where('uid', $matric);
        }
        app('db')->orderBy('sitting', "ASC");
        return app('db')->get('olevel');
    }

    public function getSubjects($matric, $exam_number){
        app('db')->where('matric', $matric);
        app('db')->where('exam_number', $exam_number);
        return app('db')->get('wassce_exams');
    }

    public function getAcademicHistory($matric = false){
        if(!$matric){
            $student = $this->get();
            $matric = $student['matric'];
            app('db')->where('matric', $matric);
            return app('db')->get('academy');
        }
    }

}