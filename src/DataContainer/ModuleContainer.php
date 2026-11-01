<?php

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Contao\Database;
use Contao\DataContainer;
use Contao\Input;

class ModuleContainer
{
    /**
     * Hide the calendar picker of modules that show all calendars
     */
    public function adjustCalendarField(DataContainer|null $dc = null): void
    {
        if (!$dc?->id || 'edit' !== Input::get('act')) {
            return;
        }

        $module = Database::getInstance()->prepare('SELECT type, cal_allCalendars FROM tl_module WHERE id=?')->execute($dc->id);

        if (!$module->cal_allCalendars || !isset($GLOBALS['TL_DCA']['tl_module']['palettes'][$module->type])) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_module']['fields']['cal_calendar']['eval']['mandatory'] = false;
        $GLOBALS['TL_DCA']['tl_module']['palettes'][$module->type] = str_replace(',cal_calendar,', ',', $GLOBALS['TL_DCA']['tl_module']['palettes'][$module->type]);
    }
}
