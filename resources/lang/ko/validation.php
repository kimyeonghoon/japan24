<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute을(를) 반드시 동의해야 합니다.',
    'accepted_if' => ':other이(가) :value일 때 :attribute을(를) 동의해야 합니다.',
    'active_url' => ':attribute은(는) 유효한 URL이 아닙니다.',
    'after' => ':attribute은(는) :date 이후 날짜여야 합니다.',
    'after_or_equal' => ':attribute은(는) :date 이후이거나 같은 날짜여야 합니다.',
    'alpha' => ':attribute은(는) 문자만 포함해야 합니다.',
    'alpha_dash' => ':attribute은(는) 문자, 숫자, 대시, 밑줄만 포함해야 합니다.',
    'alpha_num' => ':attribute은(는) 문자와 숫자만 포함해야 합니다.',
    'array' => ':attribute은(는) 배열이어야 합니다.',
    'ascii' => ':attribute은(는) 단일 바이트 영숫자 문자와 기호만 포함해야 합니다.',
    'before' => ':attribute은(는) :date 이전 날짜여야 합니다.',
    'before_or_equal' => ':attribute은(는) :date 이전이거나 같은 날짜여야 합니다.',
    'between' => [
        'array' => ':attribute은(는) :min개에서 :max개 사이여야 합니다.',
        'file' => ':attribute은(는) :min에서 :max KB 사이여야 합니다.',
        'numeric' => ':attribute은(는) :min에서 :max 사이여야 합니다.',
        'string' => ':attribute은(는) :min에서 :max 글자 사이여야 합니다.',
    ],
    'boolean' => ':attribute 필드는 true 또는 false여야 합니다.',
    'can' => ':attribute 필드에 허가되지 않은 값이 포함되어 있습니다.',
    'confirmed' => ':attribute 확인이 일치하지 않습니다.',
    'contains' => ':attribute 필드에 필수 값이 누락되었습니다.',
    'current_password' => '비밀번호가 올바르지 않습니다.',
    'date' => ':attribute은(는) 유효한 날짜가 아닙니다.',
    'date_equals' => ':attribute은(는) :date와 같은 날짜여야 합니다.',
    'date_format' => ':attribute은(는) :format 형식과 일치하지 않습니다.',
    'decimal' => ':attribute은(는) :decimal 소수점 자리를 가져야 합니다.',
    'declined' => ':attribute은(는) 거부되어야 합니다.',
    'declined_if' => ':other이(가) :value일 때 :attribute은(는) 거부되어야 합니다.',
    'different' => ':attribute과(와) :other은(는) 달라야 합니다.',
    'digits' => ':attribute은(는) :digits 자리 숫자여야 합니다.',
    'digits_between' => ':attribute은(는) :min에서 :max 자리 사이여야 합니다.',
    'dimensions' => ':attribute의 이미지 크기가 올바르지 않습니다.',
    'distinct' => ':attribute 필드에 중복 값이 있습니다.',
    'doesnt_end_with' => ':attribute은(는) 다음 중 하나로 끝나면 안 됩니다: :values.',
    'doesnt_start_with' => ':attribute은(는) 다음 중 하나로 시작하면 안 됩니다: :values.',
    'email' => ':attribute은(는) 유효한 이메일 주소여야 합니다.',
    'ends_with' => ':attribute은(는) 다음 중 하나로 끝나야 합니다: :values.',
    'enum' => '선택된 :attribute이(가) 올바르지 않습니다.',
    'exists' => '선택된 :attribute이(가) 올바르지 않습니다.',
    'extensions' => ':attribute 필드는 다음 확장자 중 하나여야 합니다: :values.',
    'file' => ':attribute은(는) 파일이어야 합니다.',
    'filled' => ':attribute 필드는 값을 가져야 합니다.',
    'gt' => [
        'array' => ':attribute은(는) :value개보다 많아야 합니다.',
        'file' => ':attribute은(는) :value KB보다 커야 합니다.',
        'numeric' => ':attribute은(는) :value보다 커야 합니다.',
        'string' => ':attribute은(는) :value글자보다 길어야 합니다.',
    ],
    'gte' => [
        'array' => ':attribute은(는) :value개 이상이어야 합니다.',
        'file' => ':attribute은(는) :value KB 이상이어야 합니다.',
        'numeric' => ':attribute은(는) :value 이상이어야 합니다.',
        'string' => ':attribute은(는) :value글자 이상이어야 합니다.',
    ],
    'hex_color' => ':attribute 필드는 유효한 16진수 색상이어야 합니다.',
    'image' => ':attribute은(는) 이미지여야 합니다.',
    'in' => '선택된 :attribute이(가) 올바르지 않습니다.',
    'in_array' => ':attribute 필드는 :other에 존재하지 않습니다.',
    'integer' => ':attribute은(는) 정수여야 합니다.',
    'ip' => ':attribute은(는) 유효한 IP 주소여야 합니다.',
    'ipv4' => ':attribute은(는) 유효한 IPv4 주소여야 합니다.',
    'ipv6' => ':attribute은(는) 유효한 IPv6 주소여야 합니다.',
    'json' => ':attribute은(는) 유효한 JSON 문자열이어야 합니다.',
    'list' => ':attribute 필드는 목록이어야 합니다.',
    'lowercase' => ':attribute은(는) 소문자여야 합니다.',
    'lt' => [
        'array' => ':attribute은(는) :value개보다 적어야 합니다.',
        'file' => ':attribute은(는) :value KB보다 작아야 합니다.',
        'numeric' => ':attribute은(는) :value보다 작아야 합니다.',
        'string' => ':attribute은(는) :value글자보다 짧아야 합니다.',
    ],
    'lte' => [
        'array' => ':attribute은(는) :value개 이하여야 합니다.',
        'file' => ':attribute은(는) :value KB 이하여야 합니다.',
        'numeric' => ':attribute은(는) :value 이하여야 합니다.',
        'string' => ':attribute은(는) :value글자 이하여야 합니다.',
    ],
    'mac_address' => ':attribute은(는) 유효한 MAC 주소여야 합니다.',
    'max' => [
        'array' => ':attribute은(는) :max개를 초과할 수 없습니다.',
        'file' => ':attribute은(는) :max KB를 초과할 수 없습니다.',
        'numeric' => ':attribute은(는) :max를 초과할 수 없습니다.',
        'string' => ':attribute은(는) :max글자를 초과할 수 없습니다.',
    ],
    'max_digits' => ':attribute은(는) :max자리를 초과할 수 없습니다.',
    'mimes' => ':attribute은(는) :values 유형의 파일이어야 합니다.',
    'mimetypes' => ':attribute은(는) :values 유형의 파일이어야 합니다.',
    'min' => [
        'array' => ':attribute은(는) 최소 :min개여야 합니다.',
        'file' => ':attribute은(는) 최소 :min KB여야 합니다.',
        'numeric' => ':attribute은(는) 최소 :min이어야 합니다.',
        'string' => ':attribute은(는) 최소 :min글자여야 합니다.',
    ],
    'min_digits' => ':attribute은(는) 최소 :min자리여야 합니다.',
    'missing' => ':attribute 필드가 누락되어야 합니다.',
    'missing_if' => ':other이(가) :value일 때 :attribute 필드가 누락되어야 합니다.',
    'missing_unless' => ':other이(가) :value가 아닐 때 :attribute 필드가 누락되어야 합니다.',
    'missing_with' => ':values이(가) 있을 때 :attribute 필드가 누락되어야 합니다.',
    'missing_with_all' => ':values이(가) 모두 있을 때 :attribute 필드가 누락되어야 합니다.',
    'multiple_of' => ':attribute은(는) :value의 배수여야 합니다.',
    'not_in' => '선택된 :attribute이(가) 올바르지 않습니다.',
    'not_regex' => ':attribute 형식이 올바르지 않습니다.',
    'numeric' => ':attribute은(는) 숫자여야 합니다.',
    'password' => [
        'letters' => ':attribute은(는) 최소 하나의 문자를 포함해야 합니다.',
        'mixed' => ':attribute은(는) 최소 하나의 대문자와 소문자를 포함해야 합니다.',
        'numbers' => ':attribute은(는) 최소 하나의 숫자를 포함해야 합니다.',
        'symbols' => ':attribute은(는) 최소 하나의 기호를 포함해야 합니다.',
        'uncompromised' => '주어진 :attribute이(가) 데이터 유출에서 발견되었습니다. 다른 :attribute을(를) 선택하세요.',
    ],
    'present' => ':attribute 필드가 있어야 합니다.',
    'present_if' => ':other이(가) :value일 때 :attribute 필드가 있어야 합니다.',
    'present_unless' => ':other이(가) :value가 아닐 때 :attribute 필드가 있어야 합니다.',
    'present_with' => ':values이(가) 있을 때 :attribute 필드가 있어야 합니다.',
    'present_with_all' => ':values이(가) 모두 있을 때 :attribute 필드가 있어야 합니다.',
    'prohibited' => ':attribute 필드는 금지되어 있습니다.',
    'prohibited_if' => ':other이(가) :value일 때 :attribute 필드는 금지되어 있습니다.',
    'prohibited_unless' => ':other이(가) :values에 없을 때 :attribute 필드는 금지되어 있습니다.',
    'prohibits' => ':attribute 필드는 :other이(가) 있는 것을 금지합니다.',
    'regex' => ':attribute 형식이 올바르지 않습니다.',
    'required' => ':attribute 필드는 필수입니다.',
    'required_array_keys' => ':attribute 필드는 다음 항목을 포함해야 합니다: :values.',
    'required_if' => ':other이(가) :value일 때 :attribute 필드는 필수입니다.',
    'required_if_accepted' => ':other이(가) 허용될 때 :attribute 필드는 필수입니다.',
    'required_if_declined' => ':other이(가) 거부될 때 :attribute 필드는 필수입니다.',
    'required_unless' => ':other이(가) :values에 없을 때 :attribute 필드는 필수입니다.',
    'required_with' => ':values이(가) 있을 때 :attribute 필드는 필수입니다.',
    'required_with_all' => ':values이(가) 모두 있을 때 :attribute 필드는 필수입니다.',
    'required_without' => ':values이(가) 없을 때 :attribute 필드는 필수입니다.',
    'required_without_all' => ':values이(가) 모두 없을 때 :attribute 필드는 필수입니다.',
    'same' => ':attribute과(와) :other이(가) 일치해야 합니다.',
    'size' => [
        'array' => ':attribute은(는) :size개여야 합니다.',
        'file' => ':attribute은(는) :size KB여야 합니다.',
        'numeric' => ':attribute은(는) :size여야 합니다.',
        'string' => ':attribute은(는) :size글자여야 합니다.',
    ],
    'starts_with' => ':attribute은(는) 다음 중 하나로 시작해야 합니다: :values.',
    'string' => ':attribute은(는) 문자열이어야 합니다.',
    'timezone' => ':attribute은(는) 유효한 시간대여야 합니다.',
    'unique' => ':attribute은(는) 이미 사용 중입니다.',
    'uploaded' => ':attribute 업로드에 실패했습니다.',
    'uppercase' => ':attribute은(는) 대문자여야 합니다.',
    'url' => ':attribute은(는) 유효한 URL이어야 합니다.',
    'ulid' => ':attribute은(는) 유효한 ULID여야 합니다.',
    'uuid' => ':attribute은(는) 유효한 UUID여야 합니다.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => '이름',
        'email' => '이메일',
        'password' => '비밀번호',
        'password_confirmation' => '비밀번호 확인',
        'visit_date' => '방문일',
        'gps_latitude' => '위도',
        'gps_longitude' => '경도',
        'photos' => '사진',
        'stamp_photo' => '스탬프 사진',
        'visit_notes' => '방문 메모',
        'device_timestamp' => '디바이스 시간',
        'phone' => '전화번호',
        'mobile' => '휴대폰번호',
        'address' => '주소',
        'birth_date' => '생년월일',
        'age' => '나이',
        'gender' => '성별',
        'title' => '제목',
        'content' => '내용',
        'description' => '설명',
        'comment' => '댓글',
        'message' => '메시지',
        'subject' => '제목',
        'body' => '본문',
        'first_name' => '성',
        'last_name' => '이름',
        'username' => '사용자명',
        'city' => '도시',
        'country' => '국가',
        'state' => '주/도',
        'zipcode' => '우편번호',
        'postal_code' => '우편번호',
        'company' => '회사',
        'website' => '웹사이트',
        'terms' => '이용약관',
        'privacy' => '개인정보처리방침',
    ],

];