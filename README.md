# Php-And-Socket.io-Realtime-Chat-System
Bu proje PHP ve Socket.io ile yazılmıştır, PHP kullanma nedeni anlık olarak konuşulan metinlerin MYSQL veritabanında saklanması ve çekilmesi sırasında uygun gördüğüm web tabanlı
backend dili oluşudur.
Socket.io  kullanımımın nedeni ise  basit ve anlık olarak veri haberleşmesi konusunda ideal bir hıza sahip oluşundan kaynaklıdır.

# Projenin içerisinde bizleri neler bekliyor ?
- Anlık olarak mesajlaşma
- Görüldü sistemi (Veritabanı ve socket tarafı)
- Yazıyor sistemi
- Atılan mesajın üzerinden geçen sürenin 30 saniye de bir güncellenmesi
- Görülmeyen mesajların sayacının tutulması
- Scrollun son mesaja çekilmesi ( Eski mesajlar okunduğu esnada bu işlem devre dışı bırakılmıştır , yani dinamiktir)
- Mesajlajma sırasında socket tarafına ve tarayıcı tarafında mesaj gönderen bilgilerinin şifrenmesi (base649
- Kullanıcı giriş (Kullanıcı kayıta gerek duymadım , ihtiyaçlar doğrultusunda ekleyebilirsiniz)

Programda chat başlatmak için kullanıcı girişi yaptıktan sonra listelenen üyelerden herhangi birine mesaj atması yeterlidir.
# Php-And-Socket.io-Realtime-Chat-System

## YAPILANDIRMA
#### Server.js Yapılandırması
**chat>Server>server.js** dosyası içerisinde bulunan
<pre>
http.listen(3000, () => {
console.log('listening on *:3000');
}); </pre> 
kısmında node.js'de hangi port ile çalışmak istiyorsanız ona göre '3000' yazan portu güncelleyebilirsiniz.

#### script.js Yapılandırması
**chat>script>script.js** Dosyası içerisinde sockete istemci olarak bağlantı yapıyoruz, bu bağlantıyı socketi kullandığınız sunucu adresi ve portu yazarak güncellemeniz gerekmekte 
<pre>
const socket = io("http://localhost:3000");
</pre>

#### Veri tabanı bağlantı dosyası yapılandırması
**chat>connection>connection.php**
Dosyası içerisinde bulunan
<pre>
 $db = new PDO("mysql:host=localhost;dbname=chat_application",'root','');
</pre>
alanında gerekli veritabanı ayarlamalarını kendinize göre güncellemeniz gerekmektedir.


## KURULUM
<ul>
  <li> Program içerisinde socket.io kullanıldığı için bilgisayarınıza veya sunucunuza node.js kurmanız gerekmektedir. </li>
  <li> Ardından chat>Server>server.js dosyasını terminal ile <pre> node server.js</pre> komutu ile çalıştırmanız gerekmekte </li>
  <li> Veya sisteminize pm2 kurmamız socket'in ne kadar veri harcadığı kaç istemcinin bağlandığı gibi bilgilieri daha güzel bir şekilde görmenize fayda sağlayacaktır. <pre>$ npm install pm2@latest -g  </pre> </li>
  bu kod size pm2'nin son versiyonunu yükleyecektir ardından <pre> $ pm2 start server.js </pre>
  komutu ile server dosyamızı çalıştırmış olacağız.
</ul>
Bu işlemlerin ardından sistemi rahatlık ile kullanabilir ve geliştirmeler yapabilirsiniz.

# PROGRAMIN EKRAN GÖRÜNTÜLERİ
![image](https://user-images.githubusercontent.com/44698680/110997352-53d51400-838e-11eb-83f3-4392889dc1a6.png)
![image](https://user-images.githubusercontent.com/44698680/110997413-6bac9800-838e-11eb-96f2-22dc458f6c82.png)
![image](https://user-images.githubusercontent.com/44698680/110997440-77985a00-838e-11eb-8347-8a360d50796d.png)
![image](https://user-images.githubusercontent.com/44698680/110997635-c0e8a980-838e-11eb-80c1-ee541c990d68.png)
![image](https://user-images.githubusercontent.com/44698680/110997742-e2e22c00-838e-11eb-9a32-d4c6a04f64ec.png)
