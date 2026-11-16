<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The singular "section_staff" table was created by a duplicated
     * migration and is not used anywhere; the model uses "section_staffs".
     */
    public function up(): void
    {
        Schema::dropIfExists('section_staff');
    }

    public function down(): void
    {
        // Not recreated: the singular table was accidental.
    }
};
