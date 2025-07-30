<?php

namespace App\Http\Controllers;

use App\Models\Point;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    
    function isValidSignature(Request $request, $secret)
    {
        $timestamp = $request->header('X-Timestamp');
        $signature = $request->header('X-Signature');
        $body = $request->getContent();
        $dataToSign = $timestamp . $body;
        $calculated = hash_hmac('sha256', $dataToSign, $secret);

        return hash_equals($signature, $calculated);
    }

    public function store(Request $request)
    {
        $secret = 'verysecret';

        if (!$this->isValidSignature($request, $secret)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
            'transacted_at' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        $transaction = Transaction::create([
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'transacted_at' => $request->transacted_at,
        ]);

        $points = floor($request->amount / 1000);
        Point::create([
            'transaction_id' => $transaction->id,
            'points' => $points
        ]);

        return response()->json([
            'code' => 201,
            'message' => 'Transaction created successfully',
            'data' => $transaction
        ], 201);
    }

    public function index(Request $request)
    {
        // dd($request->all());
        $perPage = (int) $request->input('per_page', 10);
        $query = Transaction::with('point');

        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('transacted_at', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        }

        $paginator = $query->paginate($perPage);

        $data = $paginator->getCollection()->transform(function ($trx) {
            return [
                'user_id' => $trx->user_id,
                'amount' => $trx->amount,
                'points' => $trx->point->points ?? 0,
                'description' => $trx->description,
                'transacted_at' => $trx->transacted_at,
            ];
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'total_pages' => $paginator->lastPage()
            ]
        ]);
    }

    public function seed(Request $request)
    {
        // $secret = 'verysecret';

        // if (!isValidSignature($request, $secret)) {
        //     return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        // }

        $transactions = [];

        for ($i = 0; $i < 1000; $i++) {
            $amount = rand(10000, 500000);
            $transaction = Transaction::create([
                'user_id' => 1,
                'amount' => $amount,
                'description' => 'Transaction ' . $i,
                'transacted_at' => now()->subDays(rand(0, 365))
            ]);

            Point::create([
                'transaction_id' => $transaction->id,
                'points' => floor($amount / 1000)
            ]);

            $transactions[] = $transaction;
        }

        return response()->json([
            'code' => 201,
            'message' => 'Seed 1000 transactions successfully',
            'count' => count($transactions)
        ], 201);

    }
}