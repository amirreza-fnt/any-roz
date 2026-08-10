# Ani-Roz API Documentation — مستندات API

> **Base URL:** `https://your-domain.com/api/v1`  
> **Auth:** Laravel Sanctum (Bearer Token)  
> **Format:** JSON  
> **Charset:** UTF-8  
> **Currency:** IRR (Toman — تومان)

---

## Table of Contents

1. [Authentication](#1-authentication-احراز-هویت)
2. [Categories](#2-categories-دسته‌بندی‌ها)
3. [Products](#3-products-محصولات)
4. [Type Of Weights](#4-type-of-weights-انواع-وزن)
5. [Provinces & Cities](#5-provinces--cities-استان‌ها-و-شهرها)
6. [Shipping Methods](#6-shipping-methods-روش‌های-ارسال)
7. [Articles](#7-articles-مقالات)
8. [Contact Settings](#8-contact-settings-اطلاعات-تماس)
9. [User Addresses](#9-user-addresses-آدرس‌های-کاربر)
10. [Orders](#10-orders-سفارشات)
11. [Discount & Gift Codes](#11-discount--gift-codes-کدهای-تخفیف-و-هدیه)

---

## 1. Authentication (احراز هویت)

 authentication is OTP-based using mobile number.  
**فرمت شماره موبایل:** `0912XXXXXXX` (11 رقمی، با ۰۹)

---

### POST `/auth/send-otp`

ارسال کد تأیید به شماره موبایل

**Request:**
```json
{
  "mobile": "09121234567"
}
```

**Response `200` (in development/debug mode):**
```json
{
  "message": "کد تأیید برای شما ارسال شد",
  "otp": "123456"
}
```

> در محیط production، کد واقعاً از طریق SMS ارسال می‌شود و فیلد `otp` در پاسخ وجود ندارد.  
> در محیط development (`APP_DEBUG=true`)، کد در پاسخ برگردانده می‌شود تا تست آسان‌تر باشد.  
> کد تأیید به مدت ۵ دقیقه معتبر است.  
> هر شماره موبایل حداکثر ۵ بار در هر ۱۰ دقیقه می‌تواند درخواست کد دهد.

---

### POST `/auth/verify-otp`

تأیید کد و دریافت توکن

**Request:**
```json
{
  "mobile": "09121234567",
  "otp": "123456"
}
```

**Response `200`:**
```json
{
  "message": "ورود با موفقیت انجام شد",
  "user": {
    "id": 1,
    "name": "09121234567",
    "email": "09121234567@aniroz.ir",
    "mobile": "09121234567"
  },
  "token": "1|abc123def456..."
}
```

> اگر کاربر قبلاً ثبت‌نام نکرده باشد، به‌صورت خودکار یک حساب جدید با شماره موبایل ایجاد می‌شود.

**Error `422`:**
```json
{ "message": "کد تأیید نامعتبر یا منقضی شده است" }
```

**Error `429` (too many OTP requests):**
```json
{ "message": "تعداد درخواست‌های مجاز را رد کرده‌اید. لطفاً ۱۰ دقیقه بعد تلاش کنید." }
```

---

### POST `/auth/logout` 🔒

خروج از حساب کاربری

**Headers:** `Authorization: Bearer {token}`

**Response `200`:**
```json
{ "message": "خروج با موفقیت انجام شد" }
```

---

### GET `/user/profile` 🔒

مشاهده پروفایل کاربر

**Response `200`:**
```json
{
  "id": 1,
  "name": "علیرضا احمدی",
  "email": "alireza@example.com",
  "mobile": "09121234567",
  "is_active": true,
  "created_at": "2026-07-04T12:00:00.000000Z"
}
```

---

### PUT `/user/profile` 🔒

به‌روزرسانی پروفایل کاربر

**Request (partial update — any field is optional):**
```json
{
  "name": "علیرضا احمدی",
  "email": "alireza@example.com",
  "password": "newpassword",
  "password_confirmation": "newpassword"
}
```

> شماره موبایل (`mobile`) را نمی‌توان از این طریق تغییر داد.

**Response `200`:**
```json
{
  "message": "پروفایل با موفقیت به‌روزرسانی شد",
  "user": {
    "id": 1,
    "name": "علیرضا احمدی",
    "email": "alireza@example.com",
    "mobile": "09121234567"
  }
}
```

---

## 2. Categories (دسته‌بندی‌ها)

### GET `/categories`

لیست تمام دسته‌بندی‌های فعال

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| `parent_id` | int | فیلتر بر اساس والد (اختیاری) |

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "محصولات ارگانیک",
      "slug": "organic-products",
      "image": "https://.../uploads/images/category/xxx.jpg",
      "status": "active",
      "parent_id": null,
      "children": [
        {
          "id": 3,
          "title": "سبزیجات ارگانیک",
          "slug": "organic-vegetables",
          "image": null,
          "status": "active",
          "parent_id": 1,
          "children": [],
          "products_count": 5,
          "created_at": "2026-06-01T10:00:00.000000Z"
        }
      ],
      "products_count": 12,
      "created_at": "2026-06-01T10:00:00.000000Z"
    }
  ]
}
```

---

### GET `/categories/{id}`

مشاهده جزئیات یک دسته‌بندی

**Response `200`:**
```json
{
  "data": {
    "id": 1,
    "title": "محصولات ارگانیک",
    "slug": "organic-products",
    "image": "https://.../uploads/images/category/xxx.jpg",
    "status": "active",
    "parent_id": null,
    "children": [...],
    "products_count": 12,
    "created_at": "2026-06-01T10:00:00.000000Z"
  }
}
```

---

## 3. Products (محصولات)

### GET `/products`

لیست محصولات فعال (با pagination)

**Query Parameters:**
| Param | Type | Default | Description |
|-------|------|---------|-------------|
| `page` | int | 1 | شماره صفحه |
| `per_page` | int | 12 | تعداد در هر صفحه (حداکثر 50) |
| `category_id` | int | — | فیلتر بر اساس دسته‌بندی |
| `search` | string | — | جستجو در عنوان، توضیحات کوتاه و کد رهگیری |
| `suggested` | bool | — | فقط محصولات پیشنهادی (`1`) |
| `sort` | string | newest | `price_asc`, `price_desc`, `newest`, `oldest` |

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "گوجه فرنگی ارگانیک",
      "slug": "organic-tomato",
      "tracking_code": "PRD-001",
      "category_id": 1,
      "category": {
        "id": 1,
        "title": "محصولات ارگانیک",
        "slug": "organic-products",
        "image": "https://...",
        "status": "active",
        "parent_id": null,
        "children": [],
        "products_count": 12,
        "created_at": "2026-06-01T10:00:00.000000Z"
      },
      "price": 50000,
      "price_discounted": 45000,
      "stock": 100,
      "status": "active",
      "suggested": "active",
      "mini_description": "گوجه فرنگی تازه و ارگانیک",
      "primary_image": "https://.../uploads/images/product/xxx.jpg",
      "images": [
        {
          "id": 1,
          "url": "https://.../uploads/images/product/xxx.jpg",
          "sort_order": 0
        }
      ],
      "type_of_weights": [
        {
          "id": 1,
          "title": "کیلویی",
          "weight": 1,
          "pivot": {
            "stock": 100,
            "price": 50000,
            "price_buy": 35000,
            "price_discounted": 45000
          }
        }
      ],
      "created_at": "2026-06-01T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 12,
    "total": 56
  }
}
```

---

### GET `/products/{product}`

مشاهده جزئیات کامل محصول

> Uses route-model binding — `{product}` can be the product ID.

**Response `200`:**
```json
{
  "data": {
    "id": 1,
    "title": "گوجه فرنگی ارگانیک",
    "slug": "organic-tomato",
    "tracking_code": "PRD-001",
    "category_id": 1,
    "category": {...},
    "price": 50000,
    "price_buy": 35000,
    "price_discounted": 45000,
    "has_discount": true,
    "stock": 100,
    "status": "active",
    "suggested": "active",
    "mini_description": "گوجه فرنگی تازه و ارگانیک",
    "description": "<p>توضیحات کامل محصول...</p>",
    "primary_image": "https://.../uploads/images/product/xxx.jpg",
    "images": [...],
    "type_of_weights": [...],
    "created_at": "2026-06-01T10:00:00.000000Z",
    "updated_at": "2026-06-01T10:00:00.000000Z"
  },
  "related_products": [
    { "...": "..." }
  ]
}
```

---

## 4. Type of Weights (انواع وزن)

### GET `/type-of-weights`

لیست انواع وزن/بسته‌بندی

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "کیلویی",
      "weight": 1
    },
    {
      "id": 2,
      "title": "نیم کیلو",
      "weight": 0.5
    }
  ]
}
```

---

## 5. Provinces & Cities (استان‌ها و شهرها)

### GET `/provinces`

لیست استان‌ها

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "تهران",
      "area_code": "021"
    },
    {
      "id": 2,
      "name": "اصفهان",
      "area_code": "031"
    }
  ]
}
```

---

### GET `/provinces/{province}/cities`

لیست شهرهای یک استان

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "تهران",
      "province_id": 1
    },
    {
      "id": 2,
      "name": "شیراز",
      "province_id": 7
    }
  ]
}
```

---

## 6. Shipping Methods (روش‌های ارسال)

### GET `/shipping-methods`

لیست روش‌های ارسال فعال

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "پست پیشتاز",
      "base_shipping_cost": 35000,
      "base_insurance_cost": 5000,
      "base_packaging_cost": 0,
      "package_weight_limit": 12,
      "extra_weight_cost": 3000,
      "extra_weight_per_kg": 3000,
      "description": "ارسال با پست پیشتاز تا ۳ روز کاری",
      "sort_order": 1,
      "is_active": true
    }
  ]
}
```

**Shipping Cost Formula:**
```
total = base_shipping_cost + base_insurance_cost + base_packaging_cost
if total_weight > package_weight_limit:
    extra = (total_weight - package_weight_limit) * extra_weight_cost
    total += extra
```

---

## 7. Articles (مقالات)

### GET `/articles`

لیست مقالات منتشر شده

**Query Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| `page` | int | شماره صفحه |
| `per_page` | int | تعداد در هر صفحه (حداکثر 50) |
| `category_id` | int | فیلتر بر اساس دسته‌بندی |
| `featured` | bool | فقط مقالات ویژه (`1`) |

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "خواص گوجه فرنگی ارگانیک",
      "slug": "organic-tomato-benefits",
      "excerpt": "گوجه فرنگی ارگانیک دارای خواص非常多...",
      "featured_image": "https://.../uploads/images/article/xxx.jpg",
      "category_id": 1,
      "category": {...},
      "reading_minutes": 5,
      "view_count": 120,
      "is_featured": true,
      "published_at": "2026-06-15T10:00:00.000000Z",
      "created_at": "2026-06-15T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 12,
    "total": 30
  }
}
```

---

### GET `/articles/{article}`

مشاهده جزئیات مقاله

> Uses route-model binding with `slug`. Example: `/articles/organic-tomato-benefits`

**Response `200`:**
```json
{
  "data": {
    "id": 1,
    "title": "خواص گوجه فرنگی ارگانیک",
    "slug": "organic-tomato-benefits",
    "excerpt": "خلاصه مقاله...",
    "body": "<p>محتوای کامل مقاله...</p>",
    "featured_image": "https://...",
    "meta_title": "خواص گوجه فرنگی ارگانیک",
    "meta_description": "...",
    "meta_keywords": "گوجه, ارگانیک, خواص",
    "canonical_url": null,
    "og_image": null,
    "focus_keyword": "گوجه فرنگی ارگانیک",
    "noindex": false,
    "category_id": 1,
    "category": {...},
    "tags": [
      { "id": 1, "name": "ارگانیک", "slug": "organic" }
    ],
    "author": {
      "id": 1,
      "name": "مدیر سایت"
    },
    "reading_minutes": 5,
    "view_count": 121,
    "is_featured": true,
    "published_at": "2026-06-15T10:00:00.000000Z",
    "created_at": "2026-06-15T10:00:00.000000Z",
    "updated_at": "2026-06-15T10:00:00.000000Z"
  },
  "related_articles": [...]
}
```

---

## 8. Contact Settings (اطلاعات تماس)

### GET `/contact-settings`

اطلاعات تماس فروشگاه

**Response `200`:**
```json
{
  "data": {
    "phones": ["021-12345678", "09121234567"],
    "emails": ["info@aniroz.ir"],
    "addresses": ["تهران، خیابان انقلاب، ..."],
    "socials": {
      "instagram": "https://instagram.com/aniroz",
      "telegram": "https://t.me/aniroz"
    },
    "working_hours": "شنبه تا پنجشنبه ۹ تا ۱۸",
    "fax": "021-12345679",
    "support_title": "پشتیبانی آنی رز",
    "footer_note": "تمامی حقوق محفوظ است"
  }
}
```

---

## 9. User Addresses (آدرس‌های کاربر)

> 🔒 All endpoints require authentication (Bearer Token)

### GET `/user/addresses`

لیست آدرس‌های کاربر

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "first_name": "علیرضا",
      "last_name": "احمدی",
      "full_name": "علیرضا احمدی",
      "province_id": 1,
      "province_name": "تهران",
      "city_id": 1,
      "city_name": "تهران",
      "full_address": "خیابان انقلاب، کوچه ...",
      "postal_code": "1234567890",
      "mobile": "09121234567",
      "is_default": true,
      "is_active": true,
      "created_at": "2026-06-01T10:00:00.000000Z"
    }
  ]
}
```

---

### POST `/user/addresses`

افزودن آدرس جدید

**Request:**
```json
{
  "first_name": "علیرضا",
  "last_name": "احمدی",
  "province_id": 1,
  "province_name": "تهران",
  "city_id": 1,
  "city_name": "تهران",
  "full_address": "خیابان انقلاب، پلاک ۲",
  "postal_code": "1234567890",
  "mobile": "09121234567",
  "is_default": true
}
```

**Response `201`:**
```json
{
  "message": "آدرس با موفقیت اضافه شد",
  "data": { "...": "..." }
}
```

---

### PUT `/user/addresses/{id}` 🔒

ویرایش آدرس

**Request:** (partial update — all fields optional)

**Response `200`:**
```json
{
  "message": "آدرس با موفقیت به‌روزرسانی شد",
  "data": { "...": "..." }
}
```

---

### DELETE `/user/addresses/{id}` 🔒

حذف آدرس (غیرفعال‌سازی)

**Response `200`:**
```json
{ "message": "آدرس با موفقیت حذف شد" }
```

---

### POST `/user/addresses/{id}/default` 🔒

تنظیم آدرس پیش‌فرض

**Response `200`:**
```json
{
  "message": "آدرس پیش‌فرض با موفقیت تنظیم شد",
  "data": { "...": "..." }
}
```

---

## 10. Orders (سفارشات)

> 🔒 All endpoints require authentication (Bearer Token)

### GET `/user/orders`

لیست سفارشات کاربر

**Response `200`:**
```json
{
  "data": [
    {
      "id": 1,
      "order_number": "ORD-20260704-ABC123",
      "total_amount": 150000,
      "shipping_fee": 45000,
      "discount_amount": 0,
      "final_amount": 195000,
      "total_weight": 2.5,
      "shipping_status": "pending_review",
      "shipping_status_label": "در حال بررسی",
      "payment_status": "pending",
      "payment_status_label": "در انتظار پرداخت",
      "items_count": 3,
      "created_at": "2026-07-04T14:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "total": 5
  }
}
```

---

### GET `/user/orders/{order}` 🔒

مشاهده جزئیات سفارش

**Response `200`:**
```json
{
  "data": {
    "id": 1,
    "order_number": "ORD-20260704-ABC123",
    "source": "site",
    "total_amount": 150000,
    "shipping_fee": 45000,
    "discount_amount": 0,
    "shipping_cost": 35000,
    "insurance_cost": 5000,
    "final_amount": 195000,
    "total_weight": 2.5,
    "shipping_status": "pending_review",
    "shipping_status_label": "در حال بررسی",
    "payment_status": "pending",
    "payment_status_label": "در انتظار پرداخت",
    "payment_method": null,
    "shipping_method": "پست پیشتاز",
    "shipping_address": "خیابان انقلاب...",
    "shipping_city": "تهران",
    "shipping_state": "تهران",
    "shipping_postal_code": "1234567890",
    "shipping_recipient_name": "علیرضا احمدی",
    "shipping_phone": "09121234567",
    "shipping_tracking_code": null,
    "notes": null,
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "گوجه فرنگی ارگانیک",
        "product_code": "PRD-001",
        "product_image": "https://...",
        "quantity": 2,
        "unit_price": 50000,
        "discount_percent": 0,
        "discount_amount": 0,
        "final_price": 100000,
        "product_options": { "type_of_weight_id": 1 }
      }
    ],
    "histories": [
      {
        "id": 1,
        "status": "pending_review",
        "note": "سفارش ثبت شد",
        "created_at": "2026-07-04T14:30:00.000000Z"
      }
    ],
    "created_at": "2026-07-04T14:30:00.000000Z",
    "updated_at": "2026-07-04T14:30:00.000000Z"
  }
}
```

---

### POST `/user/orders` 🔒

ثبت سفارش جدید

**Request:**
```json
{
  "items": [
    {
      "product_id": 1,
      "type_of_weight_id": 1,
      "quantity": 2
    },
    {
      "product_id": 2,
      "quantity": 1
    }
  ],
  "shipping_method_id": 1,
  "address_id": 1,
  "discount_code": "WELCOME10",
  "gift_code": "GIFT-123",
  "notes": "لطفا بعد از ساعت ۵ تحویل داده شود"
}
```

**Field Descriptions:**
| Field | Required | Description |
|-------|----------|-------------|
| `items` | ✅ | آرایه محصولات (حداقل ۱ آیتم) |
| `items[].product_id` | ✅ | شناسه محصول |
| `items[].type_of_weight_id` | ❌ | شناسه نوع وزن (اختیاری) |
| `items[].quantity` | ✅ | تعداد (حداقل ۱) |
| `shipping_method_id` | ✅ | شناسه روش ارسال |
| `address_id` | ✅ | شناسه آدرس |
| `discount_code` | ❌ | کد تخفیف |
| `gift_code` | ❌ | کد هدیه |
| `notes` | ❌ | توضیحات اضافه |

**Response `201`:**
```json
{
  "message": "سفارش با موفقیت ثبت شد",
  "data": { "...": "جزئیات سفارش ..." }
}
```

**Order Status Values:**
| Status | Label |
|--------|-------|
| `pending_review` | در حال بررسی |
| `packaging` | در حال بسته‌بندی |
| `shipping` | در حال ارسال |
| `completed` | اتمام رسیده |
| `cancelled` | لغو شده |

**Payment Status Values:**
| Status | Label |
|--------|-------|
| `pending` | در انتظار پرداخت |
| `paid` | پرداخت شده |
| `failed` | ناموفق |
| `refunded` | بازگشت وجه |

---

### POST `/user/orders/{order}/cancel` 🔒

لغو سفارش

> فقط در وضعیت‌های `pending_review` و `packaging` قابل لغو است.

**Response `200`:**
```json
{ "message": "سفارش با موفقیت لغو شد" }
```

**Error `422`:**
```json
{ "message": "امکان لغو سفارش در وضعیت فعلی وجود ندارد" }
```

---

## 11. Discount & Gift Codes (کدهای تخفیف و هدیه)

> 🔒 Requires authentication (Bearer Token)

### POST `/discount/validate`

اعتبارسنجی کد تخفیف یا کد هدیه

**Request:**
```json
{
  "code": "WELCOME10",
  "type": "discount",
  "total_amount": 150000
}
```

**Query Fields:**
| Field | Required | Description |
|-------|----------|-------------|
| `code` | ✅ | کد تخفیف یا کد هدیه |
| `type` | ✅ | `discount` یا `gift` |
| `total_amount` | ✅ | مبلغ کل سبد خرید (تومان) |

**Response `200` (valid):**
```json
{
  "valid": true,
  "type": "discount",
  "code": "WELCOME10",
  "title": "تخفیف خوش آمدگویی",
  "discount_type": "percent",
  "amount": 15000,
  "description": "۱۰٪ تخفیف"
}
```

**Response `422` (invalid):**
```json
{
  "valid": false,
  "message": "کد تخفیف منقضی شده است"
}
```

**Error Messages:**
| Message | Description |
|---------|-------------|
| `کد تخفیف نامعتبر است` | کد وجود ندارد |
| `کد تخفیف هنوز فعال نشده است` | تاریخ شروع فعال نشده |
| `کد تخفیف منقضی شده است` | تاریخ انقضا گذشته |
| `کد تخفیف به حداکثر استفاده رسیده است` | محدودیت مصرف |
| `حداقل مبلغ سفارش ...` | کمتر از حداقل مبلغ |
| `این کد هدیه برای شما قابل استفاده نیست` | کاربر مجاز نیست |

---

## Error Response Format (قالب خطاها)

**Validation Errors `422`:**
```json
{
  "errors": {
    "email": ["ایمیل قبلاً ثبت شده است"],
    "password": ["رمز عبور باید حداقل ۸ کاراکتر باشد"]
  }
}
```

**Not Found `404`:**
```json
{ "message": "محصول یافت نشد" }
```

**Unauthenticated `401`:**
```json
{ "message": "Unauthenticated" }
```

**Server Error `500`:**
```json
{ "message": "خطای داخلی سرور" }
```

---

## Request Headers

| Header | Value | Required For |
|--------|-------|--------------|
| `Accept` | `application/json` | همه درخواست‌ها |
| `Content-Type` | `application/json` | POST, PUT |
| `Authorization` | `Bearer {token}` | 🔒 Routes |

---

## Pagination

All paginated endpoints return:
```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 12,
    "total": 56
  }
}
```

---

## Postman Collection

For easy testing, import the following into Postman:

**Base variables:**
- `base_url`: `http://localhost:8000/api/v1`
- `token`: (set after login)

**Example curl:**
```bash
# Login
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"secret"}'

# Get products (public)
curl http://localhost:8000/api/v1/products?per_page=20

# Get user orders (authenticated)
curl http://localhost:8000/api/v1/user/orders \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|abc123..."
```

---

---

## 12. Shopping Cart (سبد خرید)

> سبد خرید مبتنی بر **Session** است و هیچ داده‌ای در دیتابیس ذخیره نمی‌شود.
> برای استفاده از سبد خرید، کلاینت باید کوکی سشن (session cookie) را در تمام درخواست‌ها ارسال کند.
> نیازی به احراز هویت برای مدیریت سبد خرید نیست — فقط برای **checkout** نیاز به توکن Sanctum است.

**Base URL:** `/api/v1/cart`

---

### GET `/cart`

مشاهده محتویات کامل سبد خرید با محاسبات لحظه‌ای

**Session:** ✅ (نیاز به سشن کوکی)

**Response `200` (سبد خرید خالی):**
```json
{
  "data": {
    "items": [],
    "summary": {
      "total_items": 0,
      "total_quantity": 0,
      "total_amount": 0,
      "total_weight": 0,
      "shipping_fee": 0,
      "discount_amount": 0,
      "final_amount": 0
    },
    "discount": null,
    "shipping": null
  }
}
```

**Response `200` (سبد خرید پر):**
```json
{
  "data": {
    "items": [
      {
        "item_id": "aBcDeFgHiJkLmNoP",
        "product_id": 1,
        "product": {
          "id": 1,
          "title": "گوجه فرنگی ارگانیک",
          "slug": "organic-tomato",
          "tracking_code": "PRD-001",
          "primary_image": "https://.../images/product/xxx.jpg",
          "price": 50000,
          "price_discounted": 45000,
          "has_discount": true
        },
        "type_of_weight": {
          "id": 1,
          "title": "کیلویی",
          "weight": 1
        },
        "quantity": 2,
        "unit_price": 45000,
        "line_total": 90000,
        "available_stock": 100,
        "weight": 1
      }
    ],
    "summary": {
      "total_items": 1,
      "total_quantity": 2,
      "total_amount": 90000,
      "total_weight": 1,
      "shipping_fee": 45000,
      "discount_amount": 10000,
      "final_amount": 125000
    },
    "discount": {
      "code": "WELCOME10",
      "type": "discount",
      "title": "تخفیف خوش آمدگویی",
      "description": "۱۰٪ تخفیف",
      "amount": 10000
    },
    "shipping": {
      "id": 1,
      "name": "پست پیشتاز",
      "fee": 45000
    }
  }
}
```

**توضیح فیلدها:**
| فیلد | توضیح |
|------|-------|
| `items[].item_id` | شناسه یکتای هر ردیف در سبد خرید |
| `items[].product` | اطلاعات محصول (بروز از دیتابیس) |
| `items[].type_of_weight` | تنوع وزنی انتخاب شده (در صورت وجود) |
| `items[].unit_price` | قیمت واحد نهایی پس از اعمال تخفیف محصول |
| `items[].line_total` | قیمت نهایی این ردیف = `unit_price × quantity` |
| `items[].available_stock` | موجودی فعلی در دیتابیس |
| `summary.total_amount` | مجموع قیمت همه آیتم‌ها |
| `summary.total_weight` | مجموع وزن همه آیتم‌ها |
| `summary.shipping_fee` | هزینه ارسال محاسبه شده |
| `summary.discount_amount` | تخفیف اعمال شده از کد تخفیف/هدیه |
| `summary.final_amount` | مبلغ نهایی = `total_amount + shipping_fee - discount_amount` |

---

### GET `/cart/count`

دریافت تعداد کل آیتم‌های سبد خرید (برای نمایش نشان در front-end)

**Response `200`:**
```json
{
  "data": {
    "count": 5
  }
}
```

---

### POST `/cart/items`

افزودن محصول به سبد خرید

**Request:**
```json
{
  "product_id": 1,
  "type_of_weight_id": 1,
  "quantity": 2
}
```

**فیلدهای ورودی:**
| فیلد | الزامی | توضیح |
|------|--------|-------|
| `product_id` | ✅ | شناسه محصول |
| `type_of_weight_id` | ❌ | شناسه تنوع وزنی (برای محصولاتی که تنوع وزنی دارند) |
| `quantity` | ✅ | تعداد (حداقل ۱، حداکثر ۹۹۹) |

> اگر محصول قبلاً با همان `product_id` و `type_of_weight_id` در سبد خرید وجود داشته باشد، تعداد آن افزایش می‌یابد (به‌جای افزودن دوباره).

**Response `200`:**
```json
{
  "message": "محصول با موفقیت به سبد خرید اضافه شد"
}
```

**Error `422`:**
```json
{
  "message": "موجودی ناکافی. حداکثر تعداد: 50"
}
```

---

### PUT `/cart/items/{itemId}`

به‌روزرسانی تعداد یک آیتم در سبد خرید

**Request:**
```json
{
  "quantity": 3
}
```

**Response `200`:**
```json
{
  "message": "تعداد محصول با موفقیت به‌روزرسانی شد"
}
```

**Error `404` (آیتم یافت نشد):**
```json
{
  "message": "آیتم مورد نظر در سبد خرید یافت نشد"
}
```

---

### DELETE `/cart/items/{itemId}`

حذف یک آیتم از سبد خرید

**Response `200`:**
```json
{
  "message": "محصول با موفقیت از سبد خرید حذف شد"
}
```

---

### DELETE `/cart`

خالی کردن کامل سبد خرید

**Response `200`:**
```json
{
  "message": "سبد خرید با موفقیت خالی شد"
}
```

---

### POST `/cart/apply-discount`

اعمال کد تخفیف یا کد هدیه روی سبد خرید

> این اندپوینت کد را **اعتبارسنجی** و در سشن ذخیره می‌کند.  
> مقادیر تخفیف در پاسخ `GET /cart` به‌صورت خودکار محاسبه می‌شود.  
> کد تخفیف و کد هدیه **قابل جمع** هستند (هر دو می‌توانند همزمان اعمال شوند).

**Request:**
```json
{
  "code": "WELCOME10",
  "type": "discount"
}
```

**فیلدهای ورودی:**
| فیلد | الزامی | توضیح |
|------|--------|-------|
| `code` | ✅ | کد تخفیف یا کد هدیه |
| `type` | ✅ | `discount` یا `gift` |

**Response `200`:**
```json
{
  "message": "کد با موفقیت اعمال شد",
  "data": {
    "type": "discount",
    "code": "WELCOME10",
    "amount": 10000,
    "title": "تخفیف خوش آمدگویی",
    "description": "۱۰٪ تخفیف"
  }
}
```

**Error `422` (سبد خرید خالی):**
```json
{
  "message": "سبد خرید خالی است"
}
```

**Error `422` (کد نامعتبر):**
```json
{
  "message": "کد تخفیف منقضی شده است"
}
```

---

### POST `/cart/remove-discount`

حذف کد تخفیف یا کد هدیه از سبد خرید

**Request:**
```json
{
  "type": "discount"
}
```

**فیلدهای ورودی:**
| فیلد | الزامی | توضیح |
|------|--------|-------|
| `type` | ✅ | `discount` یا `gift` |

**Response `200`:**
```json
{
  "message": "کد با موفقیت حذف شد"
}
```

---

### POST `/cart/select-shipping`

انتخاب روش ارسال

**Request:**
```json
{
  "shipping_method_id": 1
}
```

**فیلدهای ورودی:**
| فیلد | الزامی | توضیح |
|------|--------|-------|
| `shipping_method_id` | ✅ | شناسه روش ارسال (از `GET /shipping-methods`) |

**Response `200`:**
```json
{
  "message": "روش ارسال با موفقیت انتخاب شد"
}
```

---

### POST `/cart/sync`

همگام‌سازی سبد خرید با موجودی دیتابیس

> این اندپوینت موجودی همه آیتم‌های سبد خرید را با دیتابیس چک می‌کند.  
> اگر محصولی حذف یا ناموجود شده باشد، از سبد خرید حذف می‌شود.  
> اگر تعداد از موجودی بیشتر باشد، به حداکثر موجودی کاهش می‌یابد.

**Response `200`:**
```json
{
  "message": "موجودی برخی محصولات به‌روزرسانی شد",
  "synced": true
}
```

**Response `200` (بدون تغییر):**
```json
{
  "message": "همه محصولات به‌روز هستند",
  "synced": false
}
```

---

### POST `/cart/checkout` 🔒

تبدیل سبد خرید به سفارش (نیاز به احراز هویت)

> پس از ثبت موفق سفارش، سبد خرید به‌صورت خودکار خالی می‌شود.  
> کد تخفیف و کد هدیه `used_count` آن‌ها افزایش می‌یابد.  
> مبلغ نهایی با فرمول `total_amount + shipping_fee - discount_amount` محاسبه می‌شود.

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "address_id": 1,
  "notes": "لطفا بعد از ساعت ۵ تحویل داده شود"
}
```

**فیلدهای ورودی:**
| فیلد | الزامی | توضیح |
|------|--------|-------|
| `address_id` | ✅ | شناسه آدرس کاربر (از `GET /user/addresses`) |
| `notes` | ❌ | توضیحات اضافه (حداکثر ۱۰۰۰ کاراکتر) |

**Response `201`:**
```json
{
  "message": "سفارش با موفقیت ثبت شد",
  "data": {
    "id": 1,
    "order_number": "ORD-20260704-ABC123",
    "...": "..."
  }
}
```

**Error `401` (عدم احراز هویت):**
```json
{
  "message": "برای ثبت سفارش باید وارد شوید"
}
```

**Error `422` (سبد خرید خالی):**
```json
{
  "message": "سبد خرید خالی است"
}
```

---

### جریان کاری پیشنهادی (Suggested Flow)

```
1. GET  /products          → مشاهده محصولات
2. POST /cart/items        → افزودن محصول به سبد خرید
3. GET  /cart/count        → نمایش تعداد روی آیکون سبد خرید
4. GET  /cart              → مشاهده سبد خرید با قیمت‌های لحظه‌ای
5. POST /cart/apply-discount → اعمال کد تخفیف (اختیاری)
6. GET  /shipping-methods  → دریافت روش‌های ارسال
7. POST /cart/select-shipping → انتخاب روش ارسال
8. POST /auth/verify-otp   → ورود/ثبت‌نام (اگر قبلاً وارد نشده)
9. POST /cart/checkout     → ثبت نهایی سفارش
```

---

### جزئیات فنی (Technical Details)

**Session Storage:** داده‌های سبد خرید در فایل‌های سشن (`storage/framework/sessions/`) با درایور `file` ذخیره می‌شوند. طول عمر سشن ۱۲۰ دقیقه (قابل تنظیم در `.env`).

**Performance:**
- قیمت‌ها و موجودی محصولات در هر درخواست `GET /cart` مستقیماً از دیتابیس خوانده می‌شوند (داده‌های همیشه به‌روز).
- محصولات با یک کوئری `WHERE IN` و `eager loading` بارگذاری می‌شوند (حداکثر ۲ کوئری برای کل سبد خرید).

**Security:**
- کوکی سشن به‌صورت `HttpOnly` و رمزنگاری شده ارسال می‌شود.
- مقادیر ورودی در تمام اندپوینت‌ها اعتبارسنجی می‌شوند (validation).
- موجودی محصول در لحظه اضافه کردن/به‌روزرسانی/ثبت سفارش چک می‌شود.
- آیتم‌های سبد خرید با شناسه‌های تصادفی (`Str::random(16)`) ایندکس می‌شوند.
- سبد خرید خالی پس از checkout خودکار پاک می‌شود.

**Notes:**
- کد تخفیف (`discount`) و کد هدیه (`gift`) می‌توانند همزمان اعمال شوند و تخفیف آن‌ها جمع می‌شود.
- کدهای تخفیف فقط در زمان checkout مصرف می‌شوند (`used_count` افزایش می‌یابد)، نه در زمان اعمال روی سبد خرید.
- `item_id` در مسیر `PUT` و `DELETE` باید دقیقاً همان مقداری باشد که از `GET /cart` دریافت شده است.

---

> **Last updated:** July 2026  
> **Project:** Ani-Roz — فروشگاه اینترنتی محصولات کشاورزی
