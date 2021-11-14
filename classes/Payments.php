<?php
class Payments
{
    public function getFor($matric, $type)
    {
        app('db')->where('matric', $matric);
        app('db')->where('type', $type);
        return app('db')->getOne('payments');
    }
    public function getHistory($matric = false, $type = false)
    {
        if ($matric) {
            app('db')->where('matric');
        }
        if ($type) {
            app('db')->where('type', $type);
        }
        app('db')->orderBy('date_paid', 'DESC');
        return app('db')->get('payments');
    }

    public function make($data)
    {
        if (app('db')->insert('payments', $data)) {
            return true;
        }
        return false;
    }

    public function hasPaid($matric, $type)
    {
        app('db')->where('matric', $matric);
        app('db')->where('type', $type);
        if (app('db')->getOne('payments')) {
            return true;
        }
        return false;
    }

    public function hasPaid60($matric)
    {
    }

    public function hasPaid100($matric)
    {
    }


    public function calculateTotal($type = false)
    {
    }

    public function paynow()
    {
        // BASE URL
        // https://remitademo.net/remita/exapp/api/v1/send/api

        // CREDENTIALS


        $merchantId = 2547916;
        $apiKey = 1946;
        $serviceTypeId = 4430731;
        $orderId = 'kjbkuhiu';
        $s = $merchantId . $serviceTypeId . $orderId . "60000" . $apiKey;
        $apiHash = hash("sha512", $s);
        
        $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentinit',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_POSTFIELDS => '{ 
        //         "serviceTypeId": "' . $serviceTypeId .'",
        //         "amount": "60000",
        //         "orderId": "' . $orderId . '",
        //         "payerName": "John Doe",
        //         "payerEmail": "doe@gmail.com",
        //         "payerPhone": "09062067384",
        //         "description": "Payment for Septmeber Fees"
        //     }',
        //                 CURLOPT_HTTPHEADER => array(
        //                     'Content-Type: application/json',
        //                     'Authorization: remitaConsumerKey=' . $merchantId . ',remitaConsumerToken=' . $apiHash . ''
        //                 ),
        //             ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // exit($response);






        $amount = 10000;
        $postdata =  array('email' => 'hello@gmal.com', 'amount' => $amount, "callback_url" => "http://dev.nicest/student/profile");
        //
        $url = "https://api.paystack.co/transaction/initialize";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postdata));  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $headers = [
            'Authorization: Bearer sk_live_96b483875187d57710ac5e1d298dea67e07fa8a0',
            'Content-Type: application/json',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $request = curl_exec ($ch);
        curl_close ($ch);
        $response = json_decode($request, true);
        curl_close($curl);

       if($response['status']){
            $url = 'https://api.paystack.co/transaction/verify/'.$response['data']['reference'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt(
                $ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer sk_live_96b483875187d57710ac5e1d298dea67e07fa8a0']
            );
            $request = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($request, true);
            //
            if ($result['status']) {
                return $result;
            }
            else{
                return "Error in Transaction!";
            }
       }




    }
}
