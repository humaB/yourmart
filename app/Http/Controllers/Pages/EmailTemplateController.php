<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Mail\TestMail;
use App\Models\Setting\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailTemplateController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        return view('pages.email_template_page');
    }

    public function fectTemplates(){
        $data = EmailTemplate::with("added_name")->get();

        return (new ResponseCollection( $data ))
            ->response()
            ->setStatusCode(200);
    }

     public function store( Request $request ){

        EmailTemplate::create([
            'type'        => $request->type,
            'subject'     => $request->name,
            'body'        => $request->description,
            'added_by' => auth()->user()->id
        ]);

        return response()->json(['message' => 'Email Template saved successfully!'], 200);
    }

    public function update( Request $request )
    {
        EmailTemplate::where("id",$request->id)->update([
            'type'        => $request->type,
            'subject'     => $request->name,
            'body'        => $request->description,
            'added_by' => auth()->user()->id
        ]);

        return response()->json(['message' => 'Email Template saved successfully'], 200);
    }

    public function sendTestMail( Request $request ){

        $emailTemplate = EmailTemplate::find( $request->id);
        $toMail = $request->email;

        Mail::to( $toMail )->send(new TestMail($emailTemplate));

        return response()->json(['message' => 'Email Sent successfully'], 200);
    }
}
