<?php
class UserModel extends AbstractModel {

    const TABLE = 'user';
    const DEFAULT_AVATAR = 'https://xunchong.1024.pm/static/default_avatar.jpg';
    const DEFAULT_NAME = '铲屎官';

    /*
    public function exists($mobile) {
        $where['mobile'] = $mobile;
        $user = $this->db->table(self::TABLE)->where($where)->get();
        return $user;
    }
     */

    public function existsOpenid($openid) {
        $where['openid'] = $openid;
        $user = $this->db->table(self::TABLE)->where($where)->get();
        return $user;
    }

    public function fetch($id) {
        $where = ['id' => $id];
        $user = $this->db->table(self::TABLE)->where($where)->get();
        return $user;
    }

    public function fetchAll($ids) {
        $users = $this->db->table(self::TABLE)->in('id', $ids)->getAll();
        $ret = [];
        foreach($users as $user) {
            $ret[$user->id] = $user;
        }
        return $ret;
    }

    /*
    public function create($mobile) {
        $data['mobile'] = $mobile;
        $data['avatar'] = self::DEFAULT_AVATAR;
        $data['name'] = $mobile;
        $data['register_time'] = time();
        return $this->db->table(self::TABLE)->insert($data);
    }
     */

    public function createOpenid($openid) {
        $data['openid'] = $openid;
        $data['avatar'] = self::DEFAULT_AVATAR;
        $data['name'] = self::DEFAULT_NAME;
        $data['register_time'] = time();
        return $this->db->table(self::TABLE)->insert($data);
    }

    public function updateProfile($id, $data) {
        $where = ['id' => $id];
        $this->db->table(self::TABLE)->where($where)->update($data);
    }

}
