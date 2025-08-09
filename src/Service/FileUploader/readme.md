# 📁 FileUploader Service (Symfony)

Bu servis, Symfony projelerinde dosya yükleme ve silme işlemlerini kolaylaştırmak ve güvenli hale getirmek için geliştirilmiştir.

## 🚀 Özellikler

- Güvenli dosya uzantısı kontrolü (`jpg`, `png`, `pdf`, vb.)
- Benzersiz dosya adı üretimi (`uniqid`)
- Yükleme dizinine dosya taşıma
- Dosya silme işlemi
- Basit hata yönetimi

---

## 📦 Kurulum

`FileUploader.php` dosyasını `src/Service/` klasörüne yerleştirin:

## Kulanımı 

    $fileUploader = new FileUploader(yükleme noktası(string), kabul edilen uzantılar(array));

     
    $targetDir = $this->getParameter('kernel.project_dir').'/public/uploads/landing';
    $formats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $fileUploader = new FileUploader($this->getParameter('kernel.project_dir') . '/public/uploads/landing', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $upload = $fileUploader->upload(resim dosyası);

Dosya yükleme basarılı olursa dosya yolu geri dönecektir 

