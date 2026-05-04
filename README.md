# 🏢 Icon Venue & Suites Booking System

A professional, full-featured venue and suite booking management system built with Laravel 12, designed for Icon Venue and Suites to streamline their booking operations, client management, and payment processing.

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![License](https://img.shields.io/badge/License-Proprietary-yellow)

---

## 🌟 Features

### 🌐 Public Features
- **Browse Venues**: Beautiful gallery of available venues and suites
- **Real-time Availability**: Check venue availability for specific dates
- **Cost Calculator**: Automatic price estimation based on booking duration
- **Multiple Contact Channels**: Phone, Email, WhatsApp, Messenger, Facebook
- **Responsive Design**: Mobile-friendly interface

### 👥 Staff Features
- **Booking Management**: Create and manage bookings on behalf of clients
- **Payment Recording**: Record and track payment information
- **Payment Verification**: Verify payments through messaging platforms
- **Dashboard**: Overview of bookings and key metrics

### 🔐 Admin Features
- **Complete Venue Management**: CRUD operations for venues
- **Staff Management**: Create and manage staff accounts
- **System Settings**: Configure contact information and business details
- **Reports & Analytics**: Comprehensive booking and revenue reports
- **Full System Control**: Access to all features

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- MariaDB/MySQL
- XAMPP (recommended for Windows)

### Installation

1. **Create Database**
   ```sql
   CREATE DATABASE venue_booking;
   ```

2. **Run Setup Script**
   
   **Windows:**
   ```bash
   setup.bat
   ```
   
   **Manual Setup:**
   ```bash
   composer install
   php artisan migrate
   php artisan db:seed
   php artisan storage:link
   php artisan serve
   ```

3. **Access Application**
   - Public Site: http://localhost:8000
   - Admin Panel: http://localhost:8000/login

### Default Credentials

| Role  | Email                    | Password  |
|-------|--------------------------|-----------|
| Admin | admin@iconvenue.com      | admin123  |
| Staff | staff@iconvenue.com      | staff123  |

⚠️ **Important**: Change these passwords immediately after first login!

---

## 📚 Documentation

Comprehensive documentation is available in the following files:

- **[SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md)** - Detailed installation and configuration guide
- **[PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md)** - Complete project architecture and features
- **[SYSTEM_FLOW.md](SYSTEM_FLOW.md)** - User workflows and system processes
- **[QUICK_REFERENCE.md](QUICK_REFERENCE.md)** - Quick reference for common tasks

---

## 🏗️ Technology Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Database**: MariaDB/MySQL
- **Frontend**: Tailwind CSS, Font Awesome 6
- **JavaScript**: jQuery
- **Server**: Apache (XAMPP)

---

## 📁 Project Structure

```
venue-booking-system/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/               # Eloquent models
│   └── Middleware/           # Custom middleware
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   └── views/                # Blade templates
├── routes/
│   └── web.php               # Application routes
├── public/                   # Public assets
└── storage/                  # File storage
```

---

## 🎯 Key Functionalities

### For Clients (Public)
1. Browse all available venues
2. View detailed venue information
3. Check availability for specific dates
4. Get instant cost estimates
5. Contact staff through multiple channels

### For Staff
1. Create bookings for clients
2. Manage booking status
3. Record payment information
4. Verify payments
5. Track booking history

### For Administrators
1. Manage venues (add, edit, delete)
2. Upload venue images
3. Create and manage staff accounts
4. Configure system settings
5. Generate comprehensive reports
6. Monitor system activity

---

## 🔒 Security Features

- ✅ Role-based access control (Admin, Staff)
- ✅ CSRF protection on all forms
- ✅ Password hashing with Bcrypt
- ✅ SQL injection prevention via Eloquent ORM
- ✅ XSS protection with Blade templating
- ✅ Secure authentication system

---

## 📊 Database Schema

### Core Tables
- **users** - System users (Admin & Staff)
- **roles** - User roles
- **venues** - Venue information
- **bookings** - Booking records
- **payments** - Payment records
- **contact_settings** - System contact information

---

## 🎨 Design Highlights

- **Modern UI**: Clean, professional interface with purple gradient theme
- **Responsive**: Mobile-first design approach
- **Intuitive**: Easy-to-navigate admin panel
- **Visual Feedback**: Color-coded status indicators
- **Smooth Animations**: Card hover effects and transitions

---

## 🔧 Common Commands

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear

# Create storage link
php artisan storage:link

# Reset database
php artisan migrate:fresh --seed
```

---

## 🐛 Troubleshooting

### Database Connection Error
- Verify MariaDB is running in XAMPP
- Check `.env` database credentials
- Ensure database exists

### Images Not Displaying
```bash
php artisan storage:link
```

### Permission Errors
```bash
chmod -R 775 storage bootstrap/cache
```

For more troubleshooting tips, see [QUICK_REFERENCE.md](QUICK_REFERENCE.md)

---

## 📈 Future Enhancements

- [ ] Email notifications
- [ ] SMS integration
- [ ] Online payment gateway
- [ ] Calendar view
- [ ] PDF invoice generation
- [ ] Advanced analytics
- [ ] Mobile application

---

## 📞 Support

For technical support or inquiries:
- Email: support@iconvenue.com
- Documentation: See included MD files

---

## 📝 License

Proprietary - Icon Venue & Suites  
All rights reserved.

---

## 👨‍💻 Development

**Version**: 1.0.0  
**Status**: Production Ready  
**Last Updated**: January 2026

Built with ❤️ using Laravel, PHP, and modern web technologies.

---

## 🎓 Getting Started Guide

### For First-Time Users

1. **Setup the System**
   - Follow instructions in [SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md)
   - Create the database
   - Run migrations and seeders

2. **Login as Admin**
   - Use default credentials
   - Change password immediately

3. **Configure Settings**
   - Go to Settings
   - Update contact information
   - Add business hours

4. **Add Venues**
   - Navigate to Venues
   - Create your first venue
   - Upload images

5. **Create Staff Accounts**
   - Go to Staff Management
   - Add staff members
   - Assign appropriate roles

6. **Start Taking Bookings**
   - Staff can now create bookings
   - Record payments
   - Manage client relationships

### For Developers

1. **Review Documentation**
   - Read [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md)
   - Understand [SYSTEM_FLOW.md](SYSTEM_FLOW.md)

2. **Explore Codebase**
   - Check controllers in `app/Http/Controllers/`
   - Review models in `app/Models/`
   - Examine views in `resources/views/`

3. **Database Structure**
   - Review migrations in `database/migrations/`
   - Understand relationships between models

4. **Customization**
   - Modify views for branding
   - Extend functionality as needed
   - Add new features

---

## ⭐ Key Highlights

- ✨ **Modern & Professional**: Clean, intuitive interface
- 🚀 **Fast & Efficient**: Optimized Laravel application
- 🔒 **Secure**: Role-based access control
- 📱 **Responsive**: Works on all devices
- 🎯 **Feature-Rich**: Complete booking management solution
- 📊 **Analytics**: Comprehensive reporting system
- 🛠️ **Maintainable**: Well-structured, documented code

---

**Ready to manage your venue bookings professionally? Get started now!**

For detailed instructions, see [SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md)
