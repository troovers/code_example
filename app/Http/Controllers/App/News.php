<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Model\App\Device;
use App\Model\Helpers\Date;
use App\Model\Helpers\Html;
use App\Model\Helpers\Notification;
use App\Model\Helpers\PageInfo;
use App\Model\App\News as News_Model;
use Auth;
use DB;
use Illuminate\Support\Facades\Input;
use Redirect;
use Illuminate\Http\Request;
use Validator;

class News extends BaseController
{

    /**
     * Show a list of news items
     *
     * @param Request $request
     * @return mixed
     */
	public function index(Request $request)
	{
        $page_info = new PageInfo($request, 'posted_at');
        $filters   = $page_info->get_filters();

		$query = News_Model::select(['news.*', DB::raw('CONCAT(first_name, \' \', last_name) AS author')])
            ->leftJoin('users', 'users.id', '=', 'news.user_id');

		if(!empty($request->get('search'))) {
            $query->whereRaw('(news.id LIKE \'%' . $request->get('search') . '%\'
                OR CONCAT(first_name, \' \', last_name) LIKE \'%' . $request->get('search') . '%\'
                OR title LIKE \'%' . $request->get('search') . '%\'
                OR content LIKE \'%' . $request->get('search') . '%\')');
        }

        $articles = $query->orderBy($filters->column, $filters->direction)->paginate(15);

		$buttons = [
		    'add' => 'news/edit',
            'delete' => ''
        ];

		return view('app.news.dashboard')->with([
		    'articles'    => $articles,
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
			$article = new News_Model();

            $article->posted_at = date('Y-m-d H:i:s');
		} else {
            $article = News_Model::find($id);
		}

		return view('app.news.edit', [
		    'action' => (is_null($id) ? 'add' : 'edit'),
            'article' => $article,
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
			'content' => 'required',
			'posted_at' => 'required|date'
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
				$article = News_Model::find($input['id']);
			} else {
                $article = new News_Model();
			}

			$notify = isset($input['new_notification']) && $input['new_notification'] == 1;

			$input['user_id'] = Auth::user()->id;

            // Parse the dates as valid formats
            $input['posted_at'] = Date::timestamp($input['posted_at'], date('H:m:s'));

            // Filter input
            Html::filter_input($input, ['new_notification']);

            $article->fill($input);

			if($article->save()) {

			    if($notify) {
			        $devices = Device::where('notifications', 1)->get(['id', 'token'])->toArray();

                    // Insert the notifications as unread by the devices
                    $values = [];

                    foreach($devices as $device) {
                        $values[] = [
                            'device_id' => $device['id'],
                            'news_id' => $article->id
                        ];
                    }

                    DB::table('news_unread')->insert($values);

                    // Send the notification
                    $notification = new Notification('Nieuw nieuwsbericht', Auth::user()->full_name() . ' heeft een nieuw artikel geplaatst');
                    $notification->add_data(['page' => 'news']);
                    $notification->send($devices);
                }

				$request->session()->flash('alert-success', 'De gegevens zijn opgeslagen');
				return Redirect::to('app/news');
			}

			$request->session()->flash('alert-danger', 'Er is iets mis gegaan, probeer het opnieuw');
			return Redirect::to('app/news');
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
            News_Model::find($id)->delete();

            $request->session()->flash('alert-success', 'De gegevens zijn verwijderd');
            return response()->json(['error' => false, 'redirect' => 'app/news']);
        } else {
            $input = Input::all();

            foreach ($input['articles'] as $article) {
                News_Model::where('id', $article)->delete();
            }

            $request->session()->flash('alert-success', 'De gegevens zijn verwijderd');
            return Redirect::to('app/news');
        }
    }
}
