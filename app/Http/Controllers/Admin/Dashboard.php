<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\DiscountCode;
use App\Models\GiftCode;
use App\Models\MarketingSale;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class Dashboard extends Controller
{
    /**
     * خلاصهٔ آماری پنل مدیریت.
     */
    public function __invoke(Request $request)
    {
        $stats = [
            'orders' => Schema::hasTable('orders') ? Order::query()->count() : 0,
            'products' => Schema::hasTable('products') ? Product::query()->count() : 0,
            'categories' => Schema::hasTable('categories') ? Category::query()->count() : 0,
            'users' => Schema::hasTable('users') ? User::query()->count() : 0,
            'articles' => Schema::hasTable('articles') ? Article::query()->count() : 0,
            'marketing_pending' => Schema::hasTable('marketing_sales')
                ? MarketingSale::query()->where('status', MarketingSale::STATUS_PENDING)->count()
                : 0,
            'gift_codes' => Schema::hasTable('gift_codes') ? GiftCode::query()->count() : 0,
            'discount_codes' => Schema::hasTable('discount_codes') ? DiscountCode::query()->count() : 0,
        ];

        return view('backend.index', compact('stats'));
    }
}
