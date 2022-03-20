<?php
/*
 * Dasturchi: Elbek Khamdullaev (https://t.me/KhamdullaevUz)
 */
$token = "5174298272:AAE74LU4XNdry_MdyzPiYn0WwE3zGmgeeGE";
define('API_KEY', $token); 

$admin = "1305987911";

function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}

$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$cid = $message->chat->id;
$tx = $message->text;
$text = $message->text;
$name = $message->from->first_name;
$fid = $message->from->id;
$botname = bot('getme',['bot'])->result->username;
$botid = bot('getme',['bot'])->result->id;
$mid = $message->message_id;
$new = $message->new_chat_member;
$newid = $new->id;
$cty = $message->chat->type;
$adstep = file_get_contents("admin.step");

if($newid !== NULL and $newid == $botid){
	 	bot('sendMessage',[
        'chat_id'=>$cid,
        'text'=>"*Salom men guruhingizda kirdi chiqdilarni tozalayman, buning uchun menga admin berishingiz kerak!*",
        'parse_mode'=>"markdown"
        ]);
}

if($cty == "group" or $cty == "supergroup"){
	$get = file_get_contents("grid.txt");
    if(mb_stripos($get, $cid)==false){
        file_put_contents("grid.txt", "$get\n$cid");
    }
}else{
	$get = file_get_contents("usid.txt");
    if(mb_stripos($get, $fid)==false){
        file_put_contents("usid.txt", "$get\n$fid");
    }
}


if ($tx == "/start" or $tx == "/start@$botname"){
    if($cty == "group" or $cty == "supergroup"){
        bot('deleteMessage',[
        'chat_id'=>$cid,
        'message_id'=>$mid
        ]);
        $st = bot('sendMessage',[
        'chat_id'=>$cid,
        'text'=>"*Bot lichkasiga yozing*",
        'parse_mode'=>"markdown"
        ]);
        sleep(1);
        $stt = $st->result->message_id;
        bot('deleteMessage',[
        'chat_id'=>$cid,
        'message_id'=>$stt
        ]);
    } else {
    bot('sendMessage',[
    'chat_id' => $cid,
    'text' => "Salom <b>$name</b> botimizga xush kelibsiz!\nBu bot guruhingizda kirdi chiqdini tozalaydi. Guruhga qo'shing:",
    'parse_mode'=>'html',
    'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [['text'=>"➕ Guruhga qo'shish",'url'=>"https://t.me/$botname?startgroup=new"]],
    ]
        ])
    ]);
}
}

if(isset($update->message->new_chat_photo) or isset($update->message->new_chat_title) or isset($update->message->pinned_message) or isset($update->message->new_chat_member) or isset($update->message->left_chat_member)){
    bot('deleteMessage',[
        'chat_id'=>$cid,
        'message_id'=>$mid,
    ]);
}

if($text == "/panel" and $cid == $admin){
    bot('deleteMessage',[
    'chat_id' => $cid,
    'message_id' => $mid
    ]);
    bot('sendMessage',[
    'chat_id'=>$admin,
    'text'=>"Admin panel! Quyidagi menyudan foydalaning 👇",
    'parse_mode'=>"html",
    'reply_markup'=>json_encode([
        'resize_keyboard'=>true,
        'keyboard'=>[
            [['text'=>"📤 Userlarga xabar yo'llash"],['text'=>"📤 Guruhlarga xabar yo'llash"]],
            [['text'=>"📊 Statistika"]]
        ]
    ])
    ]);
}

if($text == "📤 Userlarga xabar yo'llash" and $cid == $admin){
    bot('sendMessage',[
    'chat_id'=>$admin,
    'text'=>"Userlarga yuboriladigan xabar matnini kiriting(markdown):",
    'reply_markup'=>json_encode([
    'resize_keyboard'=>true,
    'keyboard'=>[
    [['text'=>"Bekor qilish"]]
    ]
    ])
    ]);

    file_put_contents("admin.step", "us");
}

if($text == "📤 Guruhlarga xabar yo'llash" and $cid == $admin){
    bot('sendMessage',[
    'chat_id'=>$admin,
    'text'=>"Guruhlarga yuboriladigan xabarni yuboring(markdown):",
    'reply_markup'=>json_encode([
    'resize_keyboard'=>true,
    'keyboard'=>[
    [['text'=>"Bekor qilish"]]
    ]
    ])
    ]);

    file_put_contents("admin.step", "gr");
}

if($text == "Bekor qilish"){
	unlink("admin.step");
	bot('sendmessage',[
		'chat_id'=>$admin,
		'text'=>"Bekor qilindi! Quyidagi menyudan foydalaning:",
		'reply_markup'=>json_encode([
        'resize_keyboard'=>true,
        'keyboard'=>[
            [['text'=>"📤 Userlarga xabar yo'llash"],['text'=>"📤 Guruhlarga xabar yo'llash"]],
            [['text'=>"📊 Statistika"]]
        ]
    ])
]);
}

if($adstep == "us" and $text !== "Bekor qilish" and $cid == $admin){
     $userlar = file_get_contents("usid.txt");
     $idszs=explode("\n",$userlar);
     foreach($idszs as $idlat){
        $users = bot('sendMessage',[
          'chat_id'=>$idlat,
          'text'=>$text,
          'parse_mode'=>"markdown"
          ]);
     }
     if($users){
        bot('sendMessage',[
        'chat_id'=>$admin,
        'text'=>"Barcha userlarga yuborildi."
        ]);
        }
     }


if($adstep == "gr" and $text !== "Bekor qilish" and $cid == $admin){
        $guruhlar = file_get_contents("grid.txt");
         $idszs=explode("\n",$guruhlar);
          foreach($idszs as $idlat){
          $guruhs = bot('sendMessage',[
          'chat_id'=>$idlat,
          'text'=>$text,
          'parse_mode'=>"markdown"
          ]);
     }
     if($guruhs){
        bot('sendMessage',[
        'chat_id'=>$admin,
        'text'=>"Barcha guruhlarga yuborildi."
        ]);
        unlink("admin.step");
      } 
}

if($text == "📊 Statistika" and $cid == $admin){
    $us = file_get_contents("usid.txt");
    $gr = file_get_contents("grid.txt");

    $uscount = substr_count($us, "\n");
    $grcount = substr_count($gr, "\n");
    $count = $uscount + $grcount;

    bot('sendMessage',[
    'chat_id'=>$admin,
    'text'=>"📊 Statistika\n\nUserlar: *$uscount* ta\nGuruhlar: *$grcount* ta\nJami: *$count* ta",
    'parse_mode'=>"markdown"
    ]);
}