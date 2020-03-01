<?php

namespace App\Http\Controllers;

use App\TransactionsHistory;
use App\User;
use Illuminate\Http\Request;

class WithdrawFundsController extends Controller
{
    /**
     * it should let users withdraw payment
     * @param Request
     * return json
     */
    public function withdrawPayment(Request $request){
        //iniatilize constants
        $email =  $request->email;
        $amount = $request->amount;
        $password = $request->password;
        $payer = User::where('email', 'awazoneinfo@gmail.com')->get();

        //check the users balance
        $check = User::where('email', $email)->where('password', $password)->get();
        if(count($check)< 1){

            return response( ['message' => 'User not found'], 404);

        }else{
            //add the amount from user balance
            //check balance
            if(($payer[0]->balance) >= $amount){

                $subtract = $payer[0]->balance - $amount;
                User::where('email', 'awazoneinfo@gmail.com')->update(['balance' => $subtract]);
                $receiver = new TransactionsHistory;
                $receiver->amount = $amount;
                $receiver->description = $check[0]->username." Withdrawal to Awazone";
                $receiver->time = now();
                $receiver->user_id = $payer[0]->id;
                $saved = $receiver->save();


                if($saved){
                     //add payment to awazone user

                     $added = $check[0]->balance + $amount;
                     User::where('email', $email)->update(['balance'=> $added]);
                    
                     //input into transaction history
                     $transaction = new TransactionsHistory;
     
                     $transaction->amount = $amount;
                     $transaction->description = "Awazone.net withdrawal ";
                     $transaction->time = now();
                     $transaction->user_id = $check[0]->id;
     
                     $save = $transaction->save();
                
                if($save){
                    return response(['message'=> 'Payment made Successfully', 'status' => 'ok'], 200);
                }else{
                    return response(['message' => 'An error occur'], 417);
                }
                   
                }else{
                    return response(['message' => 'An error occur'], 501);
                }


            }else{
                return response(['message'=> 'Insufficient Balance'], 400);
            }
            
        }
    }
}
