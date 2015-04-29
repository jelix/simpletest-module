# Créer des tests avec Simpletest


## installation des sources

Si vous n'installez pas le module à partir de Composer, téléchargez ses sources et
installez le répertoire junittests dans un répertoire. Si ce répertoire n'est pas
connu de votre application, déclarer le module dans votre fichier application.init.php :

```php
jApp::declareModule(__DIR__.'/../chemin/vers/junittests');
```

Si vous utilisez Composer, Jelix détectera automatiquement la présence du module
junittests.


## configuration

Dans votre fichier localconfig.ini.php, uniquement dans un environnement de developpement,
déclarez le module junittests.

```ini
; for security, enable this boolean only on developement server
enableTests = on

[modules]

junittests.access = 2
```

L'option enableTests active l'interface web de junitttests, permettant de lancer alors
les tests Simpletests à partir d'un navigateur.

Vous **devez** mettre à off quand vous passez votre application sur le serveur
de production, ou utilisez une édition "optimized" de jelix. Cela évite que
n'importe qui puisse lancer les tests unitaires. Le mieux étant de ne pas installer
ce module en production.

Ensuite vous activez le module:

```bash
php cmd.php installmodule junittests
```

Copier également le répertoire jelix-modules/junittests/install/www/tests dans myApp/www/.


## les fichiers de tests

Les tests unitaires sont des scripts qui font des tests sur des classes, des
méthodes, des fonctions.

Vous devez donc réaliser des classes, qui héritent de la classe UnitTestCase de
simpletest, et vous les placez dans un ou plusieurs fichiers dans les répertoires "tests"
de vos modules.

Ces fichiers seront ensuite appelés par le module junittests quand vous voudrez
les exécuter.

Les tests peuvent être lancés soit par l'interface web de junittests, soit par
le script en ligne de commande fourni par junittests. Certains tests doivent
parfois s'exécuter uniquement via le web, ou uniquement via la ligne de commande
(tout dépend de la nature de ces tests). Aussi les noms des fichiers de tests
doivent se terminer par un suffixe précis pour faire savoir à junittests les
tests qu'il peut lancer dans tel ou tel contexte :

* //.html.php// : le test ne pourra être lancé que via l'interface web
* //.cli.php// : le test ne pourra être lancé que via la ligne de commande
* //.html_cli.php// : le test peut être lancé indifféremment via le web ou via la ligne de commande.

Le nom qui précède le suffixe importe peu. Sachez toutefois qu'il sert de
libellé lors des affichages des tests, et une transformation est effectué sur ce
nom pour un affichage plus lisible :

* les points sont transformés en ": "
* les caractères soulignés "_" sont transformés en espaces.

Par exemple si on nomme le fichier ainsi, "jdao.main_api_with_pdo.html.php", le
libellé des tests contenus dans ce fichier sera "jdao: main api with pdo". Et
comme il a le suffixe ".html.php", il ne pourra être lancé que via l'interface
web de junittests.


### Création d'un test

Pour créer un test, il faut créer une classe héritant de UnitTestCase ou des
autres classes héritières de UnitTestCase proposées par simpletest, et y écrire
des méthodes dont le nom doit commencer par "test". Ces méthodes feront alors
les tests que vous désirez, en utilisant l'API de simpletest. Pour plus de
détails sur cette API, lisez la [documentation sur le site de simpletest](http://simpletest.org/fr/),
en particulier [la page sur unittestcase](http://simpletest.org/fr/unit_test_documentation.html).
Notez que dans cette documentation, vous devez ignorer tout ce
qui concerne les "reporters" et les "group tests" : le module junittests
s'occupant déjà de tout ça. Vous pouvez aussi regarder les tests qui sont
présents dans le module unittest de l'application testapp disponible en
téléchargement.

Voici un exemple de test. Admettons que l'on veuille faire des tests sur une
classe "panier" d'un module "shop". On créer alors un fichier
"shop/tests/panier.html.php" et on y place la classe suivante :

```php
class testShopPanier extends UnitTestCase {

   function testPanierVide () {
      $panier = jClasses::create("shop~panier");
      $content = $panier->getProductList();
      $this->assertIdentical( $content, array());
   }
}
```

Le nom de la classe "testShopPanier" est totalement libre. Mais il faut faire
attention que ce ne soit pas un nom déjà pris par une autre classe de tests dans
d'autres modules. Aussi il est recommandé que le nom contienne le nom du module
ou autre signe distinctif.

On a ici créé une fonction qui teste si, lors de la création d'un panier,
celui-ci est bien vide. On instancie donc la classe //panier//, on appelle sa
méthode //getProductList// qui devrait nous renvoyer une liste de produit. Et
ensuite on test si le contenu renvoyé est bien un tableau vide.

Vous pouvez ajouter autant de méthodes de tests que vous voulez dans une même
classe de test, mais aussi de classes dans un seul fichier. Voici un deuxième
exemple :

```php
   function testAjoutProduit () {
     // creation d'un panier
     $panier = jClasses::create("shop~panier");

     // creation d'un produit
     $product = jClasses::create("shop~product");
     $product->label = "DVD coluche";
     $product->price = 12.40;

     // ajout du produit dans le panier
     $panier->addProduct($product);

     $liste = $panier->getProductList();

     // test si le panier contient bien un produit
     $this->assertTrue( count($liste) == 1);

     // test si le produit contenu correspond bien à celui mis
     $p = $liste[0];
     // verification que c'est un objet product
     if($this->assertIsA($p , 'product')){
          $this->assertEqual($p->label , 'DVD coluche');
          $this->assertEqual($p->price , 12.40);
     }

     // on enleve le produit
     $panier->removeProduct('DVD coluche');

     // on vérifie que le panier est bien vide à nouveau
     $content = $panier->getProductList();
     $this->assertIdentical( $content, array());

   }
```



### Lancement des tests via l'interface web

Le lancement des tests se fait en appelant la page principale du module
junittests. Un exemple d'url :
http://testapp.jelix.org/index.php?module=junittests . Vous pouvez d'ailleurs
vous rendre à cette url précise : vous y verrez tous les tests unitaires sur
jelix ;-)

N'oubliez pas de mettre dans la configuration le paramètre enableTests = on,
sinon vous aurez droit à une erreur 404.

Cette première page présente sur la gauche toute la liste des tests présents
dans votre application, classés par module. Il suffit de cliquer sur un des
tests pour le lancer et voir le résultat. Vous avez des liens aussi pour lancer
tous les tests d'un module, ou tous les tests de votre application (attention
cependant, les lancer tous peut être long pour les grosses applications, et
provoquer un "timeout" au niveau du navigateur).

À la fin du lancement de tests, il est affiché le nombre de tests unitaires qui
sont passés avec succès, et celui des tests échoués.

Notez que le module junittests utilise sa propre réponse HTML, et fait appel à
une feuille de style tests/design.css qui doit être placée dans le répertoire
www de votre application. Vous en trouverez une dans le répertoire //install//
du module junittests.


### Lancements des tests via la ligne de commande

Le lancement des tests se fait en exécutant un script ''tests.php'' se trouvant
dans le répertoire scripts de l'application jelix. Vous pouvez utiliser
l'application [testapp](http://jelix.org/articles/telechargement/stable) (voir
dans "Application de test") pour en voir un exemple.

Voici une listes des différentes commandes disponibles :

#### Lister tous les tests

Voir la liste de tous les tests de votre application rangés par modules. Vous
devez préciser seulement ici le nom du contrôleur car le mot-clé "help" renvoie
vers l'aide générale de la ligne de commande de Jelix. Exemple :

```bash
php tests.php default:help
```

#### Lancer tous les tests de l'application

Exécuter l'ensemble des tests de votre application. Pas besoin ici de paramètre
particulier, car exécuter tous les tests de l'application est l'action par
défaut. Exemple :

```bash
php tests.php
```

#### Lancer tous les tests d'un module

Exécuter l'ensemble des tests d'un module spécifié. Vous devez ici donner le nom
de votre module en paramètre. Exemple pour testapp :

```bash
php tests.php module jelix_tests
```

#### Lancer un test particulier

Exécuter le test spécifié. Vous devez ici donner le nom du module et le nom du
test en paramètre, qui est spécifié dans la commande help (entre parenthèses).
Exemple :

```bash
php tests.php single jelix_tests core.jlocale
```

À la fin du lancement des tests, il est affiché le nombre de tests unitaires qui
sont passés avec succès, et celui des tests échoués.

Notez que si vous n'avez pas le fichier ''tests.php'' dans le répertoire
scripts, vous pouvez récupérer tous les fichiers nécessaires (le script et le
fichier de configuration) dans le répertoire install du module junittests.


