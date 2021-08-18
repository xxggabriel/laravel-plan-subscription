<?php 

namespace Xxggabriel\LaravelPlanSubscription\Services;

use Carbon\Carbon;

class Period {

    private $interval;
    private $period;
    private $start;
    private $end;

    public function __construct($interval = 'month', $period = 1, $start = '')
    {
        if(empty($start)){
            $this->start = Carbon::now();
        } else if($start instanceof Carbon){
            $this->start = Carbon::now($this->start);
        } else {
            $this->start = $start;
        }
        $this->interval = $interval;
        $this->period = $period;
        $start = clone $this->start;

        $method = 'add'. ucfirst($this->interval). 's';
        $this->end = $start->{$method}($this->period);
    }

    public function getInterval()
    {
        return $this->interval;
    }

    public function getPeriod()
    {
        return $this->period;
    }

    public function getStartDate()
    {
        return $this->start;
    }

    public function getEndDate()
    {
        return $this->end;
    }

}