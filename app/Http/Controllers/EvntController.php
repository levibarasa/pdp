<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Yajra\DataTables\DataTables;

class EvntController extends Controller
{
    static function  sendSms($data){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://ujumbesms.co.ke/api/messaging",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "X-Authorization: NzgzYzRhNWUyMDU5YjNhYjhhMzY2ODYzNzU3MTU1",
                "email: munenelewy77@gmail.com",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        \Log::info($response);


    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $events = \DB::table('events')->get();


            return Datatables::of($events)

                ->addIndexColumn()

                ->addColumn('action', function($row){
                    $url = url('events/'.$row->id);
                    $edit ="<a class='btn btn-primary btn-icon btn-sm'  href='events/".$row->id."/edit' title='Edit'> <i class='icon ion-ios-create mr-2'></i>Edit</a>";
                    $delete ="<button data-url='".$url."' onclick='deleteData(this)' class='btn btn-action btn-danger btn-xs' title='Disable'><i class='nav-icon fas fa-trash-alt'></i> Cancel</button>";
                return $edit." ".$delete;
            })

                ->rawColumns(['action'])
                ->editColumn('id','{{$id}}')
                ->make(true);

        }






        return view('admin.events');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Event::create([
            "events"=>$request->events,
            "name"=>$request->name,
            "eventype"=>$request->eventype,
            "description"=>$request->descrption,
            "location"=>$request->location
        ]);
        $users = \DB::table('users')->get();
        $data = array(
            'data' =>
                array(
                    0 =>
                        array(
                            'message_bag' =>
                                array(
                                    'numbers' => $users->phonenumber,
                                    'message' => "Dear ".$users->name." There will be a ".$request->name." meeting on  ".$request->events." at  ".$request->location. ".Kindly plan to attend",
                                    'sender' => 'DEPTHSMS',
                                ),
                        ),
                ),
        );
         self::sendSms($data);

         return back()->with('success','Event created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Event::where('id',$id)->get();
        if($data->count() > 0){
            return view('admin.updatevent', compact('data'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data =  Event::find($id);
        $data->name = $request->name;
        $data->eventype = $request->eventype;
        $data->location = $request->location;
        $data->description = $request->description;
        $data->events = $request->events;
      // $data->user_modified = Auth::user()->id;
       if($data->save()){
        //Toastr::success('Event Updated Successfully','Success');
        // return back()->with('success','Event Updated successfully');
           return redirect()->route('events.index')->with('success','Event Updated successfully');

       }else{
       // Toastr::error('Sorry, Event Can not be Updated!, Try again Later','Error');
           return redirect()->back();
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Event::find($id);
        if($data->delete()){
        return new JsonResponse(["status"=> true]);
        }else{
        return new JsonResponse(["status"=> false]);
        }
    }
}
