<?php

namespace App\Model\Helpers;


use Maatwebsite\Excel\Facades\Excel as Excel_Facade;

class Excel
{
    /**
     * Generate an Excel file
     *
     * @param string $filename Name of the file
     * @param array $data Data for the sheet
     * @param bool $headers_included Whether the table has headers or not
     */
    public static function generate_excel($filename, $data, $headers_included = true) {
        Excel_Facade::create($filename, function($excel) use($filename, $data, $headers_included) {

            // Set the title
            $excel->setTitle($filename)
                ->setCreator('Club Manager')
                ->setCompany('Geekk')
                ->setDescription($filename);

            // Add the sheet and fill it with de participants
            $excel->sheet('Leden', function($sheet) use($data, $headers_included) {

                // Only set the first row bold, when we included headers
                if($headers_included) {
                    $sheet->cells('A1:' . self::get_last_column(count($data[0])) . '1', function($cells) {
                        // Set font weight to bold
                        $cells->setFontWeight('bold');
                    });
                }

                // Append the data to the sheet
                $sheet->rows($data);
            });

        })->download('xlsx');
    }


    /**
     * Get the last column of the data table
     *
     * @param int $columns Count of the columns
     * @return string
     */
    public static function get_last_column($columns) {
        // Set the alphabet
        $alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

        $key = $columns - 1;

        // Return the column name
        return $alphabet[$key];
    }
}