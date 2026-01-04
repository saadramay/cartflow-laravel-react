# CartFlow - E-commerce Shopping Cart System

A simple yet feature-rich e-commerce shopping cart system built with Laravel and React for the Trustfactory technical assessment.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red)
![React](https://img.shields.io/badge/React-18.x-blue)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-cyan)

## 🚀 Features

### Core Functionality
- ✅ User Authentication (Laravel Breeze + React)
- ✅ Product Browsing with real-time stock display
- ✅ Shopping Cart Management (add, update, remove items)
- ✅ Persistent cart storage (database, not session)
- ✅ Real-time cart count badge in navbar
- ✅ Checkout process with stock validation

### Advanced Features
- ✅ **Low Stock Notification**: Automated email alerts when product stock ≤ 5 units
- ✅ **Daily Sales Report**: Scheduled job that sends comprehensive sales report every evening
- ✅ **Queue System**: Background job processing for emails
- ✅ **Toast Notifications**: Real-time user feedback for all actions
- ✅ **Responsive Design**: Works on desktop, tablet, and mobile

## 📋 Tech Stack

**Backend:**
- Laravel 11.x
- MySQL Database
- Queue System (Database driver)
- Laravel Scheduler (Cron jobs)

**Frontend:**
- React 18.x
- Inertia.js
- Tailwind CSS
- React Hot Toast

**Development Tools:**
- Vite
- Composer
- NPM

## 🛠️ Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### Setup Steps

1. **Clone the repository**
```bash
git clone https://github.com/YOUR_USERNAME/cartflow-laravel-react.git
cd cartflow-laravel-react
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install NPM dependencies**
```bash
npm install
```

4. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Update `.env` file**
```env
DB_DATABASE=cartflow
DB_USERNAME=root
DB_PASSWORD=your_password

MAIL_MAILER=log
QUEUE_CONNECTION=database
```

6. **Create database**
```bash
mysql -u root -p
CREATE DATABASE cartflow;
EXIT;
```

7. **Run migrations**
```bash
php artisan migrate
```

8. **Seed database**
```bash
php artisan db:seed
```

This creates:
- Admin user: `admin@cartflow.com` / `password`
- 8 sample products (3 with low stock for testing)

9. **Create jobs table**
```bash
php artisan queue:table
php artisan migrate
```

## 🚀 Running the Application

### Development Mode

**Terminal 1: Laravel Server**
```bash
php artisan serve
```
Runs on: http://127.0.0.1:8000

**Terminal 2: Vite Dev Server**
```bash
npm run dev
```
Compiles React assets

**Terminal 3: Queue Worker (for emails)**
```bash
php artisan queue:work
```
Processes background jobs

### Testing the Scheduler

The daily sales report runs at 8:00 PM daily. To test it manually:
```bash
php artisan schedule:work
```

Or dispatch the job directly:
```bash
php artisan tinker
>>> App\Jobs\SendDailySalesReport::dispatch();
```

## 📧 Email Testing

Emails are logged to `storage/logs/laravel.log` by default.

### Test Low Stock Email
1. Add a product with ≤5 stock to cart
2. Check queue worker terminal
3. View email in `storage/logs/laravel.log`

### Test Daily Sales Report
1. Place some orders
2. Run: `php artisan tinker`
3. Execute: `App\Jobs\SendDailySalesReport::dispatch();`
4. Check `storage/logs/laravel.log`

## 👤 Default Users

**Admin (receives emails)**
- Email: `admin@cartflow.com`
- Password: `password`

**Test User (create via registration)**
- Use any email
- Password: `password`

## 🗂️ Project Structure
```
cartflow-laravel-react/
├── app/
│   ├── Http/Controllers/
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   └── CheckoutController.php
│   ├── Jobs/
│   │   ├── SendLowStockNotification.php
│   │   └── SendDailySalesReport.php
│   ├── Mail/
│   │   ├── LowStockAlert.php
│   │   └── DailySalesReport.php
│   └── Models/
│       ├── Product.php
│       ├── CartItem.php
│       ├── Order.php
│       └── OrderItem.php
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Products/Index.jsx
│   │   │   └── Cart/Index.jsx
│   │   └── Layouts/
│   │       └── AuthenticatedLayout.jsx
│   └── views/
│       └── emails/
│           ├── low-stock-alert.blade.php
│           └── daily-sales-report.blade.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── web.php
└── README.md
```

## 🧪 Testing Workflow

1. **Register a new user** at http://127.0.0.1:8000/register
2. **Browse products** - Click "Products" in navbar
3. **Add to cart** - Click "Add to Cart" on any product
   - See toast notification
   - Cart count badge updates
4. **View cart** - Click "Cart" in navbar
5. **Update quantities** - Use +/- buttons
6. **Remove items** - Click "Remove"
7. **Checkout** - Click "Proceed to Checkout"
   - Stock is reduced
   - Cart is cleared
   - Order is created
8. **Check emails** - View `storage/logs/laravel.log`

## 📊 Database Schema

### Products
- `id`, `name`, `description`, `price`, `stock_quantity`, `image`, `timestamps`

### Cart Items
- `id`, `user_id` (FK), `product_id` (FK), `quantity`, `timestamps`
- Unique constraint: `(user_id, product_id)`

### Orders
- `id`, `user_id` (FK), `total`, `status`, `timestamps`

### Order Items
- `id`, `order_id` (FK), `product_id` (FK), `quantity`, `price`, `timestamps`

## ⚙️ Key Features Implementation

### Low Stock Notification
- Triggered when product with ≤5 stock is added to cart
- Uses Laravel Queue for async processing
- Sends email to admin user

### Daily Sales Report
- Scheduled via Laravel Scheduler
- Runs daily at 8:00 PM
- Includes: total orders, revenue, items sold, detailed order list
- Only sends if there were sales that day

### Cart Management
- Stored in database, not session
- Persists across devices
- Real-time count badge
- Validates stock before checkout

## 🔧 Configuration

### Change Scheduler Time
Edit `app/Console/Kernel.php`:
```php
$schedule->job(new SendDailySalesReport())
         ->dailyAt('20:00'); // Change time here
```

### Change Low Stock Threshold
Edit `app/Models/Product.php`:
```php
public function isLowStock(): bool
{
    return $this->stock_quantity <= 5; // Change threshold here
}
```

### Change Email Driver
Edit `.env`:
```env
MAIL_MAILER=smtp  # Use SMTP instead of log
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

## 📝 Development Notes

**Time to Complete:** ~12 hours

**Key Decisions:**
- Used React (not Livewire) to showcase frontend skills
- Chose database queue for simplicity (can scale to Redis)
- Implemented toast notifications for better UX
- Added cart count badge for improved navigation
- Used gradient backgrounds and animations for modern look

**Challenges Solved:**
- Real-time cart updates across components
- Stock validation at checkout
- Async email sending without blocking UI
- Responsive design for all screen sizes

## 🚀 Production Considerations

For production deployment, consider:

1. **Queue Driver**: Switch to Redis for better performance
```env
QUEUE_CONNECTION=redis
```

2. **Mail Service**: Use Mailgun, SendGrid, or AWS SES
3. **Caching**: Implement Redis caching for products
4. **CDN**: Use for static assets
5. **Database**: Optimize with indexes
6. **Security**: Enable HTTPS, CSRF protection, rate limiting

## 📦 API Endpoints

### Products
- `GET /api/products` - List all products
- `GET /api/products/{id}` - Get single product

### Cart
- `GET /api/cart` - Get cart items
- `POST /api/cart` - Add to cart
- `PATCH /api/cart/{id}` - Update quantity
- `DELETE /api/cart/{id}` - Remove item
- `DELETE /api/cart` - Clear cart

### Checkout
- `POST /api/checkout` - Process order

## 🤝 Contributing

This is a technical assessment project, but improvements are welcome!

## 📄 License

Open source - MIT License

## 👨‍💻 Author

**Saad Arshad**
- Email: srawmay@gmail.com
- GitHub: [@saad-arshad](https://github.com/YOUR_USERNAME)
- LinkedIn: [Saad Arshad](https://linkedin.com/in/YOUR_PROFILE)

---

**Built with ❤️ for Trustfactory Technical Assessment**