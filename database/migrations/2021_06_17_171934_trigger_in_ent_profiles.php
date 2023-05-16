<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TriggerInEntProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();
        DB::unprepared(<<<QUERY
CREATE or replace FUNCTION check_if_user_is_entr() RETURNS trigger AS
\$check_if_user_is_entr\$
BEGIN
    IF not exists(select id from users u where u.id = NEW.user_id and u.type = 'entrepreneur') THEN
        RAISE EXCEPTION 'The user type is not entrepreneur';
    END IF;
    RETURN NEW;
END;
\$check_if_user_is_entr\$ LANGUAGE plpgsql;

drop trigger if exists check_entr_type on entrepreneur_profiles;

create trigger check_entr_type
    before insert or update
    on entrepreneur_profiles
    for each row
execute function check_if_user_is_entr();
QUERY
        );
        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ent_profiles', function (Blueprint $table) {
            //
        });
    }
}
