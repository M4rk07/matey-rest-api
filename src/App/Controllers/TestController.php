<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 25.10.16.
 * Time: 18.20
 */

namespace App\Controllers;


use Symfony\Component\HttpFoundation\Request;

class TestController extends AbstractController
{

    public function fillCategories (Request $request) {

        $categoriesFile = fopen("/var/www/matey-api/Texts/interests.txt", "r");


        if ($categoriesFile) {

            $lastDepth0 = '';
            $depth0id = 0;
            $lastDepth1 = '';
            $depth1id = 0;
            $lastDepth2 = '';
            $depth2id = 0;
            $lastDepth3 = '';
            $depth3id = 0;

            while (($buffer = fgets($categoriesFile)) !== false) {



                $rmCategory = str_replace('category::', '', $buffer);
                $categories = explode('>', $rmCategory);



                    $i=0;
                    foreach($categories as $category) {
                        $category = trim(preg_replace('/\s\s+/', ' ', $category));
                        echo "USAO:" . $category . "\n";
                        if ($i == 0) {
                            echo "USAO U 0:\n";
                            echo "DEPTH0id=".$depth0id."\n";
                            echo "LASTDEPTH0=".$lastDepth0."\n";
                            echo "SADA CATEGORY=".$category."\n";
                            if (strcmp($category, $lastDepth0) != 0) {
                                $lastDepth0 = $category;
                                $depth0id++;
                                $this->service->insertDepth0($depth0id, $category);
                            }

                        } else if ($i == 1) {
                            if (strcmp($category, $lastDepth1) != 0) {
                                $lastDepth1 = $category;
                                $depth1id++;
                                $this->service->insertDepth1($depth1id, $depth0id, $category);
                            }

                        } else if ($i == 2) {
                            if (strcmp($category, $lastDepth2) != 0) {
                                $lastDepth2 = $category;
                                $depth2id++;
                                $this->service->insertDepth2($depth2id, $depth1id, $category);
                            }

                        } else if ($i == 3) {
                            if (strcmp($category, $lastDepth3) != 0) {
                                $lastDepth3 = $category;
                                $depth3id++;
                                $this->service->insertDepth3($depth3id, $depth2id, $category);
                            }

                        }

                        $i++;
                        echo "ZAVRSIO PROLAZ\n";
                    }
                    echo "ZAVRSIO PETLJU\n";

            }
            if (!feof($categoriesFile)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($categoriesFile);
        }

        return $this->returnOk();

    }

    public function fillGroups() {
        $categoriesFile = fopen("/var/www/matey-api/Texts/interests.txt", "r");

        if ($categoriesFile) {

            $lastDepth0 = '';

            while (($buffer = fgets($categoriesFile)) !== false) {

                $rmCategory = str_replace('category::', '', $buffer);
                $categories = explode('>', $rmCategory);
                $categories[0] = trim(preg_replace('/\s\s+/', ' ', $categories[0]));
                if (strcmp($categories[0], $lastDepth0) != 0) {
                    $lastDepth0 = $categories[0];
                    $this->service->makeGroup($categories[0]);
                }


            }
            if (!feof($categoriesFile)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($categoriesFile);
        }

        return $this->returnOk();
    }

}