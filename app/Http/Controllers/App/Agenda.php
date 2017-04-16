<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Model\App\Device;
use App\Model\Helpers\Date;
use App\Model\Helpers\Html;
use App\Model\Helpers\Notification;
use App\Model\Helpers\PageInfo;
use App\Model\App\Agenda as Agenda_Model;
use Auth;
use DB;
use Illuminate\Support\Facades\Input;
use Redirect;
use Illuminate\Http\Request;
use Validator;

class Agenda extends BaseController
{

    /**
     * Show a list of agenda items
     *
     * @param Request $request
     * @return mixed
     */
	public function index(Request $request)
	{
        $page_info = new PageInfo($request, 'start');
        $filters   = $page_info->get_filters();

		$query = Agenda_Model::select(['*', DB::raw('CONCAT(address, \', \', zip_code, \' \', city) AS location')]);

		if(!empty($request->get('search'))) {
            $query->whereRaw('(id LIKE \'%' . $request->get('search') . '%\'
                OR start LIKE \'%' . $request->get('search') . '%\'
                OR end LIKE \'%' . $request->get('search') . '%\'
                OR title LIKE \'%' . $request->get('search') . '%\'
                OR description LIKE \'%' . $request->get('search') . '%\'
                OR address LIKE \'%' . $request->get('search') . '%\' 
                OR zip_code LIKE \'%' . $request->get('search') . '%\' 
                OR city LIKE \'%' . $request->get('search') . '%\')');
        }

        $query->where('end', '>=', date('Y-m-d H:i:s'));

        $agenda_items = $query->orderBy($filters->column, $filters->direction)->paginate(15);

		$buttons = [
		    'add' => 'agenda/edit',
            'delete' => ''
        ];

		return view('app.agenda.dashboard')->with([
		    'agenda_items'   => $agenda_items,
            'filters' => $filters,
            'buttons' => $buttons
        ]);
	}


    /**
     * Edit an agenda item
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
	public function edit($id = null)
	{
		if (is_null($id)) {
			$agenda_item = new Agenda_Model();

			$agenda_item->start = date('Y-m-d H:i:s');
			$agenda_item->end = date('Y-m-d H:i:s');
		} else {
		    $agenda_item = Agenda_Model::find($id);
		}

		return view('app.agenda.edit', [
		    'action' => (is_null($id) ? 'add' : 'edit'),
            'agenda_item' => $agenda_item,
        ]);
	}


    /**
     * Function which saves the user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
	public function save(Request $request)
	{
		$input = Input::all();

		# Set the values that need validation
		$validate = [
			'title' => 'required',
			'description' => 'required',
			'start' => 'required|date',
			'end' => 'required|date|after:start'
		];

		$validator = Validator::make($input, $validate);

		# Check if the validator fails
		if ($validator->fails()) {

			# Get the error messages
			$messages = $validator->messages();

			# Redirect our user back to the form with the errors from the validator
			return redirect()->back()
				->withInput($request->all())
				->withErrors($messages);

		} else {
			if (!empty($input['id'])) {
				$agenda_item = Agenda_Model::find($input['id']);
			} else {
                $agenda_item = new Agenda_Model();
			}

			$notify = isset($input['new_notification']) && $input['new_notification'] == 1;

			$input['user_id'] = Auth::user()->id;

            // Parse the dates as valid formats
            $input['start'] = Date::timestamp($input['start_date'], $input['start_time']);
            $input['end'] = Date::timestamp($input['end_date'], $input['end_time']);

            // Parse the zip code to a correct format
            $input['zip_code'] = str_replace(' ', '', $input['zip_code']);

            // Filter input
            Html::filter_input($input, ['start_date', 'start_time', 'end_date', 'end_time', 'new_notification']);

            $agenda_item->fill($input);

			if($agenda_item->save()) {

			    if($notify) {
			        $devices = Device::where('notifications', 1)->get(['id', 'token'])->toArray();

                    // Insert the notifications as unread by the devices
                    $values = [];

                    foreach($devices as $device) {
                        $values[] = [
                            'device_id' => $device['id'],
                            'agenda_id' => $agenda_item->id
                        ];
                    }

                    DB::table('agenda_unread')->insert($values);

                    // Send the notification
                    $notification = new Notification('Nieuw agenda-item', $agenda_item->title . ' is toegevoegd aan de agenda en staat gepland op ' . strftime('%e %B %Y', strtotime($agenda_item->start)) . ', vanaf ' . date('H:i', strtotime($agenda_item->start)));
                    $notification->add_data(['page' => 'agenda']);
                    $notification->send($devices);
                }

				$request->session()->flash('alert-success', 'De gegevens zijn opgeslagen');
				return Redirect::to('app/agenda');
			}

			$request->session()->flash('alert-danger', 'Er is iets mis gegaan, probeer het opnieuw');
			return Redirect::to('app/agenda');
		}
	}


    /**
     * Function which saves the user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
	public function delete(Request $request)
    {
        $id = $request->get('id', null);

        if(!is_null($id)) {
            Agenda_Model::find($id)->delete();

            $request->session()->flash('alert-success', 'De gegevens zijn verwijderd');
            return response()->json(['error' => false, 'redirect' => 'app/agenda']);
        } else {
            $input = Input::all();

            foreach ($input['agenda_items'] as $agenda_item) {
                Agenda_Model::where('id', $agenda_item)->delete();
            }

            $request->session()->flash('alert-success', 'De gegevens zijn verwijderd');
            return Redirect::to('app/agenda');
        }
    }
}
