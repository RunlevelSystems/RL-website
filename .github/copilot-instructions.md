# World Domination Software - Website Development Guidelines

## Site Overview
World Domination Software (WDS) is a worker co-op focused on game development, server hosting, and business applications. The website showcases our projects, facilitates community engagement, and provides information about joining our co-op.

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
- PHP files for pages
- Includes in `/includes/` (header, footer, navigation)
- Assets in `/assets/` (css, js, images)
- Projects in `/projects/{project-slug}/`
- CSS unified in `wds-unified.css`

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

## Common Issues to Avoid
1. **Text Shadows**: Never use text shadows
2. **Inconsistent Colors**: Always use the defined color palette
3. **Selected Button States**: Buttons should not look "selected" by default
4. **Black Hover Backgrounds**: Avoid harsh black backgrounds on hover (use rust instead)
5. **Separate Page Loads**: Project sub-files should load in-page, not as separate pages
6. **Unreadable Text**: Always ensure sufficient contrast on all backgrounds
7. **Phone Numbers in Forms**: Only include when absolutely necessary

## Deployment
- Production site at worlddomination.dev
- Test changes thoroughly before deployment
- Maintain backwards compatibility
- Keep documentation updated
