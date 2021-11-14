<?php

class Registration
{
    public function addCourse($data){
        if(app('db')->insert('course_reg', $data)  && $this->updateReg($data['matric'], '+')){
            return true;
        }
        return false;
    }

    public function removeCourse($matric, $code){
        app('db')->where('matric', $matric);
        app('db')->where('code', $code);
        if (app('db')->delete('course_reg') && $this->updateReg($matric, '-')){
            return true;
        }
        return false;
    }

    private function updateReg($matric, $data){
        app('db')->where('matric', $matric);
        $reg = app('db')->getOne('registered');
        $update = false;
       if($reg)
       { 
           if($data == "+"){
                $update = app('db')->update('registered', array('count', $reg['count']+1));
            }
            else if($data == "-"){
                if($reg['count'] == 1){
                    app('db')->where('matric', $matric);
                    $update = app('db')->delete('registered');
                }
                else{
                    $update = app('db')->update('registered', array('count', $reg['count'] - 1));
                }
            }
            if($update){
                return true;
            }
        }
        return false;
    }

    public function getStudents($dept, $level, $session){
        if($level){
            app('db')->where('level', $level);
        }
        if($dept){
            app('db')->where('dept', $dept);
        }
        if($session){
            app('db')->where('session', $session);
        }

        return app('db')->get('registered');
    }

    public function achieve($session, $dept, $level){
        app('db')->where('dept', $dept);
        app('db')->where('level', $level);
        app('db')->where('dept', $session);
        if (app('db')->delete('course_reg')){
            return true;
        }
        return false;
    }

    public function approve($matric){
        app('db')->where('matric', $matric);
        if (app('db')->update('registered', array('status'=>1))){
            return true;
        }
        return false;
    }

    public function disapprove($matric){
        app('db')->where('matric', $matric);
        if (app('db')->update('registered', array('status'=>0))){
            return true;
        }
        return false;
    }

    public function get($matric, $session){
        app('db')->where('matric', $matric);
        app('db')->where('session', $session);
        return app('db')->get('course_reg');
    }

    public function getStatus($session){
        
    }
}