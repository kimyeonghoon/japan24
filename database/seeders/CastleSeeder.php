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
                'name' => '五稜郭',
                'name_korean' => '고료카쿠',
                'prefecture' => '北海道(홋카이도)',
                'latitude' => 41.796763,
                'longitude' => 140.757034,
                'description' => '에도 시대 말기에 건설된 벚꽃 나무가 가득한 공원으로 해자를 갖춘 별 모양 요새입니다.',
                'official_stamp_location' => '하코다테 부교소 이타쿠라 휴게소, 고료카쿠 타워 매표소',
                'googlemap' => 'https://maps.app.goo.gl/VT2dPYnuYSc5ito29',
                'access_method' => 'JR 하코다테역에서 버스로 고료카쿠공원입구까지, 도보 10분',
                'official_website' => 'https://www.goryokaku-tower.co.jp/en/'
            ],
            [
                'name' => '弘前城',
                'name_korean' => '히로사키성',
                'prefecture' => '青森県(아오모리현)',
                'latitude' => 40.6079291,
                'longitude' => 140.46366052462452,
                'description' => '2,600여 그루의 벚꽃, 푸른 정원, 해자로 둘러싸인 유서 깊은 우아한 성과 탑입니다.',
                'official_stamp_location' => '히로사키성 안내센터',
                'googlemap' => 'https://maps.app.goo.gl/NwBwYbDnC4w4LErx7',
                'access_method' => 'JR 히로사키역에서 버스로 시야쿠쇼마에까지, 도보 5분',
                'official_website' => 'https://www.hirosakipark.jp/en/'
            ],
            [
                'name' => '会津若松城',
                'name_korean' => '아이즈와카마츠성',
                'prefecture' => '福島県(후쿠시마현)',
                'latitude' => 37.48773525,
                'longitude' => 139.92976605913537,
                'description' => '소실된 14세기 성의 1965년경 콘크리트 복제 건물로 멋진 전경과 박물관을 갖추고 있습니다.',
                'official_stamp_location' => '천수각 내부 매장',
                'googlemap' => 'https://maps.app.goo.gl/iAbkmd8e2mXxF4yA8',
                'access_method' => 'JR 아이즈와카마츠역에서 버스로 츠루가조키타구치까지, 도보 3분',
                'official_website' => 'http://www.tsurugajo.com/language/eng/'
            ],
            [
                'name' => '江戸城',
                'name_korean' => '에도성',
                'prefecture' => '東京都(도쿄도)',
                'latitude' => 35.6823383,
                'longitude' => 139.75217857343807,
                'description' => '1457년에 지은 이 성은 현재 고쿄의 일부이며, 해자와 성벽, 그 외 유적이 있습니다.',
                'official_stamp_location' => '난코, 와다쿠라, 키타노마루 휴게소',
                'googlemap' => 'https://maps.app.goo.gl/hBoY45k5tJBQJDmH7',
                'access_method' => 'JR 도쿄역 또는 오테마치 지하철역에서 도보 5분',
                'official_website' => 'https://visit-chiyoda.tokyo.t.ie.hp.transer.com/app/en/spot/detail/405'
            ],
            [
                'name' => '小田原城',
                'name_korean' => '오다와라성',
                'prefecture' => '神奈川県(가나가와현)',
                'latitude' => 35.25105465,
                'longitude' => 139.15342492702024,
                'description' => '다양한 전시품과 유물을 만나볼 수 있는 작고 견고한 성으로 탑에서 멋진 풍경을 감상할 수도 있습니다.',
                'official_stamp_location' => '천수각 1층',
                'googlemap' => 'https://maps.app.goo.gl/SeoZ2Ynw7jJS8yh99',
                'access_method' => 'JR 오다와라역에서 도보 10분',
                'official_website' => 'http://www.odawara-kankou.com.e.jk.hp.transer.com/spot/spot_area/jyoushi.html'
            ],
            [
                'name' => '上田城',
                'name_korean' => '우에다성',
                'prefecture' => '長野県(나가노현)',
                'latitude' => 36.404044,
                'longitude' => 138.24374673215738,
                'description' => '사나다 가문의 본거지로 유명한 성으로 아름다운 석벽과 벚꽃으로 사랑받는 명소입니다.',
                'official_stamp_location' => '우에다시 박물관, 우에다시 관광안내소',
                'googlemap' => 'https://maps.app.goo.gl/kiV7oaDFtg2ToKmg9',
                'access_method' => 'JR 우에다역에서 도보 10분',
                'official_website' => 'https://go.ueda-kanko.or.jp/special/castle_town/'
            ],
            [
                'name' => '松本城',
                'name_korean' => '마츠모토성',
                'prefecture' => '長野県(나가노현)',
                'latitude' => 36.23863525,
                'longitude' => 137.9688708786901,
                'description' => '검은 벽으로 잘 알려진 이 웅장한 16세기 성에는 오래된 무기가 전시되어 있습니다.',
                'official_stamp_location' => '마츠모토성 관리사무소',
                'googlemap' => 'https://maps.app.goo.gl/taqk2upiodRTMM547',
                'access_method' => 'JR 마츠모토역에서 도보 15분',
                'official_website' => 'https://www.matsumoto-castle.jp/lang/'
            ],
            [
                'name' => '金沢城',
                'name_korean' => '가나자와성',
                'prefecture' => '石川県(이시카와현)',
                'latitude' => 36.565600450000005,
                'longitude' => 136.6595753027176,
                'description' => '재건된 16세기 성으로 광대한 규모를 자랑하는 주변 정원이 유명하며, 투어를 이용할 수 있습니다.',
                'official_stamp_location' => '니노마루 안내센터, 이시카와몬 입구 안내센터',
                'googlemap' => 'hhttps://maps.app.goo.gl/wdViG7qKZa24qLMZ9',
                'access_method' => 'JR 가나자와역에서 버스로 겐로쿠엔시타까지, 도보 5분',
                'official_website' => 'http://www.pref.ishikawa.jp/siro-niwa/english/top.html'
            ],
            [
                'name' => '丸岡城',
                'name_korean' => '마루오카성',
                'prefecture' => '福井県(후쿠이현)',
                'latitude' => 36.15235865,
                'longitude' => 136.27212515000002,
                'description' => '일본에서 가장 오래된 현존 천수각 중 하나로 소박하지만 역사적 가치가 높은 성입니다.',
                'official_stamp_location' => '카스미가성 공원 관리사무소 (매표소)',
                'googlemap' => 'https://maps.app.goo.gl/UnBLDbHaeNybJYNX7',
                'access_method' => 'JR 후쿠이역에서 버스로 마루오카조까지, 도보 5분',
                'official_website' => 'https://enjoy.pref.fukui.lg.jp/en/spot/spot-20/'
            ],
            [
                'name' => '犬山城',
                'name_korean' => '이누야마성',
                'prefecture' => '愛知県(아이치현)',
                'latitude' => 35.3883304,
                'longitude' => 136.9392776,
                'description' => '기소강을 내려다보는 언덕 위에 자리한 국보 지정 목조 천수각으로 경치가 아름답습니다.',
                'official_stamp_location' => '성문 2층, 이누야마성 관리사무소',
                'googlemap' => 'https://maps.app.goo.gl/imEVJHfYW8fqPESCA',
                'access_method' => '메이테츠 이누야마유엔역에서 도보 15분',
                'official_website' => 'https://inuyamajo.jp/'
            ],
            [
                'name' => '名古屋城',
                'name_korean' => '나고야성',
                'prefecture' => '愛知県(아이치현)',
                'latitude' => 35.1853191,
                'longitude' => 136.899177,
                'description' => '1612년에 완공된 이 성채는 복원을 거쳤으며 에도 시대의 유물과 전시물을 선보입니다.',
                'official_stamp_location' => '정문 및 동문 개찰구, 종합안내소',
                'googlemap' => 'https://maps.app.goo.gl/3FhiNZtgYE8s8gbu8',
                'access_method' => '시야쿠쇼 지하철역에서 도보 5분',
                'official_website' => 'https://www.nagoyajo.city.nagoya.jp/en/'
            ],
            [
                'name' => '彦根城',
                'name_korean' => '히코네성',
                'prefecture' => '滋賀県(시가현)',
                'latitude' => 35.2771013,
                'longitude' => 136.25172038647025,
                'description' => '유서 깊은 17세기 성으로 아름다운 정원과 박물관이 있으며 투어가 제공됩니다.',
                'official_stamp_location' => '히코네성 관리사무소 (히코네시 개국기념관)',
                'googlemap' => 'https://maps.app.goo.gl/GoUnx1KT3veaEqqp9',
                'access_method' => 'JR 히코네역에서 도보 15분',
                'official_website' => 'https://visit.hikoneshi.com/en/castle/admission/'
            ],
            [
                'name' => '二条城',
                'name_korean' => '니조성',
                'prefecture' => '京都府(교토부)',
                'latitude' => 35.01417,
                'longitude' => 135.7475,
                'description' => '1603년에 사이프러스 나무로 지은 성으로 광대한 정원을 갖추고 있으며 쇼군 이에야스의 거처로 이용되었습니다.',
                'official_stamp_location' => '휴게소',
                'googlemap' => 'https://maps.app.goo.gl/ABRoudqoAjxFmRNm6',
                'access_method' => 'JR 교토역에서 버스로 니조조마에까지, 또는 니조조마에 지하철역',
                'official_website' => 'http://nijo-jocastle.city.kyoto.lg.jp/?lang=en'
            ],
            [
                'name' => '大阪城',
                'name_korean' => '오사카성',
                'prefecture' => '大阪府(오사카부)',
                'latitude' => 34.6865170739811,
                'longitude' => 135.52402853965762,
                'description' => '1597년에 지어진 이후 복원된 성으로 정원과 다양한 전시물을 갖춘 박물관이 있습니다.',
                'official_stamp_location' => '천수각 1층 안내소',
                'googlemap' => 'https://maps.app.goo.gl/4qpGELoZxZkuQQES6',
                'access_method' => 'JR 오사카조코엔역 또는 모리노미야역에서 도보 15분',
                'official_website' => 'https://www.osakacastle.net/english/'
            ],
            [
                'name' => '姫路城',
                'name_korean' => '히메지성',
                'prefecture' => '兵庫県(효고현)',
                'latitude' => 34.839331349,
                'longitude' => 134.69402,
                'description' => '1613년경에 지어진 성으로 흰 외벽, 탑, 해자, 통로, 체리 나무로 유명합니다.',
                'official_stamp_location' => '정문 옆 관리사무소',
                'googlemap' => 'https://maps.app.goo.gl/u4a4HfEHR5Q2ReyV6',
                'access_method' => 'JR 히메지역에서 도보 20분',
                'official_website' => 'http://www.himejicastle.jp/en/'
            ],
            [
                'name' => '松江城',
                'name_korean' => '마츠에성',
                'prefecture' => '島根県(시마네현)',
                'latitude' => 35.474514,
                'longitude' => 133.050833,
                'description' => '호리오 요시하루가 지은 5층 높이의 성으로 일본에 몇 채 남아 있지 않은 봉건시대 성 중 하나입니다.',
                'official_stamp_location' => '천수각 접수처',
                'googlemap' => 'https://maps.app.goo.gl/XfG17RYNqtKqXb3Y6',
                'access_method' => 'JR 마츠에역에서 버스로 오테마에까지, 도보 5분',
                'official_website' => 'https://www.visit-matsue.com/discover/city_centre/north_side'
            ],
            [
                'name' => '備中松山城',
                'name_korean' => '비추마츠야마성',
                'prefecture' => '岡山県(오카야마현)',
                'latitude' => 34.80870575,
                'longitude' => 133.62214678,
                'description' => '해발 430m 산 위에 위치한 일본에서 가장 높은 곳의 현존 천수각으로 운해로 유명합니다.',
                'official_stamp_location' => '매표소',
                'googlemap' => 'https://maps.app.goo.gl/FNvqrx4R8zwXTcqN6',
                'access_method' => 'JR 비추타카하시역에서 버스로 마츠야마성 등산로까지, 도보 50분',
                'official_website' => 'https://www.city.takahashi.okayama.jp/'
            ],
            [
                'name' => '広島城',
                'name_korean' => '히로시마성',
                'prefecture' => '広島県(히로시마현)',
                'latitude' => 34.402500,
                'longitude' => 132.459444,
                'description' => '소실된 16세기 성 유적으로 현대적인 역사 박물관으로 재건되었으며 도시 경관을 제공합니다.',
                'official_stamp_location' => '1층 박물관 매장',
                'googlemap' => 'https://maps.app.goo.gl/Xdp4B1NZzWxn1u1o9',
                'access_method' => 'JR 히로시마역에서 전차로 가미야초히가시까지, 도보 15분',
                'official_website' => 'https://visithiroshima.net/things_to_do/attractions/historical_places/hiroshima_castle.html'
            ],
            [
                'name' => '丸亀城',
                'name_korean' => '마루가메성',
                'prefecture' => '香川県(가가와현)',
                'latitude' => 34.2860216,
                'longitude' => 133.8001009,
                'description' => '도시 전망을 한눈에 감상할 수 있는 16세기 언덕 요새로 웅장한 석벽을 갖추고 있습니다.',
                'official_stamp_location' => '천수각 내부',
                'googlemap' => 'https://maps.app.goo.gl/p67aNwYfVputwLgL8',
                'access_method' => 'JR 마루가메역에서 도보 10분',
                'official_website' => 'https://www-city-marugame-lg-jp.translate.goog/site/castle/'
            ],
            [
                'name' => '松山城',
                'name_korean' => '마츠야마성',
                'prefecture' => '愛媛県(에히메현)',
                'latitude' => 33.845556,
                'longitude' => 132.765833,
                'description' => '복원된 이 17세기 성은 공원으로 둘러싸여 있으며 의자식 리프트, 케이블카 또는 도보로 이용 가능합니다.',
                'official_stamp_location' => '천수각 입구',
                'googlemap' => 'https://maps.app.goo.gl/m1cx3tifGbU9rjkd9',
                'access_method' => 'JR 마츠야마역에서 전차로 오카이도까지, 로프웨이 도보 5분',
                'official_website' => 'https://en.matsuyama-sightseeing.com/appeal/castle/'
            ],
            [
                'name' => '宇和島城',
                'name_korean' => '우와지마성',
                'prefecture' => '愛媛県(에히메현)',
                'latitude' => 33.2198186,
                'longitude' => 132.56473291,
                'description' => '우와지마만을 내려다보는 언덕에 위치한 작고 아름다운 현존 천수각을 가진 성입니다.',
                'official_stamp_location' => '천수각 내부',
                'googlemap' => 'https://maps.app.goo.gl/Dm2SbUMywNWVS29e7',
                'access_method' => 'JR 우와지마역에서 도보 20분',
                'official_website' => 'https://uwajima-tourism.org/en/spot/detail/place_id/72/'
            ],
            [
                'name' => '高知城',
                'name_korean' => '고치성',
                'prefecture' => '高知県(고치현)',
                'latitude' => 33.560691,
                'longitude' => 133.53145881,
                'description' => '1603년 건설 후 복원된 이 5층짜리 성에는 역사 전시물, 유물, 탑 전망대가 있습니다.',
                'official_stamp_location' => '천수각 입구',
                'googlemap' => 'https://maps.app.goo.gl/8w9DBHMFg4YhyT9Y9',
                'access_method' => 'JR 고치역에서 전차로 하리마야바시 경유 고치조마에역까지, 도보 5분',
                'official_website' => 'https://visitkochijapan.com/en/see-and-do/10009'
            ],
            [
                'name' => '熊本城',
                'name_korean' => '구마모토성',
                'prefecture' => '熊本県(구마모토현)',
                'latitude' => 32.8061,
                'longitude' => 130.7060,
                'description' => '1607년에 완공되었으며 재건축을 거친 이 성은 언덕 꼭대기에 위치하며 역사 박물관이 있습니다.',
                'official_stamp_location' => '니노마루 매표소, 와쿠와쿠자 매표소, 남입구 매표소, 북입구 매표소',
                'googlemap' => 'https://maps.app.goo.gl/w5hQutFvvUXYU4Sc7',
                'access_method' => 'JR 구마모토역에서 전차로 구마모토조/시야쿠쇼마에역까지, 사쿠라노바바 조사이엔까지 도보 10분',
                'official_website' => 'https://castle.kumamoto-guide.jp/en/'
            ],
            [
                'name' => '首里城',
                'name_korean' => '슈리성',
                'prefecture' => '沖縄県(오키나와현)',
                'latitude' => 26.217222,
                'longitude' => 127.719444,
                'description' => '드넓은 부지의 언덕 위에 재건된 성으로 도시 전망을 한눈에 감상할 수 있습니다.',
                'official_stamp_location' => '수이무이칸',
                'googlemap' => 'https://maps.app.goo.gl/nh8iF8NvbA9tAHTs5',
                'access_method' => '모노레일로 슈리역 또는 기보역까지, 슈레이몬까지 도보 15분',
                'official_website' => 'http://oki-park.jp/shurijo/en/'
            ]
        ];

        foreach ($castles as $castle) {
            Castle::create($castle);
        }
    }
}