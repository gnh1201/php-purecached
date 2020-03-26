<?php
// A simple PHP key value in-memory store database
// Go Namhyeon <gnh1201@gmail.com>
// 2020-02-25

class PureCached {
    private $data;
    private $limit;
    private $thresholds;
    private $blocksize;

    function PureCached($limit=0, $thresholds=1.0) {
        $this->data = array();
        $this->limit = $limit;
        $this->thresholds = $thresholds;
        $this->blocksize = 8192;
    }

    private function shift() {
        array_shift($this->data);
    }

    public function save($filename) {
        $fp = fopen($filename, "w");
        $flag = fwrite($fp, serialize($this->data));
        fclose($fp);
        return $flag;
    }

    public function restore($filename) {
        $contents = "";
        $fp = fopen($filename, "r");
        while(!feof($fp)) {
            $contents .= fread($fp, $this->blocksize);
        }
        $this->data = unserialize($contents);
        fclose($fp);
    }

    public function put($key, $value) {
        while(memory_get_usage() > ($this->limit * $this->thresholds)) {
            $this->shift();
            if(count($this->data) == 0) break;
        }
        $this->data[$key] = $value;
    }

    public function get($key) {
        if(array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return NULL;
    }
}

$cache = new PureCached(1024 * 1024 * 32, 0.8);
$cache->put("fruit", "Apple");
$cache->put("person", "John Doe");

echo $cache->get("fruit") . "\n";
echo $cache->get("person") . "\n";
