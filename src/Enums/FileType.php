<?php

namespace AdamMarshall\FilamentFileLibrary\Enums;

use Filament\Support\Contracts\HasLabel;

enum FileType: string implements HasLabel
{
    case PDF = 'application/pdf';
    case JPEG = 'image/jpeg';
    case PNG = 'image/png';
    case GIF = 'image/gif';
    case WEBP = 'image/webp';
    case SVG = 'image/svg+xml';
    case TEXT = 'text/plain';
    case CSV = 'text/csv';
    case WORD = 'application/msword';
    case WORD_DOCX = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    case EXCEL = 'application/vnd.ms-excel';
    case EXCEL_XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    case POWERPOINT = 'application/vnd.ms-powerpoint';
    case POWERPOINT_PPTX = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    case ZIP = 'application/zip';
    case MP4 = 'video/mp4';
    case MP3 = 'audio/mpeg';

    public function getLabel(): string
    {
        return match ($this) {
            self::PDF => 'Pdf',
            self::JPEG => 'Jpeg',
            self::PNG => 'Png',
            self::GIF => 'Gif',
            self::WEBP => 'Webp',
            self::SVG => 'Svg',
            self::TEXT => 'Text',
            self::CSV => 'Csv',
            self::WORD => 'Word (.doc)',
            self::WORD_DOCX => 'Word (.docx)',
            self::EXCEL => 'Excel (.xls)',
            self::EXCEL_XLSX => 'Excel (.xlsx)',
            self::POWERPOINT => 'PowerPoint (.ppt)',
            self::POWERPOINT_PPTX => 'PowerPoint (.pptx)',
            self::ZIP => 'Zip',
            self::MP4 => 'Video',
            self::MP3 => 'Audio',
        };
    }
}
