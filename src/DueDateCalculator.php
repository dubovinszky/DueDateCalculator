<?php
namespace Dubovinszky\DueDateCalculator;
use Exception;

class DueDateCalculator
{
    private $submitTimestamp = 0;
    const WORKINGDAYSTART = 9;
    const WORKINGDAYEND = 17;
    const SATURDAY = 6;

    public function CalculateDueDate($submitDate = '', $turnaroundTime = 0)
    {
        $this->submitTimestamp = strtotime($submitDate);
        $this->ValidateSubmitDate();

        $turnaroundTimeInSeconds = $this->GetTurnaroundTimeInSeconds($turnaroundTime);

        return $this->GetDueDate($turnaroundTimeInSeconds);
    }

    private function ValidateSubmitDate()
    {
        $submitHour = date('H', $this->submitTimestamp);
        if (($submitHour < self::WORKINGDAYSTART) || ($submitHour > self::WORKINGDAYEND))
        {
            throw new Exception('You can only report bugs in working hours!');
        }

        if (date('N', $this->submitTimestamp) >= self::SATURDAY)
        {
            throw new Exception('You can only report bugs on working days!');
        }
    }

    private function GetTurnaroundTimeInSeconds($turnaroundTime = 0)
    {
        return $turnaroundTime * 60 * 60;
    }

    private function GetTimeUntilEndOfDay()
    {
        return mktime(self::WORKINGDAYEND, 0, 0, date('n', $this->submitTimestamp), date('j', $this->submitTimestamp), date('Y', $this->submitTimestamp)) - $this->submitTimestamp;
    }

    private function GetNextWorkingDayStart()
    {
        $nextDay =  mktime(self::WORKINGDAYSTART, 0, 0, date('n', $this->submitTimestamp), date('j', $this->submitTimestamp) + 1, date('Y', $this->submitTimestamp));

        if (date('N', $nextDay) == self::SATURDAY)
        {
            return mktime(self::WORKINGDAYSTART, 0, 0, date('n', $this->submitTimestamp), date('j', $this->submitTimestamp) + 3, date('Y', $this->submitTimestamp));
        }

        return $nextDay;
    }

    private function GetDueDate($turnaroundTimeInSeconds)
    {
        $timeUntilEndOfDay = $this->GetTimeUntilEndOfDay();
        if ($timeUntilEndOfDay < $turnaroundTimeInSeconds)
        {
            $this->submitTimestamp = $this->GetNextWorkingDayStart();
            return $this->GetDueDate(($turnaroundTimeInSeconds - $timeUntilEndOfDay));
        }

        return date('Y-m-d H:i',  + $this->submitTimestamp + $turnaroundTimeInSeconds);
    }
}