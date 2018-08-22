<?php

/**
 * Class Firewall_IndexController
 */
class Firewall_IndexController extends Backoffice_Controller_Default
{
    /**
     * Fetch rules
     *
     * @throws Zend_Exception
     */
    public function findallAction()
    {
        $payload = [
            'title' => __('Advanced') . ' > ' . __('Firewall'),
            'icon' => 'icofont icofont-ui-fire-wall',
        ];

        $rules = (new \Firewall_Model_Rule())
            ->findAll(
                [
                    'type' => \Firewall_Model_Rule::FW_TYPE_UPLOAD
                ],
                [
                    'value ASC'
                ]
            );

        $rulesData = [];
        foreach ($rules as $rule) {
            $rulesData[] = [
                'value' => $rule->getValue()
            ];
        }

        $payload['fw_upload_rules'] = $rulesData;

        $this->_sendJson($payload);
    }

    /**
     *
     */
    public function deletefwuploadruleAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getBodyParams();

            if (empty($params)) {
                throw new \Siberian\Exception(__('Missing value'));
            }

            $value = $params['value'];

            $fwRule = (new \Firewall_Model_Rule())->find([
                'type' => \Firewall_Model_Rule::FW_TYPE_UPLOAD,
                'value' => $value
            ]);

            if ($fwRule->getId()) {
                $fwRule->delete();
            }

            $payload = [
                'success' => true,
                'message' => __('Rule have been removed.'),
            ];

        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }

    public function addfwuploadruleAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getBodyParams();

            if (empty($params)) {
                throw new \Siberian\Exception(__('Missing value'));
            }

            $value = trim($params['value']);

            if (empty($value)) {
                throw new \Siberian\Exception(__("Extension can't be empty."));
            }

            if (in_array($value, ['php', 'js', 'ico'])) {
                throw new \Siberian\Exception(__("Extension %s is strictly forbidden.", $value));
            }

            $fwRule = (new \Firewall_Model_Rule())->find([
                'type' => \Firewall_Model_Rule::FW_TYPE_UPLOAD,
                'value' => $value
            ]);

            if (!$fwRule->getId()) {
                $fwRule
                    ->setType(\Firewall_Model_Rule::FW_TYPE_UPLOAD)
                    ->setValue($value)
                    ->save();
            }

            $payload = [
                'success' => true,
                'message' => __('Rule have been added.'),
            ];

        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }
}