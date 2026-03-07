export const useTextHelpers = () => {
    // Function to limit text length and add ellipsis
    const limitText = (text, length) => {
        if (!text) return "";
        return text.length > length ? text.substring(0, length) + "..." : text;
    };

    // Function to highlight search terms within text
    const highlightText = (text, searchTerm) => {
        // Return original text if either text or searchTerm is empty/null
        if (!text || !searchTerm) return text;

        try {
            // Escape special regex characters in the search term
            // This prevents errors when searching for text containing characters like . * + ? ^ $ { } ( ) | [ ] \
            // For example: searching for "example.com" would error without this as "." is a special regex character
            const escapedSearchTerm = searchTerm.replace(
                /[.*+?^${}()|[\]\\]/g,
                "\\$&"
            );

            // Create a regular expression:
            // - The pattern is wrapped in parentheses () to create a capturing group
            // - 'gi' flags mean:
            //   g (global) - find all matches rather than stopping at the first match
            //   i (case insensitive) - match regardless of case (upper/lower)
            const regex = new RegExp(`(${escapedSearchTerm})`, "gi");

            // Replace all matches with highlighted version:
            // - $1 refers to the text captured by the first (and only) capturing group
            // - The matched text is wrapped in a <mark> tag with styling classes
            // - bg-yellow-200: light yellow background
            // - rounded: rounded corners
            // - px-1: small horizontal padding
            return text.replace(
                regex,
                '<mark class="bg-yellow-200 rounded px-0">$1</mark>'
            );
        } catch (e) {
            // If any errors occur during the highlighting process,
            // return the original text instead of breaking the application
            return text;
        }
    };

    // Return both utility functions
    return {
        limitText,
        highlightText,
    };
};
