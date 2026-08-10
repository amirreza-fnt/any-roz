<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (! Schema::hasColumn('admins', 'is_colleague_support')) {
                $table->boolean('is_colleague_support')->default(false)->after('is_colleague');
            }
        });

        // Ensure existing colleagues also get the support-chat permissions.
        $extra = ['colleague.chat.view', 'colleague.chat.send'];
        foreach (DB::table('admins')->where('is_colleague', 1)->cursor() as $row) {
            $perms = json_decode((string) $row->permissions, true);
            $perms = is_array($perms) ? $perms : [];
            $perms = array_values(array_unique(array_merge($perms, $extra)));
            DB::table('admins')->where('id', $row->id)->update(['permissions' => json_encode($perms)]);
        }
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'is_colleague_support')) {
                $table->dropColumn('is_colleague_support');
            }
        });
    }
};