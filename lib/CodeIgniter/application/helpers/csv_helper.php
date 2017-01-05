<?php
defined('BASEPATH') OR exit('No direct script access allowed');
    
if( ! function_exists('fgetcsv_reg') )
{
    /**
    * ファイルポインタから行を取得し、CSVフィールドを処理する
    * @param resource handle
    * @param int length
    * @param string delimiter
    * @param string enclosure
    * @return ファイルの終端に達した場合を含み、エラー時にFALSEを返します。
    */
    function fgetcsv_reg (&$handle, $length = null, $d = ',', $e = '"')
    {
        $d = preg_quote($d);
        $e = preg_quote($e);
        $_line = "";
        $eof = false;
        while (($eof != true)and(!feof($handle))) {
            $_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
            $itemcnt = preg_match_all('/'.$e.'/', $_line, $dummy);
            if ($itemcnt % 2 == 0) $eof = true;
        }
        $_csv_line = preg_replace('/(?:\\r\\n|[\\r\\n])?$/', $d, trim($_line));
        $_csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/';
        preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
        $_csv_data = $_csv_matches[1];
        for($_csv_i=0;$_csv_i<count($_csv_data);$_csv_i++){
            $_csv_data[$_csv_i]=preg_replace('/^'.$e.'(.*)'.$e.'$/s','$1',$_csv_data[$_csv_i]);
            $_csv_data[$_csv_i]=str_replace($e.$e, $e, $_csv_data[$_csv_i]);
        }
        return empty($_line) ? false : $_csv_data;
    }
    
}

if( ! function_exists('create_csv'))
{
    function create_csv ($filename="result.csv", $data=NULL, $labelrow=TRUE, $encode="SJIS-WIN")
    {
        $contents = "";
        // ラベル行を書き込み
        if($labelrow)
        {
            $contents = mb_convert_encoding('ドメイン,URL,ドメイン追加日,Page Authority,Domain Authority,Total Links,SEOmoz Link Met.','SJIS-win','UTF-8').PHP_EOL;
        }
        
        // データを書き込み
        foreach($data as $ln)
        {
            $contents .= $ln['domainname'].",".$ln['url'].",".$ln['insertdatetime'].",".$ln['pageAuthority'].",".$ln['domainAuthority'].",".$ln['totalLinks'].",".$ln['linksMet'].PHP_EOL;
        }
        
        file_put_contents(FILEPATH.DIRECTORY_SEPARATOR.$filename, $contents);
        
        if(file_exists(FILEPATH.DIRECTORY_SEPARATOR.$filename))
        {
            return FILEPATH.DIRECTORY_SEPARATOR.$filename;
        }
        else
        {
            return FALSE;
        }
    }
}
