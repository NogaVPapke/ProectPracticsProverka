<?
/*ИЗНАЧАЛЬНЫЙ КОД ЗАКОММЕНТИРОВАН ВНИЗУ*/
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Преподаватель");

require_once('function-proverka-otcheta.php');
$connect=connection();

if (isset($_GET['student_id'])) {
	$student_practic_id=$_GET['student_id'];
	$connect->query("UPDATE Practices.student_practic SET status='1' WHERE id = '$student_practic_id';");
}

if (isset($_GET['student_theme'])) {
	change_student_theme($connect,$_GET['student_theme'],$_GET['new_theme']);
}
$user_id = get_user_id($connect);
#$user_id=251;

$fio_resultset = $connect->query("SELECT NAME,LAST_NAME,SECOND_NAME FROM b_user WHERE ID = '$user_id';")->Fetch();
print("[debug] Пользователь: ".$fio_resultset["LAST_NAME"]." ".$fio_resultset["NAME"]." ".$fio_resultset["SECOND_NAME"]);

$groups=get_user_groups($connect,$user_id);

if (group_check($groups,18) == false){
	exit();
}

$teacher_id=get_teacher_id($connect,$user_id);


/*================= ИЗ ДРУГОГО ПРОЕКТА ==================*/

if (isset($_GET['done'])) {
	$connect->query("UPDATE Practices.student_practic SET status = 1, WHERE id =". $_GET['done'] .";");
	//Download_Templace($_GET['done']);
}
if (isset($_GET['noShow'])) {
	$connect->query("UPDATE Practices.student_practic SET status = 0, WHERE id =". $_GET['noShow'] .";");
	//Download_Templace($_GET['done']);
}
if (isset($_GET['remake'])) {
	$connect->query("UPDATE Practices.student_practic SET status = 2, WHERE id =". $_GET['remake'] .";");
	//Download_Templace($_GET['remake']);
}

/*================= ОБРАБОТЧИК КНОПКИ "ФАЙЛ" ==================*/

if (isset($_POST['download'])){  
	Download_Otchet($student_request['company_path']);
}

?>
<!DOCTYPE html>
<html>

<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous" defer></script>
    <style>

		/*========================= ОСНОВА =========================*/
        /*========================= ОСНОВА =========================*/

		.table {
			background: #ffffff !important;
			border-collapse: collapse;
			box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
			font-size: 14px;
			text-align: left;
			max-width: 1450px;
			min-width: 800px;
			width: 100%;
			margin: 0 auto;
			color: #1E8EC2;
			font-family: Helvetica Neue OTS, sans-serif;
		}

		.thead {
			border-bottom: 1px solid black;
		}

		.th {
			text-align: center;
			font-family: inherit;
		}

		.td {
			text-align: center;
			font-family: inherit;
			text-align: center;
    		vertical-align: middle;
		}

		.td-status{
			width: 150px;
		}

		.block-div{
			display: flex;
            justify-content: center;
            align-items: center;
			text-align: center;
		}

		/*========================= КНОПКИ =========================*/
        /*========================= КНОПКИ =========================*/

        .btn {
            background: none;
            color: inherit;
            border: none;
            padding: 0;
            font: inherit;
            cursor: pointer;
            outline: inherit;
			color: #1E8EC2;
        }

		.btn:hover{
			text-decoration: underline;
			color: #1E8EC2;
		}

		/*======================== ДЕЙСТВИЕ ========================*/
        /*======================== ДЕЙСТВИЕ ========================*/

		.action {
            list-style-type: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 0;
			margin: 0;
        }

		.dropdown-item1 {
			background: url(https://cdn-icons-png.flaticon.com/512/8832/8832098.png) 50% 50% no-repeat;
			background-size: contain;
			border-radius: 100%;
			width: 30px;
			height: 30px;
		}

		.dropdown-item1:hover {
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
		}

		.dropdown-item2 {
			background: url(https://cdn-icons-png.flaticon.com/512/179/179386.png) 50% 50% no-repeat;
			background-size: contain;
			border-radius: 100%;
			width: 30px;
			height: 30px;
		}

		.dropdown-item2:hover {
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
		}

		/*========================= СТАТУС =========================*/
        /*========================= СТАТУС =========================*/

		.block-status_ok {
			display: inline-block; 
			background-color: #b1f0ad; 
			padding: 5px; 
			border-radius: 15px;
			text-align: center;
		}

		.block-status_fail {
			display: inline-block; 
			background-color: #fadadd; 
			padding: 5px; 
			border-radius: 15px;
			text-align: center;
		}

		.status-check_fail {
            color: #f23a11;
        }

        .status-check_ok {
            color: #1F9254;
        }

		/*======================= НАД ТАБЛИЦЕЙ =======================*/
        /*======================= НАД ТАБЛИЦЕЙ =======================*/

        .remote {
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            font-size: 14px;
            flex-basis: 100px;
        }

        .remote-rigth {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mar-off {
            margin: 0;
        }

		.checkbox {
			margin: 2px 0 0 3px ;
		}
    </style>

</head>

<body class="body">
    <table class="table">
		<div class="remote">
			<div class="remote-left">
				<p class="mar-off">Осталось мест:</p>
			</div>
			<div class="remote-rigth">
				<p class="mar-off">Показать отмененные</p>
				<input class="checkbox" type="checkbox">
			</div>
		</div>
        <thead class="thead">
            <tr class="tr">
                <th class="th">ФИО студента</th>
                <th class="th">Компания</th>
                <th class="th">Тема практики</th>
                <th class="th">Ссылка на отчет</th>
                <th class="th">Статус</th>
                <th class="th">Действие</th>
            </tr>
        </thead>
        <tbody class="tbody">
				<!-- <tr class="tr">
                        <td class="td"><strong class="strong"> '.$student_fio.' </strong></td>
                        <td class="td"> '.$company_name.' </td>
                        <td class="td"> '.$theme.' </td>
						<td class="td">
                            <div class="block-file">
                                <button class="btn-file">Файл</button>
                            </div>
						</td>
						<td class="td status-check_fail"> Не проверено! </td>
                        <td class="td">
                            <ul class="action" aria-labelledby="btnGroupDrop1">
                                <form>
                                    <li><button type="sumbit" name="done" value="'.$sp['id'].'" class="btn dropdown-item1" href="#"></button></li>
                                </form>
                                <form>
                                    <li><button type="sumbit" name="noShow" value="'.$sp['id'].'" class="btn dropdown-item2" href="#"></button></li>
                                </form>
                            </ul>
                        </td>
                    </tr> -->
            <?php 
                    //$student_practics=get_student_practics($connect,$teacher_id);
					$student_otchet=$connect->query('SELECT * from Practices.student_otchet');
                    // foreach($student_practics as $student_practic){
						while($sp = $student_otchet->Fetch()){
							/*if ($sp["status"] == 1){
                            continue;
						}*/
                        $student_id   = $sp["student_id"];
							//print_r($student_id.'<br>');
												$student_fio  = $connect->query("SELECT fio FROM Practices.students WHERE id = '$student_id';")->Fetch()["fio"];
												$student_otchet =$connect->query("SELECT * from Practices.student_otchet WHERE id = '$student_id';");
												$student_practic =$connect->query("SELECT * from Practices.student_practic WHERE student_id = '$student_id';")->Fetch();
												$company =$connect->query('SELECT * from Practices.companies WHERE id = '.$student_practic['company_id'].';')->Fetch();
							//print_r($student_fio.'<br>');
							//$company_id   = $sp["company_id"];
							//print_r($company_id.'<br>');
							//$company_name = $connect->query("SELECT name FROM Practices.companies WHERE id = '$company_id';")->Fetch()["name"];
							//print_r($company_name.'<br>');
							//$theme        = $sp["theme"];
							//print_r($theme.'<br>');
												$student_otchet_id = $sp["id"];
							//print_r($student_practic_id.'<br>');

						/*================= СБОРКА ТАБЛИЦЫ ==================*/
						/*================= СБОРКА ТАБЛИЦЫ ==================*/
						
						echo '
							<tr class="tr">
							<td class="td">
								<div class="block-div">
									<strong class="strong"> '.$student_fio.' </strong> 
								</div>
							</td>';

							echo '
								<td class="td"> 
									<div class="block-div"> <p class="mar-off">'.$company['name'].'</p> </div>
								</td>';
						echo '
							<td class="td"> 
								<div class="block-div"> '.$student_practic['theme'].' </div">
							</td>
							<td class="td"> 
								<form class="block-div">
									<a href="'.$sp['link_ya'].'" class="btn">Ссылка</a>
								</form>
							</td>';
						if($sp["status"] == 1) // необходимо поменять условие, сделано чисто как пример
							echo ' 
								<td class="td td-status">
									<div class="block-status_ok">
										<span class="status-check_ok">Принят</span>
									</div>
								</td>';
						else
							echo ' 
								<td class="td td-status">
									<div class="block-status_fail">
										<span class="status-check_fail">Не принят</span>
									</div>
								</td>';
						echo '<td class="td">
								<ul class="action" aria-labelledby="btnGroupDrop1">
									<form>
										<li><button type="sumbit" name="done" value="'.$sp['id'].'" class="btn dropdown-item1" href="#"></button></li>
									</form>
									<form>
									<textarea class="form-control" name="comment" value="'.$tmp['comment'].'" id="comment'.$tmp['id'].'" rows="1" style = "display: none; border-radius: 5px;" aria-label="Комментарий" aria-describedby="basic-addon2" placeholder="Комментарий"></textarea>
									</form>
								</ul>
							</td>
						</tr>';
                ?>
        </tbody>
        <?php
			}
		?>
    </table>
</body>

</html>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>


/*----------------------- BACKUP -----------------------*/
/*----------------------- BACKUP -----------------------*/

<?php /*
<?
<li><button type="sumbit" name="noShow" value="'.$sp['id'].'" class="btn dropdown-item2" href="#"></button></li>
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Преподаватель");

require_once('../../../studentu/praktika/functions.php');
$connect=connection();

if (isset($_GET['student_id'])) {
	$student_practic_id=$_GET['student_id'];
	$connect->query("UPDATE Practices.student_practic SET status='1' WHERE id = '$student_practic_id';");
}

if (isset($_GET['student_theme'])) {
	change_student_theme($connect,$_GET['student_theme'],$_GET['new_theme']);
}
function change_student_theme($connect,$student_practic_id,$new_theme){
	$connect->query("UPDATE Practices.student_practic SET theme='$new_theme' WHERE id = '$student_practic_id';");
}
function get_teacher_id($connect,$user_id){
	$user = $connect->query("SELECT NAME, LAST_NAME,SECOND_NAME FROM b_user WHERE ID = '$user_id';")->Fetch();
	$fio= $user["LAST_NAME"]." ".$user["NAME"]." ".$user["SECOND_NAME"];
	$teacher_id = $connect->query("SELECT * FROM Practices.teachers WHERE fio = '$fio';")->Fetch()["id"];
	return $teacher_id;
}

function get_student_practics($connect,$teacher_id){
	$student_practics = $connect->query("SELECT * FROM Practices.student_practic WHERE teacher_id = '$teacher_id';");
	return $student_practics;
}
$user_id = get_user_id($connect);
#$user_id=251;

$fio_resultset = $connect->query("SELECT NAME,LAST_NAME,SECOND_NAME FROM b_user WHERE ID = '$user_id';")->Fetch();
print("[debug] Пользователь: ".$fio_resultset["LAST_NAME"]." ".$fio_resultset["NAME"]." ".$fio_resultset["SECOND_NAME"]);

$groups=get_user_groups($connect,$user_id);

if (group_check($groups,18) == false){
	exit();
}

$teacher_id=get_teacher_id($connect,$user_id);


?>
<!DOCTYPE html>
<html>
	<head>
		<style>
			.table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 80%;
            border: 0;
            background-color: #eeeeee;
         }
         .td{
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
         }
         .th_1,. th_2,. th_3, th_4{
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            width: 25%;
         }
		 .th_1{
			width: 25%;
		 }
         .th_2{
			width: 25%;
         }
		.th_3{
            width: 35%;
         }
		 .th_4{
            width: 15%;
         }
         .tr{
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;

         }
         .tr:hover{
            background-color: #555;
            color: white;
         }
		 .theme_text_box{
		 	width: 75%;
			font-size: 15px;
			padding: 5px 2px; 
  			justify-content: left;
		 }
		 .btn{
		 }
		 .btn_change{
		 	display: inline-block;
			height: 40px;
			width: 20%;
		 }
		</style>
	</head>
	<body>
		<table class="table">
			<tr class="tr">
				<th class="th_1">ФИО</th>
				<th class="th_2">Компания</th>
				<th class="th_3">Тема</th>
			    <th class="th_4">Статус</th>
			</tr>
			<?php 
				$student_practics=get_student_practics($connect,$teacher_id);
				foreach($student_practics as $student_practic){
					if ($student_practic["status"] == 1){
						continue;
					}
					$student_id   = $student_practic["student_id"];
					$student_fio  = $connect->query("SELECT fio FROM Practices.students WHERE id = '$student_id';")->Fetch()["fio"];
					$company_id   = $student_practic["company_id"];
					$company_name = $connect->query("SELECT name FROM Practices.companies WHERE id = '$company_id';")->Fetch()["name"];
					$theme        = $student_practic["theme"];
					$student_practic_id = $student_practic["id"];
			?>
					<tr class="tr">
						<td class="td"> <? echo $student_fio  ?> </td>
						<td class="td"> <? echo $company_name ?> </td>
						<td class="td"> 
							<form>
								<input name="new_theme" type="text" class="theme_text_box" value="<? echo $theme ?>"> 
								<button name="student_theme" class="btn_change" value="<? echo $student_practic_id ?>" type="submit">Принять изменения</button>
							</form>
						</td>
						<td class="td"> <form> <button name="student_id" class="btn" value="<? echo $student_practic_id ?>" type="submit">Принять заявку</button> </form> </td>
					</tr>
			<?php
				}
			?>
		</table>


	</body>
</html>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");*/?> 