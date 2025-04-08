<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::unprepared('
            DROP TRIGGER IF EXISTS update_staff_status;
            CREATE TRIGGER update_staff_status
            AFTER UPDATE ON users
            FOR EACH ROW
            BEGIN
                IF OLD.status <> NEW.status THEN
                    UPDATE staffMembers SET status = NEW.status WHERE user_id = NEW.id;
                END IF;
            END;
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_staff_status;');
    }
};
