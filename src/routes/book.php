<?php
/**
 * Created by PhpStorm.
 * User: ilker
 * Date: 19.03.2019
 * Time: 23:40
 */

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//Tüm kitapları getir
$app->get('/books', function (Request $request, Response $response) {
    $db = new Db();

    try{
        $db = $db->connect();
        $books = $db->query("SELECT * FROM book")->fetchAll(PDO::FETCH_OBJ);

        return $response
            ->withStatus(200)
            ->withHeader("Content-Type","application/json")
            ->withJson($books);

    } catch (PDOException $e) {
        return $response->withJson(
            array(
                "error" => array(
                    "text" => $e->getMessage(),
                    "code" => $e->getCode()
                )
            )
        );
    }
    //$db=null;
});

//sadece bir kaydı getirme
$app->get('/books/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute("id");
    $db = new Db();
    try{
        $db = $db->connect();
        $books = $db->query("SELECT * FROM book WHERE id=$id")->fetchAll(PDO::FETCH_OBJ);

        return $response
            ->withStatus(200)
            ->withHeader("Content-Type","application/json")
            ->withJson($books);

    } catch (PDOException $e) {
        return $response->withJson(
            array(
                "error" => array(
                    "text" => $e->getMessage(),
                    "code" => $e->getCode()
                )
            )
        );
    }
    //$db=null;
});

//yeni kitap ekle
$app->post('/book/add', function (Request $request, Response $response) {

     $name =  $request -> getParam("name");
     $description = $request -> getParam("description");

    $db = new Db();
    try{
        $db = $db->connect();
        $statement = "INSERT INTO book (name,description) VALUES (:name, :description)";
        $prepare = $db->prepare($statement);
        $prepare->bindParam("name",$name);
        $prepare->bindParam("description",$description);

        $book = $prepare->execute();

        if ($book) {
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type","application/json")
                ->withJson(array(
                    "text" => "Kitap başarılı bir şekilde eklenmiştir"
                ));
        } else {
            return $response
                ->withStatus(500)
                ->withHeader("Content-Type","application/json")
                ->withJson(array(
                    "error" => array(
                        "text" => "Ekleme işleminde bir hata oluştu"
                    )
                ));
        }



    } catch (PDOException $e) {
        return $response->withJson(
            array(
                "error" => array(
                    "text" => $e->getMessage(),
                    "code" => $e->getCode()
                )
            )
        );
    }
    //$db=null;
});

//kitap güncelleme
$app->put('/book/update/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute("id");
    if($id){
        $name      = $request->getParam("name");
        $description = $request->getParam("description");
        $db = new Db();
        try{
            $db = $db->connect();
            $statement = "UPDATE book SET name =:name, description =:description WHERE id = $id";
            $prepare = $db->prepare($statement);
            $prepare->bindParam("name", $name);
            $prepare->bindParam("description", $description);
            $book = $prepare->execute();
            if($book){
                return $response
                    ->withStatus(200)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "text"  => "Kitap başarılı bir şekilde güncellenmiştir.."
                    ));
            } else {
                return $response
                    ->withStatus(500)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "error" => array(
                            "text"  => "Düzenleme işlemi sırasında bir problem oluştu."
                        )
                    ));
            }
        }catch(PDOException $e){
            return $response->withJson(
                array(
                    "error" => array(
                        "text"  => $e->getMessage(),
                        "code"  => $e->getCode()
                    )
                )
            );
        }
        //$db = null;
    } else {
        return $response->withStatus(500)->withJson(
            array(
                "error" => array(
                    "text"  => "ID bilgisi eksik.."
                )
            )
        );
    }
});

//kitap sil
$app->delete('/book/delete/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute("id");
    if($id){
        $db = new Db();
        try{
            $db = $db->connect();
            $statement = "DELETE FROM book  WHERE id = :id";
            $prepare = $db->prepare($statement);
            $prepare->bindParam("id", $id);
            $book = $prepare->execute();

            if($book){
                return $response
                    ->withStatus(200)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "text"  => "Kitap başarılı bir şekilde silinmiştir.."
                    ));
            } else {
                return $response
                    ->withStatus(500)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "error" => array(
                            "text"  => "Silme işlemi sırasında bir problem oluştu."
                        )
                    ));
            }
        }catch(PDOException $e){
            return $response->withJson(
                array(
                    "error" => array(
                        "text"  => $e->getMessage(),
                        "code"  => $e->getCode()
                    )
                )
            );
        }
        //$db = null;
    } else {
        return $response->withStatus(500)->withJson(
            array(
                "error" => array(
                    "text"  => "ID bilgisi eksik.."
                )
            )
        );
    }
});
