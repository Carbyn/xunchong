<?php
namespace Explorer;
class Tbk {

    public static function getFavoritesList() {
        $req = new \TbkUatmFavoritesGetRequest();
        $req->setPageNo(1);
        $req->setPageSize(20);
        $req->setFields("favorites_title,favorites_id,type");
        $req->setType(-1);
        $resp = Ali::getTopClient()->execute($req);
        return $resp;
    }

    public static function getFavoritesItem($fid, $pn, $ps) {
        $req = new \TbkUatmFavoritesItemGetRequest();
        $req->setFavoritesId($fid);
        $req->setPageNo($pn);
        $req->setPageSize($ps);
        $req->setPlatform(2); // 1: PC, 2: WAP
        $req->setAdzoneId(109253950012); // mm_15956357_660300410_109253950012
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,coupon_start_time,coupon_end_time,coupon_id,coupon_total_count,coupon_remain_count,coupon_info,coupon_start_fee,coupon_amount,item_url,click_url,coupon_click_url,coupon_share_url,seller_id,volume,nick,shop_title,zk_final_price_wap,event_start_time,event_end_time,tk_rate,status,type");
        $resp = Ali::getTopClient()->execute($req);
        return $resp;
    }

    public static function getItemInfo($id) {
        $req = new \TbkItemInfoGetRequest();
        $req->setNumIids($id);
        $resp = Ali::getTopClient()->execute($req);
        return $resp;
    }

    public static function getCoupon() {
    }

    public static function createTpwd($url, $text, $logo) {
        $req = new \TbkTpwdCreateRequest;
        $req->setText($text);
        $req->setUrl($url);
        $req->setLogo($logo);
        $resp = Ali::getTopClient()->execute($req);
        return $resp;
    }

}
