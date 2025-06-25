<?php

namespace App\Http\Controllers;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\Inventory\Order\Order;
use App\Http\Resources\ResponseCollection;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        return view('tickets');
    }

    public function fetchTickets(Request $request)
    {
       // Start query with base conditions (fetching only tickets added by the authenticated user)
       $query = Ticket::with('added_by_name.dropshipper', 'order.shop');

       // Apply filters if they are provided in the request

       // Filter by status
       if ($request->has('status') && $request->status != '') {
           $query->where('status', $request->status);
       }

       // Filter by ticket number or order number
       if ($request->has('ticket_number_type') && $request->ticket_number_type != '') {
           if ($request->ticket_number_type == 'yourmart_ticket_number' && $request->ticket_number != '') {
               // Filter by YourMart Ticket Number
               $query->where('id', $request->ticket_number);
           } elseif ($request->ticket_number_type == 'order_number' && $request->ticket_number != '') {
               // Filter by Order Number
               $query->where('order_no', $request->ticket_number);
           }
       }

       // Fetch filtered tickets
       $tickets = $query->orderBy('id', 'desc')->get();

       // Return the tickets as a response collection with a 200 status code
       return (new ResponseCollection($tickets))
           ->response()
           ->setStatusCode(200);
    }

    public function getTicketStatusCounts(Request $request)
    {
        // Count tickets by each status
        $totalTickets = Ticket::count();
        $awaitingYourReply = Ticket::where('status', 'Awaiting Your Reply')->count();
        $awaitingYourMartReply = Ticket::where('status', 'Awaiting YourMart Reply')->count();
        $closed = Ticket::where('status', 'Closed')->count();
        $expired = Ticket::where('status', 'Expired')->count();
        $reviewed = Ticket::where('status', 'Reviewed')->count();
        $inProcess = Ticket::where('status', 'In-Process')->count();

        // Return the status counts as a JSON response
        return response()->json([
            'total_tickets' => $totalTickets,
            'awaiting_your_reply' => $awaitingYourReply,
            'awaiting_yourmart_reply' => $awaitingYourMartReply,
            'closed' => $closed,
            'expired' => $expired,
            'reviewed' => $reviewed,
            'in_process' => $inProcess,
        ], 200);
    }

    public function storeMessage(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'chatMessage' => 'required|string',
            'selectedStatus' => 'required|string',
            'ticketId' => 'required',
            'selectedFile' => 'nullable|file|mimes:jpg,jpeg,png,pdf' // Validate the file
        ]);

        // Create a new ticket message record
        TicketMessage::create([
            'ticket_id' => $request->ticketId,
            'chat_message' => $request->chatMessage,
            'status' => $request->selectedStatus,
            'file_path' => $request->hasFile('selectedFile') ? $this->image($request->selectedFile) : null,
            'added_by' => auth()->user()->id
        ]);

        Ticket::where("id",$request->ticketId)->update([
            "status" => $request->selectedStatus
        ]);

        return response()->json(['message' => 'Message successfully added'], 201);
    }

    public function getMessages(Request $request)
    {
        // Count tickets by each status
        $messages = TicketMessage::with("added_by_name")->where("ticket_id",$request->ticket_id)->orderBy("id","ASC")->get();

        // Return the status counts as a JSON response
        return response()->json([
            'messages' => $messages,
            'user' => auth()->user(),
        ], 200);
    }

    public function getTicket(Request $request)
    {
        // Count tickets by each status
        $ticket = Ticket::with('added_by_name.dropshipper', 'order.shop')->where("id",$request->ticket_id)->first();

        // Return the status counts as a JSON response
        return response()->json([
            'ticket' => $ticket,
        ], 200);
    }

    public function image( $image  ){
        $filenameWithExt = $image->getClientOriginalName();
        //get just filename
        $filename        = pathinfo($filenameWithExt);
        //get just extension
        $extension       = $image->extension();
        $nameToStore     = str_replace(' ', '' ,$filename['filename']) . "_" . time() . "." . $extension;
        //Move to folder
        $path            = $image->storeAs('public/uploads/tickets/message', $nameToStore);
        return $nameToStore;
    }

    public function apiImage( Request $request ){
        // Retrieve the uploaded file
        $image = $request->file('image');

        // Get the original filename with extension
        $filenameWithExt = $image->getClientOriginalName();

        // Get just filename
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

        // Get just extension
        $extension = $image->extension();

        // Create a unique filename to store
        $nameToStore = str_replace(' ', '', $filename) . "_" . time() . "." . $extension;

        // Store the file in the specified directory (ensure the directory exists)
        $path = $image->storeAs('public/uploads/tickets/message', $nameToStore); // Use the 'public' disk

        // Return the stored filename or a success response
        return response()->json([
            'message' => 'Image uploaded successfully',
            'name' => $nameToStore // You can also return the path if needed
        ], 201);
    }
}
