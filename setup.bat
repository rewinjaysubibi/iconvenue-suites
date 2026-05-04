@echo off
echo ========================================
echo Icon Venue & Suites - Setup Script
echo ========================================
echo.

echo Step 1: Installing dependencies...
call composer install
echo.

echo Step 2: Running migrations...
php artisan migrate
echo.

echo Step 3: Seeding database...
php artisan db:seed
echo.

echo Step 4: Creating storage link...
php artisan storage:link
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Default Login Credentials:
echo Admin: admin@iconvenue.com / admin123
echo Staff: staff@iconvenue.com / staff123
echo.
echo Starting development server...
echo Visit: http://localhost:8000
echo.
php artisan serve
