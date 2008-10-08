<?

abstract class Model
{
    public function __construct()
    {
        if(!Zend_Registry::isRegistered("db")) {
            $this->db = connectdb();
        } else {
            $this->db = Zend_Registry::get("db");
        }
    }
    
    protected function load($param)
    {
        $sql = $this->sql($param);
        dlog($sql);
        return $this->db->fetchAll($sql);
    }
 
    public function get($param = array())
    {
        return $this->load($param);
    }
    public abstract function sql($param);
/*
    public function parseParam($param)
    {
        $params = array();
        $ps = split("&", $param);
        foreach($ps as $p) {
            $key_value = split("=", $p);
            if(count($key_value) == 2) {
                $params[$key_value[0]] = $key_value[1];
            }
        }
        return $params;
    }
*/
}
