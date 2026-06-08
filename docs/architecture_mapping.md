Ringkasan arsitektur dan pemetaan fitur ke file

- **Auth**: [app/Http/Controllers/Auth/LoginController.php](app/Http/Controllers/Auth/LoginController.php)
  - Models: [app/Models/User.php](app/Models/User.php), [app/Models/Owner.php](app/Models/Owner.php), [app/Models/Staff.php](app/Models/Staff.php)
  - Routes: [routes/web.php](routes/web.php)
  - Session: memakai session driver DB (`config/session.php`) dan menyimpan `user_id`, `user`, `nama`, `role`

- **Customer flow (booking, pembayaran, tracking, invoice, profil)**
  - Controller: [app/Http/Controllers/CustomerController.php](app/Http/Controllers/CustomerController.php)
  - Views: [resources/views/customer/index.blade.php](resources/views/customer/index.blade.php)
  - Models: [app/Models/Order.php](app/Models/Order.php), [app/Models/OrderDetail.php](app/Models/OrderDetail.php), [app/Models/Pembayaran.php](app/Models/Pembayaran.php), [app/Models/Invoice.php](app/Models/Invoice.php), [app/Models/Katalog.php](app/Models/Katalog.php)
  - Routes: grup `customer` di [routes/web.php](routes/web.php)
  - Notifications: menggunakan [App/Helpers/CleanGoHelper.php](app/Helpers/CleanGoHelper.php) untuk notifikasi dan countUnread
  - Session/Cookies: session menyimpan `user_id`/`role`, cookie `remember_username` digunakan di login

- **Staff flow (ambil order, set berat, advance status, konfirmasi bayar)**
  - Controller: [app/Http/Controllers/StaffController.php](app/Http/Controllers/StaffController.php)
  - Models: [app/Models/Order.php](app/Models/Order.php), [app/Models/OrderDetail.php](app/Models/OrderDetail.php), [app/Models/Tracking.php](app/Models/Tracking.php), [app/Models/Pembayaran.php](app/Models/Pembayaran.php)
  - Routes: grup `staff` di [routes/web.php](routes/web.php)
  - Notifications: `CleanGoHelper::notifyAllStaff` / `sendNotification`

- **Owner/Admin flow (katalog, layanan, staff, laporan, settings)**
  - Controller: [app/Http/Controllers/OwnerController.php](app/Http/Controllers/OwnerController.php)
  - Models: [app/Models/Katalog.php](app/Models/Katalog.php), [app/Models/Layanan.php](app/Models/Layanan.php), [app/Models/AppSetting.php](app/Models/AppSetting.php)
  - Routes: grup `owner` di [routes/web.php](routes/web.php)
  - Exports: PDF/Excel di OwnerController memanggil package `barryvdh/laravel-dompdf` dan `maatwebsite/excel`

- **Notifications**
  - Controller: [app/Http/Controllers/NotificationController.php](app/Http/Controllers/NotificationController.php)
  - Helper: [app/Helpers/CleanGoHelper.php](app/Helpers/CleanGoHelper.php)
  - Routes: `notifications.get` dan `notifications.read` di [routes/web.php](routes/web.php)

- **Middleware**
  - Role-checking middleware: [app/Http/Middleware/RoleMiddleware.php](app/Http/Middleware/RoleMiddleware.php) — membaca `Session::get('role')`

Catatan singkat:
- Session disimpan di database sesuai `config/session.php` (driver default database).
- Cookie `remember_username` di-set oleh LoginController untuk membantu isi form login.
- Semua anotasi penting telah ditambahkan pada models, helpers, middleware, dan beberapa controllers/views.

Jika Anda ingin saya melanjutkan menambahkan komentar baris-per-baris ke file controller lain yang belum sepenuhnya ter-annotate, beri tahu file mana atau saya teruskan ke semua controller di folder `app/Http/Controllers` sampai selesai.