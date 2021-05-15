# Notions exatelier

- Entités à terminer
- refacto front/back
- CRUD sur entité(s)
  - `make:form`
  - contraintes de validation hors HTML5
- requirements + 404 + 404 custom
- navigation spécifique
- HTTP DELETE + CSRF
- Flash messages
- Fixtures

## MCD

- On commence par créer les entités qu'on va relier ensuite
  - Department
  - Job (la ManyToOne qui va détenir Department)
  - Team (qui relie Movie, Person, Job)

## Structure dossiers

- Côté contrôleurs, on range dans des sous-dossiers pour éviter la confusion front/back, notamment si on rajoute des features par la suite.
- Dans les templates,
  - un sous-dossier par contrôleur
  - convention `snake_case`


## Formulaire avec ManyToMany

:warning: Si un propriété de l'entité est une ArrayCollection, le ChoiceType (EntityType) doit être configuré avec un `multiple=true`

