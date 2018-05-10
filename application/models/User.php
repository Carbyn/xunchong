<?php
class UserModel extends AbstractModel {

    const TABLE = 'user';

    public function exists($mobile) {
        $where = ['mobile' => $mobile];
        $user = $this->db->table(self::TABLE)->where($where)->get();
        return empty($user) ? false : $user->id;
    }

    public function fetch($id) {
        $where = ['id' => $id];
        return $this->db->table(self::TABLE)->where($where)->get();
    }

    public function fetchAll($ids) {
        $users = $this->db->table(self::TABLE)->in('id', $ids)->getAll();
        $ret = [];
        foreach($users as $user) {
            $ret[$user->id] = $user;
        }
        return $ret;
    }
    
    public function create($mobile) {
        $data = compact('mobile');
        $data['register_time'] = time();
        return $this->db->table(self::TABLE)->insert($data);
    }

    public function updateProfile($id, $data) {
        $where = ['id' => $id];
        $this->db->table(self::TABLE)->where($where)->update($data);
    }

}
