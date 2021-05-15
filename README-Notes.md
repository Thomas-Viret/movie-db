## Active Record VS Data Mapper

```php

// AR
// L'objet "peut tout faire"
$movie->save();
$movie->findAll();
$movie->find(1);
// Update/Delete
$movie->find(1);
$movie->title = 'new title';
$movie->save();
// Ou
$movie->delete();

// DM
// L'objet est manipulé par
// Le manager
$manager->persist($movie);
$manager->flush();
// Le Repository
$movieRepository->findAll();
$movieRepository->find(1);

// Update
$movie = $movieRepository->find(1);
$movie->setTitle('new title');
$manager->flush();
// Delete
$movie = $movieRepository->find(1);
$manager->remove($movie);
$manager->flush();
```

## Relations avec Doctrine

Avec une relation de type `1N`, il y a une direction `ManyToOne` et `OneToMany`. En terme Doctrine, **c'est la `ManyToOne` qui détient la relation**.

Dans notre cas c'est `Review` qui détient la relation (qui détient la future clé étrangère) avec `Movie`, c'est donc `Review` qui est une `ManyToOne` vers `Movie`.

## Cas des ManyToOne

:warning: **Qui détient la relation ?** Review ou Movie
=> C'est Review (car le 1 de la relation est de son côté)
=> **C'est la ManyToOne qui détient la relation**
=> Avec Doctrine, on **doit** créer en priorité la relation ManyToOne.
=> On crée une relation ManyToOne dans Review.
    - => Many (Review) To One (Movie)
- Donc on lance `php bin/console make:entity Review` pour partir de Review
  - On suit l'assistant
  - On répond "yes" au nullable sur la relation même si le MCD nous dit le contraire
    - => en prévisions des données existantes dont on ne saurait que faire de cette nouvelle relation.
  - On répond "yes" pour créer la relation dans l'autre sens, si on le souhaite


## Cas des ManyToMany

Dans ce cas, on choisit l'entité qui détient, selon notre fonctionnement. Ici on part du principe qu'on aura une page Film sur laquelle on va sélectionner les genres associés. Donc on décide que Movie détient la relation. On va donc exécuter le Maker depuis l'entité qui détient : `bin/console make:entity Movie`