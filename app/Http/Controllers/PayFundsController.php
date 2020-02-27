<?php

namespace App\Http\Controllers;

use App\TransactionsHistory;
use App\User;

use Illuminate\Http\Request;

class PayFundsController extends Controller
{
    //
    /**
     * it should let users make payment
     * @param Request
     * return json
     */
    public function makePayment(Request $request){
        //iniatilize constants
        $email =  $request->email;
        $amount = $request->amount;
        $password = $request->password;
        $getter = User::where('username', 'Awazone')->get();

        //check the users balance
        $check = User::where('email', $email)->where('password', $password)->get();
        if(count($check)< 1){

            return response( ['message' => 'User not found'], 404);

        }else{
            //subtract the amount from users balance
            //check balance
            if(($check[0]->balance) >= $amount){
                $subtract = $check[0]->balance - $amount;
                User::where('email', $email)->update(['balance'=> $subtract]);
               
                //input into transaction history
                $transaction = new TransactionsHistory();

                $transaction->amount = $amount;
                $transaction->description = "Awazone.net Payment ";
                $transaction->time = now();
                $transaction->user_id = $check[0]->id;

                $saved = $transaction->save();

                if($saved){
                     //add payment to awazone
                $add = $getter[0]->balance + $amount;
                User::where('username', 'Awazone')->update(['balance' => $add]);
                $receiver = new TransactionsHistory();
                $receiver->amount = $amount;
                $receiver->description = $check[0]->username." Payment from Awazone";
                $receiver->time = now();
                $receiver->user_id = $getter[0]->id;
                $save = $receiver -> save();
                if($save){
                    return response(['message'=> 'Payment made Successfully'], 200);
                }else{
                    return response(['message' => 'An error occur'], 417);
                }
                   
                }else{
                    return response(['message' => 'An error occur'], 501);
                }


            }else{
                return response(['message'=> 'Insufficient Balance'], 402);
            }
            
        }
    }
}
