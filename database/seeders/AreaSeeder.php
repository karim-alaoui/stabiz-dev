<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        DB::unprepared("
INSERT INTO areas (name_ja, deleted_at) VALUES ('東京', null) on conflict do nothing;
INSERT INTO areas (name_ja, deleted_at) VALUES ('関東', null) on conflict do nothing;
INSERT INTO areas (name_ja, deleted_at) VALUES ('関西', null) on conflict do nothing;
INSERT INTO areas (name_ja, deleted_at) VALUES ('中部', null) on conflict do nothing;
INSERT INTO areas (name_ja, deleted_at) VALUES ('北海道', '2020-09-10 22:47:43') on conflict do nothing;
INSERT INTO areas (name_ja, deleted_at) VALUES ('九州', '2020-09-10 22:47:43') on conflict do nothing;
INSERT INTO areas (name_ja, deleted_at) VALUES ('東北', null) on conflict do nothing;
INSERT INTO areas (name_ja, deleted_at) VALUES ('中国・四国', '2020-09-10 22:47:43') on conflict do nothing;
INSERT INTO areas (name_ja, deleted_at) VALUES ('北陸甲信越', '2020-09-10 22:47:43') on conflict do nothing;
INSERT INTO areas (name_ja, deleted_at) VALUES ('東海', '2020-09-10 22:47:43') on conflict do nothing;
");

        $area = fn($name) => Area::query()->where('name_ja', $name)->withTrashed()->first()?->id ?? 'null';

        DB::unprepared("
        INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('北海道', " . $area('北海道') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('青森県', " . $area('東北') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('岩手県', " . $area('東北') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('宮城県', " . $area('東北') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('秋田県', " . $area('東北') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('山形県', " . $area('東北') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('福島県', " . $area('東北') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('茨城県', " . $area('関東') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('栃木県', " . $area('関東') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('群馬県', " . $area('関東') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('埼玉県', " . $area('関東') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('千葉県', " . $area('関東') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('東京都', " . $area('東京') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('神奈川県', " . $area('関東') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('新潟県', " . $area('北陸甲信越') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('富山県', " . $area('北陸甲信越') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('石川県', " . $area('北陸甲信越') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('福井県', " . $area('北陸甲信越') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('山梨県', " . $area('北陸甲信越') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('長野県', " . $area('北陸甲信越') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('岐阜県', " . $area('東海') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('静岡県', " . $area('東海') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('愛知県', " . $area('東海') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('三重県', " . $area('東海') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('滋賀県', " . $area('関西') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('京都府', " . $area('関西') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('大阪府', " . $area('関西') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('兵庫県', " . $area('関西') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('奈良県', " . $area('関西') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('和歌山県', " . $area('関西') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('鳥取県', " . $area('中国・四国') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('島根県', " . $area('中国・四国') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('岡山県', " . $area('中国・四国') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('広島県', " . $area('中国・四国') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('山口県', " . $area('中国・四国') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('徳島県', " . $area('中国・四国') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('香川県', " . $area('中国・四国') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('愛媛県', " . $area('中国・四国') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('高知県', " . $area('中国・四国') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('福岡県', " . $area('九州') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('佐賀県', " . $area('九州') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('長崎県', " . $area('九州') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('熊本県', " . $area('九州') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('大分県', " . $area('九州') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('宮崎県', " . $area('九州') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('鹿児島県', " . $area('九州') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('沖縄県', " . $area('九州') . ", null) on conflict do nothing;
INSERT INTO prefectures (name_ja, area_id, deleted_at) VALUES ('海外', null, null) on conflict do nothing;
        ");

        DB::commit();
    }
}
