
### 저작권 및 라이선스 ###

[포에시스](http://post.poesis.kr/)의 도로명주소 우편번호 검색 API를 자체 서버에서 구현하는 데 필요한 모든 프로그램을 오픈소스로 공개합니다.
라이선스는 GPL3을 따릅니다. 자유롭게 사용, 변형 및 배포가 가능하나, 변형 배포시 반드시 소스를 공개해야 합니다.

> 저작권 (c) 2014 성기진
>
> 이 프로그램은 자유 소프트웨어입니다.
> 이 소프트웨어의 피양도자는 자유 소프트웨어 재단이 공표한 GNU 일반 공중 사용 허가서 (GNU GPL) 제3판
> 또는 그 이후의 판을 임의로 선택하여, 그 규정에 따라 이 프로그램을 개작하거나 재배포할 수 있습니다.
> 
> 이 프로그램은 유용하게 사용될 수 있으리라는 희망에서 배포되고 있지만,
> 특정한 목적에 맞는 적합성 여부나 판매용으로 사용할 수 있으리라는 묵시적인 보증을 포함한 어떠한 형태의 보증도 제공하지 않습니다.
> 보다 자세한 사항에 대해서는 GNU 일반 공중 사용 허가서를 참고하시기 바랍니다.
> 
> GNU 일반 공중 사용 허가서는 이 프로그램과 함께 제공됩니다.
> 만약 이 문서가 누락되어 있다면 자유 소프트웨어 재단으로 문의하시기 바랍니다.

### 프로그램 개요 ###

#### 클라이언트 API ####

클라이언트 API는 [jQuery](http://www.jquery.com/) 플러그인으로 제공됩니다.

우편번호 검색 기능이 필요한 웹페이지에 아래와 같이 `<div>`를 생성한 후
최근 버전의 jQuery와 `api/search.js` 파일을 로딩하면 됩니다.

    <div id="postcode_search"></div>
    <script src="//code.jquery.com/jquery-1.10.2.min.js" charset="UTF-8"></script>
    <script src="api/search.js" charset="UTF-8"></script>
    <script type="text/javascript">
        $("#postcode_search").postcodify();
    </script>

위의 예제는 기본 사용법입니다. `postcodify()`를 호출하면 즉시 검색 기능을 사용할 수 있으나,
검색 결과를 폼에 입력하려면 아래와 같이 설정이 필요합니다.

    $("#검색란을_표시할_div의_id").postcodify({
        api : "api/search.php",
        controls : "#키워드_입력란을_표시할_div의_id",
        insertDbid : "#안행부_관리번호를_입력할_input의_id",
        insertPostcode5 : "#기초구역번호를_입력할_input의_id",
        insertPostcode6 : "#우편번호를_입력할_input의_id",
        insertAddress : "#도로명주소를_입력할_input의_id",
        insertDetails : "#상세주소를_입력할_input의_id",
        insertExtraInfo : "#참고항목을_입력할_input의_id",
        beforeSearch : function(keywords) {
            // 검색 직전에 호출할 콜백
        },
        afterSearch : function(keywords, results) {
            // 검색 완료 직후에 호출할 콜백
        },
        beforeSelect : function(selectedEntry) {
            // 선택한 주소를 input에 입력하기 직전에 호출할 콜백
        },
        afterSelect : function(selectedEntry) {
            // 선택한 주소를 input에 입력한 직후에 호출할 콜백
        },
        focusKeyword : true,  // 페이지 로딩 직후 키워드 입력란으로 포커스 이동
        useFullJibeon : true  // false인 경우 참고항목에 법정동과 공동주택명만 표시
                              // true인 경우 대표지번도 표시 (택배 등의 편의를 위해)
            // 익스플로러 호환성을 위해 마지막 항목 뒤에는 쉼표(,) 입력 금지
    });

스타일 지정은 CSS를 사용하시면 됩니다.
F12 키를 눌러 웹브라우저의 개발자도구를 사용하시면
자바스크립트가 생성하는 태그들의 class 속성을 찾아보실 수 있습니다.

우편번호 DB를 자체 서버에 설치한 경우 반드시 `api` 설정을 변경하여
자체 서버의 `api/search.php` 스크립트를 호출하도록 하시기 바랍니다.

무료 예제로 제공되는 포에시스 서버의 검색 스크립트는 도메인당 하루 100회 내외,
클라이언트 IP당 하루 100회 내외의 쿼리수 제한이 있으므로
대규모 사이트에 직접 적용하기에는 곤란합니다.

#### 서버 API ####

서버 API는 PHP/MySQL 스크립트로 제공됩니다.

서버 API를 자체 서버에서 구동하려면 PHP 5.3.7 이상, MySQL 5.1 이상 (또는 MariaDB), InnoDB, UTF-8 환경을 권장합니다.
그 이하의 버전에서도 구동이 가능할 수는 있으나, 성능 및 호환성을 보장할 수 없습니다.

서버 API를 사용하시려면 `api/config-example.php` 파일을 `config.php`로 복사하여
DB 접속 정보를 입력하시기 바랍니다.

#### 인덱서 ####

인덱서(indexer)는
[안전행정부](http://www.juso.go.kr/notice/OpenArchivesList.do?noticeKd=26&type=matching)에서 제공하는 도로명코드/주소/지번/부가정보 파일과
[우체국](http://www.epost.go.kr/search/zipcode/newAddressDown.jsp)에서 제공하는 사서함 정보를
MySQL DB에 입력하고 검색 키워드를 추출하는 프로그램입니다.

포에시스의 우편번호 검색 프로그램은 아래와 같은 특징이 있습니다.

1. 곧 제공이 중단될 매칭테이블이 아닌 "유통자료개선" (도로명코드/주소/지번/부가정보) 형태의 자료를 사용합니다.
   또한 안행부에서 제공하는 데이터 구조를 그대로 사용하지 않고, 검색 결과 표시 및 이해에 최적화된 형태로 가공합니다.
2. 현행 우편번호와 향후 도입될 기초구역번호를 모두 제공합니다.
3. 안행부에서 매일 업로드하는 [변경분](http://www.juso.go.kr/notice/OpenArchivesList.do?noticeKd=27&type=archives) 자료를 그때그때 간단히 적용할 수 있도록 하여,
   정기적으로 업데이트 스크립트를 실행해 주기만 하면 새로 생긴 도로명주소까지 빠짐없이 검색되도록 합니다.
4. 가능한 많은 검색어를 추출하여 별도의 테이블에 저장하여, 도로명, 법정동, 대표지번 등을 정확하게 입력하지 않아도
   단 한 번의 인덱스 스캔으로 정확한 결과를 얻을 수 있도록 합니다.

현재는 아래와 같은 단점이 있습니다.

1. 검색어 테이블이 큰 용량을 차지합니다. 지번 관련 검색어는 1600만 건, 도로명 관련 검색어는 1000만 건 내외를 추출합니다.
   주소를 저장하는 메인 테이블까지 합치면 총 7GB 내외의 용량이 필요합니다.
   빠른 검색 속도를 위해서는 InnoDB의 버퍼 크기를 1GB 이상으로 설정해 주는 것이 좋습니다.
2. 최초 DB 생성에 긴 시간이 걸립니다. 시도별로 멀티쓰레딩을 사용하는 현재의 `indexer/start.php` 스크립트의 경우
   일반 듀얼코어 PC + 4GB 메모리 + HDD/SSD 환경에서는 3-4시간,
   최신 16코어 서버 + 32GB 메모리 + 램디스크 환경에서는 30-40분이 걸립니다.
3. 업데이트의 경우 안전행정부 제공 자료에는 기초구역번호 등 일부 정보가 누락되어 있으므로
   완전한 DB를 유지하기 위해서는 매일 업데이트만 해서는 곤란합니다.
   한 달에 한 번 정도는 최신 "유통자료개선" 데이터를 다운로드받아 DB를 다시 생성해야 합니다.

### 인덱서 사용법 ###

#### 1단계 : 구동 환경 확인 ####

인덱서 스크립트를 실행하려면 서버 API와 동일한 PHP 및 MySQL 버전이 필요합니다.

PHP는 웹서버가 아닌 터미널(CLI)에서 실행할 수 있어야 하며, `Zip`, `PDO`, `iconv`, `mbstring` 모듈이 필요합니다.
DB 접속은 `mysql_connect()` 함수가 아닌 PDO를 통해 이루어지므로 반드시 `PDO_mysql` 드라이버가 있어야 합니다.

인덱서 스크립트는 리눅스 환경에서만 테스트되었습니다. 윈도우 환경에서는 정상 작동을 보장할 수 없습니다.

인덱서 스크립트는 시간 절약을 위해 멀티쓰레딩 (`pcntl_fork`) 기능을 사용하므로
해당 함수들이 컴파일되지 않았거나 php.ini에서 사용금지해 둔 경우에는 오류가 발생합니다.
일부 리눅스 배포판은 php.ini에서 `pcntl_fork` 함수를 금지해 둔 경우가 많으니 반드시 해제하고 사용하시기 바랍니다.

최근 버전의 데비안 또는 우분투 리눅스에서 구동 환경을 구축하려면 아래와 같이 하면 됩니다.

    sudo locale-gen en_US.UTF-8
    sudo locale-gen ko_KR.UTF-8
    sudo apt-get update
    sudo apt-get install mysql-server mysql-client php5-cli php5-json php5-mysql
    sudo sed -i -r 's/^disable_functions/;disable_functions/' /etc/php5/cli/php.ini

#### 2단계 : 우편번호 파일 다운로드 ####

위의 안전행정부와 우체국 링크에서 최신 우편번호 파일들을 다운로드하여 `data` 폴더에 넣습니다.
압축파일은 인덱서 프로그램이 압축 상태에서 그대로 읽어 사용하므로 미리 압축을 해제할 필요가 없습니다.

  - 안전행정부 : **도로명코드_전체분.zip**
  - 안전행정부 : **주소_서울특별시.zip**, **주소_부산광역시.zip** 등 (총 14개)
  - 안전행정부 : **지번_서울특별시.zip**, **지번_부산광역시.zip** 등 (총 14개)
  - 안전행정부 : **부가정보_서울특별시.zip**, **부가정보_부산광역시.zip** 등 (총 14개)
  - 우체국 : **newaddr_pobox_DB.zip** (사서함 정보)

위와 같이 총 44개의 파일이 필요합니다.

최신 업데이트를 원하시는 경우, 안전행정부에서 업데이트 파일들을 다운로드하여 `data/Updates` 폴더에 넣으시면 됩니다.
업데이트에 사용할 파일명은 아래와 유사합니다. 2014년 1월 현재 업데이트는 매칭테이블 형태로만 제공되고 있습니다.
향후 파일 형식이 변경될 경우 현재의 업데이트 스크립트로는 분석이 안될 수도 있습니다.

  - AlterD.JUSUBH.20131225.MatchingTable.TXT

위에서 다운로드한 도로명코드/주소/지번/부가정보 파일의 기준일 이전 업데이트는 필요하지 않습니다.
예를 들어 나머지 파일들의 기준일이 12월 24일이라면 12월 25일분 업데이트부터 적용하시면 됩니다.

#### 3단계 : 설정 ####

`indexer/config-example.php` 파일을 `config.php`로 복사하여
파일을 다운로드해 둔 경로를 입력하고, DB 접속 정보를 넣습니다.

#### 4단계 : DB 생성 스크립트 실행 ####

`indexer` 폴더로 이동한 후, 터미널에서 `php start.php`를 실행합니다.
서버 사양에 따라 최소 30분에서 최대 6시간 정도가 걸릴 수 있습니다.
실행되는 동안에는 아래와 같은 메시지가 출력됩니다.

    $ php start.php
    
    [Step 1/8] 테이블과 프로시저를 생성하는 중 ... 
    
    [Step 2/8] 도로 목록을 메모리에 읽어들이는 중 ... 
    
      -->  도로명코드_전체분.zip ...    348,947
    
    [Step 3/8] 쓰레드를 사용하여 "주소" 파일을 로딩하는 중 ... 
    
      -->  주소_강원도.zip 쓰레드 시작 ... 
      -->  주소_경기도.zip 쓰레드 시작 ... 
      -->  주소_경상남북도.zip 쓰레드 시작 ... 
      -->  주소_광주광역시.zip 쓰레드 시작 ... 
      -->  주소_대구광역시.zip 쓰레드 시작 ... 
      -->  주소_대전광역시.zip 쓰레드 시작 ... 
      -->  주소_부산광역시.zip 쓰레드 시작 ... 
      -->  주소_서울특별시.zip 쓰레드 시작 ... 
      -->  주소_세종특별자치시.zip 쓰레드 시작 ... 
      -->  주소_울산광역시.zip 쓰레드 시작 ... 
      -->  주소_인천광역시.zip 쓰레드 시작 ... 
      -->  주소_전라남북도.zip 쓰레드 시작 ... 
      -->  주소_제주특별자치도.zip 쓰레드 시작 ... 
      -->  주소_충청남북도.zip 쓰레드 시작 ... 
    
      <--  주소_세종특별자치시.zip 쓰레드 종료. 13 쓰레드 남음.
      <--  주소_울산광역시.zip 쓰레드 종료. 12 쓰레드 남음.
      <--  주소_대전광역시.zip 쓰레드 종료. 11 쓰레드 남음.
      <--  주소_제주특별자치도.zip 쓰레드 종료. 10 쓰레드 남음.
      <--  주소_광주광역시.zip 쓰레드 종료. 9 쓰레드 남음.
      <--  주소_인천광역시.zip 쓰레드 종료. 8 쓰레드 남음.
      <--  주소_대구광역시.zip 쓰레드 종료. 7 쓰레드 남음.
      <--  주소_강원도.zip 쓰레드 종료. 6 쓰레드 남음.
      <--  주소_부산광역시.zip 쓰레드 종료. 5 쓰레드 남음.
      <--  주소_서울특별시.zip 쓰레드 종료. 4 쓰레드 남음.
      <--  주소_충청남북도.zip 쓰레드 종료. 3 쓰레드 남음.
      <--  주소_경기도.zip 쓰레드 종료. 2 쓰레드 남음.
      <--  주소_전라남북도.zip 쓰레드 종료. 1 쓰레드 남음.
      <--  주소_경상남북도.zip 쓰레드 종료. 0 쓰레드 남음.
    
    [Step 4/8] 쓰레드를 사용하여 "지번" 파일을 로딩하는 중 ... 
    
      -->  지번_강원도.zip 쓰레드 시작 ... 
      -->  지번_경기도.zip 쓰레드 시작 ... 
      -->  지번_경상남북도.zip 쓰레드 시작 ... 
      -->  지번_광주광역시.zip 쓰레드 시작 ... 
      -->  지번_대구광역시.zip 쓰레드 시작 ... 
      -->  지번_대전광역시.zip 쓰레드 시작 ... 
      -->  지번_부산광역시.zip 쓰레드 시작 ... 
      -->  지번_서울특별시.zip 쓰레드 시작 ... 
      -->  지번_세종특별자치시.zip 쓰레드 시작 ... 
      -->  지번_울산광역시.zip 쓰레드 시작 ... 
      -->  지번_인천광역시.zip 쓰레드 시작 ... 
      -->  지번_전라남북도.zip 쓰레드 시작 ... 
      -->  지번_제주특별자치도.zip 쓰레드 시작 ... 
      -->  지번_충청남북도.zip 쓰레드 시작 ... 
    
      <--  지번_세종특별자치시.zip 쓰레드 종료. 13 쓰레드 남음.
      <--  지번_울산광역시.zip 쓰레드 종료. 12 쓰레드 남음.
      <--  지번_대전광역시.zip 쓰레드 종료. 11 쓰레드 남음.
      <--  지번_제주특별자치도.zip 쓰레드 종료. 10 쓰레드 남음.
      <--  지번_광주광역시.zip 쓰레드 종료. 9 쓰레드 남음.
      <--  지번_인천광역시.zip 쓰레드 종료. 8 쓰레드 남음.
      <--  지번_대구광역시.zip 쓰레드 종료. 7 쓰레드 남음.
      <--  지번_부산광역시.zip 쓰레드 종료. 6 쓰레드 남음.
      <--  지번_강원도.zip 쓰레드 종료. 5 쓰레드 남음.
      <--  지번_서울특별시.zip 쓰레드 종료. 4 쓰레드 남음.
      <--  지번_충청남북도.zip 쓰레드 종료. 3 쓰레드 남음.
      <--  지번_경기도.zip 쓰레드 종료. 2 쓰레드 남음.
      <--  지번_전라남북도.zip 쓰레드 종료. 1 쓰레드 남음.
      <--  지번_경상남북도.zip 쓰레드 종료. 0 쓰레드 남음.
    
    [Step 5/8] 쓰레드를 사용하여 "부가정보" 파일을 로딩하는 중 ... 
    
      -->  부가정보_강원도.zip 쓰레드 시작 ... 
      -->  부가정보_경기도.zip 쓰레드 시작 ... 
      -->  부가정보_경상남북도.zip 쓰레드 시작 ... 
      -->  부가정보_광주광역시.zip 쓰레드 시작 ... 
      -->  부가정보_대구광역시.zip 쓰레드 시작 ... 
      -->  부가정보_대전광역시.zip 쓰레드 시작 ... 
      -->  부가정보_부산광역시.zip 쓰레드 시작 ... 
      -->  부가정보_서울특별시.zip 쓰레드 시작 ... 
      -->  부가정보_세종특별자치시.zip 쓰레드 시작 ... 
      -->  부가정보_울산광역시.zip 쓰레드 시작 ... 
      -->  부가정보_인천광역시.zip 쓰레드 시작 ... 
      -->  부가정보_전라남북도.zip 쓰레드 시작 ... 
      -->  부가정보_제주특별자치도.zip 쓰레드 시작 ... 
      -->  부가정보_충청남북도.zip 쓰레드 시작 ... 
    
      <--  부가정보_세종특별자치시.zip 쓰레드 종료. 13 쓰레드 남음.
      <--  부가정보_대전광역시.zip 쓰레드 종료. 12 쓰레드 남음.
      <--  부가정보_울산광역시.zip 쓰레드 종료. 11 쓰레드 남음.
      <--  부가정보_광주광역시.zip 쓰레드 종료. 10 쓰레드 남음.
      <--  부가정보_제주특별자치도.zip 쓰레드 종료. 9 쓰레드 남음.
      <--  부가정보_인천광역시.zip 쓰레드 종료. 8 쓰레드 남음.
      <--  부가정보_대구광역시.zip 쓰레드 종료. 7 쓰레드 남음.
      <--  부가정보_강원도.zip 쓰레드 종료. 6 쓰레드 남음.
      <--  부가정보_부산광역시.zip 쓰레드 종료. 5 쓰레드 남음.
      <--  부가정보_서울특별시.zip 쓰레드 종료. 4 쓰레드 남음.
      <--  부가정보_충청남북도.zip 쓰레드 종료. 3 쓰레드 남음.
      <--  부가정보_경기도.zip 쓰레드 종료. 2 쓰레드 남음.
      <--  부가정보_전라남북도.zip 쓰레드 종료. 1 쓰레드 남음.
      <--  부가정보_경상남북도.zip 쓰레드 종료. 0 쓰레드 남음.
    
    [Step 6/8] 사서함 데이터를 로딩하는 중 ... 
    
      -->  newaddr_pobox_DB.zip ... 
    
      <--  경과 시간 : 1시간 40분 3초
    
    [Step 7/8] 인덱스를 생성하는 중. 아주 긴 시간이 걸릴 수 있습니다 ... 
    
      -->  postcode_addresses 쓰레드 시작 ... 
      -->  postcode_keywords_juso 쓰레드 시작 ... 
      -->  postcode_keywords_jibeon 쓰레드 시작 ... 
      -->  postcode_keywords_building 쓰레드 시작 ... 
      -->  postcode_keywords_pobox 쓰레드 시작 ... 
    
      <--  postcode_keywords_pobox 쓰레드 종료. 4 쓰레드 남음.
      <--  postcode_keywords_building 쓰레드 종료. 3 쓰레드 남음.
      <--  postcode_keywords_juso 쓰레드 종료. 2 쓰레드 남음.
      <--  postcode_keywords_jibeon 쓰레드 종료. 1 쓰레드 남음.
      <--  postcode_addresses 쓰레드 종료. 0 쓰레드 남음.
    
      <--  경과 시간 : 2시간 40분 6초
    
    [Step 8/8] 업데이트를 적용하는 중 ... 
    
      -->  AlterD.JUSUBH.20131225.MatchingTable.TXT ...        370
      -->  AlterD.JUSUBH.20131226.MatchingTable.TXT ...         37
      -->  AlterD.JUSUBH.20131227.MatchingTable.TXT ...      1,132
      -->  AlterD.JUSUBH.20131228.MatchingTable.TXT ...      2,038
      -->  AlterD.JUSUBH.20131229.MatchingTable.TXT ...         17
      -->  AlterD.JUSUBH.20131231.MatchingTable.TXT ...      2,456
      -->  AlterD.JUSUBH.20140101.MatchingTable.TXT ...        747
      -->  AlterD.JUSUBH.20140102.MatchingTable.TXT ...        145
      -->  AlterD.JUSUBH.20140103.MatchingTable.TXT ...        882
      -->  AlterD.JUSUBH.20140104.MatchingTable.TXT ...      1,086
      -->  AlterD.JUSUBH.20140105.MatchingTable.TXT ...         96
      -->  AlterD.JUSUBH.20140106.MatchingTable.TXT ...         25
      -->  AlterD.JUSUBH.20140107.MatchingTable.TXT ...          2
      -->  AlterD.JUSUBH.20140108.MatchingTable.TXT ...      1,799
      -->  AlterD.JUSUBH.20140109.MatchingTable.TXT ...        953
      -->  AlterD.JUSUBH.20140110.MatchingTable.TXT ...        676
      -->  AlterD.JUSUBH.20140111.MatchingTable.TXT ...      1,131
      -->  AlterD.JUSUBH.20140112.MatchingTable.TXT ...         20
      -->  AlterD.JUSUBH.20140113.MatchingTable.TXT ...         76
      -->  AlterD.JUSUBH.20140114.MatchingTable.TXT ...      1,044
      -->  AlterD.JUSUBH.20140115.MatchingTable.TXT ...        845
    
    작업을 모두 마쳤습니다. 경과 시간 : 3시간 18분 21초

#### 5단계 : DB 생성 스크립트 실행 ####

최초 DB 생성시에 업데이트를 함께 적용했다면 별도로 업데이트할 필요가 없습니다.

나중에 업데이트를 추가하려면 `data/Updates` 폴더에 해당 파일들을 저장한 후,
터미널에서 `php update.php`를 실행하면 됩니다.

안전행정부 제공 업데이트 파일에는 일부 정보가 누락되어 있으므로
한 달에 한 번 정도는 최신 "유통자료개선" 데이터를 다운로드받아 DB를 다시 생성하는 것이 좋습니다.

#### 6단계 : 테스트 ####

서버 API는 터미널에서도 쉽게 테스트할 수 있습니다.
`api` 폴더로 이동한 후 아래와 같이 검색 테스트를 하시면 됩니다.

    php search.php "검색할 주소"
