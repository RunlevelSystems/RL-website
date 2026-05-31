# Runlevel Systems - Website Development Guidelines

## Site Overview
Runlevel Systems (Runlevel) is a worker co-op focused on game development, server hosting, and business applications. The website showcases our projects, facilitates community engagement, and provides information about joining our co-op.

## Multi-Repo Architecture (Important for AIs)

- This repository (`WDS_Website`) is the public marketing and information site at runlevelsystems.com.
- The separate GitHub repository `World-Domination-Software/Projects` is the customer-facing hub for **all project details and interactions** (wiki pages, discussions, ideas, and issues).
- The `projects.php` page in this repo lists projects and links out to the `Projects` repo for:
  - Detailed project descriptions and design docs (GitHub Wiki)
  - Future project ideas and feature discussions (GitHub Discussions → Ideas category)
  - Bug reports and support (GitHub Issues)
  - General project conversations (GitHub Discussions)
- When changing how projects are displayed here, **do not duplicate long-form content**; instead, link to or align with structures and categories defined in the `Projects` repo.
- Other internal repos (GSP server / agents) may be referenced from both this site and the `Projects` hub, but **customer interaction lives in the `Projects` repo**, not directly in those service repos.

## Architecture Overview

### Core Structure
- **PHP-based**: Server-side includes system with modular components (`includes/`)
- **Single-page projects**: `projects.php` loads project content via AJAX from `/projects/{slug}/` directories
- **Database integration**: PDO-based authentication against OGP users table (gaming panel integration)
- **Session management**: Separate namespace sessions with role-based access control
- **Asset organization**: Unified CSS (`wds-unified.css`), Bootstrap framework, custom JavaScript

### Key Files & Patterns
- **`includes/navigation.php`**: Smart path detection for both root and project contexts
- **`includes/db-config.php`**: PDO wrapper with admin authentication functions
- **`projects.php`**: Dynamic project loading with metadata from `project.json` files
- **Each project**: Self-contained directory with `index.php` and `project.json` metadata

### Project System Architecture
- Projects auto-discovered by scanning `/projects/` directories
- Each project requires `project.json` with title, category, description, and icon
- AJAX loading keeps users within `projects.php` context - never navigate to separate pages
- Project sub-files load via `loadProjectFile()` JavaScript function, maintaining context

## Design Philosophy & Style Guide

### Color Scheme (Orwellian Theme)
- **Header/Footer Background**: `#000000` (Pure Black)
- **Main Content Background**: `#E8E4D8` (Off-white/tan/cream for readability)
- **Content Boxes**: `#D4CFC0` (Lighter tan)
- **Text on Light Backgrounds**: `#1a1a1a` (Dark gray)
- **Text on Black Backgrounds**: `#E8E4D8` (Off-white/tan/cream) - **MUST be consistent across menu, footer, and all black backgrounds**
- **Headings**: `#4a4a4a` (Dark gray)
- **Accent/Links**: `#8B4513` (Rust/brown - primary accent)
- **Secondary Links**: `#6B3410` (Darker brown)
- **Buttons Primary**: `#8B4513` background with `#E8E4D8` text
- **Buttons Hover**: `#8B4513` background with `#E8E4D8` text
- **Logo Color**: `#B8621B` (Rusty orange-brown)

### Typography Rules
- **NO TEXT SHADOWS** - Ever! This is critical for the clean Orwellian aesthetic.
- Use `Segoe UI`, Tahoma, Geneva, Verdana, sans-serif as the primary font family
- Maintain high contrast for readability
- Line height should be 1.6-1.8 for body text

### Button Standards
- **Default State**: No button should appear in a "selected" or "active" state by default
- **Hover State**: Rust background (`#8B4513`) with cream text (`#E8E4D8`)
- **Style**: All buttons should have consistent hover behavior with smooth transitions
- **No Disabled-Looking Buttons**: Avoid buttons that look inactive when they're actually clickable

### Navigation & Footer
- Black background with off-white/tan/cream text (`#E8E4D8`)
- Links use rust accent color for hover states
- Menu text must be readable and consistent across all pages
- Footer links and text should use the same cream color as menu items

## Site Architecture

### Page Loading Behavior
- **Main pages**: Load as full pages (index.php, projects.php, contact.php, joinus.php)
- **Project pages**: Load within the projects.php page via AJAX
- **Project sub-files**: Should load within the same project view, NOT as separate pages
  - Example: clicking "references.php" from space5x index.php should load that content in the same project view
  - All project-related files should remain within the project viewing context

### Projects System
- Projects are organized in `/projects/{project-slug}/` directories
- Each project has a `project.json` metadata file
- The `projects.php` page dynamically loads project content via AJAX
- Project cards should have:
  - Rust background on hover with cream text
  - Smooth transition effects
  - Consistent styling across all project types

### Contact Forms
- **Main Contact Page**: General inquiries, support requests
- **Project-Specific Forms**: For joining testing programs, dev teams, etc.
  - These should be separate from the main contact form
  - Link to a specialized contact form with:
    - Purpose dropdown (Alpha Tester, Developer, General Interest, etc.)
    - User's name (single field, not split)
    - Discord UserID (optional)
    - Message/Question field
    - Notice that submission goes to Discord server
    - Link to join Discord server

### Discord Integration
- All forms should mention they submit to Discord
- Provide clear links to join the Discord server
- Discord invite: https://discord.gg/XPFnNdWGyW

## Content Guidelines

### Professional Tone
- Use clear, professional language throughout
- Avoid casual or unprofessional phrasing
- Technical content should be detailed but accessible
- Marketing content should be engaging but not hyperbolic

### Contact Page Specifics
- Only necessary fields (no phone numbers unless specifically needed)
- Text should be fully contained within styled blocks
- Discord invitation block should be clearly formatted and legible
- Use proper spacing and padding for readability

## Development Practices

### File Organization
- **Main pages**: PHP files at root (`index.php`, `projects.php`, `contact.php`, etc.)
- **Includes**: `/includes/` - header, footer, navigation, config, database functions
- **Assets**: `/assets/` organized by type - `css/`, `js/`, `images/`, `fonts/`
- **Projects**: `/projects/{project-slug}/` with `index.php` and `project.json`
- **CSS**: Unified in `wds-unified.css` - single source of truth for all styling

### Authentication & Database
- **Database**: PDO connection to external OGP gaming panel database
- **Authentication**: Functions in `db-config.php` - `verifyAdminLogin()`, `isLoggedInAdmin()`
- **Sessions**: `$_SESSION['wds_admin_user']` for logged-in state, role-based access
- **Protection**: Define `WDS_SYSTEM` constant to prevent direct access to includes

### JavaScript Patterns
- **Project loading**: `showProject(slug, title, category)` via XMLHttpRequest
- **Sub-file loading**: `loadProjectFile(filename)` maintains project context
- **DOM parsing**: Extract content from AJAX responses using DOMParser
- **Navigation**: Smart path detection for both XAMPP local and production environments

### CSS Best Practices
- All styles should be in `wds-unified.css` for consistency
- Use `!important` sparingly and only when overriding Bootstrap
- Maintain responsive design for mobile devices
- Test on multiple screen sizes

### Testing Requirements
- Test all pages on desktop and mobile
- Verify color consistency across all pages
- Check button hover states and transitions
- Ensure all links work correctly (especially project sub-pages)
- Validate form submissions

## Login & Authentication
- Staff login available at `/login.php`
- Protected pages check authentication status
- Staff-only content should be hidden from public users
- Session management handled via PHP sessions

## Critical Implementation Patterns

### Project System Workflow
1. **Discovery**: `projects.php` scans `/projects/` directories for `project.json` files
2. **Cards**: Generate project cards with metadata (title, category, description, icon)
3. **Loading**: AJAX calls to `projects/{slug}/index.php` load content in-place
4. **Context**: All project files use `loadProjectFile()` to stay within project view
5. **Navigation**: Back button shows projects overview, maintains smooth transitions

### Database Integration Example
```php
// Always define system constant first
define('WDS_SYSTEM', true);
require_once 'includes/db-config.php';

// Check authentication
if (!isLoggedInAdmin()) {
    header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
```

### Path Detection Pattern (from `navigation.php`)
```php
// Universal path detection for XAMPP and production
$current_url = $_SERVER['REQUEST_URI'];
$is_in_projects = (strpos($current_url, '/projects/') !== false);
$base_path = $is_in_projects ? '../' : '';
```

## Common Issues to Avoid
1. **Text Shadows**: Never use text shadows - breaks Orwellian aesthetic
2. **Direct Page Navigation**: Project sub-files must load via AJAX, not separate pages
3. **Missing System Constant**: Always define `WDS_SYSTEM` before including db-config
4. **Hardcoded Paths**: Use path detection pattern for XAMPP/production compatibility
5. **Session Conflicts**: Use `wds_` prefix for all session variables
6. **Inconsistent Colors**: Always use the defined Orwellian color palette
7. **Selected Button States**: Buttons should not look "selected" by default

## Deployment & Environment
- **Production**: runlevelsystems.com (shared hosting environment)
- **Development**: XAMPP local server (different path structure)
- **Database**: External MySQL connection to gaming panel database
- **Sessions**: Namespace isolation from gaming panel sessions
