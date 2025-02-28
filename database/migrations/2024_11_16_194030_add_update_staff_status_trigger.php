<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateStaffStatusTrigger extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            DELIMITER //
            CREATE TRIGGER update_staff_status
            AFTER UPDATE ON users
            FOR EACH ROW
            BEGIN
                IF OLD.status <> NEW.status THEN
                    UPDATE staffmembers SET status = NEW.status WHERE user_id = NEW.id;
                END IF;
            END;
            //
            DELIMITER ;
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_staff_status;');
    }
}
