# YineMiÇiçek 🌸

Çiçek dükkanı / event florist için sipariş & envanter hesaplama uygulaması.
**Laravel 10 + Livewire 3 + MariaDB**, tek bir `Dockerfile` ile deploy.

Web: [yinemicicek.com](https://yinemicicek.com)

## Neyi çözer?

Bir organizasyona (düğün, mekan vs.) kaç masa, kaç buket, kaç adet serbest
çiçek/malzeme gerekli olduğunu hesaplar.

- **Masa tipi** tanımlarsın: "1 sünger / masa, 6 gül / sünger, 3 ammi visnaga…"
- Siparişte sadece **kaç masa olduğunu** girersin, geri kalan kendiliğinden çarpılır.
- Aynı şey **buketler** için: "Bir bukette 5 lisianthus + 5 karanfil" → kaç buket?
- **Serbest malzeme** ekleyebilirsin: "1 çuval yosun", "5 demet pembe lisianthus".
- **Toplam envanter** anlık olarak yan panelde ve `/envanter` özet sayfasında görünür.
- Her çiçeğe **görsel** ekleyebilirsin → mood board'a bakarken hatırlatıcı
  (32 hazır çiçek için Wikimedia Commons'tan CC-lisanslı fotoğraflar gelir).

## Ekran haritası

| Yol                      | Ne yapar                                              |
|--------------------------|-------------------------------------------------------|
| `/siparis`               | Asıl çalışma ekranı: organizasyon seç, masa/buket ekle |
| `/envanter`              | Bir organizasyon için toplam çiçek listesi (yazdırılabilir) |
| `/organizasyonlar`       | Düğün/event kayıtları                                  |
| `/ayarlar/cicekler`      | Çiçek tipi yönetimi (görsel, renk, birim)              |
| `/ayarlar/masalar`       | Masa tipi & masa başına çiçek formülü                  |
| `/ayarlar/buketler`      | Buket tipi & buket başına çiçek formülü                |

## Yerel geliştirme

```bash
composer install
cp .env.example .env
php artisan key:generate

# .env içinde DB ayarlarını gir
php artisan migrate --seed              # 32 hazır çiçek tipi seed'lenir
php artisan storage:link
php artisan flowers:download-images     # opsiyonel: Wikimedia'dan görselleri çek
php artisan serve                       # http://127.0.0.1:8000
```

PHP 8.2+, MariaDB/MySQL gerekir. Frontend için derleme adımı yok — Tailwind CDN.

## Docker ile deploy

```bash
# 1) İmajı build et
docker build -t yinemicicek .

# 2) .env dosyanı hazırla (.env.example'dan kopyala, DB bilgilerini gir)

# 3) Çalıştır
docker run -d --name yinemicicek \
    -p 8080:80 \
    --env-file .env \
    -v yinemicicek_storage:/var/www/html/storage/app/public \
    --restart unless-stopped \
    yinemicicek

# → http://localhost:8080
```

`entrypoint.sh` ilk açılışta:
- `APP_KEY` yoksa üretir
- `php artisan migrate --force` çalıştırır (DB'ye 5 deneme yapar)
- `config/route/view` cache'ler

DB **harici** olmalı (bağlantı bilgilerini `.env`'den okur). Görsel uploadlar
`storage/app/public/` altına gider, bu yüzden bir **volume mount** önerilir.

## Veri modeli

```
organizations ─< orders ─< order_tables        >─ table_types  ─< table_type_flowers   >─ flower_types
                       ─< order_bouquets      >─ bouquet_types ─< bouquet_flowers      >─ flower_types
                       ─< order_loose_flowers >─ flower_types
       order_tables ─< order_table_extras     >─ flower_types
```

- Bir organizasyonun bir order'ı var (otomatik açılır).
- `order_tables` → bir masa tipinin kaç adet kullanıldığı.
- `order_table_extras` → "bu masada normalde olmayan ekstra X çiçek" girişi.
- `order_bouquets` → buket sayısı.
- `order_loose_flowers` → masa/buket dışı çiçek & malzeme.

Toplam envanter `Order::aggregateFlowerInventory()` ile hesaplanır.

## Lisans

MIT.
