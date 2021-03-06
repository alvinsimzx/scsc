<?php

namespace App\Http\Controllers;
use Auth;
use App\Unit;
use App\Channel;
use App\Discussion;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ForumsController extends Controller
{
    public function index()
    {
        switch (request('filter')){

            case 'all-my-discussion':

                $results = Discussion::where('user_id', Auth::id())->paginate(3);

                break;

            case 'solved':

                $answered = array();

                foreach(Discussion::all() as $discussion){
                    if($discussion->hasBestAnswer()){
                        array_push($answered, $discussion);

                    }

                }

                $results= new Paginator($answered, 3);

                break;

                case 'unsolved':

                    $unanswered = array();
    
                    foreach(Discussion::all() as $discussion){
                        if(!$discussion->hasBestAnswer()){
                            array_push($unanswered, $discussion);
    
                        }
    
                    }
    
                    $results= new Paginator($unanswered, 3);
    
                    break;
            
            default:

                $results = Discussion::orderBy('created_at', 'desc')->paginate(3);

                break;


        }



        return view('forum', ['discussions' => $results]);
    }

    public function channel($slug)
    {
        $channel = Channel::where('slug', $slug)->first();

        return view('channel')->with('discussions', $channel->discussions()->paginate(5));
    }

    public function unit($slug)
    {
        $unit = unit::where('slug', $slug)->first();

        return view('unit')->with('discussions', $unit->discussions()->paginate(5));
    }
}
