🛠️ Kurulum ve Çalıştırma

1. Repository'yi klonlayın ve proje dizinine geçin:
   git clone https://github.com/batu14/portfolio_server.git
   cd portfolio_server

2. Bağımlılıkları yükleyin:
   composer install

3. Ortam değişkenlerini yapılandırın:
   .env dosyasını oluşturun (örneğin .env.local) ve gerekli veritabanı ve diğer ayarları yapın.

4. Veritabanı migrasyonlarını çalıştırın:
   php bin/console doctrine:migrations:migrate

5. Symfony geliştirme sunucusunu başlatın:
   symfony server:start

6. Tarayıcınızda uygulamayı açın:
   http://localhost:8000

💡 Not: Bu projeyi çalıştırmak için sisteminizde PHP (8.0) ve üstü gerekmektedir, Composer ve Symfony CLI yüklü olmalıdır.
