window.onload = function () {
    // GLOBAL VARIABLES
    const socket = io("http://localhost:3000");
    const write_input = document.getElementById("write-input");
    const last_seen = document.querySelector(".user .last-seen");
    const message_content = document.querySelector('.message-content');
    const content = document.querySelector('.content');

    const receiver_name = document.querySelector(".info-me .user span.name");
    const receiver_image = document.querySelector(".info-me .user-image");
    const receiver_id = document.querySelector('input[name="receiver_id"]');
    const user_id = document.querySelector('input[name="user_id"]');

    setInterval(function () { last_seen.innerText = "" }, 10000);
    function scrollBottom() {
        message_content.scrollTop = message_content.scrollHeight;
    }
    scrollBottom();
    //Enter'a basıldığında sokete veri yolluyoruz.
    write_input.addEventListener("keyup", function (e) {
        var info = {
            sender_id: user_id.value,
            receiver_id: receiver_id.value,
            message_content: write_input.value
        }
        if (e.keyCode == 13) {
            if (write_input.value != "") {

                socket.emit("message", info);
                // Insert variable  Mysql with PHP 
                $.ajax({
                    url: "../connection/ajax.php",
                    type: "POST",
                    dataType: "json",
                    data: { info, post_name: "insert_message" },
                    success: function (result) {
                    },
                    error: function (e) {
                        console.log("Error");
                    }

                })
            }
            write_input.value = "";
        }

    })
    //Yazıyor özelliği veri gönderme
    write_input.addEventListener("keyup", function (e) {
        var info = {
            receiver_id: receiver_id.value,
            sender_id: user_id.value,
            write_input_length: write_input.value.length,
            keyCode: e.keyCode

        }
        socket.emit("writing", info);
    })
    //Yazıyor özelliği veri yazdırma
    socket.on("writing", (wiriting) => {
        var repeat;
        console.log(wiriting);
        if (wiriting.receiver_id == user_id.value && wiriting.write_input_length != 0 && wiriting.sender_id == receiver_id.value) {
            if (repeat) {
                clearTimeout(repeat);
                repeat = null;
            }
            last_seen.innerText = "yazıyor..";
            repeat = setTimeout(function () { last_seen.innerText = ""; }, 2000);
        }
        else {
            last_seen.innerText = "";
        }
        if (wiriting.keyCode == 13) {
            last_seen.innerText = "";
        }

    });

    // Görüldü özelliğini mesajlara aktarma
    socket.on("message_seen", (seen) => {
        var seen_icons = document.querySelectorAll("i.fas.fa-eye");
        if (seen.seener_id == receiver_id.value && seen.receiver_id == user_id.value) {
            seen_icons.forEach(element => {
                element.style.color = "#4fc3f7";
            });
        }
    });

    // Mesajı gördüğümüzü socket'e ileten fonksiyon
    function message_seen() {

        var info = {
            seener_id: user_id.value,
            receiver_id: receiver_id.value

        }
        socket.emit("message_seen", info);


    }

    //Socketden gelen veriyi burada ekrana bastırıyoruz.
    socket.on("message", (msg) => {
        function addMessage() {

            // MESAJ BİZE Mİ GELMİŞ KONTROL EDİYORUZ
            if (msg.receiver_id == user_id.value && msg.sender_id == receiver_id.value || msg.sender_id == user_id.value && msg.receiver_id == receiver_id.value) {

                // See icon
                var seen_icon;
                if (msg.message_seen == 1) {
                    seen_icon = '<i id="seen_icon" class="fas fa-eye" style="color: #4fc3f7;"></i>';
                }
                else {
                    seen_icon = '<i id="seen_icon" class="fas fa-eye"></i>';
                }

                if (msg.sender_id == receiver_id.value) {

                    message_content.innerHTML +=
                        `
                <div class="message">
                <span>${msg.message_content}</span>
                <div class="send-time">1 dk önce</div>
                </div> 
                `
                    messageSeenUpdate(msg.sender_id);
                    message_seen();
                }
                else {
                    message_content.innerHTML +=
                        `
                <div class="message-me">
                <span>${msg.message_content}</span>
               <div class="send-time">1 dk önce ${seen_icon}</div>
                
                </div> 
                `
                }
            }

        }
        function message_print() {
            //Gelen mesajı yazdırmadan önce bu eşitliği kontrol ederek yazdırıyoruz.

            //Burada scroll olarak sonda olup olmadığımızı anlıyoruz.
            var fark = Math.abs(Math.floor(message_content.scrollHeight - (message_content.scrollTop + message_content.clientHeight)));
            addMessage();
            if (fark < 100) {
                scrollBottom();
            }
        }
        message_print();
    });


    // MESSAGE SEEN UPDATE 
    function messageSeenUpdate(sender_id) {
        $.ajax({
            url: '../connection/ajax.php',
            type: "POST",
            dataType: "json",
            data: { post_name: "messageSeenUpdate", sender_id: sender_id },
            success: function (data) {

            },
            error: function (data) {
                console.log(data);
            }
        });

    }
    // RECEIVER LINK CLICK
    const receiver_link = document.querySelectorAll("a.receiver-link");
    receiver_link.forEach(link => {
        link.addEventListener("click", function () {
            // LINK ACTIVE CLASS ADD AND REMOVE
            receiver_link.forEach(link => { link.classList.remove("active"); })
            link.classList.add("active");
            // CONTENT SECTİON DISPLAY SHOW
            content.style.display = "flex";

            // CHANGE USER INFO IN HEADER SECTION
            var sender_name = link.querySelector(".user .info span.name").innerText;
            var sender_image = link.querySelector(".user .user-image").src;
            var sender_id = link.querySelector('.user .info input[name="sender_id"]');

            receiver_name.innerText = sender_name;
            receiver_image.src = sender_image;
            receiver_id.value = sender_id.value;

            // Message seen update
            
            messageSeenUpdate(sender_id.value);//Message  Update in Database
            message_seen(); //Message seen on socket

            var countNotSeenMessage = sender_id.nextElementSibling.nextElementSibling.nextElementSibling;
            if (countNotSeenMessage != null)
                countNotSeenMessage.style.display = "none";

            // JQUERY GET MESSAGES
            $.ajax({
                url: '../connection/ajax.php',
                type: "POST",
                dataType: "json",
                data: { post_name: "get_message", receiver_id: receiver_id.value },
                success: function (result) {
                    message_content.innerHTML = "";
                    result.forEach(element => {
                        var seen_icon;
                        if (element.message_seen == 1) {
                            seen_icon = '<i id="seen_icon" class="fas fa-eye" style="color: #4fc3f7;"></i>';
                        }
                        else {
                            seen_icon = '<i id="seen_icon" class="fas fa-eye"></i>';
                        }
                        // Gelen mesaj verisi ben miyim yoksa alıcı mı onu kontrol etme alanı.
                        if (element.message_sender_id == receiver_id.value) {


                            message_content.innerHTML +=
                                `
                            <div class="message">
                            <span>${element.message_content}</span>
                            <div class="send-time">1 dk önce</div>
                            </div> 
                            `
                        }
                        else {
                            message_content.innerHTML +=
                                `
                            <div class="message-me">
                            <span>${element.message_content}</span>
                            <div class="send-time">1 dk önce ${seen_icon}</div>
                             
                            </div> 
                        `
                        }

                    })
                },
                error: function (e) {
                    console.log(e.responseText);
                },
                complete: function () {

                    scrollBottom();
                }
            });
        });
    });

}