<?php

require_once '../connection/connection.php';
if (!isset($_SESSION['user']))
	header("location:../index.php");
echo '<input type="hidden" name="user_id" value="' . base64_encode($_SESSION["user"]["user_id"]) . '"';
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
				<input type="text" name="" placeholder="Search..">
				<i class="fas fa-search"></i>
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
					where message_receiver_id = ? group by message_sender_id");
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
				 where message_sender_id = ? group by message_receiver_id");
				$query->execute(array($_SESSION['user']['user_id']));
				$two_array = $query->fetchAll(PDO::FETCH_ASSOC);

				$control = true;	
				//Burada karşılaştırma işlemini user_id'ye göre yapıyoruz.
				for ($i = 0; $i < count($two_array); $i++) {
					foreach ($first_array as $key) {
						if ($key['user_id'] != $two_array[$i]['user_id']) {
							$control = true;
						} else {
							$control = false;
						}
					}
					if ($control) {

						array_unshift($first_array, $two_array[$i]);
					}
				}



				foreach ($first_array as $yazdir) {
				?>
					<a class="receiver-link">
						<div class="user">
							<img src="img/avatar.jpg" class="user-image"></img>
							<div class="info">
								<span class="name"><?php echo $yazdir['user_name'] ?></span>
								<input type="hidden" name="sender_id" value="<?php echo base64_encode($yazdir['user_id'])  ?>">
								<!-- <span class="last-message">Nerelerdesin Be Gülüm ?</span> -->
								<!-- <span class="counter">5</span> -->
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
						<i class="fa fa-ellipsis-v"></i>
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