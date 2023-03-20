<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data insert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/foodhut.css">
    <link rel="stylesheet" href="assets/css/login.css">

</head>

<body>
   

    <div class="container-fluid form">
     
        <div class="row">
            <div class="col"></div>
                <div class="col-md-6 col-sm-6 col-xs-10">
                    <form class="py-4 pl-4 pr-4" target="_self" action="data_insert_take.php" method="post"
                        enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label for="producutnumber" class="form-label">producut number</label>
                            <input name='product_no' type="number" class="form-control" id="exampleInputEmail1"
                                aria-describedby="emailHelp" placeholder="producut number" required>
                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                        </div>
                        <div class="mb-3">
                            <label for="producut_name" class="form-label">producut name</label>
                            <input name='product_name' type="text" class="form-control" id="exampleInputEmail1"
                                aria-describedby="emailHelp" placeholder="producut name" required>
                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                        </div>
 
                        <div class="mb-3">
                            <label for="product price" class="form-label">product price</label>
                            <input name='product_price' type="number" class="form-control" id="exampleInputEmail1"
                                aria-describedby="emailHelp" placeholder="User123" required>
                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                        </div>
                        <div class="mb-3">
                            <label for="product_type"  class="form-label">product type</label>
                            <select type="text" name="product_type" class="form-control" id="exampleInputEmail1"
                                aria-describedby="emailHelp" required>
                                <option value="pizza">Pizza</option>
                                <option value="Burger">Burger</option>
                                <option value="Chinease">Chinease</option>
                                <option value="Maxican">Maxican</option>
                            </select>
                            <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
                        </div>
                        <div class="mb-3">
                            <label for="product_qty" class="form-label">product qty</label>
                            <input type="number" name='product_qty' class="form-control" id="exampleInputPassword1" required>
                            <!-- <p class="sm-text"><a href="forget.html">Forget password?</a></p> -->
                        </div>
                        <div class="mb-3">
                            <label for="product_desc" class="form-label">product desc</label>
                            <input type="textarea" name='product_desc' class="form-control" id="exampleInputPassword1" required>
                            <!-- <p class="sm-text"><a href="forget.html">Forget password?</a></p> -->
                        </div>
                        <div class="mb-3">
                            <label for="product_img" class="form-label">product qty</label>
                            <input type="file" name='product_img' class="form-control" id="exampleInputPassword1" required>
                            <!-- <p class="sm-text"><a href="forget.html">Forget password?</a></p> -->
                        </div>

                        <button type="submit" class="btn btn-primary " name="submit">Add</button>

                    </form>
                </div>
            <div class="col"></div>
        </div>
    </div>

</body>

</html>