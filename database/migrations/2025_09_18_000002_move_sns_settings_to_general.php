<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('system_settings')
            ->where('group', 'sns')
            ->update(['group' => 'general']);
    }

    public function down(): void
    {
        // Can't reliably revert which ones were moved; do nothing.
    }
};
