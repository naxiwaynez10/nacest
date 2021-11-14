<?php

class Application
{
    public function getAll($dept = false, $level= false){
        if($dept){
            app('db')->where('first_choice', $dept);
            app('db')->orWhere('second_choice', $dept);
        }
        if($level){
            app('db')->where('level', $level);
        }

        app('db')->orderBy('first_choice', 'DESC');
        return app('db')->get('application');
    }

    public function apply($data){
         $application = array(
            'app_number' => $data['app_number'],
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'gender' => $data['gender'],
            'DOB' => $data['dob'],
            'category' => $data['category'],
            'sn' => $data['sn'],
            'rank' => $data['rank'],
            'country' => $data['country'],
            'state' => $data['state'],
            'first_choice' => $data['first_choice'],
            'second_choice' => $data['second_choice'],
            'jamb_reg_no' => $data['jamb_reg_no'],
            'jamb_score' => $data['jamb_score'],
            'level' => $data['level'],
            'sponsor' => $data['sponsor'],
            'sponsor_address' => $data['sponsor_address'],
            'guardian' => $data['guardian'],
            'guardian_address' => $data['guardian_address'],
            'nok' => $data['nok'],
            'nok_address' => $data['nok_address'],
            'status' => "1",
            'date_applied' => app('db')->now(),
            'date_modified' => app('db')->now(),
            'modified_by' => NULL,
         );
         app('db')->where('app_number', $data['app_number']);
         if(app('db')->getOne('application')){
             //Already exists
             unset($application['app_number']);
             unset($application['date_applied']);
             app('db')->where('app_number', $data['app_number']);
             $query = app('db')->update('application', $application);
             $msg = array(
                'message' => 'Registration updated successfully! <br/> Application Number: <b>'.$data['app_number'].'</b>',
                'app_number' => $data['app_number'],
                'status' => true
            );
         }
         else{
           $query = app('db')->insert('application', $application);
           $msg = array(
            'message' => 'Registration submitted successfully! <br/> Application number: <b>'.$data['app_number'].'</b>',
            'app_number' => $data['app_number'],
            'status' => true
        );
         }
         if($query){
            
             return $msg;
         }
         $msg = array(
            'message' => "Couldn't process your application, please try again",
            'status' => false
         );
         return $msg;

    }

    public function achieve($dept, $level){
        if($dept){
            app('db')->where('first_choice', $dept);
            app('db')->orWhere('second_choice', $dept);
        }
        if($level){
            app('db')->where('level', $level);
        }

        if(app('db')->delete('application')){
            $message = array(
                'message' => 'Application Achieved successfully',
                'status' => true
            );

            return $message;
        }
        $message = array(
            'message' => 'An Error occured!',
            'status' => false
        );

        return $message;
    }
}