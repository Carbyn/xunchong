<?php
class FeedbackController extends \Explorer\ControllerAbstract {

    public function submitAction() {
        $contact = $this->getRequest()->getPost('contact', '');
        $content = $this->getRequest()->getPost('content', '');
        if (!$content) {
            return $this->outputError(Constants::ERR_SYS_PARAM_INVALID, '内容不能为空');
        }

        $feedbackModel = new FeedbackModel();
        $feedbackModel->create($this->userId, $contact, $content);
        $this->outputSuccess();
    }

}
