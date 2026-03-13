# Magento 2 Blog Extension

Enhance your Magento 2 store with a powerful and user-friendly blogging system. The **Magento 2 Blog Extension** allows merchants to publish blog posts, organize content with categories, and connect blog content with products to improve engagement, SEO, and conversions.

This extension provides a complete blogging platform inside Magento, enabling both administrators and customers (authors) to create and manage blog content directly from the store.

---

## Key Features

### Blog Management
- Create and manage blog posts from the Magento admin panel
- Category-based blog organization
- Blog listing page with grid layout
- Blog URL routing support
- Post history/version tracking

### Author & Content Management
- Customer author registration
- Author request approval system
- Customer blog post submission
- Auto-approval configuration for authors and posts

### Blog Post Features
- Display author information on posts
- Show last edited date
- Previous and next post navigation
- Related blog posts section
- Short description support

### Product Integration
- Display related blog posts on product pages
- Display related products within blog posts
- Slider/grid display options for related content

### Sidebar Features
- Sticky table of contents for long blog posts
- CMS static block support in sidebar
- Flexible sidebar positioning

### Search & Content Discovery
- Blog search functionality
- Recent posts widget
- Most viewed posts widget
- Monthly archive browsing

### User Engagement
- Comment system with moderation support
- Helpfulness voting for blog posts
- Social sharing integration

### Social Sharing
Users can easily share blog posts through:
- Facebook
- X (Twitter)
- WhatsApp
- Telegram
- LinkedIn
- Reddit
- Email

### SEO Optimization
- Custom meta title, description, and keywords
- SEO-friendly blog URLs
- Robots meta configuration
- Improved search engine indexing

---

## Benefits

- **Improved SEO:**  
  Regular blog content helps increase organic traffic and search engine visibility.

- **Better Customer Engagement:**  
  Share guides, news, and product insights to keep customers engaged.

- **Product Promotion:**  
  Connect blog posts with products to increase product discovery and conversions.

- **Content Marketing Support:**  
  Build a strong content marketing strategy directly within your Magento store.

---

## Compatibility

This extension is compatible with:

- Magento **2.x**

---

## Installation

### Install via Composer (Recommended)

Run the following commands from your Magento root directory:

```bash
composer require mavenbird/blog-extension-magento2
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```