<?php

namespace App\Http\Interfaces;

interface DocumentInterface
{
    public static function getDocumentSize($document_file);
    public static function getDocumentType($document_file);
}
