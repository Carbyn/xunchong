<?php
class FeedbackModel extends AbstractModel {

    const TABLE = 'feedback';

    public function create($user_id, $contact, $content) {
        $create_time = time();
        $data = compact('user_id', 'contact', 'content', 'create_time');
        return $this->db->table(self::TABLE)->insert($data);
    }
}
