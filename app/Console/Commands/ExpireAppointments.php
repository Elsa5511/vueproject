<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\AppointmentHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExpireAppointments extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set status to expired appointments';

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
        $appointments = Appointment::where(['status_name' => 'AWAITING_ACCEPTANCE', 'finished' => 0])->where('date_from', '<=', \Carbon\Carbon::now()->format('Y-m-d H:i:s'))->get();
        $admin = User::where(['role' => 'ADMIN'])->first();
        foreach ($appointments as $appointment) {
            DB::beginTransaction();
            try {
                $customer = $appointment->customer;
                $provider = $appointment->provider;

                $status = new \App\Models\AppointmentHistory();
                $status->status = 'EXPIRED';
                $status->appointment_id = $appointment->id;
                $status->user_id = $admin->id;
                $status->save();
                $appointment->status_name = $status->status;
                $appointment->finished = true;
                $appointment->current_status_id = $status->id;
                $appointment->save();


                $data = new \App\Models\Notification('Actualización de cita!');
                $data->setType('APPOINTMENT_UPDATED');
                $data->setMessage('La cita ha expirado!');
                $data->addAttribute('appointment_id', $appointment->id);
                $data->setSenderId($admin->id);
                $data->addAttribute('appointment', $appointment->toArray());
                $data->setAction('/reservations/' . $appointment->id);
                $customer->notify(new \App\Notifications\GenericNotification($customer, $data, 'APPOINTMENT_UPDATED'), ['broadcast']);
                $provider->notify(new \App\Notifications\GenericNotification($provider, $data, 'APPOINTMENT_UPDATED', ['broadcast']));
                DB::commit();
            } catch (\Exception $ex) {
                DB::rollback();
                $this->error($ex->getMessage());
            }
        }
        $this->info(sprintf('Expired %d appointments', count($appointments)));
    }

}
