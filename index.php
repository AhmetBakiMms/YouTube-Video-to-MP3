<?php

if (isset($_GET["id"]))
{
    $id = $_GET['id'];

    function getc($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
    function quality($itag)
    {
        switch ($itag)
        {
            case "17":
                return "144P";
            break;
            case "278":
                return "144P";
            break;
            case "36":
                return "240P";
            break;
            case "242":
                return "240P";
            break;
            case "18":
                return "360P";
            break;
            case "243":
                return "360P";
            break;
            case "43":
                return "360P";
            break;
            case "35":
                return "480P";
            break;
            case "44":
                return "480P";
            break;
            case "135":
                return "480P";
            break;
            case "244":
                return "480P";
            break;
            case "22":
                return "720P";
            break;
            case "136":
                return "720P";
            break;
            case "247":
                return "720P";
            break;
            case "137":
                return "1080P";
            break;
            case "248":
                return "1080P";
            break;
            case "299":
                return "1080P (60 FPS)";
            break;
            case "138":
                return "2K";
            break;
            case "264":
                return "2K";
            break;
            case "271":
                return "2K";
            break;
            case "266":
                return "4K";
            break;
            case "313":
                return "4K (60 FPS)";
            break;
            case "139":
                return " 48 Kbps";
            break;
            case "140":
                return "128 Kbps";
            break;
            case "141":
                return " 128 Kbps";
            break;
            case "171":
                return " 128 Kbps";
            break;
            case "249":
                return " 50k";
            break;
            case "250":
                return " 70k";
            break;
            case "251":
                return " 160k";
            break;
            default:
                return $itag;
            break;
        }
    }
    $data = getc("https://www.youtube.com/get_video_info?video_id=" . $id . "&asv=3&el=detailpage&hl=en_US");
    parse_str($data, $info);
    $streams = $info['player_response'];
    $jsn_str = str_replace("\u0026", "&", $streams);
    $streamin_data_json = json_decode($jsn_str, true);

    $str = $streamin_data_json["streamingData"]["formats"];

    foreach ($str as $stream)
    {

        if (isset($stream["signatureCipher"]))
        {
            parse_str($stream["signatureCipher"], $dturl);
            $json['default'] = $dturl['url'] . '&sig=' . sig($dturl['s']);
        }
        else
        {
            if (!empty($str['url']))
            {
                $json['default'] = $str['url'];
            }

        }

    }

    $aud = array();
    $vid = array();
    foreach ($streamin_data_json["streamingData"]["adaptiveFormats"] as $stream)
    {
   

        if (preg_match('/audio/', $stream['mimeType']))
        {
            $url = $stream['url'];
            $values = array(
                'quality' => quality($stream['itag']) ,
                'url' => $url,
            );
            array_push($aud, $values);
        }

    };

    $json['audio'] = $aud;
    $jsonaudio = json_encode($json["audio"]["3"]["url"], JSON_FORCE_OBJECT);
    $jsonaudio2 = ltrim($jsonaudio, '"');
 $mp3=rtrim($jsonaudio2, '"');
header("location:$mp3");
}
?>
