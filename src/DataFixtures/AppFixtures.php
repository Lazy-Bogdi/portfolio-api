<?php

namespace App\DataFixtures;

use App\Entity\Education;
use App\Entity\Experience;
use App\Entity\Project;
use App\Entity\Skill;
use App\Entity\User;
use App\Enum\ProjectCategory;
use App\Enum\SkillCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadAdmin($manager);
        $this->loadSkills($manager);
        $this->loadExperiences($manager);
        $this->loadEducation($manager);
        $this->loadProjects($manager);

        $manager->flush();
    }

    private function loadAdmin(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@portfolio.dev');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, 'admin123'));

        $manager->persist($user);
    }

    private function loadSkills(ObjectManager $manager): void
    {
        $skills = [
            ['PHP', SkillCategory::Backend, 5, 'php', 1],
            ['Symfony', SkillCategory::Backend, 5, 'symfony', 2],
            ['Doctrine', SkillCategory::Backend, 4, 'doctrine', 3],
            ['Next.js', SkillCategory::Frontend, 4, 'nextjs', 4],
            ['React', SkillCategory::Frontend, 4, 'react', 5],
            ['TypeScript', SkillCategory::Frontend, 4, 'typescript', 6],
            ['Docker', SkillCategory::Devops, 5, 'docker', 7],
            ['Railway', SkillCategory::Devops, 4, 'railway', 8],
            ['GitHub Actions', SkillCategory::Devops, 4, 'github', 9],
            ['PostgreSQL', SkillCategory::Backend, 4, 'postgresql', 10],
            ['Redis', SkillCategory::Backend, 3, 'redis', 11],
            ['Git', SkillCategory::Other, 5, 'git', 12],
        ];

        foreach ($skills as [$label, $category, $level, $icon, $order]) {
            $skill = new Skill();
            $skill->setLabel($label);
            $skill->setCategory($category);
            $skill->setLevel($level);
            $skill->setIcon($icon);
            $skill->setSortOrder($order);

            $manager->persist($skill);
        }
    }

    private function loadExperiences(ObjectManager $manager): void
    {
        $exp = new Experience();
        $exp->setPosition('Fullstack Developer');
        $exp->setCompany('Freelance');
        $exp->setDateStart(new \DateTimeImmutable('2023-01-01'));
        $exp->setDescription('Développement d\'applications web fullstack avec Symfony et Next.js. DevOps, CI/CD, déploiement Railway.');
        $exp->setStack(['Symfony', 'Next.js', 'Docker', 'Railway', 'PostgreSQL']);
        $exp->setSortOrder(1);

        $manager->persist($exp);
    }

    private function loadEducation(ObjectManager $manager): void
    {
        $edu = new Education();
        $edu->setDegree('Mastère Ingénierie Web, Mobile et Innovations Digitales');
        $edu->setSchool('IIM — Institut de l\'Internet et du Multimédia');
        $edu->setYearStart(2023);
        $edu->setYearEnd(2025);
        $edu->setDescription('Formation en ingénierie web et mobile, spécialisation fullstack et DevOps.');

        $manager->persist($edu);
    }

    private function loadProjects(ObjectManager $manager): void
    {
        $project = new Project();
        $project->setTitle('franken-railway');
        $project->setShortDescription('Symfony 7 boilerplate with FrankenPHP, Mercure & Redis');
        $project->setLongDescription('A production-ready Symfony 7 boilerplate powered by FrankenPHP, with Mercure real-time updates and async messaging via Redis — ready to deploy on Railway in minutes.');
        $project->setStack(['Symfony', 'FrankenPHP', 'Mercure', 'Redis', 'Docker', 'Railway']);
        $project->setUrlGithub('https://github.com/Lazy-Bogdi/franken-railway');
        $project->setCategory(ProjectCategory::OpenSource);
        $project->setFeatured(true);
        $project->setSortOrder(1);

        $manager->persist($project);
    }
}
