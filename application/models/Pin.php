<?php
class PinModel extends AbstractModel {

    const TABLE = 'pin';

    public function hasPinned($user_id, $screenshot_id) {
        $where = compact('user_id', 'screenshot_id');
        return $this->db->table(self::TABLE)->where($where)->get();
    }

    public function pin($user_id, $screenshot_id) {
        $create_time = time();
        $data = compact('user_id', 'screenshot_id', 'create_time');
        $id = $this->db->table(self::TABLE)->insert($data);
        return $id;
    }

    public function unpin($user_id, $screenshot_id) {
        $where = compact('user_id', 'screenshot_id');
        return $this->db->table(self::TABLE)->where($where)->delete();
    }

}
