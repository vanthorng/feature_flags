* Laravel 12
* Vue 3
* Vite
* TailwindCSS v4
* Sanctum SPA Authentication
* Laravel Pennant (Dynamic Feature Flags)
* Invoice System
* User Management (Admin Only)

You can copy & paste this directly into your `README.md`.

---

# 🚀 Feature Flags Dashboard – Laravel 12 + Vue 3 + Tailwind + Sanctum + Pennant

A full-stack application built with **Laravel 12** (API) + **Vue 3** (SPA) using:

* 🔐 **Laravel Sanctum** authentication
* 🎛 **Dynamic Feature Flags** via **Laravel Pennant**
* 📄 **Invoice management** (admin creates, users view, print controlled by feature flags)
* 👥 **User management** (admin creates users + toggles admin role)
* 🎨 **Vue 3 + TailwindCSS v4** UI
* ⚡ Built with **Vite**

This project demonstrates how to build a real-world **dynamic feature flag system** (like LaunchDarkly, Flagsmith, etc.) while integrating user access control, printing permissions, and admin tools.

---

## 📦 Features

### 🔐 Authentication

* Login / Logout using **Laravel Sanctum**
* SPA-friendly cookie authentication
* Session regeneration for security

### 🎛 Dynamic Feature Flags (Pennant)

* Admin can **create any feature** dynamically (e.g. `"print_invoice"`, `"new-dashboard"`)
* Admin can **assign or remove features** for any user
* User features are read dynamically from database (`features` table)
* No static definitions needed in `FeatureServiceProvider`

### 📄 Invoice System

* Admin can create invoices for any user
* Users can view **all invoices**
* Only users with **`print_invoice`** feature can print invoices
* Invoices include:

  * items
  * subtotal
  * taxes
  * notes
  * totals

### 👥 User Management (Admin Only)

* Create users (name, email, password, admin flag)
* List all users
* Toggle admin role dynamically
* Real-time table update

### 🎨 Modern Frontend

* **Vue 3** (Composition or Options API supported)
* **TailwindCSS v4**
* Responsive + clean UI
* Dashboard layout with:

  * Left panel (user info, features, invoices)
  * Right admin panel (features, invoices, users)

---

## 🛠 Tech Stack

| Technology      | Usage                 |
| --------------- | --------------------- |
| Laravel 12      | Backend API           |
| Vue 3           | SPA Frontend          |
| Sanctum         | Authentication        |
| Laravel Pennant | Dynamic Feature Flags |
| MySQL / MariaDB | Database              |
| Vite            | Frontend bundler      |
| TailwindCSS 4   | Styling               |
| Axios           | HTTP Client           |

---

## 📁 Project Structure

```
/routes
    api.php          # API routes (Fully SPA/JSON)
    
/app
    Http/Controllers # FeatureController, UserAdminController, Invoice controllers
    Http/Middleware  # is_admin middleware
    Models           # User, Invoice, InvoiceItem
    Providers        # FeatureServiceProvider

/resources
    js/
      app.js         # Vue entry
      axios.js       # API instance
      components/    # Vue components: UserInvoices, FeatureManager, UserManager, etc.
      App.vue        # Main layout
    css/app.css      # Tailwind

/database
    migrations/      # users, invoices, features (Pennant)
```

---

# ⚙️ Installation

## 1. Clone the Repository

```bash
git clone https://github.com/vanthorng/feature_flags.git
cd feature-flags-dashboard
```

---

## 2. Install Backend Dependencies

```bash
composer install
```

---

## 3. Copy Environment File

```bash
cp .env.example .env
```

Set database credentials in `.env`:

```env
DB_DATABASE=feature_flags
DB_USERNAME=root
DB_PASSWORD=
```

Enable Sanctum SPA mode:

```env
SESSION_DOMAIN=localhost
SANCTUM_STATEFUL_DOMAINS=localhost:5173
```

---

## 4. Install Frontend Dependencies

```bash
npm install
```

---

## 5. Run Migrations

This includes:

* users table
* features (Pennant)
* invoices
* invoice items
* is_admin field

```bash
php artisan migrate
```

---

## 6. Start Development Servers

### Laravel API

```bash
php artisan serve
```

Runs at:
➡ [http://127.0.0.1:8000](http://127.0.0.1:8000)

### Vue Frontend

```bash
npm run dev
```

Runs at:
➡ [http://localhost:5173](http://localhost:5173)

---

# 👤 Default Admin User

Create an admin manually:

```bash
php artisan tinker

>>> \App\Models\User::create([
       'name' => 'Admin',
       'email' => 'admin@example.com',
       'password' => bcrypt('123456789'),
       'is_admin' => true,
   ]);
```

Login using:

```
admin@example.com
123456789
```

---

# 🧪 API Overview

### Auth

```http
POST /api/login
POST /api/logout
GET  /api/user
```

### Feature Flags (Admin)

```http
GET  /api/features
POST /api/features/create
POST /api/features/activate     { feature, user_id }
POST /api/features/deactivate   { feature, user_id }
```

### Users (Admin)

```http
GET  /api/admin/users
POST /api/admin/users
POST /api/admin/users/{id}/toggle-admin
```

### Invoices (User)

```http
GET /api/invoices
GET /api/invoices/{id}
```

### Invoices (Admin)

```http
GET /api/admin/invoices
POST /api/admin/invoices
```

---

# 🎯 Feature Logic (How It Works)

## Dynamic features stored like:

| id | name          | scope             | value |
| -- | ------------- | ----------------- | ----- |
| 1  | print_invoice | App\Models\User|2 | true  |
| 2  | print_invoice | global            | false |

## When user logs in:

`/api/user` computes:

```json
{
  "features": {
    "print_invoice": true,
    "new-dashboard": false
  }
}
```

Vue uses:

```js
hasFeature('print_invoice')
```

And passes it to:

```vue
<UserInvoices :can-print="hasFeature('print_invoice')" />
```

---

# 🖨 Invoice Printing Logic

* Everyone sees all invoices
* **Only** users with `print_invoice` see the **Print** button
* Admin grants this in **Feature Manager**

---

# 🧹 Useful Commands

**Clear caches:**

```bash
php artisan optimize:clear
```

**Rebuild frontend:**

```bash
npm run build
```

---

# 📄 License

Open-source — feel free to use and customize.

---

# 🙌 Need Help?

If you'd like, I can also generate:

* Full SQL seeders
* API documentation (Swagger-style)
* ER Diagram (DB Schema)
* Deployment guide (Docker or shared hosting)

Just ask:
👉 **“Generate deployment guide”** or
👉 **“Generate database seeder”**
