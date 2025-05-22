# UnifiedNest ERP Database Structure

This document provides an overview of the UnifiedNest ERP database structure, with detailed information about all tables and their relationships.

## Core Tables

### Organizations
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| name | VARCHAR(255) | Organization name |
| address | TEXT | Physical address |
| email | VARCHAR(255) | Contact email |
| phone | VARCHAR(50) | Contact phone number |
| website | VARCHAR(255) | Website URL |
| logo | VARCHAR(255) | Path to logo image |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### Departments
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| organization_id | INT | Foreign key to organizations |
| name | VARCHAR(255) | Department name |
| description | TEXT | Department description |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### Roles
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| name | VARCHAR(50) | Role name |
| description | TEXT | Role description |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

Pre-defined roles:
- super-admin
- organization-owner
- department-owner
- hr
- finance
- manager
- employee

### Users
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| organization_id | INT | Foreign key to organizations |
| department_id | INT | Foreign key to departments |
| role_id | INT | Foreign key to roles |
| email | VARCHAR(255) | User email (unique) |
| password | VARCHAR(255) | Hashed password |
| first_name | VARCHAR(100) | First name |
| last_name | VARCHAR(100) | Last name |
| phone | VARCHAR(50) | Phone number |
| profile_image | VARCHAR(255) | Path to profile image |
| is_active | BOOLEAN | Account status |
| onboarding_completed | BOOLEAN | Onboarding status |
| last_login | DATETIME | Last login timestamp |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### User Profile Details
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| date_of_birth | DATE | Birth date |
| gender | VARCHAR(20) | Gender |
| blood_group | VARCHAR(10) | Blood group |
| marital_status | VARCHAR(20) | Marital status |
| emergency_contact_name | VARCHAR(255) | Emergency contact name |
| emergency_contact_phone | VARCHAR(50) | Emergency contact phone |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### User Family Details
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| name | VARCHAR(255) | Family member name |
| relationship | VARCHAR(50) | Relationship type |
| date_of_birth | DATE | Birth date |
| contact | VARCHAR(50) | Contact number |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### User Education
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| institution | VARCHAR(255) | Institution name |
| degree | VARCHAR(255) | Degree name |
| field_of_study | VARCHAR(255) | Field of study |
| start_date | DATE | Start date |
| end_date | DATE | End date |
| grade | VARCHAR(50) | Grade/Score |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### User Work Experience
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| company | VARCHAR(255) | Company name |
| position | VARCHAR(255) | Job position |
| start_date | DATE | Start date |
| end_date | DATE | End date |
| description | TEXT | Job description |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### User Addresses
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| type | VARCHAR(50) | Address type (permanent, current) |
| address_line1 | VARCHAR(255) | Address line 1 |
| address_line2 | VARCHAR(255) | Address line 2 |
| city | VARCHAR(100) | City |
| state | VARCHAR(100) | State/Province |
| country | VARCHAR(100) | Country |
| postal_code | VARCHAR(20) | Postal/Zip code |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### User Health
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| medical_condition | VARCHAR(255) | Medical conditions |
| allergies | TEXT | Known allergies |
| medications | TEXT | Current medications |
| health_insurance_provider | VARCHAR(255) | Insurance provider |
| health_insurance_number | VARCHAR(100) | Insurance number |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

## Project Management Module

### Projects
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| organization_id | INT | Foreign key to organizations |
| name | VARCHAR(255) | Project name |
| description | TEXT | Project description |
| start_date | DATE | Start date |
| end_date | DATE | End date |
| status | ENUM | Project status |
| priority | ENUM | Priority level |
| budget | DECIMAL(15,2) | Budget amount |
| client_name | VARCHAR(255) | Client name |
| client_email | VARCHAR(255) | Client email |
| created_by | INT | Foreign key to users |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

Status options: 'not_started', 'in_progress', 'on_hold', 'completed', 'cancelled'  
Priority options: 'low', 'medium', 'high', 'urgent'

### Project Members

Stores the users associated with each project.

| Column | Type | Description |
| --- | --- | --- |
| id | INT | Primary key |
| project_id | INT | Foreign key to projects |
| user_id | INT | Foreign key to users |
| role | VARCHAR(50) | Role of the user in the project (manager, member) |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### Project Documents
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| project_id | INT | Foreign key to projects |
| user_id | INT | Foreign key to users |
| title | VARCHAR(255) | Document title |
| file_path | VARCHAR(255) | Path to file |
| file_type | VARCHAR(50) | File type |
| file_size | INT | File size in bytes |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

## Task Management Module

### Tasks
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| project_id | INT | Foreign key to projects |
| organization_id | INT | Foreign key to organizations |
| title | VARCHAR(255) | Task title |
| description | TEXT | Task description |
| status | ENUM | Task status |
| priority | ENUM | Priority level |
| due_date | DATE | Due date |
| estimated_hours | DECIMAL(5,2) | Estimated hours |
| created_by | INT | Foreign key to users |
| assigned_to | INT | Foreign key to users |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

Status options: 'backlog', 'to_do', 'in_progress', 'review', 'done'  
Priority options: 'low', 'medium', 'high', 'urgent'

### Task Comments
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| task_id | INT | Foreign key to tasks |
| user_id | INT | Foreign key to users |
| comment | TEXT | Comment text |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### Task Attachments
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| task_id | INT | Foreign key to tasks |
| user_id | INT | Foreign key to users |
| file_name | VARCHAR(255) | File name |
| file_path | VARCHAR(255) | Path to file |
| file_type | VARCHAR(50) | File type |
| file_size | INT | File size in bytes |
| created_at | TIMESTAMP | Creation timestamp |

## Worklog Module

### Worklogs
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| task_id | INT | Foreign key to tasks |
| project_id | INT | Foreign key to projects |
| description | TEXT | Work description |
| hours | DECIMAL(5,2) | Hours worked |
| log_date | DATE | Date of work |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

## Leave Management Module

### Leave Types
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| organization_id | INT | Foreign key to organizations |
| name | VARCHAR(100) | Leave type name |
| description | TEXT | Description |
| default_days | INT | Default days per year |
| color_code | VARCHAR(10) | Color for calendar |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### Leave Balances
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| leave_type_id | INT | Foreign key to leave_types |
| year | INT | Year for the balance |
| total_days | DECIMAL(5,1) | Total available days |
| used_days | DECIMAL(5,1) | Days used |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### Leave Requests
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| leave_type_id | INT | Foreign key to leave_types |
| start_date | DATE | Start date |
| end_date | DATE | End date |
| half_day | BOOLEAN | Half day leave |
| reason | TEXT | Reason for leave |
| status | ENUM | Request status |
| approved_by | INT | Foreign key to users |
| approved_at | DATETIME | Approval timestamp |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

Status options: 'pending', 'approved', 'rejected', 'cancelled'

## Calendar Module

### Events
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| organization_id | INT | Foreign key to organizations |
| title | VARCHAR(255) | Event title |
| description | TEXT | Event description |
| start_datetime | DATETIME | Start date and time |
| end_datetime | DATETIME | End date and time |
| location | VARCHAR(255) | Event location |
| color | VARCHAR(10) | Event color |
| is_all_day | BOOLEAN | All day event |
| recurrence_rule | VARCHAR(255) | Recurrence rule |
| created_by | INT | Foreign key to users |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### Event Attendees
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| event_id | INT | Foreign key to events |
| user_id | INT | Foreign key to users |
| status | ENUM | Attendance status |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

Status options: 'pending', 'accepted', 'declined', 'tentative'

## Asset Management Module

### Asset Categories
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| organization_id | INT | Foreign key to organizations |
| name | VARCHAR(100) | Category name |
| description | TEXT | Category description |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### Assets
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| organization_id | INT | Foreign key to organizations |
| category_id | INT | Foreign key to asset_categories |
| name | VARCHAR(255) | Asset name |
| asset_code | VARCHAR(50) | Asset code |
| serial_number | VARCHAR(100) | Serial number |
| purchase_date | DATE | Purchase date |
| purchase_cost | DECIMAL(15,2) | Purchase cost |
| warranty_expiry | DATE | Warranty expiry date |
| status | ENUM | Asset status |
| notes | TEXT | Additional notes |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

Status options: 'available', 'assigned', 'under_maintenance', 'disposed'

### Asset Assignments
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| asset_id | INT | Foreign key to assets |
| assigned_to | INT | Foreign key to users |
| assigned_by | INT | Foreign key to users |
| assigned_date | DATE | Date of assignment |
| return_date | DATE | Return date |
| notes | TEXT | Additional notes |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

## Expense Management Module

### Expense Categories
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| organization_id | INT | Foreign key to organizations |
| name | VARCHAR(100) | Category name |
| description | TEXT | Category description |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### Expenses
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| organization_id | INT | Foreign key to organizations |
| user_id | INT | Foreign key to users |
| project_id | INT | Foreign key to projects |
| category_id | INT | Foreign key to expense_categories |
| amount | DECIMAL(15,2) | Expense amount |
| currency | VARCHAR(3) | Currency code |
| date | DATE | Expense date |
| description | TEXT | Expense description |
| receipt | VARCHAR(255) | Path to receipt file |
| status | ENUM | Expense status |
| approved_by | INT | Foreign key to users |
| approved_date | DATETIME | Approval timestamp |
| payment_date | DATETIME | Payment timestamp |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

Status options: 'pending', 'approved', 'rejected', 'paid'

## Payroll Module

### Salary Structures
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| basic_salary | DECIMAL(15,2) | Basic salary amount |
| hra | DECIMAL(15,2) | House rent allowance |
| transport_allowance | DECIMAL(15,2) | Transport allowance |
| medical_allowance | DECIMAL(15,2) | Medical allowance |
| other_allowances | DECIMAL(15,2) | Other allowances |
| effective_date | DATE | Effective date |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### Payslips
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| month | INT | Month (1-12) |
| year | INT | Year |
| basic_salary | DECIMAL(15,2) | Basic salary amount |
| hra | DECIMAL(15,2) | House rent allowance |
| transport_allowance | DECIMAL(15,2) | Transport allowance |
| medical_allowance | DECIMAL(15,2) | Medical allowance |
| other_allowances | DECIMAL(15,2) | Other allowances |
| overtime_hours | DECIMAL(5,2) | Overtime hours |
| overtime_rate | DECIMAL(15,2) | Overtime hourly rate |
| overtime_amount | DECIMAL(15,2) | Total overtime amount |
| gross_salary | DECIMAL(15,2) | Gross salary |
| tax_deductions | DECIMAL(15,2) | Tax deductions |
| other_deductions | DECIMAL(15,2) | Other deductions |
| net_salary | DECIMAL(15,2) | Net salary |
| payment_status | ENUM | Payment status |
| payment_date | DATETIME | Payment timestamp |
| payment_method | VARCHAR(50) | Payment method |
| notes | TEXT | Additional notes |
| created_by | INT | Foreign key to users |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

Status options: 'pending', 'paid', 'cancelled'

## Chat Module

### Chat Rooms
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| organization_id | INT | Foreign key to organizations |
| name | VARCHAR(255) | Room name |
| type | ENUM | Room type |
| created_by | INT | Foreign key to users |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

Type options: 'direct', 'group', 'channel'

### Chat Room Participants
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| room_id | INT | Foreign key to chat_rooms |
| user_id | INT | Foreign key to users |
| is_admin | BOOLEAN | Admin status |
| joined_at | TIMESTAMP | Join timestamp |

### Chat Messages
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| room_id | INT | Foreign key to chat_rooms |
| user_id | INT | Foreign key to users |
| message | TEXT | Message content |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

### Chat Attachments
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| message_id | INT | Foreign key to chat_messages |
| file_name | VARCHAR(255) | File name |
| file_path | VARCHAR(255) | Path to file |
| file_type | VARCHAR(50) | File type |
| file_size | INT | File size in bytes |
| created_at | TIMESTAMP | Creation timestamp |

## Notifications

### Notifications
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| title | VARCHAR(255) | Notification title |
| message | TEXT | Notification message |
| type | VARCHAR(50) | Notification type |
| reference_id | INT | Reference ID (e.g., task_id) |
| reference_type | VARCHAR(50) | Reference type (e.g., 'task') |
| is_read | BOOLEAN | Read status |
| created_at | TIMESTAMP | Creation timestamp |

## Database Relationships

### One-to-Many Relationships
- Organization -> Departments
- Organization -> Users
- Department -> Users
- Role -> Users
- User -> User Profile
- User -> User Family (multiple entries)
- User -> User Education (multiple entries)
- User -> User Experience (multiple entries)
- User -> User Addresses (multiple entries)
- User -> User Health
- Organization -> Projects
- Project -> Tasks
- Task -> Comments
- Task -> Attachments
- Organization -> Leave Types
- User -> Leave Requests
- Organization -> Events
- Organization -> Asset Categories
- Asset Category -> Assets
- Asset -> Assignments
- Organization -> Expense Categories
- Expense Category -> Expenses
- User -> Salary Structure
- User -> Payslips
- Organization -> Chat Rooms
- Chat Room -> Messages
- Message -> Attachments
- User -> Notifications

### Many-to-Many Relationships
- Projects <-> Users (through Project Members)
- Events <-> Users (through Event Attendees)
- Chat Rooms <-> Users (through Chat Room Participants) 