<?php
namespace App;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class Qiniu {
    public $auth;
    
    private $config;
    private $uploadMgr;
    public function __construct(array $config) {
        $this->config = $config;
        $this->auth = new Auth($config['AK'], $config['SK']);
        $this->uploadMgr = new UploadManager();
    }

    public function getUploadToken() {
        return $this->auth->uploadToken($this->config['bucket']);
    }

    /**
     * 获取一个安全的文件名
     *
     * @param string $name
     * @return string
     */
    public function getSafeFilename($name) {
        return preg_replace('/[^a-zA-Z\.\-_]/', '_', $name);
    }

    /**
     * 获取存储的目标文件 Key
     *
     * @param array $file
     * @return string
     */
    public function getStorageKey($file) {
        // 从 mimetype 中获取文件类型
        $ext = substr($file['type'], strrpos($file['type'], '/') + 1);
        $filename = $file['name'];
        if (!empty($ext) and strpos($filename, $ext) === false)
            $filename .= $ext;
        $filename = $this->getSafeFilename($filename);
        return $this->config['prefix'].$filename;
    }

    /**
     * 存储一个文件
     *
     * @param array $file
     * @return string|false
     */
    public function put($file) {
        [$ret, $err] = $this->uploadMgr->putFile(
            $this->getUploadToken(),
            $this->getStorageKey($file),
            $file['tmp_name']
        );
        if (!empty($err))
            return false;
        return $this->config['baseurl'].$ret['key'];
    }
}
