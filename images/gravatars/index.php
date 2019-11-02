<?php
/**
 * GRAVATAR CACHE - A simple caching system for Gravatar.
 *
 * It works by trapping cache misses from HTTP server, and fetch the current 
 * version from gravatar.com.
 *
 * @author Scott Yang <scotty@yang.id.au>
 * @url http://fucoder.com/code/gravatar-cache/
 * @version $Revision: 14 $
 */

// Setting up default configuration.
$config = array(
    'gravatar'      => 'http://www.gravatar.com/avatar.php',
    'rating'        => 'PG',
    'size'          => 48,
    'default'       => '',
    'border'        => '',
    'referrer'      => '',
    'pos_expiry'    => 604800,
    'neg_expiry'    => 43200,
    'neg_handler'   => 'file',
    'proxy'         => '',
    'basedir'       => dirname(__FILE__),
    'debug'         => false
);

$gcversion = '0.1';


// Negative Cache Handlers
class NegHandler {
    function check($gravatar_id) {
        global $config;
        if ($config['neg_expiry'] <= 0)
            return false;
        
        $mtime = $this->get_mtime($gravatar_id);
        return $mtime && ((time() - floatval($mtime)) < $config['neg_expiry']);
    }

    function get_mtime($gravatar_id) {
        // To be overriden by sub-classes
        return 0;
    }

    function purge($expiry) {
        // To be overriden by sub-classes
    }

    function set($gravatar_id) {
        global $config;
        if ($config['neg_expiry'] > 0)
            $this->set_mtime($gravatar_id, time());
    }

    function set_mtime($gravatar, $mtime) {
        // To be overriden by sub-classes
    }
};

class NegHandler_dbm extends NegHandler {
    var $handle = null;

    function cleanup() {
        if ($this->handle != null) {
            dba_close($this->handle);
            $this->handle = null;
        }
    }

    function &get_handle() {
        global $config;
        if ($this->handle == null) {
            // Find a suitable handle.
            $preferred = array('db4', 'cdb', 'db3', 'db2', 'gdbm');
            $handle = dba_handlers(true);
            foreach ($preferred as $val) {
                if (isset($handle[$val])) {
                    gcdebug("Use dbm handle '$val'");
                    $this->handle = dba_open($config['basedir'].
                        '/cache/neg.db', 'cl', $val);
                    register_shutdown_function(array($this, 'cleanup'));
                    break;
                }
            }
        }

        return $this->handle;
    }

    function get_mtime($gravatar_id) {
        $dbh =& $this->get_handle();
        return dba_fetch($gravatar_id, $dbh);
    }

    function purge($expiry) {
        $expiry = time() - $expiry;
        $dbh = $this->get_handle();
        $key = dba_firstkey($dbh);
        $deleted = array();
        while ($key !== false) {
            $val = dba_fetch($key, $dbh);
            if ($val && intval($val) < $expiry)
                $deleted[] = $key;
            $key = dba_nextkey($dbh);
        }
        if (sizeof($deleted) > 0) {
            foreach ($deleted as $key)
                dba_delete($key, $dbh);
            dba_optimize($dbh);
            gcdebug('Removed '.sizeof($deleted).' negative cache from DBM');
        }
    }

    function set_mtime($gravatar_id, $mtime) {
        $dbh =& $this->get_handle();
        dba_replace($gravatar_id, $mtime, $dbh);
    }
};

class NegHandler_file extends NegHandler {
    function get_filename($gravatar_id) {
        global $config;
    	return $config['basedir'].'/cache/_neg/'.substr($gravatar_id, 0, 4).'/'.substr($gravatar_id, 4);
    }

    function get_mtime($gravatar_id) {
        $filename = $this->get_filename($gravatar_id);
        return @filemtime($filename);
    }

    function purge($expiry) {
        global $config;
        $expiry = time() - $expiry;
        $negdir = $config['basedir'].'/cache/_neg';
        $count = 0;
        if ($dh1 = @opendir($negdir)) {
            while (($dirname = readdir($dh1)) !== false) {
                $dirpath = "$negdir/$dirname";
                if (strlen($dirname) != 4 || ! is_dir($dirpath))
                    continue;
                if ($dh2 = @opendir($dirpath)) {
                    $skipped = 0;
                    while (($filename = readdir($dh2)) !== false) {
                        $filename = "$dirpath/$filename";
                        if (! is_file($filename))
                            continue;

                        $filetime = @filemtime($filename);
                        if ($filetime && ($filetime < $expiry)) {
                            unlink($filename);
                            $count ++;
                        } else
                            $skipped ++;
                    }
                    closedir($dh2);
                    if (! $skipped)
                        rmdir($dirpath);
                }
            }
            closedir($dh1);
        }
        if ($count > 0)
            gcdebug("Removed $count negative cache from files");
    }

    function set_mtime($gravatar_id, $mtime) {
        $filename = $this->get_filename($gravatar_id);
        $negdir = dirname($filename);
        if (! file_exists($negdir))
            mkdirr($negdir);
        touch($filename);
    }
};

class NegHandler_sqlite extends NegHandler {
    var $handle = null;

    function cleanup() {
        if ($this->handle != null) {
            sqlite_close($this->handle);
            $this->handle = null;
        }
    }

    function &get_handle() {
        global $config;
        if ($this->handle == null) {
            // Find a suitable handle.
            $this->handle = sqlite_open($config['basedir'].
                '/cache/neg.sqlite', 0644, $err);
            if (!$this->handle)
                die($err);

            // Check whether we need to create table.
            $res = sqlite_query($this->handle, 'SELECT name FROM '.
                'sqlite_master WHERE type=\'table\' AND name=\'gcneg\'');
            if (sqlite_num_rows($res) <= 0)
                sqlite_query($this->handle, 'CREATE TABLE gcneg ('.
                    'gid CHAR(32) PRIMARY KEY, mtime INTEGER)');
            register_shutdown_function(array($this, 'cleanup'));
        }

        return $this->handle;
    }

    function get_mtime($gravatar_id) {
        $dbh =& $this->get_handle();
        $res = sqlite_query($dbh,
            "SELECT mtime FROM gcneg WHERE gid='$gravatar_id'");
        if (sqlite_num_rows($res) > 0)
            return sqlite_fetch_single($res);

        return false;
    }

    function purge($expiry) {
        $expiry = time() - $expiry;
        $dbh =& $this->get_handle();
        sqlite_query($dbh, "DELETE FROM gcneg WHERE mtime<$expiry");
    }

    function set_mtime($gravatar_id, $mtime) {
        $dbh =& $this->get_handle();
        sqlite_query($dbh, 'REPLACE INTO gcneg (gid, mtime) VALUES '.
            "('$gravatar_id', '$mtime')");
    }
};

function copy_file($src, $dst) {
    global $config;
    global $gcversion;

    $dstdir = dirname($dst);
    if (!file_exists($dstdir))
        mkdirr($dstdir);
    if (!is_writable($dstdir)) {
        // If we cannot write to the destination directory (permission issue, 
        // for example), we will try to do an HTTP redirect.
        do_302($src);
    }


    $dsttmp = $dst . '.tmp';
    $result = false;
    if (ini_get('allow_url_fopen')) {
        // Use the URL file copy.
        $result = copy($url, $dsttmp);
    } else {
        if (function_exists('curl_init')) {
            // Use CURL
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $src);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_USERAGENT, "GravatarCache/$gcversion");
            curl_setopt($curl, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);

            // Setting up the proxy server if it exists in the configuration.
            if (isset($config['proxy']))
                curl_setopt($curl, CURLOPT_PROXY, $config['proxy']);

            $data = curl_exec($curl);
            curl_close($curl);
        } else {
            // Use the raw HTTP request to download the gravatar.
            $data = download_url($src);
        }

        if ($data) {
            if ($handle = fopen($dsttmp, 'wb')) 
                $result = fwrite($handle, $data);
            fclose($handle);
        }
    }

    // Check whether the operation is successful.
    if ($result) {
        // If filesize is small, it is possible we are getting the empty GIF. 
        // In this case we will ignore and cache the error.
        if (filesize($dsttmp) > 100) {
            rename($dsttmp, $dst);
            return 1;
        }
        gcdebug("Downloaded file too small. Discard.");
    } else {
        gcdebug("Fail to download from URL. URL=$src");
    }
    if (file_exists($dsttmp))
        unlink($dsttmp);
    return 0;
}

function do_302($url) {
    header('HTTP/1.1 302 Moved Temporarily');
    header('Status: 302 Moved Temporarily');
    header("Location: $url");
    exit(0); 
}

function do_404() {
    do_httpstatus(404, 'Not Found', 'The requested URL '.
        $_SERVER['REQUEST_URI'].' was not found on this server.');
}

function do_httpstatus($status, $shortmsg, $longmsg) {
    global $gcversion;
    header("HTTP/1.1 $status $shortmsg");
    header("Status: $status $shortmsg");
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
  <HEAD>
    <TITLE><?php echo "$status ".htmlspecialchars($shortmsg); ?></TITLE>
  </HEAD>
  <BODY>
    <H1><?php echo htmlspecialchars($shortmsg); ?></H1>
    <P><?php echo htmlspecialchars($longmsg); ?></p>
    <HR>
    <ADDRESS>Gravatar Cache/<?php echo $gcversion; ?></ADDRESS>
  </BODY>
</HTML>
<?php
    exit(0);
}

function do_purge() {
    global $config;

    if ($config['neg_expiry'] > 0) {
        $neg_handler = 'NegHandler_'.$config['neg_handler'];
        $neg_handler = new $neg_handler;
        $neg_handler->purge($config['neg_expiry']);
    }

    $configbak = unserialize(serialize($config));
    if ($dh = @opendir($config['basedir'].'/profile')) {
        while (($filename = readdir($dh)) !== false) {
            if (substr($filename, strlen($filename) - 4) == '.php') {
                $config['profile'] = substr($filename, 0, 
                    strlen($filename) - 4);
                include($config['basedir']."/profile/$filename");

                purge_cache($config['basedir'].'/cache/'.$config['profile'],
                    $config['pos_expiry']);
            
                $config = unserialize(serialize($configbak));
            }
        }
        closedir($dh);
    }
}

function download_url($url) {
    // FIXME: proxy not supported.
    global $gcversion;

    $urlparsed = parse_url($url);
    $pos = strpos($urlparsed['host'], ':');
    if ($pos === false) {
        $urlparsed['port'] = $urlparsed['scheme'] == 'https' ? 443 : 80;
        $urlparsed['host2'] = $urlparsed['host'];
    } else {
        $urlparsed['port'] = intval(substr($urlparsed['host'], $pos + 1));
        $urlparsed['host2'] = substr($urlparsed['host'], 0, $pos);
    }

    $socket = @fsockopen(($urlparsed['scheme'] == 'https' ? 'ssl://' : '') . 
        $urlparsed['host2'], $urlparsed['port'], $errno, $errstr, 15);
    if (! $socket)
        return false;

    $uri = $urlparsed['path'];
    if ($urlparsed['query'])
        $uri .= '?' . $urlparsed['query'];
    $req = "GET $uri HTTP/1.0\r\nHost: ".$urlparsed['host'].
        "\r\nUser-Agent: GravatarCache/$gcversion\r\nReferer: ".
        $_SERVER['HTTP_REFERER']."\r\n\r\n";

    if (function_exists('stream_set_timeout'))
        stream_set_timeout($socket, 30);
    fwrite($socket, $req);

    $data = '';
    while (is_resource($socket) && ! feof($socket))
        $data .= fread($socket, 1024);
    fclose($socket);

    if (($pos = strpos($data, "\r\n\r\n")) === false)
        return $data;

    $head = substr($data, 0, $pos);
    $body = substr($data, $pos + 4);

    $lines = explode("\r\n", $data);
    foreach ($lines as $line) {
        if (strncmp($line, 'HTTP/1.', 7) == 0) {
            $tokens = explode(' ', $line);
            if (sizeof($tokens) < 2) {
                return false;
            }
            switch (intval($tokens[1])) {
            case 200:
            case 301:
            case 302:
                break;
            default:
                return false;
            }
        } else {
            $line = explode(':', $line, 2);
            if (sizeof($line) == 2) {
                if (strtolower(trim($line[0])) == 'location') {
                    $url = trim($line[1]);
                    return download_url($url);
                }
            }
        }
    }

    return $body;
}

function fetch_gravatar($gravatar_id) {
    global $config;

    if(!validate_gravatar_id($gravatar_id)) {
        do_404();
        return;
    }

    $gravatar_file = get_cachedir().'/'.$gravatar_id;
    $neg_handler = 'NegHandler_'.$config['neg_handler'];
    $neg_handler = new $neg_handler;

    // Check whether there is a negative file showing that the gravatar for 
    // this ID does not exist.
    if ($neg_handler->check($gravatar_id)) {
        gcdebug("Neg cache exists. gid=$gravatar_id");
        return_default();
    } elseif (copy_file(get_gravatar_url($gravatar_id), $gravatar_file)) {
        gcdebug("Gravatar found. gid=$gravatar_id");
        return_file($gravatar_file);
    } else {
        gcdebug("Gravatar cannot be downloaded. gid=$gravatar_id");
        $neg_handler->set($gravatar_id);
        return_default();
    }
}

function gcdebug($msg) {
    global $config; 
    if ($config['debug']) {
        $profile = $config['profile'] || '<none>';
        error_log("GravatarCache[$profile]: $msg");
    }
}

function get_cachedir() {
    global $config;
    return $config['basedir'].'/cache/'.$config['profile'];
}

function get_gravatar_url($gravatar_id) {
    global $config;
    $url = $config['gravatar'] . '?gravatar_id=' . urlencode($gravatar_id);
    if ($config['rating'])
        $url .= '&rating=' . urlencode($config['rating']);
    if ($config['size'])
        $url .= '&size=' . urlencode($config['size']);
    if ($config['border'])
        $url .= '&border=' . urlencode($config['border']);
    return $url;
}

function mkdirr($dir) {
    if (is_dir($dir) || @mkdir($dir))
        return true;
    if (! mkdirr(dirname($dir)))
        return false;
    return mkdir($dir);
}

function purge_cache($dirname, $expiry) {
    if ($dh = @opendir($dirname)) {
        $expiry = time() - $expiry;
        $count = 0;
        while  (($filename = readdir($dh)) !== false) {
            $filename = "$dirname/$filename";
            if (is_file($filename)) {
                $filetime = filemtime($filename);
                if ($filetime && ($filetime < $expiry)) {
                    unlink($filename);
                    $count ++;
                }
            }
        }
        closedir($dh);
        if ($count)
            gcdebug("Removed $count cached gravatar from '".basename($dirname).
                "'.");
    }
}

function return_default() {
    global $config;
    if ($config['default'])
        do_302($config['default']);
    else
        return_empty();
}

$empty_gif = "GIF89a\x01\x00\x01\x00\x80\x00\x00\xff\xff\xff\x00\x00\x00!".
    "\xf9\x04\x01\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02".
    "\x02D\x01\x00;";

function return_empty() {
    global $empty_gif;
    header('Content-Type: image/gif');
    header('Content-Length: '.strlen($empty_gif));
    print $empty_gif;
}

function return_file($filename) {
    if (file_exists($filename)) {
        header('Content-Type: application/octet-stream');
        header('Content-Length: '.filesize($filename));
        readfile($filename);
    } else {
        return_empty();
    }
}

function split_gravatar_id($gravatar_id) {
    $result = array();
    for ($i = 0; $i < strlen($gravatar_id); $i += 8)
        $result[] = substr($gravatar_id, $i, 8);
    return implode('/', $result);
}

function validate_gravatar_id($gravatar_id) {
    // The only valid gravatar ID is lower cased hexdecimal string of 32 
    // characters.
    return (strlen($gravatar_id) == 32) &&
        (trim($gravatar_id, '0123456789abcdef') == '');
}

// --------------------------------------------------------------------------
// MAIN PROGRAM
// --------------------------------------------------------------------------
umask(022);

$baseuri = dirname($_SERVER['PHP_SELF']);
$pathinfo = parse_url($_SERVER['REQUEST_URI']);
$pathinfo = $pathinfo['path'];

if (strncmp($pathinfo, $baseuri, strlen($baseuri)) == 0)
    $pathinfo = substr($pathinfo, strlen($baseuri));
if (strncmp($pathinfo, '/', 1) == 0)
    $pathinfo = substr($pathinfo, 1);
$pathinfo = split('/', $pathinfo);

// Loading global configuration for all profiles if it exists.
if (file_exists($config['basedir'].'/config.php')) {
    include($config['basedir'].'/config.php');
}

switch (sizeof($pathinfo)) {
case 1:
    if ($pathinfo[0] == basename($_SERVER['PHP_SELF']) || !$pathinfo[0]) {
        if (isset($_GET['purge']))
            do_purge();
        break;
    }

    do_404();
    break;
case 3:
    // sizeof($pathinfo) == 3 -- We have got a cache miss and we need to pull 
    // images from Gravatar to fill up the cache. URI should be in the format 
    // of
    //
    //  /cache/<profile>/<gravatar_id>
    if ($pathinfo[0] == 'cache') {
        $config['profile'] = $pathinfo[1];
        $profile = $config['basedir'].'/profile/'.$config['profile'].'.php';
        // Throw away bad URL with a 404. We'll do 404 if profile does not 
        // exist, i.e. there is no configuration file.
        if (file_exists($profile)) {
            include($profile);
            fetch_gravatar($pathinfo[2]);
            break;
        }
    }
default:
   do_404();
}
