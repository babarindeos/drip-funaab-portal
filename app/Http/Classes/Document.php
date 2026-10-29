<?php

namespace App\Http\Classes;

use App\Http\Interfaces\DocumentInterface;

Class Document implements DocumentInterface
{
    public static function getDocumentSize ($document_file)
    {
        // get file Size
        $fileInBytes = $document_file->getSize();

        // Determine the appropriate unit  and format the size
        if ($fileInBytes >= 1073741824)
        {
            // file size is 1 GB or more
            $fileSize = number_format($fileInBytes/1073741824, 2).' GB';
        }
        else if ($fileInBytes >= 1048576)
        {
            // file size is 1 MB or more
            $fileSize = number_format($fileInBytes/1048576, 2).' MB';
        }
        else
        {
            // file size is less than 1 MB
            $fileSize = number_format($fileInBytes/1024, 2).' KB';

        }

        return $fileSize;
    }

    public static function getDocumentType($document_file)
    {
        $documentExtension = $document_file->getClientOriginalExtension();

        if ($documentExtension == 'doc' || $documentExtension == 'docx')
        {
            $documentType = "MS Word";
        }
        else if ( $documentExtension == 'pdf')
        {
            $documentType = "PDF";
        }
        else if ( $documentExtension == 'jpg' || $documentExtension == 'jpeg' || $documentExtension == 'png')
        {
            $documentType = "Image | ".$documentExtension;
        }

        return $documentType;
    }

}

