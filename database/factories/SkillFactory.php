<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skills = [
            'PHP', 'Laravel', 'JavaScript', 'Vue.js', 'React', 'Node.js',
            'Python', 'Django', 'Flask', 'Java', 'Spring Boot', 'C#',
            'ASP.NET', 'Ruby', 'Rails', 'Go', 'Rust', 'Swift', 'Kotlin',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'Docker', 'Kubernetes',
            'AWS', 'Azure', 'GCP', 'Git', 'Linux', 'Agile', 'Scrum',
            'Project Management', 'UI/UX Design', 'Figma', 'Adobe Creative Suite',
            'Data Analysis', 'Machine Learning', 'AI', 'Blockchain', 'DevOps',
            'Cybersecurity', 'Mobile Development', 'iOS', 'Android', 'Flutter'
        ];
        
        return [
            'skill_name' => $this->faker->randomElement($skills) . ' ' . $this->faker->numberBetween(1, 1000),
        ];
    }
}