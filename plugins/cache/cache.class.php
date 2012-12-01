<?php

class Cache extends AbstractPlugin
{

	public $isCached = false;
	private $folder = '';
	private $ext = ".cache";
	private $cache = "";
	private $cache_file = "";
	private $_duration = 0;

	public function __construct($file, $duration = 0)
	{
		$this->folder = TMP . DS . 'cache' . DS;

		if (!file_exists($this->folder))
			mkdir($this->folder);

		$this->cache_file = $this->folder . $file . $this->ext;
		$this->_duration = $duration;

		if (file_exists($this->cache_file) && ($this->_duration == 0 || (filemtime($this->cache_file) + $this->_duration) > time() ))
			$this->isCached = true;

		if (Core::config('core', 'general', 'devel'))
			$this->isCached = false;
	}

	public function start()
	{
		if (!$this->isCached)
		{
			ob_start();
		}
	}

	public function stop()
	{
		if (!$this->isCached)
		{
			$this->cache = ob_get_contents();
			ob_end_clean();
		}
	}

	public function content()
	{
		if (Core::config('core', 'general', 'devel'))
		{
			return $this->cache;
		}
		else
		{
			if (!$this->isCached)
				file_put_contents($this->cache_file, $this->cache);

			return file_get_contents($this->cache_file);
		}
	}

	public function delete($file = "")
	{
		if (empty($file))
			$file = $this->cache_file;
		else
			$file = $this->folder . $file . $this->ext;

		unlink($file);
		$this->isCached = false;
	}

	public function hasCachedFile()
	{
		return $this->isCached;
	}

	public function purge()
	{
		$dir = dir($this->folder);

		while ($file = $dir->read())
		{
			if ($file != "." && $file != ".." && !is_dir($file))
			{
				unlink($this->folder . $file);
			}
		}

		$dir->close();
	}

}

?>