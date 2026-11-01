<?php

namespace Codefog\EventsSubscriptionsBundle\Model;

use Contao\Model;

class SubscriptionFieldModel extends Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_calendar_events_subscription_field';

    /**
     * @return array<SubscriptionFieldModel> All fields including disabled ones, ordered by label
     */
    public static function findAllOrdered(): array
    {
        return static::findAll(['order' => 'label'])?->getModels() ?? [];
    }
}
