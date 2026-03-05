<!-- Developed by World Domination Software LLC -->
# WDS Website CSS Guide

## SIMPLIFIED CSS STRUCTURE

The WDS website now uses a single, unified CSS file for consistent styling across all pages:

### Main CSS File
- **wds-unified.css** - The ONLY custom CSS file you need!

### Supporting CSS Files (Do Not Edit)
- bootstrap.min.css - Bootstrap framework
- magnific-popup.css - Lightbox plugin
- owl.carousel.css - Carousel plugin
- ionicons.css - Icon fonts

---

## COLOR SCHEME

### Background Colors
- `#0f0f1e` - Dark navy (main page background)
- `#1a1a2e` - Slightly lighter navy (content boxes)
- `#16213e` - Header/nav/footer background

### Text Colors
- `#e0e0e0` - Main text (light gray)
- `#64b5f6` - Headings (sky blue)
- `#81c784` - Links & accents (soft green)
- `#ffb74d` - Warnings/highlights (orange)

### Button Colors
- **Primary buttons**: `#64b5f6` background with `#0f0f1e` text
- **Secondary buttons**: Transparent with `#64b5f6` border
- **Warning buttons**: `#ffb74d` background

---

## IMPORTANT RULES

### 1. NO TEXT SHADOWS - EVER!
All text shadows are globally disabled. Do not add them back.

### 2. ALL BUTTONS MUST HAVE DARK BACKGROUNDS
Never use white or light backgrounds on buttons. Always use the defined button colors.

### 3. CONSISTENT PROJECT PAGES
All project pages use the same dark theme. No custom styles in individual pages.

---

## HOW TO USE

### Adding to a Page
```html
<link href="assets/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/wds-unified.css" rel="stylesheet">
```

### Common Classes

#### Headings
```html
<h1>Sky Blue Heading</h1>
<h2>Also Sky Blue</h2>
<h3>All headings are #64b5f6</h3>
```

#### Buttons
```html
<button class="btn">Primary Button</button>
<button class="btn-secondary">Secondary Button</button>
<button class="btn-warning">Warning Button</button>
```

#### Content Boxes
```html
<div class="content-box">
    Content with subtle background
</div>
```

#### Feature Cards
```html
<div class="feature-card">
    <div class="feature-icon"><i class="fas fa-rocket"></i></div>
    <h4>Feature Title</h4>
    <p>Feature description...</p>
</div>
```

#### Info Boxes
```html
<div class="info-box">Info message</div>
<div class="warning-box">Warning message</div>
<div class="success-box">Success message</div>
```

#### Tech Badges
```html
<span class="tech-badge">PHP</span>
<span class="tech-badge">JavaScript</span>
```

---

## TROUBLESHOOTING

### Problem: Text is hard to read
**Solution**: Use `color: #e0e0e0` for body text on dark backgrounds

### Problem: Buttons have white backgrounds
**Solution**: Remove any inline `style="background: white"` - the CSS will apply the correct dark style

### Problem: Text has shadows
**Solution**: This should never happen. Check for inline `text-shadow` styles and remove them.

### Problem: Colors look wrong
**Solution**: Make sure `wds-unified.css` is loaded AFTER bootstrap.min.css and INSTEAD OF main.css and readability-improvements.css

---

## FILE LOCATIONS

### CSS Files
- `/assets/css/wds-unified.css` - Main stylesheet
- `/assets/css/bootstrap.min.css` - Bootstrap
- `/assets/css/*.css.backup` - Backup of old files

### Pages Using Unified CSS
- index.php
- projects.php
- contact.php
- joinus.php
- coop-journey.php
- /projects/gameserver-panel/index.php
- /projects/space5x/references.php (embedded styles)

---

## MAKING CHANGES

### To Change Colors Site-Wide
1. Open `assets/css/wds-unified.css`
2. Edit the color variables at the top
3. Save - changes apply everywhere

### To Add New Styles
1. Add them to `wds-unified.css` under the appropriate section
2. Use `!important` to override inline styles if needed
3. Add comments explaining what the style does

### DO NOT:
- ❌ Create new CSS files
- ❌ Add `<style>` blocks in HTML pages
- ❌ Edit main.css or readability-improvements.css (they're deprecated)
- ❌ Add inline styles with `style=` attributes (use classes instead)
- ❌ Add text-shadow anywhere

---

## BACKUP & RECOVERY

Original CSS files are backed up:
- `assets/css/main.css.backup`
- `assets/css/readability-improvements.css.backup`

To restore if needed, remove `.backup` extension and update page includes.

---

## QUESTIONS?

The CSS is now SIMPLE and CLEAN. Each section is clearly labeled. If you need to add something:

1. Find the relevant section in `wds-unified.css`
2. Add your rule with clear comments
3. Use the established color scheme
4. Test on multiple pages to ensure consistency

**Remember: ONE CSS file, ONE color scheme, NO text shadows, NO white buttons!**
