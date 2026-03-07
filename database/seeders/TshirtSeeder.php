<?php

namespace Database\Seeders;

use App\Helpers\TitleToFolderName;
use App\Models\ShirtImage;
use App\Models\Tshirt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TshirtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define the common image filenames
        $imageFiles = ['mainImage.png', 'secondImage.jpeg', 'thirdImage.jpeg', 'forthImage.jpeg', 'fifthImage.jpeg'];

        // Array of t-shirt data with unique titles and descriptions
        $tshirts = [
            [
                'title' => "The Codefather T-Shirt",
                'description' => "This clever t-shirt design puts a clever spin on the classic movie 'The Godfather' by replacing 'Godfather' with 'Codefather', creating a humorous and relatable t-shirt for developers and programmers. The iconic Godfather logo silhouette is used, with the text 'The Codefather' prominently displayed, making this a fun and playful shirt that any coding enthusiast is sure to appreciate.",
            ],
            [
                'title' => "Coding is Just Googling T-Shirt",
                'description' => "This humorous t-shirt is perfect for all the developers and programmers out there. It playfully acknowledges the reality that a large part of coding often involves searching online and using resources like Google to find solutions, troubleshoot issues, and learn new techniques. The design features bold, colorful text that celebrates this common practice in the world of software development. Whether you're a seasoned coder or just starting out, this t-shirt is sure to resonate and bring a smile to the faces of your fellow tech enthusiasts. It's a lighthearted way to show off your coding skills and embrace the reality of being a modern software engineer.",
            ],
            [
                'title' => "Debugging: Being the Detective in a Crime Movie Where You Are Also the Murderer T-Shirt",
                'description' => "This t-shirt design takes a tongue-in-cheek approach to the debugging process, comparing it to being the detective in a crime movie where you are also the murderer. The bold, red text 'DEBUGGING' immediately grabs attention, while the humorous description below further emphasizes the often frustrating yet essential nature of debugging code. This t-shirt is sure to resonate with developers who understand the challenges and responsibilities that come with troubleshooting their own creations.",
            ],
            [
                'title' => "Don't Repeat Yourself T-Shirt",
                'description' => "The minimalist design of this t-shirt conveys a powerful message for developers - 'Don't Repeat Yourself' (DRY), a fundamental principle of writing efficient, maintainable code. By avoiding duplication and promoting code reuse, developers can create more streamlined and scalable software. This t-shirt serves as a simple yet impactful reminder of this important programming best practice, making it a great choice for any coder or programmer.",
            ],
            [
                'title' => "I'm a Fullstack Overflow Developer T-Shirt",
                'description' => "This humorous t-shirt design plays on the well-known developer resource, Stack Overflow, by dubbing the wearer a 'Fullstack Overflow Developer'. The combination of the 'I'm a Fullstack' text and the Stack Overflow-inspired 'Overflow Developer' branding creates a clever and relatable design that captures the experience of modern software engineers who often rely on online communities and forums to find solutions to their coding challenges. This t-shirt is sure to be a hit among developers who appreciate a touch of self-deprecating humor.",
            ],
            [
                'title' => "Head/Body T-Shirt",
                'description' => "This clever t-shirt design uses HTML tags to playfully represent the human body. The '<head>' element corresponds to the wearer's actual head, with the closing '/>' tag signifying where the head ends at the neck. Below that, the '<body>' tag marks the start of the t-shirt fabric, visually continuing the metaphor that the torso and body are now the focus. By aligning the HTML syntax with the physical form, this design offers a unique, meta-textual interpretation of the human form through the lens of coding and web development. It's a clever and thought-provoking way for developers and programmers to express their identity and passions.",
            ],
            [
                'title' =>  "It Works On My Machine T-Shirt",
                'description' => "The popular programmer's phrase 'It works on my machine' is the focus of this humorous t-shirt design. The text is displayed in a font that mimics programming syntax, further emphasizing the relatable experience of developers who have encountered software issues that only seem to manifest on specific machines or environments. This design taps into the shared frustrations and inside jokes of the coding community, making it a fun and lighthearted choice for any tech-savvy individual.",
            ],
            [
                'title' => "Keep It Simple Stupid T-Shirt",
                'description' => "This t-shirt design takes a bold, straightforward approach with the message 'KEEP IT SIMPLE STUPID' prominently displayed in a striking color scheme of black, white, and orange. The all-caps text conveys a direct and unapologetic reminder to focus on simplicity and avoid unnecessary complexity, a principle that is highly valued in the world of software development and engineering. The design's minimalist style makes it an impactful and attention-grabbing choice for any programmer or coder.",
            ],
            [
                'title' =>  "Front End, Back End, Weekend T-Shirt",
                'description' => "This clever t-shirt design plays on the common software development terminology of 'Front End' and 'Back End' by juxtaposing them with the playful 'Weekend' option, which is the only one checked. This humorous take on the work-life balance challenges faced by many developers serves as a relatable and lighthearted reminder that even coders need to take time to recharge and enjoy their personal lives. The simple, black-and-white aesthetic with the strategic use of a green checkmark creates a memorable and easily recognizable design.",
            ],
        ];

        // Seed each t-shirt
        foreach ($tshirts as $tshirtData) {
            // Create T-shirt record
            $tshirt = Tshirt::create([
                'title' => $tshirtData['title'],
                'slug' => Str::slug($tshirtData['title'], '-'),
                'description' => $tshirtData['description'],
                'price' => 29.00,
                'images_folder_name' => TitleToFolderName::convert($tshirtData['title']),
            ]);

            // Folder name based on the slugified title
            $folderName = TitleToFolderName::convert($tshirtData['title']);

            // Seed each image in the predefined image files array
            foreach ($imageFiles as $index => $imageFile) {
                ShirtImage::create([
                    'tshirt_id' => $tshirt->id,
                    'order' => $index + 1,
                    'url' => "/storage/tshirts/{$folderName}/{$imageFile}",
                ]);
            }
        }
    }
}
