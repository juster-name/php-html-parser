<?php
namespace Test\Parser;

require_once ('action.php');

class HrefFilter implements IActionParam
{
    private $urlHost;
    private $urlScheme;
    private $urlPath;
    private $url;
    public const USER_AGENT_DEFAULT = "Mozilla custom agent";

    function __construct($url = '', $userAgent = HrefFilter::USER_AGENT_DEFAULT)
    {
        ini_set("user_agent", $userAgent);

        if (empty($url))
        {
            $this->url = '';
            return;
        }
        $this->setUrl($url);
    }

    public function setUrl($url) 
    {
        $filteredUrl = HrefFilter::filterUrl($url);

        $this->url = HrefFilter::getRedirectUrl($filteredUrl);
        $this->urlHost = parse_url($this->url , PHP_URL_HOST);
        $this->urlScheme = parse_url($this->url , PHP_URL_SCHEME);
        $this->urlPath = parse_url($this->url , PHP_URL_PATH);
    }

    public static function getRedirectUrl($url)
    {               
        $headers = @get_headers($url, 1);     
        
        $location = key_exists('Location', $headers);

        if ($location !== false)
        {
            $location = $headers['Location'];
            if (is_array($location))
            { 
                $location = $location[0];
            }
            return $location;
        }
        return $url;
    }
    public static function filterUrl($url)
    {
        if (parse_url($url, PHP_URL_SCHEME) === null)
        {             
             $url = @get_headers("http://$url", 1)['Location'];

        }
        //$location = get_headers($url, 1)['Location'];
        //$url = is_array(get_headers($url, 1)['Location']) ?;
//
        //$host = parse_url($url, PHP_URL_HOST);

        //if(strtok($host, '.')!= 'www')
        //{
         //   $domainStart = strpos($url, '/') + 2;
         //   $url = substr_replace($url, 'www.', $domainStart, 0);
        //}
        
        return $url;
    }

    public function getUrlScheme() 
    {
        return $this->urlScheme;
    }

    public function getUrlHost() 
    {
        return $this->urlHost;
    }

    public function getUrl() 
    {
        return $this->url;    
    }
      
    public function run($var)
    {
        if (empty($this->url))
        {
            $this->setUrl($var);
        }

        if ($var == '#')
        {
            return $this->url;
        }

        if (filter_var($var, FILTER_VALIDATE_URL) === false) 
        {
           return $this->makeFullPath($var);
        }
        $redirUrl = HrefFilter::getRedirectUrl($var);

        if (parse_url($redirUrl, PHP_URL_HOST) != $this->urlHost)
        {
            return $var;
        }
     
        if (filter_var($redirUrl, FILTER_VALIDATE_URL) === false) 
        {
            return $this->makeFullPath($redirUrl);
        }
        $var = $redirUrl;
        
        return HrefFilter::filterUrl($var);
    }
    private function makeFullPath($url)
    {
        if (empty($url))
        {
            return $this->url;
        }
        
        $newUrl = $this->urlScheme . "://" . $this->urlHost;

        $url = preg_replace('/(\.+\/+)+/', '/', $url); // for urls like "./foo/" "../bar"      

        $newUrlPath= parse_url($url, PHP_URL_PATH);

        $newUrl .= $this->urlPath . substr($newUrlPath, 1, strlen($newUrlPath));

        return HrefFilter::filterUrl($newUrl);
    }
    
    public function getName() : string
    {
        return get_class($this);
    }
}

class ImgFilter implements IActionParam
{
    public BaseHtmlParser $htmlParser;
    public bool $externalDomPass;

    function __construct(BaseHtmlParser &$htmlParser, bool $externalDomPass = true)
    {
        $this->htmlParser = $htmlParser;
        $this->externalDomPass = $externalDomPass;
    }

    public function run($var)
    {
        if ($this->externalDomPass != true &&
            parse_url($var, PHP_URL_HOST) != parse_url($this->htmlParser->getPath(), PHP_URL_HOST))
        {
            return false;
        }
        return $var;//$this->htmlParser->getHrefFilter()->run($var);
    }
    public function getName() : string
    {
        return get_class($this);
    }
}

?>