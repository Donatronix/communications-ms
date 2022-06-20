<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Listeners\AlarmWarehouseEmailListener;

/**
 * Class AlarmWarehouseEmail
 *
 * @package App\Console\Commands
 */
class AlarmWarehouseEmail extends Command
{
    /**
     * Command name.
     *
     * @var string
     */
    protected $signature = 'alarmWarehouseEmail:handle';

    /**
     * Description.
     *
     * @var string
     */
    protected $description = 'Alarm warehouse email: handle';

    /**
     * AlarmWarehouseEmail constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $alarmWarehouseEmail = new AlarmWarehouseEmailListener();

        $data = [
            'display_name' => 'Test name',
            'status' => 1,
            'operationId' => 1,
            'warehouse' => [
                'id' => 1,
                'name' => 'Test name',
                'owner' => 1,
                'ownerID' => 1,
                'balance' => 0
            ],
            'productId' => 1,
            'optionProductId' => 1,
            'userId' => 1
        ];

        $alarmWarehouseEmail->handle($data);
    }
}
