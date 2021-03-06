<?php

/**
 *  Postcodify - 도로명주소 우편번호 검색 프로그램 (인덱서)
 * 
 *  Copyright (c) 2014, Kijin Sung <root@poesis.kr>
 * 
 *  이 프로그램은 자유 소프트웨어입니다. 이 소프트웨어의 피양도자는 자유
 *  소프트웨어 재단이 공표한 GNU 약소 일반 공중 사용 허가서 (GNU LGPL) 제3판
 *  또는 그 이후의 판을 임의로 선택하여, 그 규정에 따라 이 프로그램을
 *  개작하거나 재배포할 수 있습니다.
 * 
 *  이 프로그램은 유용하게 사용될 수 있으리라는 희망에서 배포되고 있지만,
 *  특정한 목적에 맞는 적합성 여부나 판매용으로 사용할 수 있으리라는 묵시적인
 *  보증을 포함한 어떠한 형태의 보증도 제공하지 않습니다. 보다 자세한 사항에
 *  대해서는 GNU 약소 일반 공중 사용 허가서를 참고하시기 바랍니다.
 * 
 *  GNU 약소 일반 공중 사용 허가서는 이 프로그램과 함께 제공됩니다.
 *  만약 허가서가 누락되어 있다면 자유 소프트웨어 재단으로 문의하시기 바랍니다.
 */

class Postcodify_ZipReader
{
    protected $_charset;
    protected $_current;
    protected $_zip;
    protected $_fp;
    
    // Zip 아카이브를 연다.
    
    public function open_archive($filename)
    {
        $this->_zip = new ZipArchive;
        $this->_zip->open($filename);
    }
    
    // Zip 아카이브 중 다음 파일을 연다.
    // 성공하면 파일명을 반환하고, 실패하면 false를 반환한다.
    
    public function open_next_file()
    {
        $index = ($this->_current === null) ? 0 : ($this->_current + 1);
        $first_filename = @$this->_zip->getNameIndex($index);
        if ($first_filename === false) return false;
        $this->_fp = $this->_zip->getStream($first_filename);
        $this->_current = $index;
        return $first_filename;
    }
    
    // Zip 아카이브 중 이름에 주어진 문자열이 포함된 파일을 연다.
    // 성공하면 파일명을 반환하고, 실패하면 false를 반환한다.
    
    public function open_named_file($str)
    {
        $count = $this->_zip->numFiles;
        for ($i = 0; $i < $count; $i++)
        {
            $filename = $this->_zip->getNameIndex($i);
            if (strpos($filename, $str) !== false)
            {
                $this->_fp = $this->_zip->getStream($filename);
                return $filename;
            }
        }
        return false;
    }
    
    // 문자셋을 지정한다.
    
    public function set_charset($charset)
    {
        $this->_charset = $charset;
    }
    
    // 한 줄을 읽어 반환한다.
    
    public function read_line($delimiter = '|')
    {
        $line = fgets($this->_fp);
        if ($line === false) return false;
        
        if ($this->_charset === 'UTF-8')
        {
            // no-op
        }
        elseif ($this->_charset !== null)
        {
            $line = mb_convert_encoding($line, 'UTF-8', $this->_charset);
        }
        else
        {
            if (mb_check_encoding($line, 'UTF-8'))
            {
                $this->_charset = 'UTF-8';
            }
            else
            {
                $this->_charset = 'CP949';
                $line = mb_convert_encoding($line, 'UTF-8', $this->_charset);
            }
        }
        
        return explode($delimiter, $line);
    }
    
    // 파일을 닫는다.
    
    public function close()
    {
        if ($this->_fp) fclose($this->_fp);
        if ($this->_zip) $this->_zip->close();
    }
}
