# Creating tests with Simpletest in Jelix

## installing

In your mainconfig.ini.php file, declare the simpletest-module directory in the modulePath
parameter of your app, and d Example:

```ini
modulesPath = lib:jelix-modules/,app:modules/,app:vendor/jelix/simpletest-module/
```

In your localconfig.ini.php, only in development environments, declare the module
module junittests

```ini
; for security, enable this boolean only on developement server
enableTests = on

[modules]

junittests.access = 2
```

The enableTests option activate the web interface of junittests, allowing to launch
Simpletests tests in a browser.

Then you can activate the module in your application.

```bash
php cmd.php installmodule junittests
```


## Test files

Test files are scripts defining your tests and are lying in your module
subfolder: "tests". You should develop classes inheriting from simpletest's UnitTestCase.

This test files will be used by JUnitTest runner's Web interface.

Test files name label interface's objects :

* s/\./ :/ (dots are replaced by ": "),
* s/_/ / (underscores are replaced by spaces).

Test file names must append any of these suffixes:

* //*.html.php// : runnable with the web interface.
* //.cli.php// : runnable with the CLI interface
* //.html_cli.php// : runnable through both interface.

For example, running tests from test-file "jdao.main.api_with_pdo.html.php" is
limited to the web interface runner. It's test-case will be labeled "jdao:
main api with pdo".


## Creating tests 

Read [SimpleTest overview](http://simpletest.org/en/) to write
your tests.

Junittests provides a reporter and a test-grouper.

Example : create the test-file "shop/tests/cart.html_cli.php" to test the class cart.php
stored in the module "shop" :

```php
/*
 Class name should be unique from all modules, it's recommanded that it contains
the module name.
*/
class testShopCart extends UnitTestCase {

  /*
   Tests that new cart method getProductList() returns an empty array.
  */
   function testNewCartHasNoProductsInList() {
      // Create a new cart
      $cart = jClasses::create("shop~cart");

      // Test the return value of cart method getProductList()
      $return = $cart->getProductList();
      $expected = array();
      $this->assertIdentical( $return, $expected );
   }
}
```

When this test passes : cart method getProductList() returns
the expected value (an empty array) when created by jClasses::create().

You can create other tests and share a fixture accross tests.

```php
class testShopCart extends UnitTestCase {
  protected static $cart;
  const PRODUCT_NAME_FIXTURE = "Dune";

  /**
   * Asserts that the cart is empty.
   *
   * @param object ShopCartClass object.
   */
  function assertCartEmpty( ShopCartClass $cart )
  {
    // Test the return value of cart method getProductList()
    $return = $cart->getProductList();
    $expected = array();
    $this->assertIdentical( $return, $expected );
  }

  function testNewCartHasNoProductsInList() {
    // Create a new cart
    self::$cart = jClasses::create("shop~cart");

    // Assert that the new cart is empty
    $this->assertCartEmpty( self::$cart );
  }

  function testAddingAFirstCartProduct() {
    // Create a product
    $product = jClasses::create("shop~product");
    $product->label = self::PRODUCT_NAME_FIXTURE;
    $product->price = 12.40;

    // Add it to the cart
    $panier->addProduct($product);

    // Test cart's method getProductList() return value
    $return = self::$class->getProductList();

    // Return value is an array
    $this->assertTrue(is_array($return));
    $this->assertEquals(count($return), 1);
    $this->assertIsA(current($return), 'productClass');
  }

  function testRemovingTheCartProduct()
  {
    self::$cart->removeProduct(self::PRODUCT_NAME_FIXTURE);

    // Assert that the new cart is empty
    $this->assertCartEmpty(self::$cart);
  }
}
```


## Web-interface test-runner

Connect to the main junittest module action as with the following URL to the Jelix test-suite:
http://testapp.jelix.org/index.php?module=junittests

Setting "enableTests = off" in the project configuration would generate a 404 HTTP page
access error response on this url.

Links are provided to run test-cases individually or all a module's tests in the left frame.

Success and failures are counted and reported at the end of the test.
Note that your browser could timeout before large tests are done.

Junittests uses it's own HTML responses, and uses it's tests/design.css which should be
copied from the "install" directory to your www directory.

## Command-line test runner

You can run the test with the ''tests.php'' script in the scripts directory of your jelix
application. You can use the [testapp](http://jelix.org/articles/en/download/stable)
application (see in "Test Application") to have a running sample of it.

Here is the list of the different commands available :


### Display all the tests

Allows you to display the list of all the tests of your application for each module of it.
You have to give the name of the controller here because the "help" keyword gives the
general help for Jelix command line.

```bash
php tests.php default:help
```

### Run all the tests of the application

Runs the whole set of tests of your Jelix application. No need for a specific parameter
here, because running all the tests is the default action.

```bash
php tests.php
```

### Run all the tests of a module

Runs the whole set of tests of a specified module. You have to specify here the name of
the module as a parameter. Here is an example for the testapp application :

```bash
php tests.php module jelix_tests
```

### Run only one test

Runs a specified test. You have here to give the name of the module and the name of the
test as parameters. You can help yourself by using the help command specified above to get
it.

```bash
php tests.php single jelix_tests core.jlocale
```

When the running of tests ends, the number of unit tests that succeeded and faild is
displayed.

If you don't have the ''tests.php'' file in the scripts directory, you can retrieve all
the necessary files (the script and the config file) in the install directory of the
junittests module.

