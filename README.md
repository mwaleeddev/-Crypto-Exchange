# 🚀 Crypto Exchange Platform

A full-featured cryptocurrency trading platform built with **Laravel** and **Vue.js**. This platform allows users to trade cryptocurrencies with real-time order matching, commission handling, and live updates.

---

## 🔗 Live Demo
Coming Soon

---

## 📦 Repository
```bash
git clone https://github.com/mwaleeddev/-Crypto-Exchange.git
cd -Crypto-Exchange
```

---

## 🛠️ Technology Stack

### Backend
- Laravel 12 – PHP Framework
- MySQL / PostgreSQL – Database
- Pusher – Real-time broadcasting
- Laravel Sanctum – API Authentication

### Frontend
- Vue.js 3 (Composition API)
- Vite – Build Tool
- Tailwind CSS 3 – Styling
- Vue Router 4 – Client-side Routing
- Axios – HTTP Client

### Real-time Features
- Laravel Echo – WebSocket integration
- Pusher Channels – Real-time event broadcasting

---

## 📋 Prerequisites

Ensure you have the following installed:
- PHP 8.2+
- Composer
- Node.js 18+ & npm
- MySQL 8.0+ or PostgreSQL 14+
- Git

---

## 🚀 Quick Setup (5 Minutes)

### Step 1: Clone & Install
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### Step 2: Configure Environment
Update your `.env` file:
```env
APP_NAME="Crypto Exchange"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crypto_exchange
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
PUSHER_SCHEME=https
```

### Step 3: Database Setup
```bash
php artisan migrate
php artisan db:seed
```

(Optional)
```bash
php artisan db:seed --class=DatabaseSeeder
```

### Step 4: Build Assets
```bash
npm run build
# OR
npm run dev
```

### Step 5: Start Servers
```bash
php artisan serve
npm run dev
```

Visit: **http://localhost:8000**

---

## 🔧 Detailed Configuration

### Pusher Setup
1. Create an account on pusher.com
2. Create a Channels app
3. Copy credentials into `.env`
4. Verify `config/broadcasting.php`

### Database Creation
```sql
CREATE DATABASE crypto_exchange;
```

### Mail Configuration (Optional)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@crypto-exchange.com"
MAIL_FROM_NAME="Crypto Exchange"
```

---

## 📊 Default Test Accounts

### Trader 1
- Email: test@example.com
- Password: password
- Balance: $100,000
- BTC: 1.5
- ETH: 10

### Trader 2
- Email: trader2@example.com
- Password: password
- Balance: $50,000
- BTC: 0.5

---

## 🎯 Features Implemented

### Core Trading
- Limit Buy/Sell Orders
- Order Book (Real-time)
- Matching Engine (Full matches)
- 1.5% Commission System
- Balance & Asset Management

### Real-time
- Live Order Updates
- Balance Updates
- Order Match Notifications
- Private Channels

### UI/UX
- Dark Mode UI
- Responsive Design
- Portfolio Dashboard
- Order History

### Security
- Authentication (Login/Register)
- CSRF Protection
- Sanctum API Security
- Atomic Transactions
- Input Validation

---

## 📁 Project Structure
```
crypto-exchange/
├── app/
│   ├── Http/Controllers/API/
│   ├── Models/
│   ├── Services/
│   └── Events/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── js/
│   │   ├── components/
│   │   ├── App.vue
│   │   └── app.js
│   └── views/
├── routes/
│   ├── api.php
│   └── web.php
└── config/
```

---

## 🔄 API Endpoints

**Auth Required (Sanctum)**

| Method | Endpoint | Description |
|------|---------|-------------|
| GET | /api/profile | User balance & assets |
| GET | /api/orders?symbol=BTC | Order book |
| POST | /api/orders | Create order |
| POST | /api/orders/{id}/cancel | Cancel order |
| GET | /api/orders/user | User orders |

---

## 💼 Business Logic

- Buy Orders: Lock USD, match sell orders
- Sell Orders: Lock assets, match buy orders
- Full order matching only
- 1.5% commission on both sides

### Example Trade
```
Buy: 0.01 BTC @ $95,000
Volume: $950
Commission: $14.25
Total Paid: $964.25
Seller Receives: 0.00985 BTC
```

---

## 🚨 Troubleshooting

### Clear Cache
```bash
php artisan optimize:clear
```

### Assets Not Loading
```bash
npm run build
```

### Pusher Issues
- Verify credentials
- Check cluster

### Database Issues
```bash
php artisan migrate:fresh --seed
```

---

## 🧪 Running Tests
```bash
php artisan test
php artisan test --filter=OrderTest
```

---

## 📈 Deployment

### Production Optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

Set:
```env
APP_ENV=production
APP_DEBUG=false
```

Deployment Options:
- Shared Hosting
- VPS (Nginx + PHP-FPM)
- Laravel Forge / Vapor

---

## 🐛 Debugging
```bash
tail -f storage/logs/laravel.log
```

---

## 🤝 Contributing
1. Fork repository
2. Create branch
3. Commit changes
4. Push & open PR

---

## 📄 License
MIT License

---

## 👥 Author
**Muhammad Waleed**

---

## 🙏 Acknowledgments
- Laravel Team
- Vue.js Team
- Tailwind CSS
- Pusher

---

## 📞 Support
📧 mwaleeddev@gmail.com

Create an issue on GitHub for help.

