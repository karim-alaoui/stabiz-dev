<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TriggerInFounderProfiles extends Migration
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
CREATE or replace FUNCTION check_if_user_is_fdr() RETURNS trigger AS
\$check_if_user_is_fdr\$
BEGIN
    IF not exists(select id from users u where u.id = NEW.user_id and u.type = 'founder') THEN
        RAISE EXCEPTION 'The user type is not founder';
    END IF;
    RETURN NEW;
END;
\$check_if_user_is_fdr\$ LANGUAGE plpgsql;

drop trigger if exists check_fdr_type on founder_profiles;

create trigger check_fdr_type
    before insert or update
    on founder_profiles
    for each row
execute function check_if_user_is_fdr();
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
        Schema::table('founder_profiles', function (Blueprint $table) {
            //
        });
    }
}
