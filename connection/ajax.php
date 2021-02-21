<?php

require_once 'connection.php';

// FETCH MESSAGES
$user_id = $_SESSION['user']['user_id'];

if (isset($_POST['post_name'])) {

    $postName = $_POST['post_name'];

    if ($postName== "get_message") {
        $receiver_id = base64_decode($_POST['receiver_id']);
        //ALICISI VE GÖNDERİCİSİ BEN OLAN TÜM MESAJLARI ÇEKİYORUZ:
        $sorgu = $db->prepare("Select * from messages where  message_receiver_id = ? or message_sender_id = ? ");
        $sorgu->execute(array($user_id, $user_id));
        $array = $sorgu->fetchAll(PDO::FETCH_ASSOC);

        // Sender id convert md5 
        $new_array = array();
        // BURADA TIKLANAN ALICININ BANA YOLLADIĞI MESAJLARI VE BENİM ONA YOLLADIĞIM MESAJLARI YAKALAYIP DİZİ OLARAK DÖNDÜRÜYORUZ.
        foreach ($array as $key) {
            if ($key['message_sender_id'] == $receiver_id) {
                array_push($new_array, $key);
            } else if ($key['message_receiver_id'] == $receiver_id and $key['message_sender_id'] == $user_id) {
                array_push($new_array, $key);
            }
        }

        // Gönderici id leri burada şifreleyip ön tarafa yolluyoruz.

        foreach ($new_array as $key){
            $key['sender_id'] = base64_encode($key['message_sender_id']);
        }
        echo json_encode($new_array, JSON_UNESCAPED_UNICODE);
    }

    else if($postName == "insert_message"){
        $data = $_POST['info'];
        $sender_id = $user_id;
        $receiver_id = base64_decode($data['receiver_id']);
        
        $query = $db->prepare("INSERT INTO messages SET
         message_content = ?,
         message_sender_id = ?,
         message_receiver_id = ?
         ");
        $insert =  $query->execute(array($data['message_content'],$sender_id,$receiver_id));
        if ($insert) {
            $result['sonuc'] = "olumlu";
        }
        else{
            $result['sonuc'] = "olumsuz";
        }
        echo json_encode($result);
    }
    
} else {
    echo 'Not post';
}