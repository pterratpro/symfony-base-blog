<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('fr_FR');
        for($j = 0;$j < 3;$j++){
            // Créer l'objet category
            $category = new Category();
            //Remplir category
            $category -> setTitle($faker->sentence($nbWords = 2, $variableNbWords = true))
                      -> setDescription($faker->paragraph($nbSentences = 5, $variableNbSentences = true));
            $manager -> persist($category);
            for($i = 0; $i < 5; $i++){
                $article = new Article();
                $article -> setTitle($faker->sentence($nbWords = 5, $variableNbWords = true))
                         -> setContent($faker->paragraph($nbSentences = 5, $variableNbSentences = true))
                         -> setImage($faker->imageUrl($width = 350, $height = 250))
                         -> setCreatedAt($faker->dateTimeBetween('-3 months'))
                         -> setCategory($category);
                $manager -> persist($article);
                for($k = 0; $k < 5;$k++){
                    $comment = new Comment();
                    $comment -> setTitle($faker -> sentence(5,true))
                             -> setAuthor($faker -> name())
                             -> setContent($faker -> paragraph(3,true))
                             -> setCreatedAt($faker->dateTimeBetween('-3 months'))
                             -> setArticle($article);
                    $manager -> persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
