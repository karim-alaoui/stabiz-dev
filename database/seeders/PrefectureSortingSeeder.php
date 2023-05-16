<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Prefecture;
use Illuminate\Database\Seeder;

class PrefectureSortingSeeder extends Seeder
{
    /**
     * Prefecture sorting when an area selected
     *
     * @return void
     */
    public function sortingByArea()
    {
        /**
         * This was copy pasted from an excel file
         * The first value is prefecture id from prefectures table
         * second is prefecture name (name_ja in prefectures table)
         * third is area name of the prefecture
         * fourth is the order when that area is selected.
         * So, when an area is selected, the prefectures under that area would come
         * in the asc of the sort order
         */
        $prefectures = <<<PREFECTURES
1	北海道	北海道	1
2	青森県	東北	1
3	岩手県	東北	2
4	宮城県	東北	3
5	秋田県	東北	4
6	山形県	東北	5
7	福島県	東北	6
8	茨城県	関東	1
9	栃木県	関東	2
10	群馬県	関東	3
11	埼玉県	関東	4
12	千葉県	関東	5
14	神奈川県	関東	7
15	新潟県	北陸甲信越	1
16	富山県	北陸甲信越	2
17	石川県	北陸甲信越	3
18	福井県	北陸甲信越	4
19	山梨県	北陸甲信越	5
20	長野県	北陸甲信越	6
21	岐阜県	東海	1
22	静岡県	東海	2
23	愛知県	東海	3
24	三重県	東海	4
25	滋賀県	関西	1
26	京都府	関西	2
27	大阪府	関西	3
28	兵庫県	関西	4
29	奈良県	関西	5
30	和歌山県	関西	6
31	鳥取県	中国・四国	1
32	島根県	中国・四国	2
33	岡山県	中国・四国	3
34	広島県	中国・四国	4
35	山口県	中国・四国	5
36	徳島県	中国・四国	6
37	香川県	中国・四国	7
38	愛媛県	中国・四国	8
39	高知県	中国・四国	9
40	福岡県	九州	1
41	佐賀県	九州	2
42	長崎県	九州	3
43	熊本県	九州	4
44	大分県	九州	5
45	宮崎県	九州	6
46	鹿児島県	九州	7
48	海外		1
50	その他		1
51	東京都	関東	6
52	沖縄県	沖縄	1
PREFECTURES;
        $prefecturesSplit = preg_split('/\n/', $prefectures);
        foreach ($prefecturesSplit as $item) {
            $split = preg_split('/\t/', $item);
            $prefectureId = trim($split[0]);
            $prefectureName = trim($split[1]);
            $area = trim($split[2]);
            $sort = trim($split[3]);

            $area = Area::query()
                ->where('name_ja', 'ilike', $area)
                ->first();
            Prefecture::query()
                ->where('id', $prefectureId)
                ->where('name_ja', 'ilike', $prefectureName)
                ->whereRaw($area ? "area_id=$area->id" : 'area_id is null')
                ->update(['in_area_sort_order' => $sort]);
        }
    }

    /**
     * Sort by this order when all the prefectures are listed
     * The values are copy pasted from excel sheet.
     * The first value means prefecture id
     * 2nd value is prefecture name
     * 3rd is sort_order when all the prefectures are listed
     */
    public function sorting()
    {
        $prefectures = <<<PREFECTURES
1	北海道	1
2	青森県	2
3	岩手県	3
4	宮城県	4
5	秋田県	5
6	山形県	6
7	福島県	7
8	茨城県	8
9	栃木県	9
10	群馬県	10
11	埼玉県	11
12	千葉県	12
14	神奈川県	14
15	新潟県	15
16	富山県	16
17	石川県	17
18	福井県	18
19	山梨県	19
20	長野県	20
21	岐阜県	21
22	静岡県	22
23	愛知県	23
24	三重県	24
25	滋賀県	25
26	京都府	26
27	大阪府	27
28	兵庫県	28
29	奈良県	29
30	和歌山県	30
31	鳥取県	31
32	島根県	32
33	岡山県	33
34	広島県	34
35	山口県	35
36	徳島県	36
37	香川県	37
38	愛媛県	38
39	高知県	39
40	福岡県	40
41	佐賀県	41
42	長崎県	42
43	熊本県	43
44	大分県	44
45	宮崎県	45
46	鹿児島県	46
48	海外	47
50	その他	48
51	東京都	13
52	沖縄県	49
PREFECTURES;
        $prefecturesSplit = preg_split('/\n/', $prefectures);
        foreach ($prefecturesSplit as $item) {
            $item = preg_split('/\t/', $item);
            $prefectureID = $item[0];
            $prefectureName = $item[1];
            $sortOrder = $item[2];

            Prefecture::query()
                ->where('name_ja', 'ilike', $prefectureName)
                ->where('id', $prefectureID)
                ->update(['sort_order' => $sortOrder]);
        }
    }

    public function run()
    {
        $this->sortingByArea();
        $this->sorting();
    }
}
