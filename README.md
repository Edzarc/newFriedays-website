# Friedays Bocaue - Restaurant Ordering System

A complete full-stack restaurant ordering and management system built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

### Customer Features
- User registration and login with email verification
- Browse menu by categories (Chicken & Fried Items, Sides & Sandwiches, Beverages, Pasta & Mains)
- Real-time search and category filtering
- Shopping cart with localStorage persistence
- Loyalty program with 4 tiers (Bronze, Silver, Gold, Platinum)
- Order checkout with multiple payment options
- Real-time queue status tracking

### Admin Features
- Secure admin login
- Order management with status updates
- User management with loyalty tier adjustments
- Comprehensive analytics and reporting
- CSV/PDF export functionality
- Queue management system
- Revenue and order analytics with charts

## Technology Stack

- **Backend:** PHP 8.0+, MySQL 8.0+
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Architecture:** MVC pattern
- **Database:** MySQL with prepared statements
- **Charts:** Chart.js for analytics

## Installation

1. **Database Setup:**
   ```bash
   # Import the database schema
   mysql -u root -p < database.sql
   ```

2. **Web Server Configuration:**
   - Place the project in your web server's document root
   - Ensure PHP 8.0+ and MySQL 8.0+ are installed
   - Update database credentials in `config/db.php`

3. **Permissions:**
   - Ensure the web server has write permissions for session handling

## Database Schema

### Tables
- `users` - Customer information and loyalty data
- `products` - Menu items with categories and pricing
- `orders` - Order headers with customer and payment info
- `order_items` - Individual items within orders
- `loyalty_tiers` - Tier definitions with benefits
- `queue` - Queue management for order processing

## Usage

### Customer Flow
1. Register/Login with email and password
2. Browse menu and add items to cart
3. Proceed to checkout with order type selection
4. View queue status after ordering
5. Track loyalty tier progression in dashboard

### Admin Flow
1. Login with admin credentials (admin@friedays.com / admin123)
2. View dashboard with key metrics
3. Manage orders and update statuses
4. View user information and adjust loyalty tiers
5. Generate reports and analytics

## Security Features

- Password hashing with `password_hash()`
- Prepared statements for SQL injection prevention
- Session-based authentication
- Input validation and sanitization
- Admin access controls

## File Structure

```
friedays-bocaue/
├── config/
│   └── db.php                 # Database configuration
├── includes/
│   └── functions.php          # Common functions and utilities
├── models/                    # Data models (if expanded)
├── controllers/               # MVC controllers
│   ├── auth.php              # Authentication logic
│   ├── menu.php              # Menu display logic
│   ├── checkout.php          # Order processing
│   ├── queue.php             # Queue management
│   └── dashboard.php         # User dashboard
├── views/                     # HTML templates
│   ├── home.php
│   ├── login.php
│   ├── register.php
│   ├── menu.php
│   ├── checkout.php
│   ├── queue.php
│   └── dashboard.php
├── admin/                     # Admin section
│   ├── controllers/
│   ├── views/
│   └── models/
├── public/                    # Static assets
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── main.js
│   │   ├── menu.js
│   │   ├── checkout.js
│   │   ├── queue.js
│   │   ├── admin.js
│   │   ├── admin-orders.js
│   │   ├── admin-users.js
│   │   └── admin-analytics.js
│   └── images/
├── api/                       # AJAX endpoints
├── database.sql               # Database schema
└── index.php                  # Main entry point
```

## API Endpoints

### Customer APIs
- `GET /api/queue_status.php` - Get current queue status

### Admin APIs
- `GET /api/admin_stats.php` - Dashboard statistics
- `GET /api/admin_recent_orders.php` - Recent orders
- `POST /api/admin_serve_next.php` - Serve next order in queue
- `POST /api/admin_update_order.php` - Update order status
- `GET /api/admin_export_orders.php` - Export orders
- `POST /api/admin_update_user_tier.php` - Update user loyalty tier
- `GET /api/admin_user_orders.php` - Get user's order history
- `GET /api/admin_revenue_chart.php` - Revenue chart data
- `GET /api/admin_order_type_chart.php` - Order type distribution
- `GET /api/admin_export_analytics.php` - Export analytics

## Loyalty Program

### Tiers
- **Bronze** (0-999 spent): Welcome discount, 0% discount
- **Silver** (1000-4999 spent): 5% discount, free delivery over ₱500
- **Gold** (5000-9999 spent): 10% discount, free delivery, priority queue
- **Platinum** (10000+ spent): 15% discount, free delivery, priority queue, exclusive items

## Development Notes

- Uses MVC architecture for maintainability
- Responsive design with mobile-first approach
- Real-time updates using JavaScript polling
- Local storage for cart persistence
- Chart.js integration for analytics visualization

## Testing

Test the complete user journey:
1. User registration → login → menu browsing → cart → checkout → queue
2. Admin login → dashboard → order management → user management → analytics

## Future Enhancements

- Email notifications for order status updates
- Payment gateway integration
- Mobile app development
- Advanced reporting features
- Inventory management
- Multi-location support