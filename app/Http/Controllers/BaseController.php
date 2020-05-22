<?php

 namespace App\Http\Controllers;
use App\Services\FirebaseService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $firebase;
    public $database;
    public $client;
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebase = $firebaseService->firebase;
        $this->database = $this->firebase->getDatabase();
    }
}
