<?php


use JsonApiPresenter\ResourceManager;


require_once '../../vendor/autoload.php';

class App
{

    public function start()
    {
        $manager = new ResourceManager();
        $manager->register('books', new BooksDataSource());
        $manager->register('people', new PeopleDataSource());

        $books = $manager
            ->createQueryBuilder()
            ->select('people')
            ->withId(1)
            ->include('books')
            ->getSingleResult();

        echo json_encode($books);
    }

}

$app = new App();
$app->start();