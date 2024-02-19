<?php /*

function change_student_theme($connect,$student_practic_id,$new_theme){
	$connect->query('UPDATE Practices.student_practic SET theme='. $new_theme .' WHERE id = '. $student_practic_id .';');
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
function connection() {
      $host = 'localhost';
      $user = 'root';
      $pass = '';
      $db = 'Practices';
      try{
        #$connect = new mysqli($host, $user,$pass,$db);
		$connect = Bitrix\Main\Application::getConnection();
      }
      catch(Exception $e){
         die("[1] - connection_error");
      }

   return $connect;
   } 
	function get_user_id($connect){
		$user_id = $connect->query("SELECT LAST_USER_ID FROM b_stat_guest WHERE ID = " . $_COOKIE['BITRIX_SM_GUEST_ID']. ";")->Fetch()["LAST_USER_ID"];
		return $user_id;
	}
function get_user_groups($connect,$user_id){
		$groups_query = $connect->query("SELECT GROUP_ID FROM b_user_group WHERE USER_ID = '$user_id';");
		$groups=array();

		foreach($groups_query as $group){
			array_push($groups,$group["GROUP_ID"]);
		}
		return $groups;
	}
	function group_check($groups,$group){
	if (in_array($group, $groups)){
		return true;
	}
	return false;
}
*/?>
<?php
function change_student_theme($connect,$student_practic_id,$new_theme){
	$connect->query('UPDATE Practices.student_practic SET theme='. $new_theme .' WHERE id = '. $student_practic_id .';');
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
    function get_user_id($connect){
        $user_id = $connect->query("SELECT LAST_USER_ID FROM b_stat_guest WHERE ID = " . $_COOKIE['BITRIX_SM_GUEST_ID']. ";")->Fetch()["LAST_USER_ID"];
        return $user_id;
    }
    function get_user_groups($connect,$user_id){
        $groups_query = $connect->query("SELECT GROUP_ID FROM b_user_group WHERE USER_ID = '$user_id';");
        $groups=array();

        foreach($groups_query as $group){
            array_push($groups,$group["GROUP_ID"]);
        }
        return $groups;
    }
    function group_check($groups,$group){
    if (in_array($group, $groups)){
        return true;
    }
    return false;
}

   function create_excel($group_name){
      $connect=connection();

      $group_parts         = explode("-", $group_name);

      $stream_name         = $group_parts[0]."-".$group_parts[1];;

      $resultset         = $connect->query("SELECT id, full_name,code,profile_id FROM Practices.streams WHERE name ='$group_parts[0]' AND year = '$group_parts[1]';");
      $result             = $resultset->Fetch();
      $stream_id         = $result["id"];
      $profile_id          = $result["profile_id"];
      $stream_code      = $result["code"];
      $full_stream_name = $result["full_name"];

      $group_id           = $connect->query("SELECT id FROM Practices.groups WHERE stream_id ='$stream_id' AND group_number = '$group_parts[2]';")->Fetch()["id"];

      $resultset          = $connect->query("SELECT name,faculty_id FROM Practices.profiles WHERE id ='$profile_id';");
      $result             = $resultset->Fetch();
      $profile            = $result["name"];
      $faculty_id         = $result["faculty_id"];

      $faculty_name     = $connect->query("SELECT name FROM Practices.faculty WHERE id ='$faculty_id';")->Fetch()["name"];

      $group_students_resultset = $connect->query("SELECT id FROM Practices.students WHERE group_id ='$group_id';");
      $group_students = array();
      foreach ($group_students_resultset as $result) {
         array_push($group_students,$result["id"]);
      }
      $practice_students_resultset = $connect->query("SELECT student_id FROM Practices.student_practic");
      $practice_students = array();
      foreach ($practice_students_resultset as $result) {
         array_push($practice_students,$result["student_id"]);
      }

      $students_id=array_intersect($group_students,$practice_students);
      $group_count = sizeof($students_id);

      $date = date("y-n");
      $date = explode("-", $date);
      if ($date[1] > 9) {
         $group_course = $date[0]-$group_parts[1]+1;
      }
      else{
         $group_course = $date[0]-$group_parts[1];
      }

      #can't get for now
      $faculty_code     = '46';
      $practice_type     = 'производственной';
      $practice_code     = '2';
      $practice_type_2     = 'технологической (проектно-технологической) ';
      $practice_code_2     = '489';
      $date_first         = '20.06.2022';
      $date_second         = '17.07.2022';


      ob_end_clean(); 
      $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

      $sheet = $spreadsheet->getActiveSheet();

      $sheet->setTitle('Название листа');
      #head
      $sheet->mergeCells('B1:G1');
      $sheet->mergeCells('B2:G2');
      $sheet->mergeCells('C3:G3');
      $sheet->mergeCells('C5:I5');
      $sheet->mergeCells('C6:I6');
      $sheet->mergeCells('C8:G8');
      $sheet->mergeCells('C9:G9');

      $sheet->setCellValue('B1', 'Шаблон');   
      $sheet->setCellValue('I1', '5');

      $sheet->setCellValue('B2', 'проекта приказа на практику студентов');

      $sheet->setCellValue('B3', 'Факультет');
      $sheet->setCellValue('C3', $faculty_name);
      $sheet->setCellValue('H3', 'Код');
      $sheet->setCellValue('I3', $faculty_code);

      $sheet->setCellValue('B4', 'Группа');
      $sheet->setCellValue('C4', $group_name);
      $sheet->setCellValue('F4', 'Всего '.$group_count.' чел.');
      $sheet->setCellValue('G4', $group_count);
      $sheet->setCellValue('H4', 'Курс');
      $sheet->setCellValue('I4', $group_course);

      $sheet->setCellValue('B5', 'Направление');
      $sheet->setCellValue('C5', $profile);

      $sheet->setCellValue('B6', 'Профиль');
      $sheet->setCellValue('C6', $full_stream_name);

      $sheet->setCellValue('B7', 'Поток');
      $sheet->setCellValue('C7', $stream_name);
      $sheet->setCellValue('H7', 'Код');
      $sheet->setCellValue('I7', $stream_code);

      $sheet->setCellValue('B8', 'Вид практики');
      $sheet->setCellValue('C8', $practice_type);
      $sheet->setCellValue('H8', 'Код');
      $sheet->setCellValue('I8', $practice_code);

      $sheet->setCellValue('B9', 'Тип практики');
      $sheet->setCellValue('C9', $practice_type_2);
      $sheet->setCellValue('H9', 'Код');
      $sheet->setCellValue('I9', $practice_code_2);

      $sheet->setCellValue('B10', 'Сроки практики с');
      $sheet->setCellValue('C10', $date_first);
      $sheet->setCellValue('D10', 'по');
      $sheet->setCellValue('E10', $date_second);

      $sheet->setCellValue('B11', 'Выпускающая кафедра: ');

      $sheet->setCellValue('A13', '№ п/п');
      $sheet->setCellValue('B13', 'Студ.ИД');
      $sheet->setCellValue('C13', 'ФИО Студента');
      $sheet->setCellValue('D13', 'Категория');
      $sheet->setCellValue('E13', 'Наименование предприятия');
      $sheet->setCellValue('F13', 'Место нахождения предприятия');
      $sheet->setCellValue('G13', 'Способы проведения практик');
      $sheet->setCellValue('H13', 'ФИО руководителя полностью в вин. падеже(назначить кого)');
      $sheet->setCellValue('I13', 'Должность руководителя в вин. падеже (назначить кого)');
      $sheet->setCellValue('J13', '3-сторон. дог.');
      $sheet->setCellValue('K13', 'Работа по профилю');

      #body
      $startline = 13;
      $i=1;
      foreach ($students_id as $student_id) {
         $resultset = $connect->query("SELECT teacher_id,company_id FROM Practices.student_practic WHERE student_id = '$student_id' ;");
         $result = $resultset->Fetch();
         $teacher_id = $result["teacher_id"];
         $company_id = $result["company_id"];

         $student_fio = $connect->query("SELECT fio FROM Practices.students WHERE id ='$student_id';")->Fetch()["fio"];

         $resultset = $connect->query("SELECT fio,post FROM Practices.teachers WHERE id ='$teacher_id';");
         $result = $resultset->Fetch();
         $teacher_fio = $result["fio"];
         $teacher_post= $result["post"];

         $resultset = $connect->query("SELECT name FROM Practices.companies WHERE id ='$company_id';")->Fetch()["name"];

         $sheet->setCellValue('A'.($startline+$i), $i);
         $sheet->setCellValue('C'.($startline+$i), $student_fio);
         $sheet->setCellValue('E'.($startline+$i), $company_name);
         $sheet->setCellValue('H'.($startline+$i), $teacher_fio);
         $sheet->setCellValue('I'.($startline+$i), $teacher_post);

         $i=$i+1;
      }
      ;

      #tail
      $lastline = 14+$i;
      $sheet->mergeCells('D'.($lastline+0).':F'.($lastline+0));
      $sheet->mergeCells('G'.($lastline+0).':H'.($lastline+0));

      $sheet->mergeCells('D'.($lastline+2).':J'.($lastline+2));

      $sheet->mergeCells('D'.($lastline+6).':F'.($lastline+6));
      $sheet->mergeCells('G'.($lastline+6).':H'.($lastline+6));

      $sheet->mergeCells('D'.($lastline+8).':F'.($lastline+8));
      $sheet->mergeCells('G'.($lastline+8).':H'.($lastline+8));

      $sheet->mergeCells('D'.($lastline+10).':F'.($lastline+10));
      $sheet->mergeCells('G'.($lastline+10).':H'.($lastline+10));

      $sheet->setCellValue('B'.($lastline+0), 'Ответственный руководитель практики');
      $sheet->setCellValue('D'.($lastline+0), '_____________     / ________________________ /');
      $sheet->setCellValue('G'.($lastline+0), '"___" _____________  20__ г');

      $sheet->setCellValue('B'.($lastline+2), 'Основание:');
      $sheet->setCellValue('D'.($lastline+2), 'представление директора института ИТ и АД Говоркова А.С.');

      $sheet->setCellValue('B'.($lastline+4), 'СОГЛАСОВАНО:');

       $sheet->setCellValue('B'.($lastline+6), 'Декан/Директор');
      $sheet->setCellValue('D'.($lastline+6), '_____________     / ________________________ /');
      $sheet->setCellValue('G'.($lastline+6), '"___" _____________  20__ г');

      $sheet->setCellValue('B'.($lastline+6), 'Зав. кафедрой');
      $sheet->setCellValue('D'.($lastline+8), '_____________     / 
         ________________________ /');
      $sheet->setCellValue('G'.($lastline+8), '"___" _____________  20__ г');

      $sheet->setCellValue('B'.($lastline+10), 'Начальник отдела практик и СТВ');
      $sheet->setCellValue('D'.($lastline+10), '_____________     / ________________________ /');
      $sheet->setCellValue('D'.($lastline+10), '"___" _____________  20__ г');
      ob_end_clean(); 
      $path = '../../../sotrudniku/praktika/direktsiya/uploads/'.$group_name.'.Xls';

      $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
      $writer->save($path);

      $resultset = $connect->query("SELECT count(*) FROM Practices.templates WHERE group_id = '$group_id';")->Fetch()["count(*)"];
      if (!$resultset){
          $connect->query("INSERT INTO Practices.templates (group_id, name ,decanat_check) VALUES ('$group_id','$path','0');");
      }
   }
   function connection() {
      $host = 'localhost';
      $user = 'root';
      $pass = '';
      $db = 'Practices';
      try{
        #$connect = new mysqli($host, $user,$pass,$db);
        $connect = Bitrix\Main\Application::getConnection();
      }
      catch(Exception $e){
         die("[1] - connection_error");
      }

   return $connect;
   } 
   function company_file_upload(){
      $target_dir = "uploads/";
      $target_file = $target_dir . basename($_FILES["company_file"]["name"]);
      move_uploaded_file($_FILES["company_file"]["tmp_name"], $target_file);
      return $target_file;

   }

   function submit_handler($connect,$student_id) {
      try{
         if (enrolled_check($connect,$student_id)){
            if (work_load_check($connect)){
               $path = company_file_upload();
               $theme = $_POST['theme_field'];
//if (isset($_POST["theme"]))
               if ($_POST['cbMyCompany'] == false)
               {
                  if ($_POST["theme"] != "Своя тема")
                  {
                      $theme = $_POST["theme"];
                  }
print_r('<br>');
print_r($student_id);
print_r('<br>');
                   print_r($_POST['company_id']);
print_r('<br>');
print_r($_POST['teacher_id']);
print_r('<br>');
print_r($theme);
print_r('<br>');
print_r($path);
print_r('<br>');
print_r($_POST['cbMyCompany']);
                  $connect->query("INSERT INTO Practices.student_practic (student_id, teacher_id, company_id, theme, company_path, status) 
                        VALUES ('$student_id ','".$_POST['teacher_id']."', '".$_POST['company_id']."', '".$theme."', '".$path."', 0)");
               }
               else
                 {
                  $connect->query("INSERT INTO Practices.student_practic (student_id, teacher_id, theme, company_path, status) 
                        VALUES ('$student_id','".$_POST['teacher_id']."', '".$theme."', '".$path."',0)");
               }

               work_load_decriment($connect);
               succesfull_insert();
            }
            else{
               work_over_load();
            }
         }
         else{
            aleady_enrolled();
         }
      }
      catch(Exception $e){
         die("[2] - insert_error");
      }
   }

   function enrolled_check($connect,$student_id) {
      try{
         $resultset=$connect->query("SELECT student_id FROM Practices.student_practic Where student_id = $student_id");
         $result = $resultset->Fetch()["student_id"];
         if($result){
            return FALSE;
         }
         else{
            return TRUE;
         }
      }
      catch(Exception $e){
         die("[5] - select_error");
      }
   }

   function work_load_decriment($connect){
      try{
         $resultset=$connect->query("SELECT work_load FROM Practices.teachers Where id = '".$_POST['teacher_id']."'");
      }
      catch(Exception $e){
         die("[3] - select_error");
      }
      $result = $resultset->Fetch()["work_load"];
      try{
         $connect->query("UPDATE Practices.teachers SET work_load = ".--$result." WHERE id = ".$_POST['teacher_id']);
      }
      catch(Exception $e){
         die("[4] - update_error");
      }
   }

   function work_load_check($connect){
      try{
         $resultset=$connect->query("SELECT work_load FROM Practices.teachers Where id = '".$_POST['teacher_id']."'");
         $result = $resultset->Fetch()["work_load"];
      }
      catch(Exception $e){
         die("[3] - select_error");
      }

      if ($result["work_load"]>0){
         return True;
      }
      else{
         return False;
      }
   }

   function aleady_enrolled() {
      echo '<script type="text/javascript"> alert("Вы уже записаны!"); </script>';
   } 
   function work_over_load() {
      echo '<script type="text/javascript"> alert("У руководитель нет мест!"); </script>';
   } 
      function succesfull_insert() {
      echo '<script type="text/javascript"> alert("Вы Успешно записаны!"); </script>';
   }
function add_student_otchet($connect,$student_id) {
      try{
			      $connect->query("INSERT INTO Practices.student_otchet (student_id, link_ya, status) 
						VALUES ('$student_id ','".$_POST['YaUrl']."', 0)");

               succesfull_insert();
      }
      catch(Exception $e){
         die("[2] - insert_error");
      }
   }


?>