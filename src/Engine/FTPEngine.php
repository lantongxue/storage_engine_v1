<?php


namespace V1\StorageEngine\Engine;


use V1\StorageEngine\Entity\FileInfo;
use V1\StorageEngine\Entity\StreamBuffer;

/**
 * FTP
 * Class FTPEngine
 * @package V1\StorageEngine\Engine
 */
class FTPEngine extends BaseEngine
{
    /**
     * @var resource
     */
    protected $ftpHandle;

    protected string $charset;

    protected function init()
    {
        if(!extension_loaded('ftp'))
        {
            throw new \Exception('ftp extension not found');
        }

        $port = $this->options['port'] ?? 21;
        $user = empty($this->options['user']) ? 'anonymous' : $this->options['user'];
        $password = $this->options['password'] ?? '';
        $passive = $this->options['passive'] ?? false;
        $ssl = $this->options['ssl'] ?? false;
        $timeout = $this->options['timeout'] ?? 90;
        $this->charset = $this->options['charset'] ?? 'UTF-8';

        if($ssl)
        {
            $this->ftpHandle = ftp_ssl_connect($this->options['host'], $port, $timeout);
        }
        else
        {
            $this->ftpHandle = ftp_connect($this->options['host'], $port, $timeout);
        }

        $login = ftp_login($this->ftpHandle, $user, $password);
        if($login && $passive)
        {
            ftp_pasv($this->ftpHandle, $passive);
        }

        // 切换到设置的根目录
        $this->ftp_ch_root();
    }

    private function ftp_ch_root() : void
    {
        $this->ftp_mkdir($this->Root);
    }

    /**
     * “递归” 创建目录
     * @param string $dir
     */
    private function ftp_mkdir(string $dir) : void
    {
        if(@ftp_chdir($this->ftpHandle, $dir) || empty($dir))
        {
            return;
        }
        $dir_arr = explode('/', $dir);
        $dir_arr = array_filter($dir_arr);
        foreach ($dir_arr as $_dir)
        {
            if(!@ftp_chdir($this->ftpHandle, $_dir))
            {
                /**
                 * 创建目录的时候很可能没有权限，这里不管，直接抛错出来
                 */
                if(ftp_mkdir($this->ftpHandle, $_dir))
                {
                    @ftp_chdir($this->ftpHandle, $_dir);
                }
            }
        }
    }

    private function ftp_write(string $content) : int
    {
        $temp = tempnam(sys_get_temp_dir(), 'ftp');
        $bytes = file_put_contents($temp, $content);
        /**
         * 为什么要通过这种方式去创建目录？
         * 因为当前ftp已经切换到设置的根目录（$this->Root）上，所以这里只需要取这个文件的相对路径（FileInfo构造函数传递的路径）去创建
         */
        $this->ftp_mkdir(pathinfo($this->FileInfo->File, PATHINFO_DIRNAME));
        $result = ftp_put($this->ftpHandle, $this->FileInfo->Name, $temp);
        unlink($temp);
        ftp_chdir($this->ftpHandle, $this->Root); // 退回到根目录
        $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $bytes]);
        unset($content);
        return $result === FTP_FINISHED ? $bytes : 0;
    }

    private function ftp_append(string $content)
    {
        $temp = tempnam(sys_get_temp_dir(), 'ftp');
        $bytes = file_put_contents($temp, $content);
        /**
         * 为什么要通过这种方式去创建目录？
         * 因为当前ftp已经切换到设置的根目录（$this->Root）上，所以这里只需要取这个文件的相对路径（FileInfo构造函数传递的路径）去创建
         */
        $this->ftp_mkdir(pathinfo($this->FileInfo->File, PATHINFO_DIRNAME));
        $result = ftp_append($this->ftpHandle, $this->FileInfo->Name, $temp);
        ftp_chdir($this->ftpHandle, $this->Root); // 退回到根目录
        $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $bytes]);
        unset($content);
        return $result ? $bytes : 0;
    }

    public function ReadAsText(): string
    {
        $temp = tempnam(sys_get_temp_dir(), 'ftp');
        $result = ftp_get($this->ftpHandle, $temp, $this->FileInfo->FullName);
        $text = $result ? file_get_contents($temp) : '';
        unlink($temp);
        return $text;
    }

    public function ReadAsStreamBuffer(): StreamBuffer
    {
        $temp = tempnam(sys_get_temp_dir(), 'ftp');
        $result = ftp_get($this->ftpHandle, $temp, $this->FileInfo->FullName);
        $buffer = $result ? StreamBuffer::FromFile(new FileInfo($temp, true)) : null;
        unlink($temp);
        return $buffer;
    }

    public function WriteText(string $content): int
    {
        return $this->ftp_write($content);
    }

    public function WriteStream(StreamBuffer $buffer): int
    {
        $bytes = $this->ftp_write($buffer->ToString());
        unset($buffer);
        return $bytes;
    }

    public function AppendText(string $content): int
    {
        return $this->ftp_append($content);
    }

    public function AppendStream(StreamBuffer $buffer): int
    {
        $bytes = $this->ftp_append($buffer->ToString());
        unset($buffer);
        return $bytes;
    }

    public function CopyTo(string $target): bool
    {
        $content = $this->ReadAsText();

        /**
         * 通过替换当前FileInfo，实现向根路径写入新文件
         * 完了之后再替换回去
         */
        $oldFile = $this->FileInfo;
        $newFile = new FileInfo($target, true);
        $this->FileInfo = $newFile;

        $this->ftp_write($content);
        $this->FileInfo = $oldFile;

        unset($newFile);
        unset($content);
        return true;
    }

    public function MoveTo(string $target): bool
    {
        $result = ftp_rename($this->ftpHandle, $this->FileInfo->FullName, $this->Root.'/'.$target);
        $this->FileInfo->Trigger(FileInfo::EVENT_MOVED, ['target' => $target, 'root' => $this->Root]);
        return $result;
    }

    public function Delete(): bool
    {
        return ftp_delete($this->ftpHandle, $this->FileInfo->FullName);
    }
}