#!/bin/php
<?php
error_reporting(E_ALL ^ E_NOTICE);

$now   = new DateTime;

$log_argv = $argv;
array_shift($log_argv);
$script_name=explode("/", $argv[0]);
#file_put_contents("/tmp/saidaphp.log",serialize($log_argv). "\n",FILE_APPEND);
gera_log(end($script_name) . " " . implode (" ", $log_argv));

set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');

if (count($argv) < 3) {
    die ("Erro. Missing arguments!\n\nUsage: {$argv[0]} storage_ip cmd discovery|debug|cmd info\n\nex: {$argv[0]} 172.16.57.100 system discovery\n\n");
}
if (in_array("debug",$argv) || $argv[2] == "all") {
    $debug = "on";
} else {
    $debug = "off";
}
include 'Net/SSH2.php';
$server = $argv[1];
$cmd_base = 'uemcli -d ' . $server . ' -u storage_user -p storage_password ';
$comandos = array('sistema' => $cmd_base . '/sys/general show -detail',
                  'procs'   => $cmd_base . '/env/sp show',
                  'discos'  => $cmd_base . '/env/disk show -detail',
                  'bateria' => $cmd_base . '/env/bat show',
                  'pools'   => $cmd_base . '/stor/config/pool show -detail',
                  'fontes'  => $cmd_base . '/env/ps show');

$output = array();

function gera_log($msg) {
  $my_pid = getmypid();
  $now   = new DateTime;
  file_put_contents("/tmp/saidaphp.log", $now->format( 'Y-m-d H:i:s' ) . " $msg (pid:$my_pid)\n", FILE_APPEND);
}


function coleta_vnxe($arg_cmd) {
    global $argv, $comandos, $ssh, $debug;
    $cmd_out = array();
    $idx = 0;
    foreach ($comandos as $id => $cmd) {
        if ($id == $arg_cmd || $arg_cmd == 'all') {
            if ($debug == 'on') {
                print "catalogando $id...\n";
            }
            $x = explode("\n", $ssh->exec($cmd));
            foreach ($x as $line) {
                if (preg_match("/^(\d+):\s+(.*)\s+=\s+(.*)$/", $line, $m)) {
                    $idx =  preg_replace("/\W/", "_", $m[3]);
                    $item = preg_replace("/\s+$/", "", $m[2]);
                    $item = preg_replace("/\W/", "_", $item);
                    $cmd_out[$id][$idx][$item] = $idx;
                }
                else if (preg_match("/\s+(.*)\s+=\s+(.*)$/", $line, $m)) {
                    $item = preg_replace("/\s+$/", "", $m[1]);
                    $item = preg_replace("/\W/", "_", $item);
                    $cmd_out[$id][$idx][$item] = $m[2];
                }
            }
        }
    }
    if ($debug == "on") {
        var_dump($cmd_out);
    }
    return($cmd_out);
}

function imprime_saida($cmd_out, $arg_cmd2, $arg_cmd3, $arg_cmd4="") {
    global $debug;
    foreach ($cmd_out as $out) {
            foreach ($out as $item_id => $item_value) {
                if ($arg_cmd3 == "discovery") {
                    $arg_cmd2 = preg_replace("/\W/", "_", $arg_cmd2);
                    $out_data['data'][] = array('{#' . strtoupper($arg_cmd2) . '}' => $item_id);
                } else {
                    if ($arg_cmd3 == $item_id || $debug == "on") {
                        if ($debug == "on"){
                            var_dump($item_value);
                        }
                        print $item_value[$arg_cmd4] ."\n";
                    }
            }
        }
    }
    if ($arg_cmd3 == "discovery") {
        print json_encode($out_data) . "\n";
    }
}

function check_pidfile($action) {
    global $argv;
$tmp_file = "/tmp/vnxephp.pid";
$my_pid = getmypid();
  switch ($action) {
        case 'criar':
           while (file_exists($tmp_file)) {
              gera_log("Aguardando fim da execucao do processo $my_pid");
              sleep(rand(1,3));
           }
           gera_log("Iniciando execucao...");
           file_put_contents($tmp_file, $my_pid, LOCK_EX);
           if (!file_exists ($tmp_file)){
                gera_log("arquivo pid nao encontrado");
                exit;
                }
        break;
        case 'sair':
           if (array_key_exists(4, $argv)) {
                //gera_log("Leitura via cache concluida com sucesso");
                exit;
           }
           if (file_exists($tmp_file)) {
              $file_pid = file_get_contents($tmp_file);
              if ($file_pid == $my_pid) {
                unlink($tmp_file);
                gera_log("Execucao concluida com sucesso");
             } else {
                gera_log("script em execucao com pid diferente $file_pid");
             }
           } else {
                gera_log("arquivo $tmp_file n▒o existe.");
           }
           exit;
        break;
  }
}

###################
#   Start here    #
###################

$cfile = "/tmp/{$argv[1]}.{$argv[2]}.cache";

if (array_key_exists(4, $argv)) {
  //Look for cache file
  if (file_exists($cfile)) {
    //print "reading from cache..\n";
    $cmd_out = unserialize(file_get_contents($cfile));
  } else {
    die("cache file not found");
  }
}

// run cmd
//check_pidfile('criar');

if ($debug == "on") {
  print "conecting to vnxe to colect info from $server storace\n";
}

$ssh = new Net_SSH2('VNXE_IP_ADDRESS');
if (!$ssh->login('VNXE_LINUX_USERNAME', 'VNXE_USER_PASSWORD')) {
   print('Login Failed');
   check_pidfile('sair');
}

if ($debug == "on") {
echo "running $cmd...\n";
}

//verify args
$arg_cmd2 = preg_replace("/\W/", "", $argv[2]);
$arg_cmd3 = preg_replace("/\W/", "", $argv[3]);
$arg_cmd4 = preg_replace("/\W/", "", $argv[4]);
if ($arg_cmd3 == "discovery") {
    $cmd_out = coleta_vnxe($arg_cmd2);
    file_put_contents($cfile,serialize($cmd_out), LOCK_EX);
} else {
  $cmd_out = unserialize(file_get_contents($cfile));
}

imprime_saida($cmd_out, $arg_cmd2, $arg_cmd3, $arg_cmd4);

check_pidfile('sair');
?>
