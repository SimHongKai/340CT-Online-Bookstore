<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use DB;

class CatalogueController extends Controller
{
    /**
     * Show the Manage Stocks Page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function catalogueListView() {
        $stocks = DB::table('stock')->paginate(3);
        return view('catalogue')->with(compact('stocks'));
    }

    /**
     * Show the Manage Stocks Page with Filtered Results
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function filterCatalogue(Request $request) {   
        
        $query = DB::table('stock');

        if ($request->filled('bookTitle')){
            $query = $query->where('bookTitle', 'LIKE', '%' . $request->bookTitle . '%');
        }

        if ($request->filled('qty')){
            $query = $query->where('qty', '>=', $request->qty);
        }

        $stocks = $query->paginate(3);

        return view('catalogue')->with(compact('stocks'));
    }

    /**
     * Show the Book Details Page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function bookDetailsView(Request $request)
    {
        if ($request != null){
            $stock = Stock::find($request->ISBN13);
            return view('bookDetail')->with('stock', $stock);
        }
        else{
            return view('home');
        }

    }
   
}
