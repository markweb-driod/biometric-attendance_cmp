# Exam Eligibility Calculation Rules

## Overview
This document outlines all rules and requirements for calculating student exam eligibility based on attendance records.

## 1. Attendance Threshold Rule

### Default Threshold
- **Default Value:** 75.00%
- **Configurable:** Yes, via method parameter
- **Storage:** Stored in `required_threshold` field in database

### Rule
Students with attendance percentage **equal to or above the threshold** are eligible.
Students with attendance percentage **below the threshold** are ineligible.

## 2. Eligibility Status Rule

### Status Types
Three possible status values:
- **`eligible`**: Student's attendance >= threshold
- **`ineligible`**: Student's attendance < threshold
- **`overridden`**: Status manually changed by HOD

### Calculation Logic
```php
if (attendance_percentage >= threshold) {
    status = 'eligible'
} else {
    status = 'ineligible'
}
```

## 3. Attendance Calculation Rule

### Method
Uses `calculateStudentAttendance($studentId, $courseId)` method

### Formula
```
Total Sessions = Count of all attendance sessions for student
Total Present = Count of sessions where student was marked present
Percentage = (Total Present / Total Sessions) * 100

If Total Sessions = 0, then Percentage = 0
```

### Per-Course Calculation
- Attendance is calculated **per course**
- Each student-course combination is evaluated separately
- Eligibility records created for each course the student is enrolled in

### Attendance Status Categories
- **Excellent** (≥75%): `excellent`
- **Good** (60-74%): `good`
- **Warning** (50-59%): `warning`
- **Critical** (<50%): `critical`

## 4. Semester and Academic Year Rule

### Auto-Detection
Current semester and academic year are automatically detected based on system date:

#### Semester Detection
- **September - January**: `First` Semester
- **February - June**: `Second` Semester
- **July - August**: `Summer` Semester

#### Academic Year Detection
- **If month >= September**: Current year / (Current year + 1)
- **If month < September**: (Current year - 1) / Current year

### Example
- Current Date: October 2025
- Semester: `First`
- Academic Year: `2025/2026`

## 5. Database Constraints Rule

### Unique Constraint
```
Combination: student_id + course_id + semester + academic_year
```
Only one eligibility record per student-course-semester-year combination.

### Stored Fields
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `student_id` | Foreign Key | Yes | References students table |
| `course_id` | Foreign Key | Yes | References courses table |
| `semester` | String | Yes | First/Second/Summer |
| `academic_year` | String | Yes | Format: YYYY/YYYY |
| `attendance_percentage` | Decimal(5,2) | Yes | Calculated attendance % |
| `required_threshold` | Decimal(5,2) | Yes | Threshold used (default 75.00) |
| `status` | Enum | Yes | eligible/ineligible/overridden |
| `validated_at` | Timestamp | Yes | When calculation ran |
| `overridden_by` | Foreign Key | No | HOD who overrode |
| `override_reason` | Text | No | Reason for override |
| `overridden_at` | Timestamp | No | When overridden |

## 6. Scope Rule

### Department-Based
- Calculations are **department-specific**
- Only processes students in the specified department
- Ignores students from other departments

### Active Students Only
- Only considers active students
- Archived or inactive students are excluded

## 7. Error Handling Rule

### Transaction-Based
- All eligibility updates wrapped in database transaction
- If any error occurs, entire operation is rolled back

### Individual Student Errors
- If one student's calculation fails, others continue
- Errors logged per student without stopping batch
- Failed records tracked in results array

### Rollback Triggers
- Database constraint violations
- Missing required data
- Calculation exceptions

## 8. Audit Logging Rule

### All Calculations Logged
Every eligibility calculation creates an audit log entry with:
- User who initiated calculation
- Department affected
- Threshold used
- Semester and academic year
- Total students processed
- Count of eligible vs ineligible
- Number of errors encountered

### Immutable Logs
- Audit logs cannot be modified or deleted
- Provides permanent record of eligibility decisions

## 9. Override Rule

### HOD Override Authority
Head of Department can manually override eligibility:
- Change status from ineligible to eligible
- Change status from eligible to ineligible
- Must provide override reason
- Automatically sets status to `overridden`
- Records who made override and when

### Override Tracking
All overrides are tracked with:
- `overridden_by`: HOD who made decision
- `override_reason`: Justification provided
- `overridden_at`: Timestamp of override
- Status automatically set to `overridden`

## 10. Calculation Process Rule

### Step-by-Step Process
1. Get all students in department
2. Get all courses for each student
3. For each student-course combination:
   - Calculate attendance percentage
   - Compare to threshold
   - Determine eligibility status
   - Update or create eligibility record
4. Commit transaction
5. Create audit log
6. Return results summary

### Results Summary
Returns:
- Total eligible count
- Total ineligible count
- List of updated records
- List of errors (if any)

## 11. Course Requirement Rule

### Per-Course Evaluation
- Eligibility tracked at the **course level**, not student level
- Student must meet threshold in **each course** independently
- A student can be eligible in Math but ineligible in Physics

### Enrollment Matching
Only evaluates courses where:
- Student is enrolled in a classroom
- Classroom is associated with the course
- Course belongs to the department

## 12. Validation Timing Rule

### When Calculations Run
- Manually triggered by HOD
- Via "Calculate Eligibility" button in dashboard
- Via API endpoint: `POST /hod/exam/api/eligibility/calculate`
- Can be scheduled or automated

### Timing Considerations
- Run before exam registration
- Run after attendance data is complete
- Re-run after significant attendance updates
- Can run multiple times (updates existing records)

## 13. Data Integrity Rule

### Update vs Create
Uses `updateOrCreate` method:
- If record exists: Updates attendance and status
- If record doesn't exist: Creates new record
- Maintains historical data with timestamps

### No Duplicates
Unique constraint prevents:
- Multiple records for same student-course-semester-year
- Data inconsistencies
- Conflicting eligibility statuses

## 14. Performance Rule

### Bulk Processing
- Processes all students in batch
- Single database transaction for all updates
- Optimized with proper indexing

### Indexing
Database indexes on:
- `status`, `semester`, `academic_year`
- `attendance_percentage`, `required_threshold`
- Unique constraint on key combination

## Configuration Examples

### Default Calculation
```php
$service->validateEligibility(
    departmentId: 1,
    threshold: 75. indifferent,
    semester: null,        // Auto-detected
    academicYear: null     // Auto-detected
);
```

### Custom Threshold
```php
$service->validateEligibility(
    departmentId: 1,
    threshold: 80.0,       // 80% threshold
    semester: 'First',
    academicYear: '2025/2026'
);
```

### Per-Course Calculation
```php
$attendance = $service->calculateStudentAttendance(
    studentId: 1,
    courseId: 5
);
```

## Summary of Key Rules

1. ✅ **75% default threshold** (configurable)
2. ✅ **Per-course calculation** (not per-student)
3. ✅ **Auto-detect semester/year** from current date
4. ✅ **Department-scoped** processing
5. ✅ **Transaction-based** with rollback on error
6. ✅ **Individual error logging** without stopping batch
7. ✅ **HOD override capability** with reason required
8. ✅ **Audit logging** of all calculations
9. ✅ **Unique per course-student-semester-year**
10. ✅ **Update existing or create new** records

---

## Implementation Summary

### System Architecture

#### Components Created
1. **Service Layer**
   - `ExamEligibilityService.php` - Core eligibility calculation logic
   - `AttendanceCalculationService.php` - Attendance percentage calculation
   
2. **Controllers**
   - `HodExamEligibilityController.php` - Eligibility management endpoints
   - `HodTwoFactorController.php` - 2FA verification handler
   
3. **Middleware**
   - `RequireTwoFactorAuth` - Protects critical eligibility operations
   - `EnsureHODRole` - Ensures HOD authentication
   - `HODSessionTimeout` - Session management
   
4. **Views**
   - `resources/views/hod/exam/eligibility.blade flex.php` - Main eligibility dashboard
   - `resources/views/hod/exam/configuration.blade.php` - Configuration page
   - `resources/views/hod/two-factor/verify.blade.php` - 2FA verification page
   - `resources/views/hod/components/sidebar.blade.php` - Updated navigation

#### Database Schema
```sql
exam_eligibilities:
  - id (Primary Key)
  - student_id (Foreign Key → students)
  - course_id (Foreign Key → courses)
  - semester (String)
  - academic_year (String)
  - attendance_percentage (Decimal 5,2)
  - required_threshold (Decimal 5,2) - Default: 75.00
  - status (Enum: eligible, ineligible, overridden)
  - override_reason (Text, Nullable)
  - overridden_by (Foreign Key → hods, Nullable)
  - overridden_at (Timestamp, Nullable)
  - validated_at (Timestamp, Nullable)
  - validation_details (JSON, Nullable)
  - timestamps
```

### Security Implementation

#### Two-Factor Authentication (2FA)
- **Purpose**: Protect critical eligibility operations from unauthorized access
- **Protected Routes**:
  - `POST /hod/exam/api/eligibility/calculate` - Run eligibility calculation
  - Future: Bulk overrides, configuration changes
  
#### 2FA Flow
1. User initiates protected action
2. Middleware checks 2FA session
3. If not verified, redirect to `/hod/two-factor/verify`
4. User enters 6-digit verification code
5. Upon verification, action proceeds
6. 2FA session expires after use or timeout

#### Current 2FA Implementation
- **Method**: Simplistic code-based (temporary)
- **Code Generation**: Based on HOD ID + Department ID
- **Format**: 6-digit numeric code
- **Note**: Should be upgraded to TOTP (Google Authenticator) in production

### API Endpoints

#### Eligibility Management
| Method | Endpoint | Description | Protection |
|--------|----------|-------------|------------|
| GET | `/hod/exam/eligibility` | Eligibility dashboard | HOD Auth |
| GET | `/hod/exam/configuration` | Configuration page | HOD Auth |
| GET | `/hod/exam/api/eligibility/data` | Get eligibility data | HOD Auth |
| GET | `/hod/exam/api/eligibility/stats` | Get statistics | HOD Auth |
| GET | `/hod/exam/api/eligibility/at-risk` | Get at-risk students | HOD Auth |
| POST | `/hod/exam/api/eligibility/calculate` | Calculate eligibility | 2FA Required |
| POST | `/hod/exam/api/eligibility/override` | Override student | HOD Auth |
| POST | `/hod/exam/api/eligibility/bulk-override` | Bulk override | 2FA Recommended |
| POST | `/hod/exam/api/configuration` | Save configuration | HOD Auth |
| GET | `/hod/exam/api/eligibility/export` | Export data | HOD Auth |

#### Two-Factor Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/hod/two-factor/verify` | Show 2FA form |
| POST | `/hod/two-factor/verify` | Verify code |
| POST | `/hod/two-factor/resend` | Resend code |
| POST | `/hod/two-factor/clear` | Clear 2FA session |

### Features Implemented

#### ✅ Eligibility Dashboard
- View all student eligibility statuses
- Filter by semester, academic year, course, status
- Search students by name or matric number
- Display statistics and KPIs
- Export functionality

#### ✅ Configuration Page
- Set attendance threshold (0-100%)
- Configure academic calendar (semester, year)
- View calculation rules documentation
- Run eligibility calculation with 2FA protection

#### ✅ Eligibility Calculation
- Per-course calculation for each student
- Batch processing of all department students
- Transaction-based with rollback on error
- Individual error logging without stopping batch
- Audit trail creation

#### ✅ Eligibility Override
- Single student override with reason
- Bulk override capability
- Status change tracking
- HOD identity recording
- Timestamp tracking

#### ✅ At-Risk Students
- Identification of students below threshold
- Sorting by attendance percentage
- Academic level grouping
- Export capability

#### ✅ Audit Logging
- All eligibility calculations logged
- Override actions tracked
- User identification
- IP address and user agent tracking
- Immutable log records

### Navigation Integration

#### Sidebar Updates
Added to HOD sidebar:
- **Eligibility Config** (New) - Direct link to configuration page
- **Exam Eligibility** (Existing) - Main eligibility dashboard

### Testing Checklist

#### Functional Testing
- [ ] HOD can access configuration page
- [ ] Configuration settings save correctly
- [ ] 2FA verification page displays correctly
- [ ] 2FA code validation works
- [ ] Eligibility calculation runs with 2FA
- [ ] Eligibility calculation creates proper records
- [ ] Statistics display correctly
- [ ] Override functionality works
- [ ] Export generates proper data

#### Security Testing
- [ ] Protected routes require 2FA
- [ ] 2FA session expires properly
- [ ] Invalid 2FA codes are rejected
- [ ] Audit logs capture all actions
- [ ] Department isolation enforced

#### Integration Testing
- [ ] Navigation links work correctly
- [ ] Page transitions smooth
- [ ] API responses valid
- [ ] Error handling graceful
- [ ] UI responsive on mobile

---

**Last Updated:** October 28, 2025
**Version:** 2.0 - With Implementation Summary

