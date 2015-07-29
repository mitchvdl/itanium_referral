<?php
/**
 * File: Observer.php
 *
 * User: Mitch Vanderlinden
 * email: mitchvdl@gmail.com
 * Date: 22.07.15 14:20
 * Package: Itanium_Referral
 */

class Itanium_Referral_Model_Observer extends Varien_Object
{
    /**
     * Update reward points for customer, send notification
     *
     * @param Varien_Event_Observer $observer
     * @return Itanium_Referral_Model_Observer
     */
    public function saveReferralPoints($observer)
    {
        if (!Mage::helper('itanium_referral')->isEnabled()) {
            return;
        }

        $request = $observer->getEvent()->getRequest();
        $customer = $observer->getEvent()->getCustomer();
        $data = $request->getPost('referral');
        if ($data) {
            if (!isset($data['store_id'])) {
                if ($customer->getStoreId() == 0) {
                    $data['store_id'] = Mage::app()->getAnyStoreView()->getStoreId();
                } else {
                    $data['store_id'] = $customer->getStoreId();
                }
            }
            $reward = Mage::getModel('itanium_referral/reward')
                ->setCustomer($customer)
                ->setWebsiteId(Mage::app()->getStore($data['store_id'])->getWebsiteId())
                ->loadByCustomer();
            if (!empty($data['points_delta'])) {
                $reward->addData($data)
                    ->setAction(Itanium_Referral_Model_Reward::REFERRAL_ACTION_ADMIN)
                    ->setActionEntity($customer)
                    ->updateRewardPoints();
            } else {
                $reward->save();
            }
        }
        return $this;
    }

    /**
     * Update reward notifications for customer
     *
     * @param Varien_Event_Observer $observer
     * @return Itanium_Referral_Model_Observer
     */
    public function saveReferralNotifications($observer)
    {
        if (!Mage::helper('itanium_referral')->isEnabled()) {
            return;
        }

        $request = $observer->getEvent()->getRequest();
        $customer = $observer->getEvent()->getCustomer();

        $data = $request->getPost('referral');
        // TODO: revise if this is needed
        $subscribeByDefault = (int)Mage::helper('itanium_referral')
            ->getNotificationConfig('subscribe_by_default', (int)$customer->getWebsiteId());
        if ($customer->isObjectNew()) {
            $data['referral_update_notification']  = $subscribeByDefault;
            $data['referral_warning_notification'] = $subscribeByDefault;
        }

        $customer->setData('itanium_referral_update_not', !empty($data['referral_update_notification']) ? 1 : 0);
        $customer->setData('itanium_referral_warn_not', !empty($data['referral_warning_notification']) ? 1 : 0);

        return $this;
    }
}
 