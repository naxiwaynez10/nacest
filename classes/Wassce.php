<?php

class Wassce
{
    public function get($uid){
        app('db')->where('uid', $uid);
        $wassce = app('db')->getOne('olevel');
        $wassce['status'] = true;
        return $wassce;
    }

    public function add($data){
        $exam = array(
            'sitting' => $data['sitting'],
            'exam' => $data['exam_type'],
            'centre' => $data['centre'],
            'exam_number' => $data['exam_number'],
            'card_pin' => $data['card_pin'],
            'exam_date' => $data['exam_date'],
            'date_added' => app('db')->now(),
            'uid' => $data['uid'],
            'subject' => $data['subject'],
            'grade' => $data['grade']
        );
        if(app('db')->insert('olevel', $exam)){
            app('db')->where('uid', $data['uid']);
            $data = app('db')->getOne('olevel');
            $data['exam_type'] = $data['exam'];
            $data['status'] = true;
            $data['message'] = 'Added successfully';
            unset($data['csrftoken']);
            return $data;
        }
        return array(
            "status" => false,
            "message"=> "Couldn't add record"
            );
    }

    public function delete($id){
        app('db')->where('id', $id);
        if(app('db')->delete('olevel')){
            return array('status' => true);
        }
        else{
            return array('status' => false);
        }
    }
}