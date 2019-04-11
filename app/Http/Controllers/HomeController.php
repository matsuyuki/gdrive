<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }
    public function index()
    {
        $sub = DB::table('sub_api_folder')->select('*')->orderBy('name','asc')->get();
        $main = DB::table('main_folder')->select('*')->orderBy('email','asc')->get();
        return view('index',compact('sub','main'));
    }

    public function fail(){
      $data = array(
        'status'  => 'Unauthorized'
      );
      return json_encode($data);
    }

    public function folder(Request $request){
      $sub = DB::table('sub_api_folder')->select('*')->orderBy('name','asc')->get();
      return response()->json($sub);
    }

    public function list(){
      $check = DB::table('sub_api_folder')->select('*')->where('folder_id',$_GET['folder_id'])->count();
      if($check == 0){
        return response()->json([
            'message' => 'Folder ID Invalid'
        ], 401);
      }
      $data='';
      $folder = 'application/vnd.google-apps.folder';
      $folder_id = "'$_GET[folder_id]'+in+parents";
      $filter =  'fields=files(fullFileExtension%2Cid%2CmimeType%2Cname%2CoriginalFilename%2CwebContentLink%2CwebViewLink)';
      $filter2 = 'fields=files(fileExtension%2Cid%2CmimeType%2Cname%2CoriginalFilename%2CwebContentLink%2CwebViewLink)';
      $api_key = 'AIzaSyA86pql3oG6Jy0bXW_XjjGQoORtAq52z_4';
      $results = file_get_contents("https://www.googleapis.com/drive/v3/files?q=".$folder_id."&".$filter."&key=".$api_key);
      $results = json_decode ($results);
      foreach($results->files as $key => $val){
        $list[] = (object) [
          'name' => str_replace('.',' ',substr($val->name, 0, strrpos($val->name, "."))),
          'link' =>  $val->webViewLink
        ];
      }
      if(empty($list)){
        return response()->json([
            'message' => 'Folder Empty'
        ], 401);
      }else{
        return response()->json($list);
      }
    }

    public function sync(){
      $list = DB::table('main_folder')->select('*')->orderBy('email','asc')->get();
      $folder = 'application/vnd.google-apps.folder';
      $filter =  'fields=files(fullFileExtension%2Cid%2CmimeType%2Cname%2CoriginalFilename%2CwebContentLink%2CwebViewLink)';
      $filter2 = 'fields=files(fileExtension%2Cid%2CmimeType%2Cname%2CoriginalFilename%2CwebContentLink%2CwebViewLink)';
      $api_key = 'AIzaSyA86pql3oG6Jy0bXW_XjjGQoORtAq52z_4';
      foreach($list as $k => $v){
        if(isset($v->folder_id) && !empty($v->folder_id)){
          $folder_id = "'$v->folder_id'+in+parents";
          $results = file_get_contents("https://www.googleapis.com/drive/v3/files?q=".$folder_id."&".$filter."&key=".$api_key);
          $results = json_decode ($results);
          foreach($results->files as  $val){
            if($val->mimeType == $folder){
              $check_folder = "'$val->id'+in+parents";
              $check_result = file_get_contents("https://www.googleapis.com/drive/v3/files?q=".$check_folder."&".$filter."&key=".$api_key);
              $check_result = json_decode ($check_result);
              if($check_result->files[0]->mimeType == $folder){
                foreach($check_result->files as  $c){
                  if($c->mimeType == $folder){
                    $check = DB::table('sub_api_folder')->select('*')->orderBy('name','asc')->where('folder_id',$c->id)->count();
                  //  print_r($q_check);exit;
                    if($check == 0){
                      $name = str_replace('.',' ',$c->name);
                      $save = DB::table('sub_api_folder')->insert([
                        'main_api_folder_id'  => $val->id,
                        'name'       => $name,
                        'folder_id' => $c->id
                      ]);
                      if(!$save){
                        $r[] = $name.'--'.$c->id;
                      }
                    }
                  }
                }
              }else{
                $check = DB::table('sub_api_folder')->select('*')->orderBy('name','asc')->where('folder_id',$val->id)->count();
              //  print_r($q_check);exit;
                if($check == 0){
                  $name = str_replace('.',' ',$val->name);
                  $save = DB::table('sub_api_folder')->insert([
                    'main_api_folder_id'  => $v->id,
                    'name'       => $name,
                    'folder_id' => $val->id
                  ]);
                  if(!$save){
                    $r[] = $name.'--'.$v->id;
                  }
                }
              }
            }
          }
        }
      }
      $list = DB::table('sub_api_folder')->select('*')->orderBy('name','asc')->get();
      $folder = array();
      foreach($list as $k => $v){
        $folder[$k]['name'] = str_replace('.',' ',$v->name);
        $folder[$k]['folder_id'] = $v->folder_id;
      }
      echo json_encode($folder,true);
    }

    public function json(){
      $data='';
      $folder = 'application/vnd.google-apps.folder';
      $folder_id = "'$_POST[folder_id]'+in+parents";
      $filter =  'fields=files(fullFileExtension%2Cid%2CmimeType%2Cname%2CoriginalFilename%2CwebContentLink%2CwebViewLink)';
      $filter2 = 'fields=files(fileExtension%2Cid%2CmimeType%2Cname%2CoriginalFilename%2CwebContentLink%2CwebViewLink)';
      $api_key = 'AIzaSyA86pql3oG6Jy0bXW_XjjGQoORtAq52z_4';
      $results = file_get_contents("https://www.googleapis.com/drive/v3/files?q=".$folder_id."&".$filter."&key=".$api_key);
      $results = json_decode ($results);
      foreach($results->files as $key => $val){
        $list[] = (object) [
          'title' => str_replace('.',' ',substr($val->name, 0, strrpos($val->name, "."))),
          'link' =>  $val->webViewLink
        ];
      }
      if(empty($list)){
        $error = (object)[
          'status'  => 'Folder Kosong'
        ];
        print_r(json_encode($error));exit;
      }else{
        print_r(json_encode($list,JSON_UNESCAPED_SLASHES));exit;
      }
    }

    public function list_file(){
      $data='';
      $folder = 'application/vnd.google-apps.folder';
      $folder_id = "'$_POST[folder]'+in+parents";
      $check = DB::table('sub_api_folder')->select('*')->where('folder_id',$_POST['folder'])->count();
      if($check ==0){
        echo 'ID Folder salah';exit;
      }
      $filter =  'fields=files(fullFileExtension%2Cid%2CmimeType%2Cname%2CoriginalFilename%2CwebContentLink%2CwebViewLink)';
      $filter2 = 'fields=files(fileExtension%2Cid%2CmimeType%2Cname%2CoriginalFilename%2CwebContentLink%2CwebViewLink)';
      $api_key = 'AIzaSyA86pql3oG6Jy0bXW_XjjGQoORtAq52z_4';
      $results = file_get_contents("https://www.googleapis.com/drive/v3/files?q=".$folder_id."&".$filter."&key=".$api_key);
      $results = json_decode ($results);
      foreach($results->files as $key => $val){
        $list[] = (object) [
          'title' => str_replace('.',' ',substr($val->name, 0, strrpos($val->name, "."))),
          'link' =>  $val->webViewLink
        ];
      }
      if(empty($list)){
        $error = (object)[
          'error'  => 'Folder Kosong'
        ];
        print_r(json_encode($error));exit;
      }else{
        print_r(json_encode($list,JSON_UNESCAPED_SLASHES));exit;
      }
    }

    public function curl_api(){
      $data='';
      $epi = $_POST['episode'];
      $folder = 'application/vnd.google-apps.folder';
      $folder_id = "'$_POST[folder]'+in+parents";
      $check = DB::table('sub_api_folder')->select('*')->where('folder_id',$_POST['folder'])->count();
      if($check ==0){
        echo 'ID Folder salah';exit;
      }
      $filter =  'fields=files(fullFileExtension%2Cid%2CmimeType%2Cname%2CoriginalFilename%2CwebContentLink%2CwebViewLink)';
      $filter2 = 'fields=files(fileExtension%2Cid%2CmimeType%2Cname%2CoriginalFilename%2CwebContentLink%2CwebViewLink)';
      $api_key = 'AIzaSyA86pql3oG6Jy0bXW_XjjGQoORtAq52z_4';
      $results = file_get_contents("https://www.googleapis.com/drive/v3/files?q=".$folder_id."&".$filter."&key=".$api_key);
      $results = json_decode ($results);
      foreach($results->files as $key => $val){
        if($key < $epi){
          $url='https://advc.cloudstreamapi.com/api/v4/link/'.$val->id.'?access_key=VJoF5KiIY8C2_H-xzld2AA&ipv4=true%27';
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $r = curl_exec($ch);
          curl_close($ch);
          $r = json_decode($r);
          if(isset($r->error) && !empty($r->error)){
            $save = DB::table('log_curl')->insert([
              'name'        => $val->name,
              'folder_id'   => $_POST['folder'],
              'file_id'     => $val->id,
              'status'      => 0,
              'detail'      => $r->error
            ]);
          }else{
            $save = DB::table('log_curl')->insert([
              'name'        => $val->name,
              'folder_id'   => $_POST['folder'],
              'file_id'     => $val->id,
              'status'      => 1,
              'detail'      => '-'
            ]);
          }
        }
      }
      echo 'done';exit;
    }

    public function page_folder(){
      return view('form');
    }

    public function save_folder(){
      $check = DB::table('main_folder')->select('*')->where( 'email',$_POST['email'])->where('folder_id', $_POST['folder'])->count();
      if($check > 0){
        echo 'not_found'; exit;
      }
      $save = DB::table('main_folder')->insert([
        'email'       => $_POST['email'],
        'folder_id'   => $_POST['folder'],
        'created_at'  => date('Y-m-d H:i:s')
      ]);
      if($save){
        echo 'sukses';exit;
      }else{
        echo 'gagal';exit;
      }

    }

}
