<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Codefog\EventsSubscriptionsBundle\Model\EventGroupModel;
use Codefog\EventsSubscriptionsBundle\Exporter;
use Codefog\EventsSubscriptionsBundle\ExporterHelper;
use Codefog\EventsSubscriptionsBundle\Model\CalendarGroupModel;
use Codefog\EventsSubscriptionsBundle\Services;
use Codefog\HasteBundle\Formatter;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\CalendarModel;
use Contao\Config;
use Contao\Controller;
use Contao\Date;
use Contao\Environment;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Contao\Widget;

class ExportCalendarController
{
    /**
     * @var Exporter
     */
    protected $exporter;

    /**
     * ExportController constructor.
     */
    public function __construct()
    {
        $this->exporter = Services::getExporter();
    }

    /**
     * Run the controller
     *
     * @return string
     */
    public function run()
    {
        // Export a single calendar, or all calendars of a calendar group
        if (Input::get('key') === 'subscriptions_exportGroup' && ($groupModel = CalendarGroupModel::findByPk(Input::get('id'))) !== null) {
            $subject = CalendarModel::findBy('calendarGroup', $groupModel->id)?->getModels() ?? [];
            $title = $groupModel->title;
        } elseif (Input::get('key') === 'subscriptions_exportEventGroup' && ($subject = EventGroupModel::findByPk(Input::get('id'))) !== null) {
            $title = $subject->title;
        } elseif (Input::get('key') === 'subscriptions_exportCalendar' && ($subject = CalendarModel::findByPk(Input::get('id'))) !== null) {
            $title = $subject->title;
        } else {
            Controller::redirect('contao/main.php?act=error');
        }

        // Only calendars the user may work with
        if (\is_array($subject)) {
            $subject = array_values(array_filter($subject, static fn ($calendar) => Access::calendar((int) $calendar->id)));

            if (!$subject) {
                Access::requireCalendar(0);
            }
        } else {
            Access::requireCalendar((int) ($subject instanceof EventGroupModel ? $subject->pid : $subject->id));
        }

        System::loadLanguageFile('tl_calendar');
        System::loadLanguageFile('tl_calendar_group');
        System::loadLanguageFile('tl_calendar_event_group');

        /** @var Widget $startDateWidget */
        $startDateWidget = new $GLOBALS['BE_FFL']['text'](Widget::getAttributesFromDca([
            'label' => &$GLOBALS['TL_LANG']['tl_calendar']['export.startDate'],
            'eval' => ['rgxp' => 'date'],
        ], 'startDate'));

        /** @var Widget $endDateWidget */
        $endDateWidget = new $GLOBALS['BE_FFL']['text'](Widget::getAttributesFromDca([
            'label' => &$GLOBALS['TL_LANG']['tl_calendar']['export.endDate'],
            'eval' => ['rgxp' => 'date'],
        ], 'endDate'));

        $formSubmit = 'events-subscriptions-calendar-export';

        // Process the form
        if (Input::post('FORM_SUBMIT') === $formSubmit) {
            $startDateWidget->validate();
            $endDateWidget->validate();

            if (!$startDateWidget->hasErrors() && !$endDateWidget->hasErrors()) {
                $this->processForm($subject, $title, $startDateWidget, $endDateWidget);
            }
        }

        return $this->createTemplate($subject, $title, $startDateWidget, $endDateWidget, $formSubmit)->parse();
    }

    /**
     * Create the template
     */
    protected function createTemplate(CalendarModel|EventGroupModel|array $subject, string $title, Widget $startDateWidget, Widget $endDateWidget, $formSubmit)
    {
        $calendarData = ['title' => $title];

        // Format the calendar data
        if ($subject instanceof CalendarModel) {
            foreach ($subject->row() as $k => $v) {
                $calendarData[$k] = System::getContainer()->get(Formatter::class)->dcaValue($subject::getTable(), $k, $v);
            }
        }

        $template = new BackendTemplate('be_events_subscriptions_export_calendar');
        $template->backUrl = Backend::getReferer();
        $template->calendar = $calendarData;
        $template->explanation = match (true) {
            $subject instanceof CalendarModel => null,
            $subject instanceof EventGroupModel => $GLOBALS['TL_LANG']['tl_calendar_event_group']['export.explanation'],
            default => $GLOBALS['TL_LANG']['tl_calendar_group']['export.explanation'],
        };
        $template->action = Environment::get('request');
        $template->formSubmit = $formSubmit;
        $template->excelFormatSupport = true;
        $template->startDate = $startDateWidget;
        $template->endDate = $endDateWidget;
        $template->datePickerFormat = Date::formatToJs(Config::get('dateFormat'));

        return $template;
    }

    /**
     * Process the form
     */
    protected function processForm(CalendarModel|EventGroupModel|array $subject, string $title, Widget $startDateWidget, Widget $endDateWidget)
    {
        if (isset($_POST['export_excel'])) {
            $format = Exporter::FORMAT_EXCEL;
        } elseif (isset($_POST['export_csv'])) {
            $format = Exporter::FORMAT_CSV;
        } else {
            Controller::reload();
        }

        $startTstamp = null;
        $endTstamp = null;

        // Determine the start date
        if ($startDateWidget->value) {
            try {
                $startTstamp = (new Date($startDateWidget->value, Date::getNumericDateFormat()))->tstamp;
            } catch (\OutOfBoundsException $e) {}
        }

        // Determine the end date
        if ($endDateWidget->value) {
            try {
                // Until the end of that day
                $endTstamp = (new Date($endDateWidget->value, Date::getNumericDateFormat()))->tstamp + 86399;
            } catch (\OutOfBoundsException $e) {}
        }

        $file = match (true) {
            $subject instanceof CalendarModel => $this->exporter->exportByCalendar($subject, $startTstamp, $endTstamp, $format),
            $subject instanceof EventGroupModel => $this->exporter->exportByEventGroup($subject, $startTstamp, $endTstamp, $format),
            default => $this->exporter->exportByCalendars($subject, $startTstamp, $endTstamp, $format),
        };

        $fileName = sprintf(
            '%s_%s.%s',
            StringUtil::standardize($title),
            StringUtil::standardize(Date::parse('Y-m-d')),
            $file->getExtension()
        );

        System::getContainer()->get(ExporterHelper::class)->download($file, $fileName);
    }
}
