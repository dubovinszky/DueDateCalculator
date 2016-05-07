<?php
namespace Dubovinszky\DueDateCalculator;

class DueDateCalculatorTest extends \PHPUnit_Framework_TestCase
{   
    private $dueDateCalculator;
    private $validSubmitDate;

    protected function setUp()
    {
        $this->dueDateCalculator = new DueDateCalculator();
        $this->validSubmitDate = date('Y-m-d H:i', mktime(12, 0, 0, 5, 2, 2016));
    }

    public function testCalculateDueDateOutBeforeWorkingHours()
    {
        $submitDate = date('Y-m-d H:i', mktime(7, 0, 0, 5, 2, 2016));
        $this->setExpectedException('Exception');

        $this->dueDateCalculator->CalculateDueDate($submitDate, 1);
    }

    public function testCalculateDueDateOutAfterWorkingHours()
    {
        $submitDate = date('Y-m-d H:i', mktime(19, 0, 0, 5, 2, 2016));
        $this->setExpectedException('Exception');

        $this->dueDateCalculator->CalculateDueDate($submitDate, 1);
    }

    public function testCalculateDueDateOutOfWorkingDays()
    {
        $submitDate = date('Y-m-d H:i', mktime(16, 0, 0, 5, 7, 2016));
        $this->setExpectedException('Exception');

        $this->dueDateCalculator->CalculateDueDate($submitDate, 1);
    }

    public function testCalculateDueDateWithoutDayTurn()
    {
        $this->assertEquals('2016-05-02 13:00', $this->dueDateCalculator->CalculateDueDate($this->validSubmitDate, 1));
    }

    public function testCalculateDueDateWithtDayTurn()
    {
        $this->assertEquals('2016-05-03 12:00', $this->dueDateCalculator->CalculateDueDate($this->validSubmitDate, 8));
    }

    public function testCalculateDueDateWithtDaysTurn()
    {
        $this->assertEquals('2016-05-05 12:00', $this->dueDateCalculator->CalculateDueDate($this->validSubmitDate, 24));
    }

    public function testCalculateDueDateWithtWeeksTurn()
    {
        $this->assertEquals('2016-05-10 12:00', $this->dueDateCalculator->CalculateDueDate($this->validSubmitDate, 48));
    }

    public function testCalculateDueDateWithtTwoWeeksTurn()
    {
        $this->assertEquals('2016-05-16 12:00', $this->dueDateCalculator->CalculateDueDate($this->validSubmitDate, 80));
    }
}