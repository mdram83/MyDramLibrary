<?php

namespace MyDramLibrary\Catalog;

use MyDramLibrary\User\User;
use MyDramLibrary\Utilities\Database\Database;
use MyDramLibrary\Utilities\Http\Request;

class TitleFactory
{
    protected static ?TitleFactory $instance = null;
    protected Database $database;

    private function __construct()
    {
        $this->database = Database::instance();
    }

    public static function instance(): TitleFactory
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getUserTitles(User $user): TitleCollection
    {
        $titleCollection = new TitleCollection();
        $sql = 'select id from title where user = :userid';
        if ($data = $this->database->run($sql, ['userid' => $user->getUserId()])->fetchAll(\PDO::FETCH_COLUMN, 0)) {
            foreach ($data as $titleId) {
                $titleId = (int) $titleId;
                $titleCollection->addItem(new Title($titleId), $titleId);
            }
        }
        return $titleCollection;
    }

    public function createUserTitleFromRequet(User $user, Request $request): Title
    {
        return new Title(
            id:          null,
            user:        $user,
            title:       $request->getPostParamTrim('title'),
            author:      (
                $request->getPostParamTrim('authorFirstname') && $request->getPostParamTrim('authorLastname'))
                ? new Author(
                    null,
                    $request->getPostParamTrim('authorFirstname'),
                    $request->getPostParamTrim('authorLastname')
                )
                : null,
            publisher:
                ($request->getPostParamTrim('publisher') != '')
                ? new Publisher(null, $request->getPostParamTrim('publisher'))
                : null,
            isbn:        ($request->getPostParamTrim('isbn') != '') ? $request->getPostParamTrim('isbn') : null,
            series:      ($request->getPostParamTrim('series') != '') ? $request->getPostParamTrim('series') : null,
            volume:      ($request->getPostParamTrim('volume') != '') ? $request->getPostParamTrim('volume') : null,
            pages:       ($request->getPostParamTrim('pages') != '') ? $request->getPostParamTrim('pages') : null,
            description:
                ($request->getPostParamTrim('description') != '')
                ? $request->getPostParamTrim('description')
                : null,
            comment:     ($request->getPostParamTrim('comment') != '') ? $request->getPostParamTrim('comment') : null,
            categories:  CategoryFactory::instance()->createCategoryCollectionFromNamesArray(
                $request->getPostParam('category')
            )
        );
    }
}
