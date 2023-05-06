<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Torann\GeoIP\Facades\GeoIP;
use Illuminate\Support\Facades\DB;

class OlahragaController extends Controller
{
    public function index(Request $request){
        // Send a GET request to the ip-api.com/json API
        $response = file_get_contents('http://ip-api.com/json');

        // Decode the JSON response into a PHP object
        $ipData = json_decode($response);
        
        // Get Current Location
        $currentLocation = GeoIP::getLocation($ipData->query);

        $sortby_key = $request['sortby'];
        $search_key = $request['search'];
        
        $data['search_key'] = $search_key;
        $data['object_type'] = 'sarana olahraga';
        
        $data['myLatitude']  = $currentLocation['lat'];
        $data['myLongitude'] = $currentLocation['lon'];

        if($search_key && $sortby_key){
            if($sortby_key == 'rating'){
                $data['data'] = DB::table('object')
                    ->where('jenis', 'Sarana Olahraga')
                    ->where('nama', 'ilike', '%'.$search_key.'%')
                    ->orderBy('rating', 'desc')
                    ->get();
                $data['others'] = DB::table('object')
                    ->where('jenis', 'Sarana Olahraga')
                    ->orderBy('rating', 'desc')
                    ->get();
                $data['sortby'] = 'rating';
            } else{
                $data['data'] = DB::table('object')
                    ->where('jenis', 'Sarana Olahraga')
                    ->where(function($query) use ($search_key){
                        $query->where('nama', 'ilike', '%'.$search_key.'%')
                        ->orWhere('alamat', 'ilike', '%'.$search_key.'%');
                    })
                    // ->orderBy('rating', 'desc') // Nanti diganti order by jarak
                    ->get();
                $data['sortby'] = 'distance';
                $data['others'] = DB::table('object')
                    ->where('jenis', 'Sarana Olahraga')
                    ->get();
            }
        } else if($search_key){
            $data['data'] = DB::table('object')
                ->where('jenis', 'Sarana Olahraga')
                ->where(function($query) use ($search_key){
                    $query->where('nama', 'ilike', '%'.$search_key.'%')
                    ->orWhere('alamat', 'ilike', '%'.$search_key.'%');
                })
                // ->orderBy('rating', 'desc') // Nanti diganti order by jarak
                ->get();
            $data['sortby'] = 'distance';
            $data['others'] = DB::table('object')
                ->where('jenis', 'Sarana Olahraga')
                ->get();

        } else if($sortby_key){
            if($sortby_key == 'rating'){
                $data['data'] = DB::table('object')
                    ->where('jenis', 'Sarana Olahraga')
                    ->orderBy('rating', 'desc')
                    ->get();
                $data['sortby'] = 'rating';
            } else{
                $data['data'] = DB::table('object')
                    ->where('jenis', 'Sarana Olahraga')
                    // ->orderBy('rating', 'desc') // Nanti diganti order by jarak
                    ->get();
                $data['sortby'] = 'distance';
            }
        } else{
            $data['data'] = DB::table('object')
                ->where('jenis', 'Sarana Olahraga')
                // ->orderBy('rating', 'desc') // Nanti diganti order by jarak
                ->get();
            $data['sortby'] = 'distance';
        }
        // dd($data);
        return view("pages.search", $data);
    }

    public function detail($id){
        $data['object_type'] = 'sarana olahraga';
                
        // Send a GET request to the ip-api.com/json API
        $response = file_get_contents('http://ip-api.com/json');

        // Decode the JSON response into a PHP object
        $ipData = json_decode($response);
        
        // Get Current Location
        $currentLocation = GeoIP::getLocation($ipData->query);

        $data['myLatitude'] = $currentLocation['lat'];
        $data['myLongitude'] = $currentLocation['lon'];

        $data['data'] = DB::table('object')
            ->where('jenis', 'Sarana Olahraga')
            ->where('id', $id)
            ->first();

        $data['rekomendasi'] = DB::table('object')
            ->where('jenis', 'Sarana Olahraga')
            ->orderBy('rating', 'desc')
            ->take(6)
            ->get();
        
        // dd($data);
        return view("pages.detail", $data);
    }
}
