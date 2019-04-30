<?php


use JsonApiPresenter\ResourceManager;


require_once '../../vendor/autoload.php';

class App
{

    public function start()
    {
        $manager = new ResourceManager();
        $manager->register('books', new BooksRepository());
        $manager->register('people', new PeopleRepository());

        $books = $manager
            ->createQueryBuilder()
            ->select('people')
            ->withIds(1, 2)
            ->include('books')
            ->getResult();

        echo json_encode($books);
    }

}

$app = new App();
$app->start();