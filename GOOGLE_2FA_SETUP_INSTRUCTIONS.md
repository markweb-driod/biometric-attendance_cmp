# Google 2FA Setup Instructions

## Implementation Status: ✅ COMPLETE

All code has been implemented. The following steps are needed to activate the system.

## Prerequisites

### PHP Extensions Required
Before installing the Google2FA package, ensure these PHP extensions are enabled:
- `ext-oci8` (for Oracle database - already in use)
- `ext-gd` (for Excel/phpspreadsheet - already in use)  
- `ext-zip` (for phpspreadsheet - already in use)

**Note**: These are already required by existing packages. The 2FA package itself only needs standard PHP.

## Installation Steps

### 1. Install Google2FA Package

Once PHP extensions are properly configured, run:

```bash
composer install
```

Or to install just the 2FA package:

```bash
composer require pragmarx/google2fa-laravel
```

### 2. Database Migrations

✅ **Already Complete** - The migrations have been run:
- `add_two_factor_to_superadmins_table` (Batch 6)
- `add_two_factor_to_users_table` (Batch 6)

The database columns `two_factor_secret` and `two_factor_enabled` are now available.

### 3. Verify Installation

After installing the package, verify it works:

```php
php artisan tinker
>>> use PragmaRX\Google2FA\Google2FA;
>>> $google2fa = new Google2FA();
>>> $secret = $google2fa->generateSecretKey();
>>> echo $secret;
```

## How It Works

### For Users

1. **Setup 2FA**:
   - Navigate to account settings (route varies by user type)
   - Click "Enable Two-Factor Authentication"
   - Scan QR code with Google Authenticator, Authy, or similar app
   - Enter verification code to confirm

2. **Login with 2FA**:
   - Enter username/password
   - If 2FA is enabled, redirected to verification page
   - Enter 6-digit code from authenticator app
   - Access granted

3. **Disable 2FA**:
   - Go to account settings
   - Click "Disable Two-Factor Authentication"
   - Enter password to confirm

### Routes Available

**Superadmin:**
- `GET /superadmin/2fa` - Verification page
- `POST /superadmin/2fa` - Verify code
- `GET /superadmin/2fa/setup` - Setup page with QR code
- `POST /superadmin/2fa/confirm` - Confirm and enable
- `POST /superadmin/2fa/disable` - Disable 2FA

**HOD:**
- `GET /hod/two-factor` - Verification page
- `POST /hod/two-factor` - Verify code
- `GET /hod/two-factor/setup` - Setup page
- `POST /hod/two-factor/confirm` - Confirm and enable
- `POST /hod/two-factor/disable` - Disable 2FA

**Lecturer:**
- `GET /lecturer/2fa` - Verification page
- `POST /lecturer/2fa` - Verify code
- `GET /lecturer/2fa/setup` - Setup page
- `POST /lecturer/2fa/confirm` - Confirm and enable
- `POST /lecturer/2fa/disable` - Disable 2FA

## Testing Checklist

After package installation:

- [ ] Login as superadmin without 2FA - should work normally
- [ ] Enable 2FA for superadmin account
- [ ] Scan QR code with authenticator app
- [ ] Login as superadmin with 2FA - should prompt for code
- [ ] Enter correct code - should allow access
- [ ] Enter incorrect code - should reject
- [ ] Test protected routes requiring 2FA middleware
- [ ] Repeat tests for HOD and Lecturer accounts
- [ ] Test disable 2FA functionality

## Security Features

- ✅ TOTP-based (Time-based One-Time Password)
- ✅ Secrets encrypted in database
- ✅ Secrets hidden from model serialization
- ✅ Password required to disable 2FA
- ✅ Tolerance window of 2 time steps (30 seconds each)
- ✅ Session-based verification to reduce prompts

## QR Code Generation

**Current Implementation**: Uses external API (`api.qrserver.com`) for QR code generation in views.

**Note for Production**: Consider using a local QR code library like `simplesoftwareio/simple-qrcode` for better security and offline capability:

```bash
composer require simplesoftwareio/simple-qrcode
```

Then update views to use:
```php
{!! QrCode::size(200)->generate($qrCodeUrl) !!}
```

## Troubleshooting

### Package Installation Fails
- Ensure all PHP extensions are enabled
- Run `composer diagnose` to check configuration
- Try `composer clear-cache` then retry

### 2FA Not Working After Installation
- Clear application cache: `php artisan config:clear`
- Clear route cache: `php artisan route:clear`
- Verify package is autoloaded: `composer dump-autoload`

### QR Code Not Displaying
- Check internet connection (if using external API)
- Verify QR code URL is properly formatted
- Check browser console for image loading errors

### Verification Always Fails
- Ensure system time is synchronized (TOTP is time-sensitive)
- Check tolerance window settings
- Verify secret is being retrieved correctly

## Integration with Settings Pages

To add 2FA management to user settings pages, add buttons/links:

**Superadmin Settings:**
```blade
@if(auth('superadmin')->user()->hasTwoFactorEnabled())
    <form action="{{ route('superadmin.2fa.disable') }}" method="POST">
        @csrf
        <input type="password" name="password" placeholder="Enter password" required>
        <button type="submit">Disable 2FA</button>
    </form>
@else
    <a href="{{ route('superadmin.2fa.setup') }}">Enable Two-Factor Authentication</a>
@endif
```

Similar pattern for HOD and Lecturer settings.

## Implementation Complete ✅

All code is in place. Once the composer package is installed, the system is ready to use.

