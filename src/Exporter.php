<?php

namespace Codefog\EventsSubscriptionsBundle;

use Codefog\EventsSubscriptionsBundle\Features;
use Codefog\EventsSubscriptionsBundle\Event\ExportCalendarEvent;
use Codefog\EventsSubscriptionsBundle\Event\ExportEvent;
use Codefog\EventsSubscriptionsBundle\Model\PaymentModel;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentRequest;
use Codefog\EventsSubscriptionsBundle\Subscription\ExportAwareInterface;
use Codefog\EventsSubscriptionsBundle\Subscription\SubscriptionInterface;
use Codefog\EventsSubscriptionsBundle\Model\EventGroupModel;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Config;
use Contao\Controller;
use Contao\Database;
use Contao\Date;
use Contao\File;
use Contao\System;
use League\Csv\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Filesystem\Filesystem;

class Exporter
{
    const FORMAT_CSV = 'csv';
    const FORMAT_EXCEL = 'excel';

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var SubscriptionFactory
     */
    private $factory;

    /**
     * Exporter constructor.
     *
     * @param EventDispatcher     $eventDispatcher
     * @param SubscriptionFactory $factory
     */
    public function __construct(EventDispatcher $eventDispatcher, SubscriptionFactory $factory)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->factory         = $factory;
    }

    public function exportByEvent(CalendarEventsModel $event, $format = self::FORMAT_CSV): \SplFileInfo
    {
        $subscriptions = [];

        // Create the subscription instances
        if (($subscriptionModels = SubscriptionModel::findBy('pid', $event->id)) !== null) {
            /** @var SubscriptionModel $subscriptionModel */
            foreach ($subscriptionModels as $subscriptionModel) {
                try {
                    $subscription = $this->factory->createFromModel($subscriptionModel);
                } catch (\InvalidArgumentException $e) {
                    continue;
                }

                // Only the export aware subscriptions
                if ($subscription instanceof ExportAwareInterface) {
                    $subscriptions[] = $subscription;
                }
            }
        }

        $columns = $this->getColumns($subscriptions);
        $data = array_merge([$columns], $this->prepareData($event, $subscriptions, $columns));

        // Dispatch the event
        $this->eventDispatcher->dispatch(
            EventDispatcher::EVENT_ON_EXPORT,
            $event = new ExportEvent($data, $format, $event, $subscriptions)
        );

        $data = $event->getData();

        return match ($format = $event->getFormat()) {
            self::FORMAT_CSV => $this->exportToCsv($data),
            self::FORMAT_EXCEL => $this->exportToExcel($data),
            default => throw new \InvalidArgumentException(sprintf('Unsupported export type: %s', $format)),
        };
    }

    private function exportToCsv(array $data): \SplFileInfo
    {
        $csv = Writer::createFromString();

        foreach ($data as $row) {
            $csv->insertOne($row);
        }

        $fileInfo = new \SplFileInfo(sprintf('%s.csv', tempnam(sys_get_temp_dir(), 'export')));
        (new Filesystem())->dumpFile($fileInfo->getPathname(), $csv->toString());

        return $fileInfo;
    }

    private function exportToExcel(array $data): \SplFileInfo
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $rowIndex = 1;

        foreach ($data as $row) {
            $sheet->fromArray(array_values($row), null, 'A' . $rowIndex++);
        }

        $fileInfo = new \SplFileInfo(sprintf('%s.xlsx', tempnam(sys_get_temp_dir(), 'export')));

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileInfo->getPathname());

        return $fileInfo;
    }

    public function exportByCalendar(CalendarModel $calendar, $startTstamp = null, $endTstamp = null, $format = self::FORMAT_CSV): \SplFileInfo
    {
        [$data, $subscriptions] = $this->collectEventsData(CalendarEventsModel::findBy('pid', $calendar->id), $startTstamp, $endTstamp);

        // Dispatch the event
        $this->eventDispatcher->dispatch(
            EventDispatcher::EVENT_ON_EXPORT_CALENDAR,
            $event = new ExportCalendarEvent($data, $format, $calendar, $subscriptions)
        );

        $data = $event->getData();

        return match ($format = $event->getFormat()) {
            self::FORMAT_CSV => $this->exportToCsv($data),
            self::FORMAT_EXCEL => $this->exportToExcel($data),
            default => throw new \InvalidArgumentException(sprintf('Unsupported export type: %s', $format)),
        };
    }

    /**
     * Export the subscriptions of several calendars (e.g. a calendar group) into one file
     *
     * @param array<CalendarModel> $calendars
     */
    public function exportByCalendars(array $calendars, $startTstamp = null, $endTstamp = null, $format = self::FORMAT_CSV): \SplFileInfo
    {
        $ids = array_map(static fn (CalendarModel $calendar) => (int) $calendar->id, $calendars);
        $events = $ids ? CalendarEventsModel::findBy(['pid IN ('.implode(',', $ids).')'], [], ['order' => 'startTime']) : null;

        [$data] = $this->collectEventsData($events, $startTstamp, $endTstamp);

        return $this->exportData($data, $format);
    }

    /**
     * Export the subscriptions of all events of an event group into one file
     */
    public function exportByEventGroup(EventGroupModel $eventGroup, $startTstamp = null, $endTstamp = null, $format = self::FORMAT_CSV): \SplFileInfo
    {
        [$data] = $this->collectEventsData(CalendarEventsModel::findBy('eventGroup', (int) $eventGroup->id, ['order' => 'startTime']), $startTstamp, $endTstamp);

        return $this->exportData($data, $format);
    }

    private function exportData(array $data, string $format): \SplFileInfo
    {
        return match ($format) {
            self::FORMAT_CSV => $this->exportToCsv($data),
            self::FORMAT_EXCEL => $this->exportToExcel($data),
            default => throw new \InvalidArgumentException(sprintf('Unsupported export type: %s', $format)),
        };
    }

    /**
     * Get the export rows (header first) and the subscriptions of the given events
     *
     * @return array{0: array, 1: array}
     */
    private function collectEventsData($events, $startTstamp, $endTstamp): array
    {
        $data = [];
        $subscriptions = [];

        if ($events !== null) {
            $columns = ['pid IN (' . implode(', ', $events->fetchEach('id')) . ')'];
            $values = [];

            if ($startTstamp !== null) {
                $columns[] = 'dateCreated>=?';
                $values[] = $startTstamp;
            }

            if ($endTstamp !== null) {
                $columns[] = 'dateCreated<=?';
                $values[] = $endTstamp;
            }

            // Create the subscription instances
            if (($subscriptionModels = SubscriptionModel::findBy($columns, $values)) !== null) {
                /** @var SubscriptionModel $subscriptionModel */
                foreach ($subscriptionModels as $subscriptionModel) {
                    try {
                        $subscription = $this->factory->createFromModel($subscriptionModel);
                    } catch (\InvalidArgumentException $e) {
                        continue;
                    }

                    // Only the export aware subscriptions
                    if ($subscription instanceof ExportAwareInterface) {
                        $subscriptions[] = $subscription;
                    }
                }
            }

            $exportColumns = $this->getColumns($subscriptions);

            /** @var CalendarEventsModel $event */
            foreach ($events as $event) {
                $eventSubscriptions = [];

                // Get only the subscriptions for the currently iterated event
                foreach ($subscriptions as $subscription) {
                    if ((int) $event->id === (int) $subscription->getSubscriptionModel()->pid) {
                        $eventSubscriptions[] = $subscription;
                    }
                }

                if (count($eventSubscriptions) === 0) {
                    continue;
                }

                $data = array_merge($data, $this->prepareData($event, $eventSubscriptions, $exportColumns));
            }
        }

        if (isset($exportColumns)) {
            $data = [$exportColumns, ...$data];
        }

        return [$data, $subscriptions];

    }

    /**
     * Get the columns
     *
     * @param array $subscriptions
     *
     * @return array
     */
    private function getColumns(array $subscriptions)
    {
        $headerFields = $GLOBALS['TL_LANG']['MSC']['events_subscriptions.exportHeaderFields'];

        $columns = [
            'event_id'                 => $headerFields['event_id'],
            'event_title'              => $headerFields['event_title'],
            'event_group'              => $headerFields['event_group'],
            'event_start'              => $headerFields['event_start'],
            'event_end'                => $headerFields['event_end'],
            'subscription_date'        => $headerFields['subscription_date'],
            'subscription_type'        => $headerFields['subscription_type'],
            'subscription_waitingList' => $headerFields['subscription_waitingList'],
            'subscription_attended'    => Features::attendance() ? $headerFields['subscription_attended'] : null,
            'payment_status'           => $headerFields['payment_status'],
            'payment_amount'           => $headerFields['payment_amount'],
            'payment_method'           => $headerFields['payment_method'],
            'purchase_order'           => $headerFields['purchase_order'],
            'payment_reference'        => $headerFields['payment_reference'],
            'subscription_firstname'   => $headerFields['subscription_firstname'],
            'subscription_lastname'    => $headerFields['subscription_lastname'],
            'subscription_email'       => $headerFields['subscription_email'],
            'subscription_numberOfParticipants' => $headerFields['subscription_numberOfParticipants'],
        ];

        $columns = array_filter($columns, static fn ($label) => null !== $label);

        /** @var ExportAwareInterface $subscription */
        foreach ($subscriptions as $subscription) {
            foreach ($subscription->getExportColumns() as $name => $label) {
                if (!array_key_exists($name, $columns)) {
                    $columns[$name] = $label;
                }
            }
        }

        return $columns;
    }

    /**
     * Prepare the data
     *
     * @param CalendarEventsModel $event
     * @param array               $subscriptions
     * @param array               $columns
     *
     * @return array
     */
    private function prepareData(CalendarEventsModel $event, array $subscriptions, array $columns)
    {
        Controller::loadDataContainer('tl_calendar_events');
        System::loadLanguageFile('tl_calendar_events');

        $data      = [];
        $eventData = [
            'event_id'    => $event->id,
            'event_title' => $event->title,
            'event_group' => (string) Database::getInstance()->prepare('SELECT title FROM tl_calendar_event_group WHERE id=?')->execute((int) $event->eventGroup)->title,
            'event_start' => Date::parse(Config::get('datimFormat'), $event->startTime),
            'event_end'   => Date::parse(Config::get('datimFormat'), $event->endTime),
        ];

        // Get only the columns keys and make sure that values do not contain any data so we don't have
        // the indexes (after flip) as values upon merge later on
        $columns = array_map(
            function () {
                return '';
            },
            array_flip(array_keys($columns))
        );

        /** @var ExportAwareInterface|SubscriptionInterface $subscription */
        foreach ($subscriptions as $subscription) {
            $model = $subscription->getSubscriptionModel();
            $tmp = array_merge(
                $subscription->getExportRow(),
                $eventData,
                [
                    'subscription_date'        => Date::parse(Config::get('datimFormat'), $model->dateCreated),
                    'subscription_type'        => $GLOBALS['TL_DCA']['tl_calendar_events']['fields']['subscription_types']['reference'][$model->type],
                    'subscription_waitingList' => $subscription->isOnWaitingList() ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no'],
                    'subscription_attended'    => $model->attended ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no'],
                ],
                $this->getPaymentData($model),
            );

            $data[] = array_merge($columns, array_intersect_key($tmp, $columns));
        }

        return $data;
    }

    /**
     * Payment columns of a subscription, empty for free events
     */
    private function getPaymentData(SubscriptionModel $model): array
    {
        $payment = $model->payment ? PaymentModel::findByPk($model->payment) : null;

        return [
            'payment_status' => $model->paymentStatus ? ($GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentStatus'][$model->paymentStatus] ?? $model->paymentStatus) : '',
            'payment_amount' => $payment ? PaymentRequest::decimal((int) $payment->amount) : '',
            'payment_method' => $payment ? ($GLOBALS['TL_LANG']['tl_calendar_events']['subscription_paymentMethodsRef'][$payment->method] ?? $payment->method) : '',
            'purchase_order' => $payment?->purchaseOrder ?? '',
            'payment_reference' => $payment?->reference ?? '',
        ];
    }
}
