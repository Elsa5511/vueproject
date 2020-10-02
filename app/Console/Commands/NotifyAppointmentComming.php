<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;

class NotifyAppointmentComming extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $appointments = Appointment::where(['finished' => 0, 'sms_reminder_sent' => 0])->where('date_from', '>', \Carbon\Carbon::now()->format('Y-m-d H:i:s'))->where('date_from', '<=', \Carbon\Carbon::now()->addMinutes(30)->format('Y-m-d H:i:s'))->get();
        foreach ($appointments as $appointment) {
            DB::transaction(function() use ($appointment) {
                $customer = $appointment->customer;
                $provider = $appointment->provider;

                \App\Lib\SMSManager::getInstance()->sendSMS($customer->completePhone(), 'Hola ' . $customer->name . ' tu cita con ' . $provider->name . ' es en menos de 30 minutos. Por favor estar pendiente. Detalles https://app.vaceate.com/#/reservations/' . $appointment->id);
                if ($appointment->status_name == 'PENDING') {
                    \App\Lib\SMSManager::getInstance()->sendSMS($provider->completePhone(), 'Hola ' . $provider->name . ' tu cita con ' . $customer->name . ' es en menos de 30 minutos. Por favor estar preparada. Detalles https://app.vaceate.com/#/reservations/' . $appointment->id);
                }
                
                $appointment->sms_reminder_sent=1;
                $appointment->save();
            });
        }
    }

}
