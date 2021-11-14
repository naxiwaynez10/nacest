<?php

class Departments
{
    public function getAll($faculty){
        app('db')->where('faculty', $faculty);
        return app('db')->get('departments');
    }
    
    public function getName($code){
        app('db')->where('code', $code);
        $dept = app('db')->getOne('departments');
        return $dept['title'];
    }

    public function getCourses($dept, $level, $semester){
        if($dept){
            app('db')->where('dept', $dept);
        }
        if($semester){
            app('db')->where('dept', $semester);
        }
        if($level){
            app('db')->where('level', $level);
        }
        return app('db')->get('courses');
    }
    
    public function getFaculty($code){
        app('db')->where('code', $code);
        $f =  app('db')->getOne('departments');
        app('db')->where('code', $f['faculty']);
        $faculty = app('db')->getOne('faculty');
        return $faculty['title'];
    }

    public function InFaculty($faculty){
        app('db')->where('faculty', $faculty);
        return app('db')->get('departments');
    }

    public function count($faculty = false){
        if($faculty){
            app('db')->where('faculty', $faculty);
        }
        $dept = app('db')->get('departments');
        return app('db')->count;
    }

    public function countFaculty(){
        app('db')->get('faculty');
        return app('db')->count;
    }


}