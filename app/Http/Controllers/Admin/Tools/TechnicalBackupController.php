<?php

namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\ContactSetting;
use App\Models\DiscountCode;
use App\Models\GiftCode;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ShippingConfig;
use App\Models\TypeOfWeight;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class TechnicalBackupController extends Controller
{
    public const MODULE_LABELS = [
        'categories' => 'دسته‌بندی‌ها',
        'type_of_weights' => 'انواع وزن',
        'products' => 'محصولات',
        'orders' => 'فاکتورها (سفارشات)',
        'order_products' => 'اقلام فاکتور',
        'articles' => 'مقالات',
        'gift_codes' => 'کدهای هدیه',
        'discount_codes' => 'کدهای تخفیف',
        'users' => 'کاربران',
        'shipping_configs' => 'روش‌های ارسال',
        'contact_settings' => 'تنظیمات ارتباطات',
    ];

    public function index()
    {
        return view('backend.tools.TechnicalBackupIndex', [
            'moduleLabels' => self::MODULE_LABELS,
        ]);
    }

    public function download(Request $request)
    {
        if (! class_exists(ZipArchive::class)) {
            message('error', 'افزونهٔ ZipArchive روی سرور فعال نیست.');

            return redirect()->back();
        }

        $allowed = array_keys(self::MODULE_LABELS);
        $selected = $request->query('modules', $allowed);
        if (! is_array($selected)) {
            $selected = $allowed;
        }
        $selected = array_values(array_intersect($selected, $allowed));
        if ($selected === []) {
            $selected = $allowed;
        }

        $warnings = [];
        $files = [];

        foreach ($selected as $key) {
            $csv = match ($key) {
                'categories' => $this->exportCategories(),
                'type_of_weights' => $this->exportTypeOfWeights(),
                'products' => $this->exportProducts(),
                'orders' => $this->exportOrders(),
                'order_products' => $this->exportOrderProducts(),
                'articles' => Schema::hasTable('articles') ? $this->exportArticles() : null,
                'gift_codes' => Schema::hasTable('gift_codes') ? $this->exportGiftCodes() : null,
                'discount_codes' => Schema::hasTable('discount_codes') ? $this->exportDiscountCodes() : null,
                'users' => $this->exportUsers(),
                'shipping_configs' => $this->exportShippingConfigs(),
                'contact_settings' => Schema::hasTable('contact_settings') ? $this->exportContactSettings() : null,
                default => null,
            };

            if ($csv === null) {
                $warnings[] = self::MODULE_LABELS[$key].': هنوز داده‌ای وجود ندارد یا جدول در دسترس نیست.';

                continue;
            }
            $files[$key.'.csv'] = $csv;
        }

        if ($files === []) {
            message('warning', implode(' ', $warnings) ?: 'هیچ فایلی برای خروج ساخته نشد.');

            return redirect()->route('admin.technical-backup.index');
        }

        $tmp = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'tb-'.uniqid('', true).'.zip';
        $zip = new ZipArchive;
        if ($zip->open($tmp, ZipArchive::OVERWRITE | ZipArchive::CREATE) !== true) {
            message('error', 'ایجاد فایل فشرده با خطا مواجه شد.');

            return redirect()->back();
        }
        foreach ($files as $name => $content) {
            $zip->addFromString($name, $content);
        }
        $zip->close();

        if ($warnings !== []) {
            message('warning', 'بخش‌های بدون داده در بسته قرار نگرفتند. '.implode(' ', $warnings));
        } else {
            message('success', 'بستهٔ پشتیبان آماده شد.');
        }

        $filename = 'technical-backup-'.date('Y-m-d-His').'.zip';
        $length = (string) filesize($tmp);

        return response()->download($tmp, $filename, [
            'Content-Type' => 'application/zip',
            'Content-Length' => $length,
            'Cache-Control' => 'private, no-store, must-revalidate',
            'Pragma' => 'public',
        ])->deleteFileAfterSend(true);
    }

    private function csvFromRows(array $headers, array $rows): string
    {
        $fh = fopen('php://memory', 'r+');
        fwrite($fh, "\xEF\xBB\xBF");
        fputcsv($fh, $headers);
        foreach ($rows as $row) {
            fputcsv($fh, $row);
        }
        rewind($fh);

        return stream_get_contents($fh) ?: '';
    }

    private function exportCategories(): ?string
    {
        $rows = Category::query()->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return null;
        }
        $headers = ['id', 'title', 'parent_id', 'status', 'image', 'created_at', 'updated_at'];
        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                $r->id,
                $r->title,
                $r->parent_id,
                $r->status,
                $r->image,
                (string) $r->created_at,
                (string) $r->updated_at,
            ];
        }

        return $this->csvFromRows($headers, $data);
    }

    private function exportTypeOfWeights(): ?string
    {
        $rows = TypeOfWeight::query()->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return null;
        }
        $headers = ['id', 'title', 'weight', 'created_at', 'updated_at'];
        $data = [];
        foreach ($rows as $r) {
            $data[] = [$r->id, $r->title, $r->weight, (string) $r->created_at, (string) $r->updated_at];
        }

        return $this->csvFromRows($headers, $data);
    }

    private function exportProducts(): ?string
    {
        $rows = Product::query()->withTrashed()->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return null;
        }
        $headers = ['id', 'title', 'slug', 'tracking_code', 'category_id', 'price', 'price_buy', 'price_discounted', 'stock', 'status', 'suggested', 'deleted_at', 'created_at', 'updated_at'];
        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                $r->id,
                $r->title,
                $r->slug,
                $r->tracking_code,
                $r->category_id,
                $r->price,
                $r->price_buy,
                $r->price_discounted,
                $r->stock,
                $r->status,
                $r->suggested,
                (string) $r->deleted_at,
                (string) $r->created_at,
                (string) $r->updated_at,
            ];
        }

        return $this->csvFromRows($headers, $data);
    }

    private function exportOrders(): ?string
    {
        $rows = Order::query()->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return null;
        }
        $headers = [
            'id', 'user_id', 'order_number', 'total_amount', 'shipping_fee', 'discount_amount', 'shipping_cost', 'insurance_cost',
            'final_amount', 'total_weight', 'payment_status', 'payment_method', 'payment_transaction_id', 'payment_date',
            'shipping_status', 'shipping_method', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_postal_code',
            'shipping_recipient_name', 'shipping_phone', 'shipping_tracking_code', 'notes', 'sent_to_supply', 'sent_to_supply_at',
            'created_at', 'updated_at',
        ];
        $data = [];
        foreach ($rows as $o) {
            $data[] = [
                $o->id,
                $o->user_id,
                $o->order_number,
                $o->total_amount,
                $o->shipping_fee,
                $o->discount_amount,
                $o->shipping_cost,
                $o->insurance_cost,
                $o->final_amount,
                $o->total_weight,
                $o->payment_status,
                $o->payment_method,
                $o->payment_transaction_id,
                (string) $o->payment_date,
                $o->shipping_status,
                $o->shipping_method,
                $o->shipping_address,
                $o->shipping_city,
                $o->shipping_state,
                $o->shipping_postal_code,
                $o->shipping_recipient_name,
                $o->shipping_phone,
                $o->shipping_tracking_code,
                $o->notes,
                $o->sent_to_supply ? '1' : '0',
                (string) $o->sent_to_supply_at,
                (string) $o->created_at,
                (string) $o->updated_at,
            ];
        }

        return $this->csvFromRows($headers, $data);
    }

    private function exportOrderProducts(): ?string
    {
        $rows = OrderProduct::query()->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return null;
        }
        $headers = ['id', 'order_id', 'product_id', 'product_name', 'product_code', 'product_image', 'quantity', 'unit_price', 'discount_percent', 'discount_amount', 'final_price', 'product_options_json', 'created_at', 'updated_at'];
        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                $r->id,
                $r->order_id,
                $r->product_id,
                $r->product_name,
                $r->product_code,
                $r->product_image,
                $r->quantity,
                $r->unit_price,
                $r->discount_percent,
                $r->discount_amount,
                $r->final_price,
                json_encode($r->product_options ?? [], JSON_UNESCAPED_UNICODE),
                (string) $r->created_at,
                (string) $r->updated_at,
            ];
        }

        return $this->csvFromRows($headers, $data);
    }

    private function exportArticles(): ?string
    {
        $rows = Article::query()->withTrashed()->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return null;
        }
        $headers = ['id', 'title', 'slug', 'status', 'published_at', 'category_id', 'author_id', 'view_count', 'is_featured', 'noindex', 'deleted_at', 'created_at', 'updated_at'];
        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                $r->id,
                $r->title,
                $r->slug,
                $r->status,
                (string) $r->published_at,
                $r->category_id,
                $r->author_id,
                $r->view_count,
                $r->is_featured ? '1' : '0',
                $r->noindex ? '1' : '0',
                (string) $r->deleted_at,
                (string) $r->created_at,
                (string) $r->updated_at,
            ];
        }

        return $this->csvFromRows($headers, $data);
    }

    private function exportGiftCodes(): ?string
    {
        $rows = GiftCode::query()->withTrashed()->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return null;
        }
        $headers = ['id', 'code', 'title', 'value_type', 'amount', 'percent', 'max_amount', 'min_order_amount', 'usage_limit', 'used_count', 'per_user_limit', 'starts_at', 'expires_at', 'applies_to', 'category_ids_json', 'product_ids_json', 'user_ids_json', 'status', 'deleted_at', 'created_at', 'updated_at'];
        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                $r->id,
                $r->code,
                $r->title,
                $r->value_type,
                $r->amount,
                $r->percent,
                $r->max_amount,
                $r->min_order_amount,
                $r->usage_limit,
                $r->used_count,
                $r->per_user_limit,
                (string) $r->starts_at,
                (string) $r->expires_at,
                $r->applies_to,
                json_encode($r->category_ids ?? [], JSON_UNESCAPED_UNICODE),
                json_encode($r->product_ids ?? [], JSON_UNESCAPED_UNICODE),
                json_encode($r->user_ids ?? [], JSON_UNESCAPED_UNICODE),
                $r->status,
                (string) $r->deleted_at,
                (string) $r->created_at,
                (string) $r->updated_at,
            ];
        }

        return $this->csvFromRows($headers, $data);
    }

    private function exportDiscountCodes(): ?string
    {
        $rows = DiscountCode::query()->withTrashed()->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return null;
        }
        $headers = ['id', 'code', 'title', 'discount_type', 'discount_amount', 'discount_percent', 'max_discount_amount', 'min_order_amount', 'usage_limit', 'used_count', 'per_user_limit', 'starts_at', 'expires_at', 'applies_to', 'category_ids_json', 'product_ids_json', 'status', 'deleted_at', 'created_at', 'updated_at'];
        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                $r->id,
                $r->code,
                $r->title,
                $r->discount_type,
                $r->discount_amount,
                $r->discount_percent,
                $r->max_discount_amount,
                $r->min_order_amount,
                $r->usage_limit,
                $r->used_count,
                $r->per_user_limit,
                (string) $r->starts_at,
                (string) $r->expires_at,
                $r->applies_to,
                json_encode($r->category_ids ?? [], JSON_UNESCAPED_UNICODE),
                json_encode($r->product_ids ?? [], JSON_UNESCAPED_UNICODE),
                $r->status,
                (string) $r->deleted_at,
                (string) $r->created_at,
                (string) $r->updated_at,
            ];
        }

        return $this->csvFromRows($headers, $data);
    }

    private function exportUsers(): ?string
    {
        $rows = User::query()->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return null;
        }
        $hasActive = Schema::hasColumn('users', 'is_active');
        $headers = $hasActive
            ? ['id', 'name', 'email', 'mobile', 'is_active', 'email_verified_at', 'created_at', 'updated_at']
            : ['id', 'name', 'email', 'mobile', 'email_verified_at', 'created_at', 'updated_at'];
        $data = [];
        foreach ($rows as $u) {
            $row = [
                $u->id,
                $u->name,
                $u->email,
                $u->mobile,
            ];
            if ($hasActive) {
                $row[] = $u->is_active ? '1' : '0';
            }
            $row[] = (string) $u->email_verified_at;
            $row[] = (string) $u->created_at;
            $row[] = (string) $u->updated_at;
            $data[] = $row;
        }

        return $this->csvFromRows($headers, $data);
    }

    private function exportShippingConfigs(): ?string
    {
        $rows = ShippingConfig::query()->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return null;
        }
        $headers = ['id', 'name', 'base_shipping_cost', 'base_insurance_cost', 'base_packaging_cost', 'package_weight_limit', 'extra_weight_cost', 'is_active', 'sort_order', 'description', 'additional_settings_json', 'created_at', 'updated_at'];
        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                $r->id,
                $r->name,
                $r->base_shipping_cost,
                $r->base_insurance_cost,
                $r->base_packaging_cost,
                $r->package_weight_limit,
                $r->extra_weight_cost,
                $r->is_active ? '1' : '0',
                $r->sort_order,
                $r->description,
                json_encode($r->additional_settings ?? [], JSON_UNESCAPED_UNICODE),
                (string) $r->created_at,
                (string) $r->updated_at,
            ];
        }

        return $this->csvFromRows($headers, $data);
    }

    private function exportContactSettings(): ?string
    {
        if (! Schema::hasTable('contact_settings')) {
            return null;
        }
        $row = ContactSetting::query()->first();
        if (! $row) {
            return null;
        }
        $headers = ['id', 'phones_json', 'emails_json', 'addresses_json', 'socials_json', 'working_hours', 'fax', 'support_title', 'footer_note', 'created_at', 'updated_at'];
        $data = [[
            $row->id,
            json_encode($row->phones ?? [], JSON_UNESCAPED_UNICODE),
            json_encode($row->emails ?? [], JSON_UNESCAPED_UNICODE),
            json_encode($row->addresses ?? [], JSON_UNESCAPED_UNICODE),
            json_encode($row->socials ?? [], JSON_UNESCAPED_UNICODE),
            $row->working_hours,
            $row->fax,
            $row->support_title,
            $row->footer_note,
            (string) $row->created_at,
            (string) $row->updated_at,
        ]];

        return $this->csvFromRows($headers, $data);
    }
}
