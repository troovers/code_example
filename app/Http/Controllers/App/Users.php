<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mails\Mails;
use App\Model\Configuration;
use App\Model\Helpers\PageInfo;
use App\Model\Member;
use App\Model\Users\User;
use App\Notifications\Users\NewAppAccountNotification;
use Auth;
use DB;
use Hash;
use Illuminate\Support\Facades\Input;
use Redirect;
use Illuminate\Http\Request;
use Validator;

class Users extends BaseController
{


	public function index(Request $request)
	{
        $page_info = new PageInfo($request, 'users.id');
        $filters   = $page_info->get_filters();

		$query = User::select(['users.*', DB::raw('(SELECT logged_in FROM app_logins WHERE user_id = users.id ORDER BY logged_in DESC LIMIT 1) AS last_app_login'), DB::raw('IF(users.member_id IS NULL, users.email, members.email) as email'), DB::raw('IF(users.member_id IS NULL, ' . User::DISPLAY_NAME . ', ' . Member::DISPLAY_NAME . ') as display_name')])
            ->leftJoin('members', 'members.id', '=', 'users.member_id')
            ->with('devices');

		if(!empty($request->get('search'))) {
            $query->whereRaw('users.id LIKE \'%' . $request->get('search') . '%\'
                OR ' . User::DISPLAY_NAME . ' LIKE \'%' . $request->get('search') . '%\'
                OR users.email LIKE \'%' . $request->get('search') . '%\'');
        }

        $users = $query->orderBy($filters->column, $filters->direction)->paginate(15);

		$buttons = [
		    'add' => 'users/edit',
            'delete' => ''
        ];

		return view('app.users.dashboard')->with([
		    'users'   => $users,
            'filters' => $filters,
            'buttons' => $buttons
        ]);
	}


    /**
     * Edit an app user
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
	public function edit($id = null)
	{
	    $members = Member::select(['id', DB::raw('CONCAT(first_name, \' \', last_name) AS display_name')])->where('email', '!=', null)->get();

		if (is_null($id)) {
			$user = new User();
		} else {
			$user = User::find($id);

		    // A locked user cannot be edited by any other user, but itself
            if(is_null($user->member_id)) {
                session()->flash('alert-danger', 'Deze gegevens mogen niet bewerkt worden');
                return Redirect::to('/app/users');
            }
		}

		return view('app.users.edit', [
		    'action' => (is_null($id) ? 'add' : 'edit'),
            'members' => $members,
            'user' => $user,
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
			'member_id' => 'required|not_in:0',
		];

		# If a user is being created, we need to validate the password
		if(empty($input['id'])) {
			$validate['password'] = 'required';
			$validate['password-confirm'] = 'required|same:password';
		}

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
				$user = User::find($input['id']);

				if (!empty($input['password'])) {
					$user->password = Hash::make($input['password']);
				}

				$created = $user->save();
			} else {
                $member = Member::find($input['member_id']);

                $attributes = [
					'member_id' => $input['member_id'],
					'password' => Hash::make($input['password']),
                    'app_user' => 1,
                    'email' => $member->email
				];

				// Create a new user
				User::create($attributes);

				if ((int) Configuration::getSetting('send_mail_app_user_creation') == 1) {

				    $lines = [];
				    $lines[] = Auth::user()->full_name() . ' heeft een nieuw account voor je aangemaakt, voor de Geekk Club App. Je gegevens zijn:';
				    $lines[] = '<b>Gebruikersnaam</b>: ' . $member->email . '<br><b>Wachtwoord</b>: ' . $input['password'] . '<br>';
				    $lines[] = 'Je kunt de app downloaden voor zowel Apple als Android apparaten.';

				    // Send the notification about the new account
				    $member->notify(new NewAppAccountNotification('Accountgegevens App', 'Beste ' . $member->first_name . ',', $lines));
				}

				$created = true;
			}

			if($created) {
				$request->session()->flash('alert-success', 'De gegevens zijn opgeslagen');
				return Redirect::to('app/users');
			}

			$request->session()->flash('alert-danger', 'Er is iets mis gegaan, probeer het opnieuw');
			return Redirect::to('app/users');
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
            User::find($id)->delete();

            $request->session()->flash('alert-success', 'De gegevens zijn verwijderd');
            return response()->json(['error' => false, 'redirect' => 'app/users']);
        } else {
            $input = Input::all();

            foreach ($input['users'] as $user) {
                User::where('id', $user)->delete();
            }

            $request->session()->flash('alert-success', 'De gegevens zijn verwijderd');
            return Redirect::to('app/users');
        }
    }
}
