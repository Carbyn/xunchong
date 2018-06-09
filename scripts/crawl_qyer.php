<?php
class CrawlQyer {
    public $countries_path = './countries.json';

    public $cities_cmd = "curl -s 'http://www.qyer.com/qcross/place/country.php?action=listcity&type=city&countryId={countryId}&page=1&timer=1527929852569' -H 'Cookie: _guid=Rfb304f4-abcc-8af8-909c-b10a32def039; __utmz=253397513.1520682818.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); als=0; isnew=1520682854831; NTKF_T2D_CLIENTID=guest239B68A3-5DD6-8348-3E82-0FC37BBA63D9; PHPSESSID=e50aa2c43dc047448dc4a05cf4eb743c; __utmc=253397513; ql_guid=QL636fe8-9f8a-4c5d-92ac-b1bb9ecc791a; cdb_cookietime=2952000; cdb_auth=e8d7K%2BOLAYxdLrviNC32GBua%2F%2BtOcY1DqlIFbQHvnCmS90aafQHZzofKPw4Es1fdtMuJThsv58fnJTud5TAYwconnPjaYw; __utma=253397513.1780918767.1520682818.1527916958.1527929317.4; __utmt=1; ql_created_session=1; ql_stt=1527929316642; ql_vts=2; session_time=1527929317.361; init_refer=; new_uv=4; new_session=0; __utmb=253397513.3.10.1527929317; ql_seq=3' -H 'DNT: 1' -H 'Accept-Encoding: gzip, deflate' -H 'Accept-Language: en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3447.3 Safari/537.36' -H 'Accept: application/json, text/javascript, */*; q=0.01' -H 'Referer: http://www.qyer.com/u/1145280/footprint' -H 'X-Requested-With: XMLHttpRequest' -H 'Connection: keep-alive' --compressed";

    public function run() {
        $countries = @json_decode(@file_get_contents($this->countries_path), true);
        foreach ($countries['data']['continent_list'] as $continent => $continent_data) {
            foreach ($continent_data['country_list'] as $country) {
                $cities_cmd_truth = str_replace('{countryId}', $country['country_id'], $this->cities_cmd);
                $cmd = $cities_cmd_truth.' > ./cities/'.$country['country_id'].'.json';
                echo $country['country_id']."\n";
                exec($cmd);
                sleep(2);
            }
        }
    }
}

(new CrawlQyer())->run();
