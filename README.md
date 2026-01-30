# Supershop - Laravel E-Commerce Platform

A comprehensive Laravel 11 e-commerce application with advanced features including product management, shopping cart, checkout, analytics, and marketing tools.

## 🚀 Features

### Core E-Commerce
- **Product Management** - Categories, Brands, Products with images
- **Shopping Cart** - Session-based cart with quantity controls
- **Checkout System** - Guest & registered user checkout
- **Order Management** - Complete order tracking and history
- **Product Variants** - Support for sizes, colors, and variations

### Customer Features
- **Product Search & Filters** - Advanced filtering by category, price
- **Product Reviews** - Star ratings and comments
- **Wishlist** - Save favorites for later
- **User Profiles** - Extended profiles with shipping addresses

### Promotions & Marketing
- **Coupons & Discounts** - Percentage or fixed amount discounts
- **Product Recommendations** - "You May Also Like" suggestions
- **Marketing Analytics** - Campaign tracking, conversion funnels
- **Google Analytics & Facebook Pixel** - Integrated tracking

### Admin Dashboard
- **Sales Analytics** - Revenue charts, top products
- **Order Processing** - Status management and updates
- **Inventory Alerts** - Low stock warnings
- **Financial Reports** - Accounting ledger, P&L statements
- **Campaign Management** - ROI tracking, CPA analysis

### Technical Features
- **Authentication** - Laravel Breeze
- **Authorization** - Role-based access (7 roles)
- **Email Notifications** - Order confirmations and updates
- **Premium UI** - Tailwind CSS with dark mode
- **Charts & Visualizations** - Chart.js integration

## 📋 Requirements

- PHP 8.2+
- Composer
- Node.js & NPM
- SQLite (or MySQL/PostgreSQL)

## 🛠️ Installation

1. **Clone the repository**
```bash
git clone https://github.com/YOUR_USERNAME/supershop.git
cd supershop
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Create database**
```bash
touch database/database.sqlite
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Seed database (optional)**
```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=ContentSeeder
```

7. **Build assets**
```bash
npm run build
```

8. **Start development server**
```bash
php artisan serve
```

Visit: http://localhost:8000

## 👤 Default Users

After seeding, you can create an admin user:
```bash
php artisan tinker
$user = User::first();
$user->assignRole('Super Admin');
```

## 📊 Marketing Setup

1. **Google Analytics**: Add your `GOOGLE_ANALYTICS_ID` to `.env`
2. **Facebook Pixel**: Add your `FACEBOOK_PIXEL_ID` to `.env`
3. See `ANALYTICS_SETUP.md` for detailed instructions

## 🗂️ Database Schema

- **24+ Tables** including:
  - Users, Roles, Permissions
  - Products, Categories, Brands, Variants
  - Orders, Order Items
  - Reviews, Wishlists
  - Coupons, Campaigns
  - Marketing Metrics, Transactions

## 🎨 Tech Stack

- **Framework**: Laravel 11
- **Frontend**: Blade, Tailwind CSS, Alpine.js
- **Database**: SQLite (configurable for MySQL/PostgreSQL)
- **Charts**: Chart.js
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission
- **Build Tool**: Vite

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── Admin/          # Admin controllers
│   └── ...             # Public controllers
├── Models/             # Eloquent models
├── Services/           # Business logic (CartService)
└── Mail/              # Email templates

resources/
├── views/
│   ├── admin/         # Admin views
│   ├── components/    # Blade components
│   └── ...            # Public views
└── css/               # Styles

database/
├── migrations/        # Database migrations
└── seeders/          # Data seeders
```

## 🔐 Security

- CSRF protection
- XSS prevention
- SQL injection protection
- Role-based access control
- Secure password hashing

## 📝 License

Open-source. Free to use and modify.

## 👨‍💻 Development

**Key Commands:**
```bash
# Run tests
php artisan test

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Watch for changes (dev)
npm run dev
```

## 🤝 Contributing

Contributions welcome! Please submit pull requests or open issues for bugs and feature requests.

## 📧 Support

For questions or support, please open an issue on GitHub.

---

**Built with Laravel 11** 🚀
