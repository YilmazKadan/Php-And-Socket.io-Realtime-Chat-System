<?php

require_once '../connection/connection.php';
if (!isset($_SESSION['user']))
	header("location:../index.php");
$user_id = $_SESSION["user"]["user_id"];
echo '<input type="hidden" name="user_id" value="' . base64_encode($user_id) . '">';
?>
<!DOCTYPE html>
<html>

<head>
	<title>Chat Application Design</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
	<link rel="stylesheet" href="css/style.css">
	<script src="http://localhost:3000/socket.io/socket.io.js"></script>
</head>

<body>
	<div class="deneme">

	</div>
	<div class="chat">
		<div class="sidebar">
			<div class="search">

			</div>
			<div class="contact">
				<?php
				/*
				Mesajlar tablosundan  alıcının oturum açan id olduğu mesajları 'message_sender_id' alanına göre
				gruplar , ancak karşı tarafa sadece bu  kullanıcı mesaj atmış ise bu durumda alıcı olarak bu kullanıcı olmayacağı
				için , aşağıda farklı bir sql sorgusu ile göndericinin biz olduğu mesajları da grupluyoruz.
				
				*/
				$query = $db->prepare("SELECT * FROM  messages m inner join users u 
					on m.message_sender_id = u.user_id  
					where  message_id in 
				 
				 ( select max(message_id) from messages where message_receiver_id = ? group by message_sender_id ) order by message_id desc ");
				$query->execute(array($_SESSION['user']['user_id']));
				$first_array = $query->fetchAll(PDO::FETCH_ASSOC);


				/*
					Burada göndericinin biz olduğu mesajları 'message_receiver_id' alanına göre yani alıcılara göre grupluyoruz.
					Daha sonra bu alıcılar kendi id'leri ile user tablosu user_id ile ilişklendiriliyor. Daha sonra yukarıdaki ilk
					dizi ile bir karşılaştırma yapılıyor eğer ikinci dizideki kullanıcı birinci dizide çekilmemiş ise onu da diziye ekliyoruz.
					Ve bu sayede sadece bizim mesaj atmış olduğumuz kullanıcılar da listeleniyor.


				*/
				$query = $db->prepare("SELECT * FROM  messages m inner join users u 
				 on m.message_receiver_id = u.user_id  
				 where  message_id in 
				 
				 ( select max(message_id) from messages where message_sender_id = ? group by message_receiver_id ) order by message_id desc");
				$query->execute(array($_SESSION['user']['user_id']));
				$second_array = $query->fetchAll(PDO::FETCH_ASSOC);

				//Burada karşılaştırma işlemini user_id'ye göre yapıyoruz.
				for ($i = count($second_array) - 1; $i >= 0; $i--) {
					if (count($first_array) != 0) { // İlk array eğer boş ise ilk arrayı ikinci arraya eşitliyoruz.
						foreach ($first_array as $key) {
							if ($key['user_id'] != $second_array[$i]['user_id']) {
								$control = true;
							} else {
								$control = false;
								break;
							}
						}
						if ($control) {
							array_unshift($first_array, $second_array[$i]);
						}
					} else {
						$first_array = $second_array;
					}
				}
				rsort($first_array); // En son etkileşimde bulunan mesajları sırası ile büyükten küçüğe doğru listeleme

				foreach ($first_array as $yazdir) {
				?>
					<a class="receiver-link">
						<div class="user">
							<img src="img/avatar.jpg" class="user-image"></img>
							<div class="info">
								<span class="name"><?php echo $yazdir['user_name'] ?></span>
								<input type="hidden" name="sender_id" value="<?php echo base64_encode($yazdir['user_id'])  ?>">
								<br>
								<span class="last-message"><?php echo substr($yazdir['message_content'], 0, 50) ?></span>
								<?php
								$sorgu = $db->prepare("Select * from messages where message_receiver_id = ? and message_sender_id = ? and message_seen = 0");
								$sorgu->execute(array($user_id, $yazdir['message_sender_id']));
								if ($sorgu->rowCount()) {
									echo '	<span class="counter">' . $sorgu->rowCount() . '</span>';
								}

								?>
							</div>
						</div>
					</a>
				<?php } ?>

			</div>
		</div>
		<div class="content">
			<header>
				<div class="info-me">
					<img src="img/avatar.jpg" class="user-image">
					<div class="user">
						<span class="name"></span>
						<span class="last-seen"></span>
						<input type="hidden" name="receiver_id">
					</div>
					<div class="icon">
						<i class="fa fa-info-circle"></i>
						<i class="fas fa-sign-out-alt"></i>
					</div>
				</div>
			</header>
			<div class="message-content">
				<!-- Message content -->
			</div>
			<div class="write-message">
				<i class="fa fa-laugh"></i>
				<input type="text" name="" id="write-input" placeholder="Bir şeyler yaz..">
				<i class="fa fa-microphone"></i>
				<i class="fa fa-image"></i>
			</div>
		</div>
	</div>
</body>
<script src="script/jquery.min.js"></script>
<script src="script/script.js"></script>

</html>