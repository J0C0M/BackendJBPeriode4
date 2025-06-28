# Wordle Application - View Folder Structure

## Complete View Directory Structure

```
resources/views/
├── auth/                          # Authentication views
│   ├── login.blade.php
│   ├── register.blade.php
│   └── forgot-password.blade.php
│
├── admin/                         # Admin panel views
│   ├── dashboard/
│   │   ├── index.blade.php        # Main admin dashboard
│   │   ├── system-info.blade.php  # System information
│   │   └── logs.blade.php         # Application logs
│   ├── users/
│   │   ├── index.blade.php        # User listing with search/filters
│   │   ├── create.blade.php       # Create new user
│   │   ├── show.blade.php         # User details
│   │   ├── edit.blade.php         # Edit user
│   │   ├── game-history.blade.php # User's game history
│   │   └── friendships.blade.php  # User's friendships
│   └── words/
│       ├── index.blade.php        # Word listing with filters
│       ├── create.blade.php       # Create new word
│       ├── show.blade.php         # Word details and usage stats
│       ├── edit.blade.php         # Edit word
│       └── bulk-import.blade.php  # Bulk word import
│
├── games/                         # Game-related views
│   ├── index.blade.php            # User's games list
│   ├── create.blade.php           # Create new game
│   ├── show.blade.php             # Game board/play area
│   ├── history.blade.php          # Game history
│   └── leaderboard.blade.php      # Game leaderboard
│
├── users/                         # User profile views
│   ├── index.blade.php            # User search/browse
│   ├── show.blade.php             # User profile
│   ├── edit.blade.php             # Edit profile
│   └── game-history.blade.php     # User's game history
│
├── settings/                      # User settings views
│   ├── index.blade.php            # Settings dashboard
│   ├── profile.blade.php          # Profile settings
│   ├── password.blade.php         # Password change
│   └── privacy.blade.php          # Privacy settings
│
├── friends/                       # Friendship views
│   ├── index.blade.php            # Friends list
│   ├── create.blade.php           # Add friends/search
│   ├── requests.blade.php         # Friend requests
│   └── search.blade.php           # User search
│
├── comments/                      # Comment views
│   ├── index.blade.php            # Comments listing
│   ├── create.blade.php           # Create comment
│   └── edit.blade.php             # Edit comment
│
├── leaderboard/                   # Leaderboard views
│   ├── index.blade.php            # Main leaderboard
│   ├── daily.blade.php            # Daily leaderboard
│   ├── weekly.blade.php           # Weekly leaderboard
│   └── all-time.blade.php         # All-time leaderboard
│
├── components/                    # Reusable components
│   ├── game-board.blade.php       # Wordle game board
│   ├── keyboard.blade.php         # Virtual keyboard
│   ├── user-card.blade.php        # User profile card
│   ├── game-card.blade.php        # Game summary card
│   ├── friend-card.blade.php      # Friend profile card
│   ├── comment-form.blade.php     # Comment input form
│   ├── pagination.blade.php       # Pagination component
│   ├── search-form.blade.php      # Search input form
│   ├── notification.blade.php     # Flash messages
│   ├── loading.blade.php          # Loading spinner
│   └── modal.blade.php            # Modal dialog
│
├── layouts/                       # Layout templates
│   ├── app.blade.php              # Main application layout
│   ├── admin.blade.php            # Admin panel layout
│   ├── guest.blade.php            # Guest layout (login/register)
│   ├── navigation.blade.php       # Navigation menu
│   ├── sidebar.blade.php          # Admin sidebar
│   └── footer.blade.php           # Footer component
│
├── errors/                        # Error pages
│   ├── 404.blade.php              # Not found page
│   ├── 403.blade.php              # Forbidden page
│   ├── 500.blade.php              # Server error page
│   └── maintenance.blade.php      # Maintenance mode
│
├── home.blade.php                 # Homepage
├── dashboard.blade.php            # User dashboard
└── welcome.blade.php              # Welcome page (existing)
```

## Required Files by Feature

### Authentication (3 files)
- `auth/login.blade.php` - Login form
- `auth/register.blade.php` - Registration form  
- `auth/forgot-password.blade.php` - Password reset

### Admin Panel (12 files)
- `admin/dashboard/index.blade.php` - Main admin dashboard
- `admin/dashboard/system-info.blade.php` - System health
- `admin/dashboard/logs.blade.php` - Application logs
- `admin/users/index.blade.php` - User management
- `admin/users/create.blade.php` - Create user
- `admin/users/show.blade.php` - User details
- `admin/users/edit.blade.php` - Edit user
- `admin/users/game-history.blade.php` - User game history
- `admin/users/friendships.blade.php` - User friendships
- `admin/words/index.blade.php` - Word management
- `admin/words/create.blade.php` - Create word
- `admin/words/show.blade.php` - Word details
- `admin/words/edit.blade.php` - Edit word
- `admin/words/bulk-import.blade.php` - Bulk import

### Games (5 files)
- `games/index.blade.php` - Games list
- `games/create.blade.php` - Create game
- `games/show.blade.php` - Game board
- `games/history.blade.php` - Game history
- `games/leaderboard.blade.php` - Leaderboard

### Users (4 files)
- `users/index.blade.php` - User search
- `users/show.blade.php` - User profile
- `users/edit.blade.php` - Edit profile
- `users/game-history.blade.php` - User game history

### Settings (4 files)
- `settings/index.blade.php` - Settings dashboard
- `settings/profile.blade.php` - Profile settings
- `settings/password.blade.php` - Password change
- `settings/privacy.blade.php` - Privacy settings

### Friends (4 files)
- `friends/index.blade.php` - Friends list
- `friends/create.blade.php` - Add friends
- `friends/requests.blade.php` - Friend requests
- `friends/search.blade.php` - User search

### Comments (3 files)
- `comments/index.blade.php` - Comments list
- `comments/create.blade.php` - Create comment
- `comments/edit.blade.php` - Edit comment

### Leaderboard (4 files)
- `leaderboard/index.blade.php` - Main leaderboard
- `leaderboard/daily.blade.php` - Daily rankings
- `leaderboard/weekly.blade.php` - Weekly rankings
- `leaderboard/all-time.blade.php` - All-time rankings

### Components (12 files)
- `components/game-board.blade.php` - Wordle board
- `components/keyboard.blade.php` - Virtual keyboard
- `components/user-card.blade.php` - User card
- `components/game-card.blade.php` - Game card
- `components/friend-card.blade.php` - Friend card
- `components/comment-form.blade.php` - Comment form
- `components/pagination.blade.php` - Pagination
- `components/search-form.blade.php` - Search form
- `components/notification.blade.php` - Notifications
- `components/loading.blade.php` - Loading spinner
- `components/modal.blade.php` - Modal dialog

### Layouts (6 files)
- `layouts/app.blade.php` - Main layout
- `layouts/admin.blade.php` - Admin layout
- `layouts/guest.blade.php` - Guest layout
- `layouts/navigation.blade.php` - Navigation
- `layouts/sidebar.blade.php` - Admin sidebar
- `layouts/footer.blade.php` - Footer

### Errors (4 files)
- `errors/404.blade.php` - Not found
- `errors/403.blade.php` - Forbidden
- `errors/500.blade.php` - Server error
- `errors/maintenance.blade.php` - Maintenance

### Main Pages (3 files)
- `home.blade.php` - Homepage
- `dashboard.blade.php` - User dashboard

## Total Files Required: 75 files

## Priority Order for Creation:
1. **Layouts** (6 files) - Foundation for all pages
2. **Components** (12 files) - Reusable UI elements
3. **Authentication** (3 files) - User access
4. **Main Pages** (2 files) - Home and dashboard
5. **Games** (5 files) - Core functionality
6. **Users** (4 files) - User profiles
7. **Settings** (4 files) - User preferences
8. **Friends** (4 files) - Social features
9. **Comments** (3 files) - Social interaction
10. **Leaderboard** (4 files) - Competition
11. **Admin Panel** (14 files) - Administration
12. **Errors** (4 files) - Error handling

## Key Features to Implement:
- **Responsive Design** - Mobile-friendly layouts
- **Real-time Updates** - Live game state updates
- **Interactive Game Board** - Drag-and-drop or click interface
- **Virtual Keyboard** - On-screen keyboard for mobile
- **Search & Filter** - Advanced user and game search
- **Pagination** - Efficient data loading
- **Notifications** - Flash messages and alerts
- **Modal Dialogs** - Confirmation and detail views
- **Loading States** - User feedback during operations
- **Error Handling** - Graceful error display 