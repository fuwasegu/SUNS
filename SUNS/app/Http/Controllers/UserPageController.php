<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class UserPageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request,$id)
    {   
        $user = DB::table('users')->where('id',$id)->first();
        $tweets = DB::table('tweets')->where('user_id',$id)->orderBy('updated_at','desc')->get();
        $fav = DB::table('tweet_fav')->where('user_id',$id)->get();
        return view('userpage',['user' => $user , 'tweets' => $tweets , 'favs' => $fav]);
    }

    public function userTweet(Request $request)
    {
        date_default_timezone_set("Asia/Tokyo"); // タイムゾーンを日本に設定
        $user = Auth::user();
        $now = date('Y/m/d H:i:s');

        DB::table('tweets')->insert([
            'user_id' => $user->id,
            'tweet' => $request->tweet,
            'updated_at' => $now
        ]);

        return redirect()->route('userpage',['id' => $user->id]);
    }

    public function deleteTweet(Request $request)
    {
        $user = Auth::user();

        DB::table('tweets')->where('id',$request->tweet_id)->delete();
        DB::table('tweet_fav')->where('id',$request->tweet_id)->delete();

        return redirect()->route('userpage',['id' => $user->id]);
    }


    public function tweetFav(Request $request)
    {
        $user = Auth::user();

        DB::table('tweet_fav')->insert([
            'user_id' => $user->id,
            'tweet_id' => $request->tweet_id
        ]);

        return redirect()->route('userpage',['id' => $user->id]);
    }

    public function tweetFavReset(Request $request)
    {
        $user = Auth::user();

        DB::table('tweet_fav')->where('user_id',$user->id)->where('tweet_id',$request->tweet_id)->delete();

        return redirect()->route('userpage',['id' => $user->id]);
    }
}
