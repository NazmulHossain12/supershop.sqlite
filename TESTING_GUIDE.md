# 🚀 Testing Supershop on Localhost

## Quick Start Guide

### ✅ What's Already Done
- Composer dependencies installed
- Frontend assets built
- Database seeded with sample data
- Roles configured

### 🎯 Start the Server

**Option 1: Using Built-in PHP Server**
```bash
php artisan serve
```
Then visit: **http://localhost:8000**

**Option 2: Using a Different Port**
```bash
php artisan serve --port=8080
```

---

## 👤 Creating an Admin User

Since the database is already seeded, you need to create an admin user:

### Method 1: Using Tinker (Recommended)
```bash
php artisan tinker
```

Then paste this code:
```php
$user = App\Models\User::factory()->create([
    'name' => 'Admin User',
    'email' => 'admin@supershop.com',
    'password' => bcrypt('password123')
]);
$user->assignRole('Super Admin');
exit
```

### Method 2: Register + Assign Role
1. Go to http://localhost:8000/register
2. Create an account
3. Then in tinker:
```php
$user = App\Models\User::where('email', 'your@email.com')->first();
$user->assignRole('Super Admin');
exit
```

---

## 🔍 Key URLs to Test

### Public Pages
- **Homepage**: http://localhost:8000
- **Shop**: http://localhost:8000/shop
- **Product Details**: Click any product
- **Cart**: http://localhost:8000/cart
- **Checkout**: http://localhost:8000/checkout

### Admin Pages (Login Required)
- **Login**: http://localhost:8000/login
- **Admin Dashboard**: http://localhost:8000/admin/dashboard
- **Products**: http://localhost:8000/admin/products
- **Orders**: http://localhost:8000/admin/orders
- **Coupons**: http://localhost:8000/admin/coupons
- **Marketing**: http://localhost:8000/admin/marketing
- **Reports**: http://localhost:8000/admin/reports

---

## 🧪 Test Credentials

After creating admin user:
- **Email**: admin@supershop.com
- **Password**: password123

---

## ✨ Features to Test

### 1. **Shopping Flow**
- Browse products
- Add to cart
- Apply coupon code (if you created one)
- Checkout
- View order confirmation

### 2. **Admin Functions**
- View sales dashboard
- Create/edit products
- Manage orders (change status)
- Create coupon codes
- View marketing analytics
- Check financial reports

### 3. **Customer Features**
- Create account
- Add product review
- Add to wishlist
- View order history
- Update profile

---

## 🛠️ Troubleshooting

### Server Running?
Check if the server started successfully. You should see:
```
Starting Laravel development server: http://127.0.0.1:8000
```

### Can't Access Admin?
Make sure you assigned the role:
```bash
php artisan tinker
App\Models\User::first()->assignRole('Super Admin');
```

### Database Issues?
Reset and reseed:
```bash
php artisan migrate:fresh --seed
```

### Asset Issues?
Rebuild assets:
```bash
npm run build
```

---

## 📊 Sample Data Available

After seeding, you should have:
- **Categories**: Electronics, Fashion, Home
- **Brands**: Apple, Samsung, Sony, etc.
- **Products**: ~15 sample products
- **Roles**: 7 roles (Super Admin, Store Manager, etc.)

---

## 🎨 What You'll See

- **Premium UI** with dark mode support
- **Responsive design** for mobile/desktop
- **Interactive charts** on admin dashboard
- **Product recommendations** on product pages
- **Low stock alerts** on admin dashboard
- **Conversion funnel** visualization

---

## ⚡ Next Steps

1. **Start Server**: `php artisan serve`
2. **Create Admin**: Use tinker method above
3. **Visit**: http://localhost:8000
4. **Login**: http://localhost:8000/login
5. **Explore**: Test all features!

---

## 🔔 Note: Server is Running

The development server is currently running in the background. Keep this terminal open or the server will stop.

To stop the server: Press `Ctrl+C` in the terminal running `php artisan serve`

---

**Happy Testing!** 🎉
