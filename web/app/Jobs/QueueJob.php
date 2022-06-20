<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class QueueJob extends Job implements ShouldQueue, Runnable
{
    use InteractsWithQueue, SerializesModels;

    protected $data;

    /**
     * SendMessageToQueueJob constructor.
     *
     * @param $properties
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Handler
     */
    public function handle() {

        $this->run();
    }
}
