<?php
        /*
            Script om topdesk meldingen automatich in natios op acknowledged te zetten.
	    (alleen handig als nagios ook atomatich topdesk meldingen aan maakt door middel van import funtie)
            
            In Topdesk een Niewe POST regel aanmaken met het onderstaande bericht:
            <?xml version="1.0" encoding="UTF-8"?>
            <incident>
            <id>[Incidentnummer]</id>
            <omschrijving>[Korte_omschrijving_(Details)]</omschrijving>
            </incident>
        */
        $xmlData = file_get_contents('php://input');
		//TODO Nog netjes wegwerken
        $url = 'http://YOUR_SERVER/nagios/cgi-bin/cmd.cgi';
        $username = 'YOUR_USERNAME';
        $password = 'YOUR_PASSWORD';
        $incident = new SimpleXMLElement($xmlData);

        $com_data = $incident->id;
        $host = strtok($incident->omschrijving, "\n");
        $service = strpos($incident->omschrijving, "HOST DOWN") ? "" : strtok(substr(strstr($incident->omschrijving, "\n"), 1), ":");
        $cmd_typ = strlen($service) > 0 ? '34' : '33';

        $data = array(
            'cmd_typ' => $cmd_typ,
            'cmd_mod' => '2',
            'host' => $host,
            'service' => $service,
            'sticky_ack' => 'on',
            'send_notification' => 'on',
            'com_data' => (string)$com_data,
            'btnSubmit' => 'Commit'
        );


        // use key 'http' even if you send the request to https://...
        $options = array(
                        'http' => array(
                                'header' => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Basic " . base64_encode("$username:$password"),
                                'method' => 'POST',
                                'content' => http_build_query($data)
                )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) {
        }

        var_dump($result);
?>
