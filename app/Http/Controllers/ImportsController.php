<?php

namespace App\Http\Controllers;

use App\Jobs\ImportsXmlJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImportsController extends Controller
{
    public function import(Request $request)
    {
        $path = database_path('xml');

        $files = [
            'hotels'       => $path . '/hotels.xml',
            'rooms'        => $path . '/rooms.xml',
            'rates'        => $path . '/rates.xml',
            'reservations' => $path . '/reservations.xml',
        ];

        foreach ($files as $key => $file_path) {
            if (!File::exists($file_path)) {
                return response()->json(['error' => "Arquivo não encontrado: $key ($file_path)"], 404);
            }
        }

        $hotels_xml       = File::get($files['hotels']);
        $rooms_xml        = File::get($files['rooms']);
        $rates_xml        = File::get($files['rates']);
        $reservations_xml = File::get($files['reservations']);

        ImportsXmlJob::dispatch(
            $hotels_xml,
            $rooms_xml,
            $rates_xml,
            $reservations_xml
        );

        return response()->json(['message' => 'Importação de dados feita com sucesso.'], 202, [], JSON_UNESCAPED_UNICODE);
    }
}
