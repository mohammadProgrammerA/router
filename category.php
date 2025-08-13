<?php
	//include "model/category.php";
	//include "menu.php";
	// $selectQuery="SELECT * FROM category";
	// $result=$newDbconnection->select($selectQuery);
	
	//$categoryConnection=new categoryConnection();
	// $model-_product=$category= factoryClass :: makeObject("product");
    $result = category::select() -> count('product' , 'categoryid') ->get();
    // $result = category::select() -> count('product' , 'categoryid')  ->where("name" ,"کالای دیجیتال2")->get();
	//  echo $result -> num_rows;
	while($row=$result->fetch_assoc()){
		//  var_dump($row);
		//  echo "<br>";
		// $products = product::select() -> where("categoryid",$row["id"],"=") ->get();
		
?>
	<div style="width:820px;height:72px;background-color:yellow;margin-top:10px;">
		<div style="width:100px;height:50px;background-color:red;margin-left:10px;float:left;">
			<span> <?=$row['title'];?><span>
		</div>
		<div style="width:100px;height:50px;background-color:red;margin-left:10px;float:left;">
			<span> <?=$row['numberproduct'];?><span>
		</div>
		<div style="width:100px;height:50px;background-color:red;margin-left:10px;float:left;">
			<span> <?=$row['disciription'];?><span>
		</div>
		<div style="width:100px;height:50px;background-color:aqua;margin-left:10px;float:left;">
			<span> تعداد پروداکت<?=$row['product_count'];?><span>
		</div>
		<a href="http://localhost/router/editCategory/<?=$row["id"];?>">
		<div style="width:100px;height:50px;background-color:green;margin-left:10px;float:left;">
			<span style="backgroun-color:skyblue;"> edit <span>
		</div>
	    </a>
		<a href="http://localhost/router/deletCategory/<?=$row["id"];?>">
		<div style="width:100px;height:50px;background-color:skyblue;margin-left:10px;float:left;">
			<span style="backgroun-color:skyblue;color:green;"> delet <span>
		</div>
	    </a>
		<a href="http://localhost/router/showCategory/<?=$row["id"];?>">
		<div style="width:100px;height:50px;background-color:skyblue;margin-left:10px;float:left;">
			<span style="backgroun-color:skyblue;color:green;"> show <span>
		</div>
	    </a>
	</div>

<?php
	  }
?>