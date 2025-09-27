# Invoice System - Cron Setup Guide

## Overview
The invoice system uses Laravel's task scheduler to automatically generate invoices and check for overdue payments.

## Scheduled Tasks

### 1. Monthly Invoice Generation
- **Command**: `invoices:generate-monthly`
- **Schedule**: Every 1st of the month at midnight (00:00)
- **Purpose**: Automatically generates monthly invoices for all active enrollments

### 2. Overdue Invoice Check
- **Command**: `invoices:check-overdue`
- **Schedule**: Daily at 6:00 AM
- **Purpose**: Checks and updates invoice status to 'overdue' for past due invoices

## Setup Instructions

### Step 1: Add Cron Entry
Add this line to your server's crontab:

```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### Step 2: Verify Setup
Test the scheduler is working:

```bash
# Test monthly generation
php artisan invoices:generate-monthly

# Test overdue check
php artisan invoices:check-overdue

# View scheduled tasks
php artisan schedule:list
```

### Step 3: Monitor Logs
Check Laravel logs for scheduler execution:

```bash
tail -f storage/logs/laravel.log
```

## Manual Commands

### Generate Invoices for Specific Date
```bash
php artisan invoices:generate-monthly --date=2025-10-01
```

### Check Overdue Invoices
```bash
php artisan invoices:check-overdue
```

## Features

### Auto-Generation
- Generates invoices based on enrollment payment method
- Monthly: Creates 1 invoice per month
- Semester: Creates 1 invoice per 6 months
- Yearly: Creates 1 invoice per year

### Duplicate Prevention
- System checks for existing invoices for the same period
- No duplicate invoices will be created

### Due Date Calculation
- Monthly invoices: Due in 7 days
- Semester invoices: Due in 14 days
- Yearly invoices: Due in 30 days

## Troubleshooting

### Scheduler Not Running
1. Check if cron is installed and running
2. Verify the cron entry is correct
3. Check file permissions
4. Review Laravel logs

### Invoices Not Generated
1. Check if there are active enrollments
2. Verify enrollment dates are correct
3. Check payment amounts are set
4. Review service logs

### Permission Issues
```bash
# Fix file permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## Production Considerations

1. **Log Rotation**: Set up log rotation for Laravel logs
2. **Monitoring**: Monitor scheduler execution
3. **Backup**: Regular database backups before generation
4. **Testing**: Test in staging environment first

## Support

For issues with the invoice system, check:
1. Laravel logs (`storage/logs/laravel.log`)
2. Scheduler status (`php artisan schedule:list`)
3. Database connectivity
4. File permissions
