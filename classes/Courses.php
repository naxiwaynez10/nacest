<?php

class Courses
{
    public function get($code){
        app('db')->where('code', $code);
        $course = app('db')->getOne('courses');
        if($course){
            $data = array();
            $data['name'] = $course['title'];
            $data['code'] = $course['code'];
            return $data;
        }
        else{
            return false;
        }
    }
    public function getAll($semester = false){
        if($semester){
            app('db')->where('semester', $semester);
        }
        return app('db')->get('courses');
    }
    public function getDepartment($code){
        app('db')->where('code', $code);
        $course = app('db')->getOne('courses');
        app('db')->where('id', $course['department']);
        $department = app('db')->getOne('department');
        return $department['title'];
    }

    public function getFaculty($code){

    }

    
    public function add($data){
        $course = app('db')->insert('courses', $data);
        if($course){
            return true;
        }
        return false;
    }

    public function update($data){
        if(array_key_exists('code', $data)){
            $course = app('db')->update('courses', $data);
            if($course){
                return true;
            }
            return false;
        }
    }

    public function exists($code){
        app('db')->where('code', $code);
        if(app('db')->getOne('courses')){
            return true;
        }
        return true;
    }

    public function delete($code){
        app('db')->where('code', $code);
        if(app('db')->delete('courses')){
            return true;
        }
        return false;
    }

    public function restrict($code, $matric){
        
    }

    public function restore($code, $matric){
        
    }
}