<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Safaricom\Mpesa\Mpesa;

class PaymentsController extends Controller
{
    //

    public function index(Request  $request){

        \Log::info($request->all());

     dd($request->all()["Body"]["stkCallback"]["ResultCode"]);
     dd($request->all()["Body"]["stkCallback"]["CallbackMetadata"]["Item"][1]["Value"]);




    // if($request->all()["Body"]["stkCallback"]["ResultCode"]==0) {
    //     $member = Member::where('checkoutid', $request->all()["Body"]["stkCallback"]["CheckoutRequestID"])->first();
    //     $payment = Payment::where('reference', $request->all()["Body"]["stkCallback"]["CheckoutRequestID"])->first();
    //     $payment->mpesaref=($request->all()["Body"]["stkCallback"]["CallbackMetadata"]["Item"][1]["Value"]);
    //     $payment->save();

    //         if ($member) {

    //             User::where('id',$member->user_id)->update(["feepaid"=>1]);

    //         $data = array(
    //             'data' =>
    //                 array(
    //                     0 =>
    //                         array(
    //                             'message_bag' =>
    //                                 array(
    //                                     'numbers' => $member->phonenumber,
    //                                     'message' => "Dear ".$member->name." Thank you for registering with PDP.\n Your application is under review.",
    //                                     'sender' => 'DEPTHSMS',
    //                                 ),
    //                         ),
    //                 ),
    //         );
    //         self::sendSms($data);

    //         }else{

    //             //invlid checkout id
    //         }


    // }else{
    //     //payment gone wrong

    // }



    }


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


    public function complete($id){

       $user= User::find($id);

        return view("membership.reset")->with(["userdata"=>$user]);

    }

    public function update(Request  $request){

        $user= User::find($request->id);

        Auth::login($user);

        //return redirect("/dashboard");
        return redirect("/home");
       // return view("membership.reset")->with(["userdata"=>$user]);

    }


    public function verify_view(){
        return view("membership.verify");
    }

    public function notify_event(){
        $events = \DB::table('events')->get();
        return view("events.notify", ['events' => $events]);
    }

    public function verify(Request $request){

       $member=User::where("docnumber",$request->docnumber)->where("name",$request->firstname.' '.$request->lastname)->where("verified",1)->first();

        $result=["status"=>"danger","message"=>ucfirst($request->firstname.' '.$request->lastname)." is not a PDP member"] ;



       if($member){

           $result=["status"=>"success","message"=>ucfirst($request->firstname.' '.$request->lastname).' is a verified PDP Member'];



       }
        return view("membership.verify")->with(["userdata"=>$result]);

    }

     public function donate(Request $request){


        return view("membership.donate");

     }


     public function getinv(Request $request){

         return view("membership.involved");

     }
        public function lipaNaMpesaPassword()
        {
                //timestamp
                $timestamp = Carbon::rawParse('now')->format('YmdHms');
                //passkey
                $passKey ="bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
                $businessShortCOde =174379;
                //generate password
                $mpesaPassword = base64_encode($businessShortCOde.$passKey.$timestamp);

                return $mpesaPassword;
            
        }
        public function newAccessToken()
        {
                $consumer_key="2sh2YA1fTzQwrZJthIrwLMoiOi3nhhal";
                $consumer_secret="CKaCnw224K4Lc56w";
                $credentials = base64_encode($consumer_key.":".$consumer_secret);
                $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";


                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic ".$credentials,"Content-Type:application/json"));
                curl_setopt($curl, CURLOPT_HEADER,false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $curl_response = curl_exec($curl);
                $access_token=json_decode($curl_response);
                curl_close($curl);
            
                return $access_token->access_token;  
            
        }

     public function donateaction(Request $request){  
        $amount = $request->amount; 
        $phoneNumber = $request->phonenumber; 
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $curl_post_data = [
            'BusinessShortCode' =>174379,
            'Password' => $this->lipaNaMpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phoneNumber, 
            'PartyB' => 174379,
            'PhoneNumber' => $phoneNumber,  
            'CallBackURL' => 'https://7db63514dbd1.ngrok.io/api/stk/push/callback/url',
            'AccountReference' => "PDP DONATION",
            'TransactionDesc' => "PAYBILL"
        ];


        $data_string = json_encode($curl_post_data);


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->newAccessToken()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);  

        $data=json_decode($curl_response);
        
        if( isset($data->CheckoutRequestID)) {
            //key exists, do stuff
            Payment::create([
                "reference"=>$data->CheckoutRequestID,
                "amount"=>$request->amount,
                "payment_method"=>"MPESA",
                "payment_type"=>"DONATION",
                "payer"=>$request->firstname.' '.$request->lastname,
            ]);

            $status='success';
            $message="Donation Request  Processed  successfully please check your phone to complete the transaction";
        }else{
            $message=$data->errorMessage;
            $status='error';
        }

        \Log::info($curl_response);
           \Log::info(env("APP_URL")."/api/payment_result");



        return back()->with($status,$message);

    }


    public function getinvaction(Request $request){

        //send mail here


        $options = $request->all();



        $validator = Validator::make($options, [
            'firstname' => 'required',
            'lastname'=>'required',
            'phonenumber' => 'required',
            'constituency' => 'required',
            'county' => 'required',
           // 'email' => 'required',
            'activities'=>'required'

        ])->validate();

        $options['activities']=json_encode($options['activities']);

        \App\Models\Volunteer::Create($options);


        return back()->with('success','Request Processed  successfully. We will reach out soon');
    }



    public function testMpesa(Request $request){


        $mpesa= new Mpesa();

        $LipaNaMpesaPasskey="bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $TransactionType="CustomerPayBillOnline";
        $Amount=$request->amount;
        $PartyA=$request->phonenumber;
        $PartyB=174379;
        $PhoneNumber=$request->phonenumber;
        $CallBackURL=env("APP_URL")."/api/payment_result";
        $AccountReference="";
        $TransactionDesc="PAYBILL";
        $Remarks="PDP";
        $stkPushSimulation=$mpesa->STKPushSimulation(174379, $LipaNaMpesaPasskey, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remarks);
        \Log::info($stkPushSimulation);



       return $stkPushSimulation;

    }





}
