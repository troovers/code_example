<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Model\App\Device;
use App\Model\Helpers\Html;
use App\Model\Helpers\Notification;
use App\Model\Helpers\PageInfo;
use App\Model\Member;
use App\Model\Poll;
use App\Model\Polls\Vote;
use App\Model\Polls\Answer;
use Auth;
use DB;
use Illuminate\Support\Facades\Input;
use Redirect;
use Illuminate\Http\Request;
use Validator;

class Polls extends BaseController
{

    /**
     * Show a list of polls
     *
     * @param Request $request
     * @return mixed
     */
	public function index(Request $request)
	{
        $page_info = new PageInfo($request, 'id');
        $filters   = $page_info->get_filters();

        $unvoted = '(SELECT COUNT(poll_id) FROM polls_unvoted WHERE poll_id = polls.id)';
        $voted   = '(SELECT COUNT(poll_id) FROM poll_votes WHERE poll_id = polls.id)';

		$query = Poll::select(['polls.*', DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS display_name'), DB::raw('(' . $voted . ' / (' . $voted . ' + ' . $unvoted . ')) * 100 AS percentage_voted')])
            ->leftJoin('users', 'users.id', '=', 'polls.created_by');

		if(!empty($request->get('search'))) {
            $query->whereRaw('(polls.id LIKE \'%' . $request->get('search') . '%\'
                OR CONCAT(first_name, \' \', last_name) LIKE \'%' . $request->get('search') . '%\'
                OR question LIKE \'%' . $request->get('search') . '%\')');
        }

        $polls = $query->orderBy($filters->column, $filters->direction)->paginate(15);

		$buttons = [
		    'add' => 'polls/edit',
            'delete' => ''
        ];

		return view('app.polls.dashboard')->with([
		    'polls'   => $polls,
            'filters' => $filters,
            'buttons' => $buttons
        ]);
	}


    /**
     * Edit a poll
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
	public function edit($id = null)
	{
		if (is_null($id)) {
			$poll = new Poll();

            $x_axis = [];
			$votes   = [];
			$percentages = [];
			$total_votes = 0;
			$unvoted = 0;
            $most_voted = [];
		} else {
            $poll = Poll::find($id);

            // Get the answer id's
            $answers = Answer::select(['id', 'answer'])->where('poll_id', $poll->id)->get();

            // Get the answer
            //$x_axis = Answer::where('poll_id', $poll->id)->orderBy('id')->pluck('answer')->toJson();

            $total_votes = Vote::where('poll_id', $poll->id)->count();

            $x_axis = [];
            $votes  = [];
            $percentages = [];

            foreach($answers as $answer) {
                $x_axis[] = $answer->answer;

                $count    = Vote::select('poll_id')->where('answer_id', $answer->id)->count();
                $votes[]  = $count;

                $percentages[] = $total_votes != 0 ? number_format(($count / $total_votes) * 100, 1) : 0;
            }

            // Get the most voted answers
            $most_voted = Vote::getMostVoted($poll->id);

            $unvoted = DB::table('polls_unvoted')
                ->select([DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS full_name')])
                ->leftJoin('users', 'users.id', '=', 'polls_unvoted.user_id')
                ->where('poll_id', $poll->id)
                ->pluck('full_name')
                ->toArray();
		}

        $x_axis = json_encode($x_axis);
        $votes = json_encode($votes);
        $percentages = json_encode($percentages);

		return view('app.polls.edit', [
		    'action' => (is_null($id) ? 'add' : 'edit'),
            'poll' => $poll,
            'x_axis' => $x_axis,
            'votes' => $votes,
            'percentages' => $percentages,
            'total_votes' => $total_votes,
            'unvoted'    => $unvoted,
            'most_voted' => $most_voted
        ]);
	}


    /**
     * Function which saves the poll
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
	public function save(Request $request)
	{
		$input = Input::all();

		# Set the values that need validation
		$validate = [
			'question' => 'required',
            'deadline' => 'required|date'
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
				$poll = Poll::find($input['id']);
			} else {
                $poll = new Poll();
			}

			// Change the date format
			$input['deadline'] = date('Y-m-d', strtotime($input['deadline']));

			$notify = isset($input['new_notification']) && $input['new_notification'] == 1;

			// Only store the creator when the poll is begin made for the first time
			if(empty($input['id'])) {
			    $input['created_by'] = Auth::user()->id;
            }

            $voters  = isset($input['voters']) ? $input['voters'] : null;
            $answers = isset($input['answers']) ? $input['answers'] : null;
            $id      = $input['id'];

            // Filter input
            Html::filter_input($input, ['new_notification', 'voters', 'answers']);

            $poll->fill($input);

			if($poll->save()) {
                if(empty($id)) {
                    // Add voters to the poll
                    $this->add_voters($poll, $voters);

                    // Add answers to the poll
                    $this->add_answers($poll, $answers);
                }

			    if($notify) {
			        $devices = Device::where('notifications', 1)->get(['id', 'token'])->toArray();

                    // Send the notification
                    $notification = new Notification('Nieuwe poll', Auth::user()->full_name() . ' heeft een nieuwe poll geplaatst');
                    $notification->add_data(['page' => 'polls']);
                    $notification->send($devices);
                }

				$request->session()->flash('alert-success', 'De gegevens zijn opgeslagen');
				return Redirect::to('app/polls/edit/' . $poll->id);
			}

			$request->session()->flash('alert-danger', 'Er is iets mis gegaan, probeer het opnieuw');
			return Redirect::to('app/polls');
		}
	}


    /**
     * Add the voters to the poll
     *
     * @param Poll $poll
     * @param integer $selection
     */
	private function add_voters($poll, $selection) {

	    $members = [];

	    if($selection == 1) {
            // Add all of the members
            $members = Member::all_members_with_device();
        } elseif($selection == 2) {
            // Add only the youth members
            $members = Member::youth_members_with_device();
        } elseif($selection == 3) {
            // Add only the senior members
            $members = Member::senior_members_with_device();

            dd($members);
        }

        $values = [];

        foreach($members as $member) {
            $values[] = [
                'poll_id' => $poll->id,
                'user_id' => $member->user_id
            ];
        }

        // Insert the unvoted users
        DB::table('polls_unvoted')->insert($values);
    }


    /**
     * Add answers to the poll
     *
     * @param Poll $poll
     * @param array $answers
     */
    private function add_answers($poll, $answers) {

        // Insert the answers
        foreach($answers as $answer) {
            Answer::create([
                'poll_id' => $poll->id,
                'answer' => ucfirst($answer)
            ]);
        }
    }


    /**
     * Function which deletes a poll
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
	public function delete(Request $request)
    {
        $id = $request->get('id', null);

        if(!is_null($id)) {
            Poll::find($id)->delete();

            $request->session()->flash('alert-success', 'De gegevens zijn verwijderd');
            return response()->json(['error' => false, 'redirect' => 'app/polls']);
        } else {
            $input = Input::all();

            foreach ($input['polls'] as $poll) {
                Poll::where('id', $poll)->delete();
            }

            $request->session()->flash('alert-success', 'De gegevens zijn verwijderd');
            return Redirect::to('app/polls');
        }
    }
}
