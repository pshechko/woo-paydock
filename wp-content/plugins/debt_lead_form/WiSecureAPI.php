<?php
    $_POST['WiSecureAPIKEY'] = 'p!:e`E9=9gX%_wQf';

    $result = gateway($_POST);

    $resultArray = json_decode($result, true);

    if ($resultArray['status'] == 'success'){
        $leadId = $resultArray['result']['leadId'];

        $message .= 'Insightly LeadId: '.$leadId."\n\n";
        foreach ($_POST as $key => $value){
            $message .= $key.': '.$value."\n\n";
        }


        mail('matthewdotbarry@gmail.com', 'New Debt Site Lead', $message);
    }

    echo $result;
    
    function gateway($fields){
        $url = "http://api.wisecure.it/testInsightlyGateway.php";

        $postvars='';
        $sep='';
        foreach($fields as $key=>$value)
        {
                $postvars.= $sep.urlencode($key).'='.urlencode($value);
                $sep='&';
        }

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
?>