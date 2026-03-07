<?php

namespace App\Helpers;

class TitleToFolderName
{
    /**
     * Convert a given title to a suitable folder name.
     *
     * @param string $title
     * @param int $wordLimit
     * @return string
     */
    public static function convert(string $title, int $wordLimit = 3): string
    {
        // Split the title into words based on spaces
        $words = explode(' ', $title);

        // Take only the specified number of words (or fewer if there are less)
        $selectedWords = array_slice($words, 0, $wordLimit);

        // Join the words with underscores
        $joinedWords = implode('_', $selectedWords);

        // Remove any characters that are not letters, numbers, or underscores (case-insensitive)
        $cleanedName = preg_replace('/[^a-zA-Z0-9_]/', '', $joinedWords);

        // Convert the folder name to lowercase for consistency
        return strtolower($cleanedName);
    }
}