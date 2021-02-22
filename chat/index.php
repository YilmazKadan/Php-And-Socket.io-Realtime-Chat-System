<?php

require_once '../connection/connection.php';
if (!isset($_SESSION['user']))
    header("location:../index.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        a {
            text-decoration: none;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        html,
        body {
            height: 100%;
        }

        body {
            background-color: #a1cae2;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        div.users {
            width: 400px;
            min-height: 400px;
            border: 1px solid #fff;
            padding: 30px;
        }

        div.users ul li {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        div.users ul li a {
            color: rgba(0, 0, 0, .5);
            font-size: 20px;
            display: inline-flex;
            height: 20px;
            align-items: center;

        }

        div.users ul li a:hover {
            color: black;
        }

        header.head {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="users">
        <header class="head">
            <h3>USERS</h3>
            <a href="chat.php">Chat Box</a>
        </header>
        <hr>
        <ul>

            <?php
            try {
                echo '<input type="hidden" name="user_id" value="' . $_SESSION['user']['user_id'] . '">';
                $query = $db->prepare("SELECT * FROM users where user_id not in (?)");
                $query->execute(array($_SESSION['user']['user_id']));
                $response = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($response as $key) {
            ?>
                    <li>
                        <a href="#"><?php echo $key['user_name'] ?></a>
                        <input type="hidden" name="receiver_id" value="<?php echo base64_encode($key['user_id']) ?>">
                        <input type="text" name="message_content" placeholder="Mesajınızı giriniz ve entere basınız">
                    </li>
            <?php }
            } catch (PDOException $ex) {
                $ex->getMessage();
            }

            ?>
        </ul>
    </div>
</body>
<script src="script/jquery.min.js"></script>
<script>
    const message_input = document.querySelectorAll("ul li input[name='message_content']");
    const user_id = document.querySelector('input[name="user_id"]');
    message_input.forEach((element) => {
        element.addEventListener('keyup', function(e) {
            if (e.keyCode == 13) {
                var receiver_id = element.previousElementSibling.value;
                var info = {
                    sender_id: user_id.value,
                    receiver_id: receiver_id,
                    message_content: element.value
                }
                $.ajax({
                    url: "../connection/ajax.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        info,
                        post_name: "insert_message"
                    },
                    success: function(result) {
                        console.log(result);
                    },
                    error: function(e) {
                        console.log("Error");
                    }

                })

            }
        })
    });
</script>

</html>