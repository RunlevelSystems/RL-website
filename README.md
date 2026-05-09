<!-- Developed by Core Loop Development -->
# Core Loop Development Website

A professional corporate website showcasing game development, server hosting, and business application services. Built with PHP and modern responsive design principles.

## Overview

This is the corporate website for Core Loop Development, a company specializing in indie game development, game server hosting, and custom business applications. The site presents a professional, modern Cobalt2-inspired interface reflecting the company’s developer-first identity.

## Roadmap & Documentation

Public roadmap and documentation for Core Loop projects are managed in the separate **Projects** hub on GitHub:

- **Roadmap (Discussions category):** https://github.com/World-Domination-Software/Projects/discussions/categories/roadmap
- **Projects hub repository:** https://github.com/World-Domination-Software/Projects
- **Projects wiki (documentation hub):** https://github.com/World-Domination-Software/Projects/wiki

The website `projects.php` page links out to this hub for detailed project information, roadmap updates, and community interaction.

## Site Structure

### Main Pages
- **`index.php`** - Homepage with hero section, service overview, and company introduction
- **`projects.php`** - Portfolio showcase of completed games and projects
- **`contact.php`** - Contact form and business inquiry page
- **`joinus.php`** - Recruitment and career opportunities
- **`coop-journey.php`** - Information about co-op programs and developer partnerships
- **`staff-info.php`** - Team member profiles and company information
- **`login.php`** / **`logout.php`** - Client portal authentication

### Project Portfolio
Individual project pages located in `/projects/` directory:
- **Roadkill series** (`roadkill.php`, `roadkill-v2.php`) - Vehicular combat games
- **Space 4X** (`space-4x.php`) - Strategic space exploration game
- **Alien Apocalypse** (`alien-apocalypse.php`) - Sci-fi action game
- **Neverwards** (`neverwards.php`) - Fantasy adventure game
- **BBS Revival** (`bbs-revival.php`) - Retro bulletin board system
- **PureOps** (`pureops.php`) - Tactical operations game
- **GameServers World** (`gameservers-world.php`) - Hosting platform showcase
- **WorldDomination.dev** (`worlddomination-dev.php`) - Developer tools platform

## Design & Styling

### Visual Theme
- **Dark Mode Interface**: Deep cobalt/navy backgrounds with high contrast text
- **Color Palette**:
  - Primary accents: Cyan and electric blue (`#36f3ff`, `#2d7fff`)
  - Backgrounds: Midnight/cobalt layers (`#071228`, `#0a1730`, `#0d1a33`)
  - Text: Soft white and muted steel-blue (`#eaf3ff`, `#a8bedc`)
  - Secondary accents: Warm gold highlights (`#ffd166`)

### Typography
- **Primary Font**: 'Exo 2' / 'Rajdhani' - Developer-style sans-serif for headings
- **Body Font**: 'Inter' - Readable sans-serif for body text and UI elements
- **Font Weights**: 300 (light), 400 (regular), 500 (medium), 700 (bold)

### Layout Features
- **Responsive Grid System**: Bootstrap-based responsive layout
- **CSS Grid**: Modern layouts with `grid-template-columns` for complex arrangements
- **Card-Based Design**: Content organized in bordered, rounded cards with subtle shadows
- **Glassmorphism Elements**: Semi-transparent overlays with blur effects
- **Grid Patterns**: Subtle dot-grid backgrounds for visual interest

### UI Components
- **Custom Buttons**: Rounded buttons with hover animations and multiple variants (primary, ghost)
- **Service Cards**: Three-column grid showcasing main business services
- **Project Showcases**: Detailed project pages with feature lists and technical specs
- **Navigation**: Clean top navigation with responsive mobile menu
- **Bullet Lists**: Custom checkmark bullets with cyan accent styling

## Technical Stack

### Frontend
- **HTML5**: Semantic markup with accessibility considerations
- **CSS3**: Custom styles with modern features (Grid, Flexbox, CSS Variables)
- **JavaScript**: jQuery for interactions, Owl Carousel for sliders
- **Bootstrap**: Responsive framework for layout consistency

### Backend
- **PHP**: Server-side logic and templating
- **Include System**: Modular PHP includes for headers, navigation, and footers
- **Database Integration**: MySQL connection configuration in `includes/db-config.php`

### Assets
- **CSS Libraries**: Bootstrap, Magnific Popup, Owl Carousel, Ionicons
- **Fonts**: Google Fonts integration, Glyphicons, Ionicons
- **Images**: Company branding, hero graphics, code illustrations
- **Documents**: Recruitment materials, co-op packages, audio content

## Key Features

### Business Focus
- **Service Presentation**: Clear categorization of game development, hosting, and business app services
- **Portfolio Integration**: Detailed project showcases with technical achievements
- **Professional Branding**: Consistent corporate identity throughout
- **Client Portal**: Secure login system for client access
- **Contact Integration**: Direct email links and consultation requests

### User Experience
- **Mobile Responsive**: Optimized for all device sizes
- **Fast Loading**: Optimized assets and efficient code structure
- **Accessibility**: Semantic HTML and ARIA labels
- **SEO Ready**: Proper meta tags and content structure
- **Professional Tone**: Clear, confident copy that builds trust

### Developer Features
- **Modular Architecture**: Reusable PHP includes and components
- **Maintainable CSS**: Organized stylesheets with clear naming conventions
- **Version Control Ready**: Git-friendly structure with proper .md documentation
- **Asset Organization**: Logical folder structure for easy maintenance

This website serves as both a marketing platform and a demonstration of the company's technical capabilities, showcasing professional web development skills while promoting their core business services.
