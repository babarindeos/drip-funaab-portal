<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Storage;




class PDFController extends Controller
{
    //
    public function generatePDF()
    {


        //return view('pdf.document');
        $message = "If you need the output as a string, you can get the rendered PDF with the output() function, so you can save/output it yourself.

        Use php artisan vendor:publish to create a config file located at config/dompdf.php which will allow you to define local configurations to change some settings (default paper etc). You can also use your ConfigProvider to set certain keys.";

        $data = [
            'title' => 'Welcome to Laravel PDF Generation',
            'date' => date('m/d/Y'),
            'message' => $message
        ];

        $pdf = PDF::loadView('pdf.document', $data); // Load the view file

        // You can also specify the paper size and orientation
        // $pdf->setPaper('A4', 'landscape');

        return $pdf->download('sample.pdf'); // Download the generated PDF
    }

    
}
