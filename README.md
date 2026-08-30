# 💳 Dompetify (FinanceApp)

> Modern Personal Finance Tracking Ecosystem with **Laravel 12 REST API**, **MySQL**, **Google Gemini Vision AI Receipt OCR**, and **Flutter 3+** Mobile Client.

---

## 🌟 Key Features

1. **Multi-Wallet Engine**:
   - Manage Bank accounts (BCA, BRImo), E-Wallets (GoPay, OVO, DANA, ShopeePay), Cash, and Savings.
   - Real-time Total Net Worth computation.
   - Atomic inter-wallet transfers with admin fee tracking.

2. **Smart AI Receipt Scanner (Gemini Flash Vision OCR)**:
   - Photograph or upload receipts from supermarkets, cafes, and restaurants.
   - Automatically extracts merchant name, timestamp, total amount, line items, and Indonesian categories.

3. **Indonesian Banking & E-Wallet Notification Webhook**:
   - Auto-record transactions from Android push notifications / SMS (BCA, BRImo, GoPay, OVO, DANA).
   - Smart Indonesian currency parser (`Rp 50.000`, `150.000,00`).

4. **Production-Ready Flutter Frontend**:
   - State management with **Riverpod**.
   - Network layer with **Dio**.
   - Light Blue & White glassmorphic theme with **Plus Jakarta Sans**.
   - Interactive financial analytics with **FL Chart**.

---

## 🏗️ Architecture & Tech Stack

- **Backend**: Laravel 12, PHP 8.4+, MySQL
- **AI OCR**: Google Gemini 1.5 Flash Vision API
- **Frontend**: Flutter 3 (Dart 3), Flutter Riverpod, Dio, FL Chart
- **Testing**: PHPUnit feature test suite (20/20 tests passing)
- **Code Standards**: Laravel Pint

---

## 🚀 Getting Started

### 1. Backend Setup (Laravel API)

```bash
# Clone the repository
git clone https://github.com/MohammadKevin/dompetify.git
cd dompetify

# Install dependencies
composer install

# Environment configuration
cp .env.example .env
php artisan key:generate

# Configure MySQL in .env (or run Laragon / XAMPP)
# DB_CONNECTION=mysql
# DB_DATABASE=finance_db
# GEMINI_API_KEY=your_gemini_api_key

# Run migrations & seeders
php artisan migrate --seed

# Create storage symlink
php artisan storage:link

# Start development server
php artisan serve
```

### 2. Frontend Setup (Flutter)

```bash
# Open frontend directory
cd frontend

# Get Flutter packages
flutter pub get

# Run on Android / iOS / Desktop / Web
flutter run
```

---

## 📖 API Endpoints Summary

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/wallets` | List all wallets + Net Worth calculation |
| `POST` | `/api/wallets` | Create dynamic wallet |
| `PUT` | `/api/wallets/{id}` | Update wallet details |
| `DELETE` | `/api/wallets/{id}` | Archive / Delete wallet |
| `GET` | `/api/categories` | List categories (Expense / Income) |
| `GET` | `/api/transactions` | Filtered paginated ledger + period statistics |
| `POST` | `/api/transactions` | Record transaction with atomic balance sync |
| `DELETE` | `/api/transactions/{id}`| Delete transaction & restore balances |
| `POST` | `/api/receipts/scan` | Multipart receipt upload -> Gemini Vision OCR |
| `POST` | `/api/webhook/notification` | Android notification listener hook |

---

## 🧪 Running Automated Tests

```bash
php artisan test
```

---

## 📄 License
Open-sourced under the [MIT license](LICENSE).
