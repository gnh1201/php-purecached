<?php
// A simple PHP key value in-memory store database
// Go Namhyeon <gnh1201@gmail.com>
// 2020-02-25

class PureCached {
    private $fp;

    function PureCached() {
        $this->fp = fopen("php://memory", "r+");
    }

    private function read() {
        rewind($this->fp);
        return stream_get_contents($this->fp);
    }

    public function put($key, $value) {
        $data = unserialize($this->read($this->fp));
        $data[$key] = $value;
        rewind($this->fp);
        fwrite($this->fp, serialize($data));
    }

    public function get($key) {
        $data = unserialize($this->read($this->fp));
        return (array_key_exists($key, $data) ? $data[$key] : false);
    }
}

$cache = new PureCached();
$cache->put("fruit", "Apple");
$cache->put("person", "John Doe");

echo $cache->get("fruit") . "\n";
echo $cache->get("person") . "\n";
