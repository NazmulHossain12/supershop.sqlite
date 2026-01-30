# Marketing Analytics Setup Guide

## 🎯 Overview

Supershop now includes **Google Analytics 4** and **Facebook Pixel** integration for comprehensive marketing tracking.

---

## 📊 Tracked Events

### Google Analytics 4
- **PageView** - All pages automatically tracked
- **view_item** - Product detail page views
- **add_to_cart** - Products added to cart (via event)
- **begin_checkout** - Checkout page initiated
- **purchase** - Completed orders with transaction details

### Facebook Pixel
- **PageView** - All pages automatically tracked
- **ViewContent** - Product detail page views
- **AddToCart** - Products added to cart (via event)
- **InitiateCheckout** - Checkout page views
- **Purchase** - Completed orders with revenue

---

## ⚙️ Setup Instructions

### 1. Get Your Tracking IDs

**Google Analytics 4:**
1. Go to [Google Analytics](https://analytics.google.com/)
2. Create a GA4 property
3. Copy your **Measurement ID** (format: `G-XXXXXXXXXX`)

**Facebook Pixel:**
1. Go to [Facebook Events Manager](https://business.facebook.com/events_manager/)
2. Create a pixel
3. Copy your **Pixel ID** (format: `1234567890`)

### 2. Configure Environment Variables

Edit `.env` file:
```env
# Marketing & Analytics
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX
FACEBOOK_PIXEL_ID=1234567890
```

### 3. Deploy & Test

After adding IDs, clear cache:
```bash
php artisan config:clear
php artisan view:clear
```

---

## ✅ Verification

### Test Google Analytics
1. Install [Google Analytics Debugger](https://chrome.google.com/webstore/detail/google-analytics-debugger/) extension
2. Browse your shop
3. Check console for GA events

### Test Facebook Pixel
1. Install [Facebook Pixel Helper](https://chrome.google.com/webstore/detail/facebook-pixel-helper/) extension
2. Browse your shop
3. Check extension icon for pixel fires

---

## 📈 E-Commerce Tracking

### Automatic Events
- ✅ All page views
- ✅ Product views (PDP)
- ✅ Checkout initiated
- ✅ Purchase completed

### Manual Event (Optional)
For "Add to Cart" tracking, you can trigger manually via JavaScript:

```javascript
// Google Analytics
gtag('event', 'add_to_cart', { /* data */ });

// Facebook  Pixel
fbq('track', 'AddToCart', { /* data */ });
```

---

## 🔒 Privacy & GDPR

**Note:** This implementation loads tracking scripts on all pages. For GDPR compliance:

1. **Add Cookie Consent Banner** (e.g., CookieYes, OneTrust)
2. **Conditional Loading** - Only load scripts after user consent
3. **Privacy Policy** - Update to mention analytics

Example conditional loading:
```blade
@if(session('cookie_consent'))
    <x-analytics />
@endif
```

---

## 📊 Reports Available

### Google Analytics
- **Realtime** - Live visitor data
- **Acquisition** - Traffic sources
- **Engagement** - User behavior
- **Monetization** - E-commerce performance
- **Retention** - User retention

### Facebook Pixel
- **Events** - Pixel event activity
- **Conversions** - Funnel analysis
- **Custom Audiences** - Retargeting segments
- **Ad Performance** - Campaign tracking

---

## 🎯 Use Cases

1. **Marketing Campaigns** - Track ROI from ads
2. **Conversion Optimization** - Identify drop-off points
3. **Retargeting** - Build custom audiences
4. **A/B Testing** - Test variations
5. **Product Analytics** - Best-selling products

---

## 🔧 Customization

### Add Custom Events

Edit `resources/views/components/analytics-events.blade.php`:

```blade
{{-- Custom Event Example --}}
@if(isset($trackWishlist) && $trackWishlist)
<script>
    gtag('event', 'add_to_wishlist', { /* data */ });
    fbq('track', 'AddToWishlist', { /* data */ });
</script>
@endif
```

Then use in views:
```blade
<x-analytics-events :trackWishlist="true" :product="$product" />
```

---

## ✨ Files Modified

- [`config/services.php`](file:///c:/Projects/supershop/config/services.php) - Service configuration
- [`.env`](file:///c:/Projects/supershop/.env) - Environment variables
- [`resources/views/components/analytics.blade.php`](file:///c:/Projects/supershop/resources/views/components/analytics.blade.php) - Base tracking
- [`resources/views/components/analytics-events.blade.php`](file:///c:/Projects/supershop/resources/views/components/analytics-events.blade.php) - E-commerce events
- [`resources/views/layouts/app.blade.php`](file:///c:/Projects/supershop/resources/views/layouts/app.blade.php) - Main layout
- [`resources/views/layouts/guest.blade.php`](file:///c:/Projects/supershop/resources/views/layouts/guest.blade.php) - Guest layout
- Product, Checkout, Order views - Event triggers

---

## 🚀 Next Steps

1. Add your tracking IDs to `.env`
2. Test with browser extensions
3. Set up conversion goals in GA4
4. Create custom audiences in Facebook
5. Connect to ad platforms for campaign tracking

**Your shop is now fully instrumented for marketing success!** 📈
