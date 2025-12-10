# Notification System Analysis & Bug Fix

## Critical Bug Found ❌

### The Problem

There was a **major logic error** in the notification system where job seekers would **NEVER receive notifications**.

### Root Cause

In `app/Actions/SendJobSeekerNotification.php`, the action was incorrectly setting **both** `id_job_seeker` AND `id_company`:

```php
// ❌ WRONG - Before fix
Notification::create([
    'id_job_seeker' => $jobSeeker->id,    // Recipient
    'id_company' => $company?->id,         // Sender (stored but shouldn't be!)
    'message' => $message,
    'link_url' => $linkUrl,
]);
```

But in `NotificationController.php`, the query was filtering for notifications where:
```php
// Job seeker notifications
$query->where('id_job_seeker', $jobSeekerId)
      ->whereNull('id_company');  // ❌ This would NEVER match!
```

**Result:** Since `id_company` was being set (to the sender's ID), the `whereNull('id_company')` filter would exclude ALL job seeker notifications!

### The Fix ✅

Changed `SendJobSeekerNotification` to only store the **recipient**, not the sender:

```php
// ✅ CORRECT - After fix
Notification::create([
    'id_job_seeker' => $jobSeeker->id,
    'id_company' => null,  // Job seeker notification - id_company must be null
    'message' => $message,
    'link_url' => $linkUrl,
]);
```

## Notification System Design

### Schema Design
The `notifications` table uses a **mutually exclusive** design:

- **Job Seeker Notification**: `id_job_seeker` is set, `id_company` is NULL
- **Company Notification**: `id_company` is set, `id_job_seeker` is NULL

This means:
- Each notification has exactly ONE recipient (either a job seeker OR a company)
- The sender information is NOT stored in the database
- The sender context is included in the message text itself

### Database Schema
```sql
notifications
├── id (primary key)
├── id_job_seeker (nullable, FK to job_seekers)
├── id_company (nullable, FK to companies)
├── message (text)
├── is_read (boolean, default false)
├── link_url (nullable)
├── created_at
└── updated_at

Constraints:
- Exactly one of (id_job_seeker, id_company) must be non-null
- Foreign keys cascade on delete
```

### Action Classes

#### SendJobSeekerNotification
```php
// Usage: Send notification TO a job seeker
($this->sendJobSeekerNotification)(
    $jobSeeker,      // Recipient
    $company,        // Context (not stored, only for message composition)
    $message,        // The notification message
    $linkUrl         // Optional link
);
```

**Database record created:**
- `id_job_seeker`: jobSeeker's ID (recipient)
- `id_company`: NULL
- `message`: Full message text (may include company name in text)

#### SendCompanyNotification
```php
// Usage: Send notification TO a company
($this->sendCompanyNotification)(
    $company,        // Recipient
    $message,        // The notification message
    $linkUrl         // Optional link
);
```

**Database record created:**
- `id_job_seeker`: NULL
- `id_company`: company's ID (recipient)
- `message`: Full message text

### Controller Filtering Logic

**NotificationController::index()** filters notifications based on user role:

```php
// Job Seeker (user role)
$query->where('id_job_seeker', $jobSeekerId)
      ->whereNull('id_company');

// Company (company role)
$query->where('id_company', $companyId)
      ->whereNull('id_job_seeker');

// Admin
// No filter - sees all notifications
```

### Authorization Logic

**NotificationController::markAsRead()** ensures users can only mark their own notifications:

```php
// Job seeker can mark if:
$notification->id_job_seeker === $user->jobSeeker?->id 
    && is_null($notification->id_company)

// Company can mark if:
$notification->id_company === $user->company?->id 
    && is_null($notification->id_job_seeker)
```

## Notification Usage Examples

### 1. Application Submitted
**File:** `ApplicationController::store()`
```php
// Notify company about new application
($this->sendCompanyNotification)(
    $jobPosting->company,
    "{$applicantName} telah melamar pada posisi {$jobPosting->job_title}.",
    route('company.applications.index')
);
```

### 2. Application Status Changed
**File:** `ApplicationController::maybeSendStatusNotification()`
```php
// Notify job seeker about status change
($this->sendJobSeekerNotification)(
    $jobSeeker,
    $company,
    "Status lamaran Anda untuk posisi {$jobTitle} di {$companyName} kini berlanjut ke tahap interview.",
    route('user.applications.index')
);
```

### 3. Interview Scheduled
**File:** `InterviewController::store()`
```php
// Notify job seeker about interview
($this->sendJobSeekerNotification)(
    $application->jobSeeker,
    $application->jobPosting?->company,
    "Jadwal interview untuk {$jobTitle} di {$companyName} telah dijadwalkan pada {$formattedDate}...",
    route('interviews.index')
);
```

### 4. Payment Success
**File:** `WebhookController::handlePayment()`
```php
// Notify company about successful payment
($this->sendCompanyNotification)(
    $company,
    "Pembayaran berhasil! Paket {$planName} Anda telah aktif...",
    route('company.subscriptions.index')
);
```

## Files Modified

1. ✅ `app/Actions/SendJobSeekerNotification.php` - Fixed to set `id_company` = null
2. ✅ `app/Http/Controllers/NotificationController.php` - Removed unnecessary eager loading
3. ✅ `resources/views/notifications/index.blade.php` - Removed sender display logic

## Testing Recommendations

### Manual Testing Steps

1. **As Job Seeker:**
   - Submit an application
   - Check notifications page - should see confirmation
   - Have company change application status
   - Check notifications - should see status update
   - Have company schedule interview
   - Check notifications - should see interview notification

2. **As Company:**
   - Wait for job seeker to apply
   - Check notifications - should see new application notification
   - Make a payment
   - Check notifications - should see payment confirmation

3. **Mark as Read:**
   - Click on notification
   - Verify it's marked as read
   - Verify unread count decreases
   - Test "Mark All as Read" functionality

4. **Authorization:**
   - Try to access another user's notification URL
   - Should get 403 Forbidden error

### Database Verification

```sql
-- Job Seeker notifications should have id_company = NULL
SELECT * FROM notifications 
WHERE id_job_seeker IS NOT NULL 
AND id_company IS NOT NULL;  
-- Should return 0 rows

-- Company notifications should have id_job_seeker = NULL
SELECT * FROM notifications 
WHERE id_company IS NOT NULL 
AND id_job_seeker IS NOT NULL;  
-- Should return 0 rows

-- All notifications should have exactly one recipient
SELECT * FROM notifications 
WHERE (id_job_seeker IS NULL AND id_company IS NULL)
OR (id_job_seeker IS NOT NULL AND id_company IS NOT NULL);
-- Should return 0 rows
```

## Best Practices Followed

✅ **Single Responsibility** - Each action class has one clear purpose
✅ **Authorization** - Proper checks before marking notifications as read
✅ **Performance** - Proper database indexes on filtered columns
✅ **User Experience** - Clear messages with context and actionable links
✅ **Data Integrity** - Foreign key constraints with cascade delete
✅ **Security** - Role-based filtering ensures users only see their notifications

## Potential Future Enhancements

1. **Notification Types** - Add a `type` column to categorize notifications
2. **Bulk Actions** - Add ability to delete notifications
3. **Email Integration** - Send email notifications for important events
4. **Real-time Updates** - Use WebSockets/Pusher for instant notifications
5. **Notification Preferences** - Let users control which notifications they receive
6. **Sender Information** - If needed, add `sender_type` and `sender_id` polymorphic columns
