<?php

namespace Database\Factories;

use App\Models\Categories;
use App\Models\Courses;
use App\Models\Reaction;
use App\Models\Reviews;
use App\Models\User;
use App\Models\UserCourseRecomended;
use App\Models\UserInterest;
use App\Models\UserProfile;
use App\Models\View;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '$2a$10$GVetLQByRmmFpCEPOeDPaOpgz8Is8RDWUUr3q3Latd9LFlmWCyGaa',
            'roles' => ['ROLE_USER']
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public $coursesId = [];
    
    public $rating = [1, 2, 3, 4, 4, 4, 4, 4, 4, 4, 4, 5, 5, 5, 5, 5, 5, 5, 5];

    public $reviews = [3, 4, 5];

    public $likes = [3, 4, 5];

    public $reaction = ['BAD', 'REGULAR', 'GOOD'];

    public function configure(): Factory
    {
        $faker = FakerFactory::create('es_ES');
        return $this->afterCreating(function (User $user) {

            $courses = Courses::all();

            // Obtener todos los IDs de los cursos
            $this->coursesId = $courses->pluck('id')->toArray();

            $categories = Categories::all();
            
            // Mantenemos un arreglo de categorías ya utilizadas para evitar repeticiones
            static $usedCategoryIds = [];
            
            // Filtrar las categorías para obtener solo las no utilizadas
            $availableCategories = $categories->reject(function ($category) use ($usedCategoryIds) {
                return in_array($category->id, $usedCategoryIds);
            });
            
            // Si no hay categorías disponibles, reiniciar el arreglo
            if ($availableCategories->isEmpty()) {
                $usedCategoryIds = [];
                $availableCategories = $categories;
            }
            
            // Seleccionar una categoría aleatoria disponible
            $selectedCategory = $availableCategories->random();
            
            // Marcar la categoría como utilizada
            $usedCategoryIds[] = $selectedCategory->id;

            $userProfile = UserProfile::factory()->create([
                'userId' => $user->id,
                'knowledgeLevel' => $this->faker->randomElement(['beginner', 'intermediate', 'expert']),
                'interest' => $this->standardizeCategory($selectedCategory->name),
                'availableHoursTime' => $this->faker->numberBetween(1, 8),
                'budget' => $this->faker->numberBetween(0, 19500000),
                'platformPreference' => $this->faker->randomElement(['hibrido', 'virtual', 'presencial', 'a distancia']),
            ]);

            UserInterest::factory(1)->create([
                'userProfileId' => $userProfile->id,
                'categoryId' => $selectedCategory->id
            ]);

            // Crear copia del array para ir eliminando elementos utilizados
            $availableCourseIds = $this->coursesId;

            $coursesIdSelected = [];

            for ($i = 0; $i < 3 && !empty($availableCourseIds); $i++) {
                // Seleccionar un índice aleatorio
                $randomIndex = array_rand($availableCourseIds);
                $selectedCourseId = $availableCourseIds[$randomIndex];

                // Eliminar el ID usado para evitar repeticiones
                unset($availableCourseIds[$randomIndex]);

                $randomIndexReaction = array_rand($this->reaction);
                $selectedReaction = $this->reaction[$randomIndexReaction];

                Reviews::factory(1)->create([
                    'content' => $this->faker->realText(100),
                    'rating' => $this->selectedRating($selectedReaction),
                    'userId' => $user->id,
                    'courseId' => $selectedCourseId
                ]);

                // Para Like, puedes usar el mismo ID o seleccionar otro diferente
                Reaction::factory(1)->create([
                    'userId' => $user->id,
                    'courseId' => $selectedCourseId,
                    'type' => $selectedReaction
                ]);
                $coursesIdSelected[] = $selectedCourseId;
            }

            $totalVistas = random_int(5, 20);

            for ($i = 0; $i <= $totalVistas && !empty($availableCourseIds); $i++) {

                $selectedCourseId = "";

                if (count($coursesIdSelected) > 0) {
                    $selectedCourseId = $coursesIdSelected[0];
                    unset($coursesIdSelected[0]);
                    $coursesIdSelected = array_values($coursesIdSelected);
                } else {
                    // Seleccionar un índice aleatorio
                    $randomIndex = array_rand($availableCourseIds);
                    $selectedCourseId = $availableCourseIds[$randomIndex];
                    // Eliminar el ID usado para evitar repeticiones
                    unset($availableCourseIds[$randomIndex]);
                };

                View::factory()->create([
                    'courseId' => $selectedCourseId,
                    'userId' => $user->id
                ]);
            }
            $courseRecomendedUser = $this->courseRecomended($userProfile);
            // var_dump($courseRecomendedUser);
        });


    }

    public function courseRecomended($userProfile)
    {
        $courses = Courses::with('category')->get();
        $userCoursesRecomended = [];

        foreach($courses as $course)
        {

            $courseReviews = Reviews::where('courseId', $course->id)->get();

            $maxReaction = Reaction::where('courseId', $course->id);

            $totalViews = View::where('courseId', $course->id)->count();

            $averageRating = $courseReviews->avg('rating') ?? 0;
            $averageRating = round($averageRating, 2); // Redondear a 2 decimales

            $maxReaction = $maxReaction->max('type') ?? 'NONE';

            // var_dump($course->toJson(JSON_PRETTY_PRINT));
            $courseRecomended = UserCourseRecomended::factory()->create([
                'userProfileId' => $userProfile->id,
                'courseId' => $course->id,
                'courseCategory' => $this->standardizeCategory($course->category->name),
                'courseModality' => $this->standardizeModality($course->modality),
                'courseHours' => $this->extractHoursFromDuration($course->duration),
                'ratingAvg' => $averageRating, 
                'maxReaction' => $maxReaction,
                'totalViews' => $totalViews,
                'reviewsCount' => $courseReviews->count(),
                'recomended' => $this->booleanRecomended($userProfile, $course, $maxReaction, $averageRating),
            ]);
            $userCoursesRecomended[] = $courseRecomended;
        }

        return $userCoursesRecomended;
    }

    /**
     * Determina si un curso debe ser recomendado a un usuario basado en el árbol de decisión.
     * 
     * @return bool True si el curso debe ser recomendado, false en caso contrario
     */
    function booleanRecomended($userProfile, $course, $maxReaction, $averageRating): bool {
        // Paso 1: ¿El curso coincide con el interés del usuario?
        if ($this->standardizeCategory($course->category->name) == $userProfile->interest) {
            // Paso 2: ¿La modalidad coincide con la preferencia de plataforma?
            if ($this->standardizeModality($course->modality) == $userProfile->platformPreference) {
                // Paso 3: ¿La reacción máxima es 'GOOD' o 'NONE'?
                if ($maxReaction == 'GOOD' || $maxReaction == 'NONE') {
                    return true;
                } else {
                    // Paso 4: ¿El rating promedio es >= 3?
                    if ($averageRating >= 3) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                // Paso 5: Si la modalidad no coincide con la preferencia
                if ($userProfile->platformPreference == 'hibrido') {
                    // Modalidad del curso es presencial o virtual con rating >= 3.5
                    if (($this->standardizeModality($course->modality) == 'presencial' || 
                         $this->standardizeModality($course->modality) == 'virtual') && 
                        $averageRating >= 3.5) {
                        return true;
                    } else {
                        return false;
                    }
                } else if ($userProfile->platformPreference == 'presencial') {
                    // Si el curso es hibrido con rating >= 3.5
                    if ($this->standardizeModality($course->modality) == 'hibrido' && $averageRating >= 3.5) {
                        return true;
                    } else {
                        return false;
                    }
                } else if ($userProfile->platformPreference == 'a distancia') {
                    // Si el curso es virtual o hibrido con rating >= 3.5
                    if (($this->standardizeModality($course->modality) == 'virtual' || 
                         $this->standardizeModality($course->modality) == 'hibrido') && 
                        $averageRating >= 3.5) {
                        return true;
                    } else {
                        return false;
                    }
                } else if ($userProfile->platformPreference == 'virtual') {
                    // Si el curso es a distancia o hibrido con rating >= 3.5
                    if (($this->standardizeModality($course->modality) == 'a distancia' || 
                         $this->standardizeModality($course->modality) == 'hibrido') && 
                        $averageRating >= 3.5) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } else {
            // Paso 6: Si el curso no coincide con el interés del usuario
            if ($this->standardizeModality($course->modality) == $userProfile->platformPreference) {
                // El curso coincide con la preferencia de plataforma
                if ($averageRating >= 4.0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    function standardizeModality($modality) {
        // Trim whitespace and convert to lowercase for case-insensitive comparison
        $modality = strtolower(trim($modality));
        
        // Hibrido (Rojo) - Red color coded modalities
        if (preg_match('/(hibrid|blended|mixt|semipresencial|presencial con sesiones remotas|presencial - sesiones remotas|presencial y virtual)/i', $modality)) {
            return 'hibrido';
        }
        
        // Presencial (Rosa) - Pink color coded modalities
        if (preg_match('/(presencial|asistencia personal|campus ternera|edad mínima)/i', $modality)) {
            return 'presencial';
        }
        
        // Virtual (Azul) - Blue color coded modalities
        if (preg_match('/(virtual|online|teams|zoom|webex|remota con sesiones)/i', $modality)) {
            return 'virtual';
        }
        
        // A distancia (Azul oscuro) - Dark blue color coded modalities
        if (preg_match('/(distancia|remot|a distancia|último modulo presencial|último fin de semana presencial)/i', $modality)) {
            return 'a distancia';
        }
        
        // If none of the above match, return none (Gris)
        return 'none';
    }

    function standardizeCategory($category) {
        // Trim whitespace and convert to lowercase for case-insensitive comparison
        $category = strtolower(trim($category));
        
        // arquitectura (Rojo)
        if (preg_match('/(arquitectura y diseno|arquitectura y diseño)/i', $category)) {
            return 'arquitectura';
        }
        
        // artes y humanidades (azul petroleo)
        if (preg_match('/(artes|artes y humanidades|ciencias humanas|humanidades)/i', $category)) {
            return 'artes y humanidades';
        }
        
        // ciencias sociales (celeste)
        if (preg_match('/(centro interdisciplinario de estudios sobre desarrollo|ciencias sociales|ciencias sociales y humanidades)/i', $category)) {
            return 'ciencias sociales';
        }
        
        // ciencias de la educacion (lila)
        if (preg_match('/(ciencias de la educacion|educacion)/i', $category)) {
            return 'ciencias de la educacion';
        }
        
        // ciencias de la salud (fucsia)
        if (preg_match('/(ciencias de la salud|enfermeria|medicina|odontologia|psicologia|quimica y farmacia)/i', $category)) {
            return 'ciencias de la salud';
        }
        
        // ciencias (verde claro)
        if (preg_match('/(ciencias|ciencias basicas)/i', $category)) {
            return 'ciencias';
        }
        
        // derecho (verde oscuro)
        if (preg_match('/(ciencias juridicas|derecho|derecho canonico|escuela de negocios leyes y sociedad)/i', $category)) {
            return 'derecho';
        }
        
        // ciencias politicas (naranja)
        if (preg_match('/(ciencias politicas y relaciones internacionales|escuela de gobierno alberto lleras camargo|escuela de gobierno y etica publica)/i', $category)) {
            return 'ciencias politicas';
        }
        
        // comunicacion y lenguaje (gris)
        if (preg_match('/(comunicacion y lenguaje)/i', $category)) {
            return 'comunicacion y lenguaje';
        }
        
        // ingenieria (amarillo)
        if (preg_match('/(diseño e ingenieria|ingenieria)/i', $category)) {
            return 'ingenieria';
        }
        
        // ambiental (negro)
        if (preg_match('/(estudios ambientales y rurales|instituto ideeas|instituto pensar|vicerrectoria de investigacion y creacion)/i', $category)) {
            return 'ambiental';
        }
        
        // filosofia (rojo oscuro)
        if (preg_match('/(filosofía|teología|filosofia|teologia)/i', $category)) {
            return 'filosofia';
        }
        
        // nutricion y dietetica (blanco)
        if (preg_match('/(nutricion y dietetica)/i', $category)) {
            return 'nutricion y dietetica';
        }
        
        // ciencias economicas (violeta)
        if (preg_match('/(economía|empresariales|administrativas|negocios|finanzas|economia)/i', $category)) {
            return 'ciencias economicas';
        }

        // direccion de internacionalizacion (marron)
        if (preg_match('/(direccion de internacionalizacion)/i', $category)) {
            return 'direccion de internacionalizacion';
        }
        
        // If none of the above match, return the original category with (Gris)
        return 'none';
    }

    public function selectedRating(String $selectedReaction)
    {
        if ($selectedReaction == 'BAD') {
            $ratings = [1, 2];
            $randomIndex = array_rand($ratings);
            $selectedRating = $ratings[$randomIndex];
            return $selectedRating;
        }
        if ($selectedReaction == 'REGULAR') {
            $selectedRating = 3;
            return $selectedRating;
        }
        if ($selectedReaction == 'GOOD') {
            $ratings = [4, 5];
            $randomIndex = array_rand($ratings);
            $selectedRating = $ratings[$randomIndex];
            return $selectedRating;
        }
    }

    /**
     * Extract hours from course duration string using regex
     *
     * @param string|null $duration
     * @return int
     */
    private function extractHoursFromDuration(?string $duration): int
    {
        // Return default value if duration is null
        if ($duration === null) {
            return 40; // Default hours value
        }

        // Extract hours using regex
        if (preg_match('/(\d+)\s*(horas|Horas|HORAS)/i', $duration, $matches)) {
            return (int) $matches[1]; 
        }

        // If no hours found, return default value
        return 40;
    }
}
