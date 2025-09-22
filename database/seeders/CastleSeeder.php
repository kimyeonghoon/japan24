<?php

namespace Database\Seeders;

use App\Models\Castle;
use Illuminate\Database\Seeder;

class CastleSeeder extends Seeder
{
    public function run(): void
    {
        $castles = [
            [
                'name' => '江戸城',
                'name_korean' => '에도성',
                'prefecture' => '東京都',
                'latitude' => 35.685175,
                'longitude' => 139.753348,
                'description' => '일본의 수도 도쿄에 위치한 대표적인 성으로, 현재 황궁이 자리하고 있다.',
                'historical_info' => '1457년 오타 도칸에 의해 축성된 성으로, 에도 막부의 중심지였다.',
                'official_stamp_location' => '황궁 동쪽 정원 휴게소',
                'visiting_hours' => '09:00-17:00',
                'entrance_fee' => 0
            ],
            [
                'name' => '大阪城',
                'name_korean' => '오사카성',
                'prefecture' => '大阪府',
                'latitude' => 34.687315,
                'longitude' => 135.526201,
                'description' => '도요토미 히데요시가 축성한 일본의 대표적인 성이다.',
                'historical_info' => '1583년 도요토미 히데요시에 의해 축성되었으며, 안토-모모야마 시대의 상징이다.',
                'official_stamp_location' => '오사카성 천수각 1층',
                'visiting_hours' => '09:00-17:00',
                'entrance_fee' => 600
            ],
            [
                'name' => '名古屋城',
                'name_korean' => '나고야성',
                'prefecture' => '愛知県',
                'latitude' => 35.185043,
                'longitude' => 136.899189,
                'description' => '도쿠가와 이에야스가 축성한 성으로, 금빛 샤치호코로 유명하다.',
                'historical_info' => '1612년 도쿠가와 이에야스의 명령으로 축성되었다.',
                'official_stamp_location' => '나고야성 정문 안내소',
                'visiting_hours' => '09:00-16:30',
                'entrance_fee' => 500
            ],
            [
                'name' => '熊本城',
                'name_korean' => '구마모토성',
                'prefecture' => '熊本県',
                'latitude' => 32.806340,
                'longitude' => 130.705650,
                'description' => '가토 기요마사가 축성한 난공불락의 성으로 불린다.',
                'historical_info' => '1607년 가토 기요마사에 의해 완성되었다.',
                'official_stamp_location' => '구마모토성 입구 안내소',
                'visiting_hours' => '09:00-17:00',
                'entrance_fee' => 800
            ],
            [
                'name' => '姫路城',
                'name_korean' => '히메지성',
                'prefecture' => '兵庫県',
                'latitude' => 34.839428,
                'longitude' => 134.693493,
                'description' => '세계문화유산으로 지정된 일본의 대표적인 목조 성이다.',
                'historical_info' => '1333년 아카마츠 노리무라가 축성하기 시작하여 여러 차례 개축되었다.',
                'official_stamp_location' => '히메지성 관리사무소',
                'visiting_hours' => '09:00-17:00',
                'entrance_fee' => 1000
            ],
            [
                'name' => '松本城',
                'name_korean' => '마츠모토성',
                'prefecture' => '長野県',
                'latitude' => 36.238611,
                'longitude' => 137.968889,
                'description' => '일본에서 가장 오래된 목조 천수각이 있는 성이다.',
                'historical_info' => '1504년 시마다타 시게요리에 의해 축성되기 시작했다.',
                'official_stamp_location' => '마츠모토성 관리사무소',
                'visiting_hours' => '08:30-17:00',
                'entrance_fee' => 700
            ],
            [
                'name' => '犬山城',
                'name_korean' => '이누야마성',
                'prefecture' => '愛知県',
                'latitude' => 35.388889,
                'longitude' => 136.938889,
                'description' => '기소강변에 위치한 아름다운 경치의 성이다.',
                'historical_info' => '1537년 오다 노부야스에 의해 축성되었다.',
                'official_stamp_location' => '이누야마성 입구',
                'visiting_hours' => '09:00-17:00',
                'entrance_fee' => 550
            ],
            [
                'name' => '彦根城',
                'name_korean' => '히코네성',
                'prefecture' => '滋賀県',
                'latitude' => 35.276111,
                'longitude' => 136.251944,
                'description' => '비와호가 보이는 아름다운 성으로 히코냥으로도 유명하다.',
                'historical_info' => '1622년 이이 나오마사의 아들 나오카츠에 의해 완성되었다.',
                'official_stamp_location' => '히코네성 박물관',
                'visiting_hours' => '08:30-17:00',
                'entrance_fee' => 800
            ],
            [
                'name' => '松江城',
                'name_korean' => '마츠에성',
                'prefecture' => '島根県',
                'latitude' => 35.474722,
                'longitude' => 133.050556,
                'description' => '신지코 호수 근처에 위치한 흑색의 아름다운 성이다.',
                'historical_info' => '1611년 호리오 요시하루에 의해 완성되었다.',
                'official_stamp_location' => '마츠에성 천수각 입구',
                'visiting_hours' => '08:30-18:30',
                'entrance_fee' => 680
            ],
            [
                'name' => '備中松山城',
                'name_korean' => '비추마츠야마성',
                'prefecture' => '岡山県',
                'latitude' => 34.810278,
                'longitude' => 133.616667,
                'description' => '일본에서 가장 높은 곳에 위치한 현존 천수각을 가진 성이다.',
                'historical_info' => '1240년 아키바 시게토시에 의해 축성되기 시작했다.',
                'official_stamp_location' => '후이고토게 휴게소',
                'visiting_hours' => '09:00-17:30',
                'entrance_fee' => 500
            ],
            [
                'name' => '丸岡城',
                'name_korean' => '마루오카성',
                'prefecture' => '福井県',
                'latitude' => 36.152222,
                'longitude' => 136.272222,
                'description' => '일본에서 가장 오래된 목조 천수각 중 하나이다.',
                'historical_info' => '1576년 시바타 카츠이에의 조카 카츠토요에 의해 축성되었다.',
                'official_stamp_location' => '마루오카성 입구 매표소',
                'visiting_hours' => '08:30-17:00',
                'entrance_fee' => 450
            ],
            [
                'name' => '宇和島城',
                'name_korean' => '우와지마성',
                'prefecture' => '愛媛県',
                'latitude' => 33.226944,
                'longitude' => 132.560556,
                'description' => '바다를 바라보는 아름다운 현존 천수각이 있는 성이다.',
                'historical_info' => '1595년 도도 다카토라에 의해 축성되었다.',
                'official_stamp_location' => '우와지마성 천수각 입구',
                'visiting_hours' => '06:00-18:30',
                'entrance_fee' => 200
            ],
            [
                'name' => '高知城',
                'name_korean' => '고치성',
                'prefecture' => '高知県',
                'latitude' => 33.559722,
                'longitude' => 133.531111,
                'description' => '야마우치 가문의 성으로 혼마루 전체가 현존하는 유일한 성이다.',
                'historical_info' => '1603년 야마우치 카즈토요에 의해 축성되었다.',
                'official_stamp_location' => '고치성 혼마루 고텐',
                'visiting_hours' => '09:00-17:00',
                'entrance_fee' => 420
            ],
            [
                'name' => '丸亀城',
                'name_korean' => '마루가메성',
                'prefecture' => '香川県',
                'latitude' => 34.289722,
                'longitude' => 133.797222,
                'description' => '일본에서 가장 높은 석벽을 자랑하는 성이다.',
                'historical_info' => '1597년 이코마 치카마사에 의해 축성되었다.',
                'official_stamp_location' => '마루가메성 천수각',
                'visiting_hours' => '09:00-16:30',
                'entrance_fee' => 200
            ],
            [
                'name' => '松山城',
                'name_korean' => '마츠야마성',
                'prefecture' => '愛媛県',
                'latitude' => 33.845556,
                'longitude' => 132.765833,
                'description' => '가츠야마산 정상에 위치한 현존 천수각이 있는 성이다.',
                'historical_info' => '1602년 가토 요시아키에 의해 축성되기 시작했다.',
                'official_stamp_location' => '마츠야마성 천수각 입구',
                'visiting_hours' => '09:00-17:00',
                'entrance_fee' => 520
            ],
            [
                'name' => '首里城',
                'name_korean' => '슈리성',
                'prefecture' => '沖縄県',
                'latitude' => 26.217222,
                'longitude' => 127.719444,
                'description' => '류큐 왕국의 왕궁이었던 독특한 건축양식의 성이다.',
                'historical_info' => '14세기경 축성되어 류큐 왕국의 정치, 외교, 문화의 중심지였다.',
                'official_stamp_location' => '슈리성 정전',
                'visiting_hours' => '09:00-18:00',
                'entrance_fee' => 400
            ],
            [
                'name' => '弘前城',
                'name_korean' => '히로사키성',
                'prefecture' => '青森県',
                'latitude' => 40.606944,
                'longitude' => 140.464167,
                'description' => '벚꽃으로 유명한 아름다운 성이다.',
                'historical_info' => '1611년 츠가루 노부히라에 의해 완성되었다.',
                'official_stamp_location' => '히로사키성 천수각',
                'visiting_hours' => '09:00-17:00',
                'entrance_fee' => 320
            ],
            [
                'name' => '会津若松城',
                'name_korean' => '아이즈와카마츠성',
                'prefecture' => '福島県',
                'latitude' => 37.487778,
                'longitude' => 139.925556,
                'description' => '백호대의 역사로 유명한 성이다.',
                'historical_info' => '1384년 아시나 나오모리에 의해 축성되기 시작했다.',
                'official_stamp_location' => '츠루가성 천수각 매표소',
                'visiting_hours' => '08:30-17:00',
                'entrance_fee' => 410
            ],
            [
                'name' => '二条城',
                'name_korean' => '니조성',
                'prefecture' => '京都府',
                'latitude' => 35.014167,
                'longitude' => 135.748333,
                'description' => '에도 막부의 교토 거점이었던 성이다.',
                'historical_info' => '1603년 도쿠가와 이에야스에 의해 축성되었다.',
                'official_stamp_location' => '니조성 휴게소',
                'visiting_hours' => '08:45-17:00',
                'entrance_fee' => 1300
            ],
            [
                'name' => '安土城',
                'name_korean' => '안즈치성',
                'prefecture' => '滋賀県',
                'latitude' => 35.143611,
                'longitude' => 136.137222,
                'description' => '오다 노부나가가 축성한 혁신적인 성이다.',
                'historical_info' => '1576년 오다 노부나가에 의해 축성되었으나 현재는 터만 남아있다.',
                'official_stamp_location' => '안즈치성 곽 고고박물관',
                'visiting_hours' => '09:00-17:00',
                'entrance_fee' => 200
            ],
            [
                'name' => '小谷城',
                'name_korean' => '오다니성',
                'prefecture' => '滋賀県',
                'latitude' => 35.495556,
                'longitude' => 136.329722,
                'description' => '아사이 가문의 본거지였던 산성이다.',
                'historical_info' => '1516년 아사이 스케마사에 의해 축성되었다.',
                'official_stamp_location' => '오다니성 전국역사자료관',
                'visiting_hours' => '09:00-17:00',
                'entrance_fee' => 300
            ],
            [
                'name' => '竹田城',
                'name_korean' => '다케다성',
                'prefecture' => '兵庫県',
                'latitude' => 35.304167,
                'longitude' => 134.836111,
                'description' => '구름바다 위에 떠있는 천공의 성으로 불린다.',
                'historical_info' => '1441년 야마나 소젠에 의해 축성되었다.',
                'official_stamp_location' => '와다야마성 향토자료관',
                'visiting_hours' => '08:00-18:00',
                'entrance_fee' => 500
            ],
            [
                'name' => '岩国城',
                'name_korean' => '이와쿠니성',
                'prefecture' => '山口県',
                'latitude' => 34.176944,
                'longitude' => 132.190556,
                'description' => '킨타이교 다리와 함께 아름다운 경관을 이루는 성이다.',
                'historical_info' => '1608년 키카와 히로이에에 의해 축성되었다.',
                'official_stamp_location' => '이와쿠니성 로프웨이 승강장',
                'visiting_hours' => '09:00-16:45',
                'entrance_fee' => 270
            ],
            [
                'name' => '月山富田城',
                'name_korean' => '갓산토다성',
                'prefecture' => '島根県',
                'latitude' => 35.440833,
                'longitude' => 133.243333,
                'description' => '아마고 가문의 난공불락 산성이었다.',
                'historical_info' => '1370년경 야마나 시게우지에 의해 축성되었다.',
                'official_stamp_location' => '야스기시 역사자료관',
                'visiting_hours' => '09:00-17:00',
                'entrance_fee' => 0
            ]
        ];

        foreach ($castles as $castle) {
            Castle::create($castle);
        }
    }
}