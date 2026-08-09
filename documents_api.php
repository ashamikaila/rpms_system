<?php
session_start();
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=UTF-8');
$userEmail=$_SESSION['user_email']??'rpms@ceu.edu.ph'; $userName=$_SESSION['user_name']??'CEU RPMS';
$userKey=hash('sha256',$userEmail); $base=__DIR__.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'documents'.DIRECTORY_SEPARATOR.$userKey;
if(!is_dir($base)&&!mkdir($base,0775,true)){http_response_code(500);echo json_encode(['ok'=>false,'message'=>'Document storage could not be created.']);exit;}
$indexFile=$base.DIRECTORY_SEPARATOR.'index.json';
function readIndex($file){$data=is_file($file)?json_decode(file_get_contents($file),true):[];return is_array($data)?$data:[];}
function writeIndex($file,$data){return file_put_contents($file,json_encode(array_values($data),JSON_PRETTY_PRINT),LOCK_EX)!==false;}
function inputJson(){return json_decode(file_get_contents('php://input'),true)?:[];}
function findDoc($docs,$id){foreach($docs as $doc)if(hash_equals((string)$doc['id'],(string)$id))return $doc;return null;}
$action=$_GET['action']??$_POST['action']??'list'; $docs=readIndex($indexFile);
if($action==='list'){echo json_encode(['ok'=>true,'documents'=>$docs]);exit;}
if($action==='upload'){
 if($_SERVER['REQUEST_METHOD']!=='POST'||!isset($_FILES['document'])){http_response_code(400);echo json_encode(['ok'=>false,'message'=>'No document was provided.']);exit;}
 $file=$_FILES['document']; if($file['error']!==UPLOAD_ERR_OK){http_response_code(400);echo json_encode(['ok'=>false,'message'=>'The upload did not complete.']);exit;}
 if($file['size']>20*1024*1024){http_response_code(413);echo json_encode(['ok'=>false,'message'=>'Files must be 20 MB or smaller.']);exit;}
 $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));$allowed=['pdf','doc','docx','txt','rtf','odt'];
 if(!in_array($ext,$allowed,true)){http_response_code(415);echo json_encode(['ok'=>false,'message'=>'This file type is not allowed.']);exit;}
 $id=bin2hex(random_bytes(12));$stored=$id.'.'.$ext;$target=$base.DIRECTORY_SEPARATOR.$stored;
 if(!move_uploaded_file($file['tmp_name'],$target)){http_response_code(500);echo json_encode(['ok'=>false,'message'=>'The uploaded file could not be stored.']);exit;}
 $doc=['id'=>$id,'originalName'=>basename($file['name']),'storedName'=>$stored,'size'=>(int)$file['size'],'mime'=>(new finfo(FILEINFO_MIME_TYPE))->file($target),'uploadedBy'=>$userName,'uploadedAt'=>date(DATE_ATOM),'student'=>trim($_POST['student']??''),'documentType'=>trim($_POST['documentType']??'Other'),'stage'=>trim($_POST['stage']??'Stage 1'),'reviewStatus'=>'Submitted','reviewRemarks'=>''];
 array_unshift($docs,$doc);if(!writeIndex($indexFile,$docs)){@unlink($target);http_response_code(500);echo json_encode(['ok'=>false,'message'=>'Document metadata could not be saved.']);exit;}echo json_encode(['ok'=>true,'document'=>$doc]);exit;
}
$payload=inputJson();$id=$_GET['id']??$payload['id']??'';$doc=findDoc($docs,$id);
if(!$doc){http_response_code(404);echo json_encode(['ok'=>false,'message'=>'Document not found.']);exit;}$path=$base.DIRECTORY_SEPARATOR.basename($doc['storedName']);
if($action==='file'){header_remove('Content-Type');header('Content-Type: '.$doc['mime']);header('Content-Length: '.filesize($path));header('Content-Disposition: '.(($_GET['download']??'')==='1'?'attachment':'inline').'; filename="'.str_replace('"','',$doc['originalName']).'"');readfile($path);exit;}
if($action==='review'){$allowed=['Under Review','Received','Verified','Resubmission Requested'];$status=trim($payload['status']??'');if(!in_array($status,$allowed,true)){http_response_code(422);echo json_encode(['ok'=>false,'message'=>'Invalid review status.']);exit;}foreach($docs as &$item){if($item['id']===$id){$item['reviewStatus']=$status;$item['reviewRemarks']=trim($payload['remarks']??'');$item['reviewedAt']=date(DATE_ATOM);$item['reviewedBy']=$userName;break;}}unset($item);if(!writeIndex($indexFile,$docs)){http_response_code(500);echo json_encode(['ok'=>false,'message'=>'Review could not be saved.']);exit;}echo json_encode(['ok'=>true]);exit;}
if($action==='delete'){if(is_file($path))@unlink($path);$docs=array_values(array_filter($docs,fn($item)=>$item['id']!==$id));writeIndex($indexFile,$docs);echo json_encode(['ok'=>true]);exit;}
if($action==='summarize'){
 $text='';$ext=strtolower(pathinfo($path,PATHINFO_EXTENSION));
 if(in_array($ext,['txt','rtf'],true))$text=file_get_contents($path,false,null,0,300000);
 elseif($ext==='docx'){try{$archive=new PharData($path);if(isset($archive['word/document.xml'])){$xml=file_get_contents('phar://'.$path.'/word/document.xml');$text=strip_tags(str_replace(['</w:p>','</w:tab>'],[".\n",' '],$xml));}}catch(Throwable $e){$text='';}}
 elseif($ext==='pdf'){$raw=file_get_contents($path,false,null,0,1000000);if(preg_match_all('/\(([^()]*)\)\s*Tj/',$raw,$matches))$text=implode(' ',$matches[1]);}
 $text=preg_replace('/^\x{FEFF}/u','',$text);
 $text=trim(preg_replace('/\s+/u',' ',$text));
 if($text===''){http_response_code(422);echo json_encode(['ok'=>false,'message'=>'Text could not be extracted from this file. Scanned PDFs and legacy Word files require an OCR or AI document service.']);exit;}
 $sentences=preg_split('/(?<=[.!?])\s+/u',$text,-1,PREG_SPLIT_NO_EMPTY);$summary=implode(' ',array_slice($sentences,0,4));if(mb_strlen($summary)>900)$summary=mb_substr($summary,0,897).'...';
 echo json_encode(['ok'=>true,'summary'=>$summary,'wordCount'=>str_word_count($text)]);exit;
}
http_response_code(400);echo json_encode(['ok'=>false,'message'=>'Unknown action.']);
