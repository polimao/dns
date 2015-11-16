<?php namespace App\Traits;

use App\Models\AutoType;
use App\Models\OperatingLog;
use Illuminate\Support\Facades\Cache;
use Sentry;

trait BaseTrait {

    protected static $cache_version = 9;
    protected static $cache = true;

    protected $price_ratio = 10000;

    private function abled($key=null){

        $able_id = $key.'_id';
        $able_type = $key.'_type';

        if(array_key_exists($able_id, $this->attributes) && array_key_exists($able_type, $this->attributes)){
            $obj = new $this->$able_type();
            return $obj->find($this->$able_id);
        }
        $cur_model_name = self::modelName();
        throw new \OutOfBoundsException("Properties of {$able_id} and {$able_type} was not found in  {$cur_model_name}", self::modelType());
    }

    private static function getModelName(){
        return get_called_class();
    }

    private static function getCacheModelName(){
        return str_ireplace('App\\Models\\','', self::getModelName());
    }

    /**
     * Find a model by its primary key.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return \Illuminate\Support\Collection|static|null
     */
    public static function find($id, $columns = array('*'))
    {
        $key = self::key_cache($id);
        return Cache::tags(self::getModelName())->rememberForever($key, function() use($id, $columns)
        {
            return static::query()->find($id, $columns);
            // return parent::find($id, $columns);
        });
    }

    public static function flush(){
        Cache::tags(self::getModelName())->flush();
        //laravel－topka
        Cache::tags(self::getCacheModelName())->flush();
    }

    public static function forget($id){
        $key = self::key_cache($id);
        if(Cache::tags(self::getModelName())->has($key)){
            Cache::tags(self::getModelName())->forget($key);
            //laravel－topka
            Cache::tags(self::getCacheModelName())->forget($key);
        }
    }

    public static function saveData($attributes=array())
    {
        $obj = new static;
        foreach ($attributes as $key => $value) {
            $obj->$key = $value;
        }
        $obj->save();
        return static::find($obj->id);
    }

    public static function modelName(){
        return self::getModelName();
    }

    public static function modelType(){
        return AutoType::getBase(self::getModelName());
    }

    /**
     * Fire the given event for the model.
     *
     * @param  string  $event
     * @param  bool    $halt
     * @return mixed
     */
    protected function fireModelEvent($event, $halt = true)
    {
        parent::fireModelEvent($event, $halt);

        if($event == "created")
            $this->afterCreate();

        if($event == "updated")
            $this->afterUpdate();

        if($event == "deleted")
            $this->afterDelete();

        // if cache enabled
        if (static::$cache === true)
        {
            if (in_array($event, array('updated', 'saved', 'deleted')))
            {
                self::forget($this->id);
            }
        }
    }

    protected function afterUpdate()
    {
        $this->addOperatingLog(__FUNCTION__);
    }

    protected function afterDelete()
    {
        $this->addOperatingLog(__FUNCTION__);
    }

    protected function afterCreate()
    {
        $this->addOperatingLog(__FUNCTION__);
    }

    public function getChangeData($type=""){

         $original = $attributes = [];

        if($type == 'delete' || $type == 'deleted')
            $original = $this->original;

        else if($type=='create' || $type == 'inserted')
            $attributes = $this->attributes;

        else foreach ($this->attributes as $k => $v) {

            if(!is_null($v) && !isset($this->original[$k]))
                $attributes[$k] = $v;

            else if(array_get($this->original, $k, '') !== $this->attributes[$k]){
                $attributes[$k] = $v;
                $original[$k] = array_get($this->original, $k, '');
            }
        }

        $original = array_except($original,['updated_at', 'deleted_at']);
        $attributes = array_except($attributes,['updated_at', 'deleted_at']);

        return [$original, $attributes];
    }

    protected function addOperatingLog($method)
    {
        if(\Auth::user() && \Auth::user()->admin())
        {
            $admin_id = \Auth::user()->admin()->id;
            $type = str_replace("after", "", strtolower($method));
            OperatingLog::add($admin_id, $this, $type, Input::get("delete_reason",""));
        }
    }

    public function scopeMultiWhere($query){

        $table = $this->getTable();

        $numargs = func_num_args();
        $arg_lists = func_get_args();

        if($numargs < 2 || !is_array($arg_lists[1]) || count($arg_lists[1]) == 0)
            return $query;

        $wheres = [];

        if(is_array($arg_lists[1])){
            $wheres = $arg_lists[1];
        }
        else{
            for($i = 1 ; $i < $numargs ; $i++){
                $wheres[] = $arg_lists[$i];
            }
        }

        foreach($wheres as $k => $where){
            if(!is_array($where)){
                $where = [$k, $where];
            }
            if(strpos($where[0],'.') === false){
                $where[0] = $table.'.'.$where[0];
            }
            $query = call_user_func_array([$query , 'where'], $where);
        }
        return $query;
    }

    public function copy(){
        return clone $this;
    }

    public function scopeClone($query){
        return clone $query;
    }

    /**
     * 获取指定的对象成员
     * @param  [type] $select [description]
     * @return [type]         [description]
     * @author mawenhao
     * eg: return $user->only('id', 'name', 'name("aaaa","11") nickname', 'avatar("120x130") avatar_tb', 'avatar() avatar_url');
     */
    public function only($select=[])
    {
        if (func_num_args() > 1 || !is_array($select)){
            $select = func_get_args();
        }

        if(!$select) {
            return $this;
        }

        $attributes = $original = [];

        if(in_array('*',$select)){
            foreach($this->attributes as $k => $v){
                $attributes[$k] = $original[$k] = $v;
            }
        }

        foreach($select as $attr){
            if($attr === '*') {
                continue;
            }

            $key = $attr = trim($attr);

            if($args = explode(' ', $attr)){
                if(count($args) > 1)
                    list($attr, $key) = $args;
            }

            $method = $attr;
            $attr = preg_replace('/\(.*\)/', '', $attr);
            $key = preg_replace('/\(.*\)/', '', $key);

            if(array_key_exists($attr, $this->attributes)){
                $attributes[$key] = $original[$key] = $this->$attr;
            }

            if( method_exists($this, $attr)){
                $attributes[$key] = $original[$key] = eval('return $this->'.$method.';');
            }
        }

        $object = $this;

        $object->attributes = $attributes;
        $object->original = $original;

        return $object;
    }

    /**
     * 排除指定的对象成员
     * @param  [type] $select [description]
     * @return [type]         [description]
     * @author mawenhao
     */
    public function except($select=[])
    {
        if (func_num_args() > 1 || !is_array($select)){
            $select = func_get_args();
        }

        $select = array_diff(array_keys($this->attributes), $select);

        return $this->only($select);
    }

    /**
     * 获取当前登录的admin_id
     * @author hu.xiongfei
     * @date   2015-05-02
     * @return [type]
     */
    public static function getCurrentAdminId()
    {
        $user = Sentry::getUser();

        if($user && $user->admin()){
            return $user->id;
        }

        return 0;
    }

    /**
     * 重新加载model
     *
     * @author mawenhao
     * @version [version]
     * @date    2015-06-25
     * @return  [type]     [description]
     */
    public function reloadModel(){
        $this->setRawAttributes($this->find($this->id)->getAttributes(), true);
    }

    /**
     * Get uniqe key
     *
     * @param  integer $id
     * @param  string  $array
     */
    private static function key_cache($id)
    {
        return 'slc/'.snake_case(str_plural(self::getCacheModelName())).'/'.$id.'/'.static::$cache_version;
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        // 自动转 model_id
        $field_id = snake_case($key).'_id';
        if(!array_key_exists($key, $this->attributes) && array_key_exists($field_id, $this->attributes))
        {
            $modelname = $this->modelName();
            $model = substr($modelname,0, strrpos($modelname,'\\')+1).studly_case($key);
            return $model::find($this->$field_id);
        }

        return parent::__get($key);
    }

}