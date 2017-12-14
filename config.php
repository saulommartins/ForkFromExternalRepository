<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
 *
 * Arquivo geral de configuração Urbem e inicialização
 *
 * Data de Criação: 26/05/2008
 * @author Desenvolvedor: Lucas Stephanou
 * $Id: config.php 63850 2015-10-23 17:45:08Z gelson $
 */

 /* hack para evitar erros */
//set_error_handler(create_function('$code,$msg', 'throw new Exception($msg, $code);'), E_ALL & ~E_NOTICE);
$path = realpath(dirname(__FILE__)).'/';

# diretorio do arquivo em execução
$current_path = realpath(dirname($_SERVER["SCRIPT_FILENAME"]));

#uri chamada
$request_uri = $_SERVER['REQUEST_URI'];

#path_relativo
$rel_path = str_replace($path,'',$current_path);

# seguro?
$protocolo = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';

# caso estejam no mesmo dir, é prq estamos na raiz url/fisica
if ($rel_path === $current_path) {
    $ar_req_uri = explode('/',$request_uri);
    array_shift($ar_req_uri); # remove [0] que é sempre vazio
    $ar_req_uri_len = count($ar_req_uri) -1 ;
    strpos($ar_req_uri[$ar_req_uri_len],'.php') ? array_pop($ar_req_uri) : true ;
    $root_url = implode('/',$ar_req_uri);
    $root_url = $protocolo.$_SERVER['HTTP_HOST'].'/'.$root_url;
} else {
    # descobre url raiz do urbem baseado na retirada do
    # caminho relativo do script em execução e do config.php
    $pos_rel_path = strpos($request_uri, $rel_path);
    $root_url = substr($request_uri,0,$pos_rel_path);
    $root_url = $protocolo.$_SERVER['HTTP_HOST'].$root_url;
}

#normalizar final
$root_url = substr($root_url,strlen($root_url) -1,1) != '/' ? $root_url.'/' : $root_url;
$path = substr($path,strlen($path) -1 ,1) != '/' ? $path.'/' : $path;

# padroniza variaveis para uso no urbem
if (!defined('URBEM_ROOT_URL'))  define('URBEM_ROOT_URL', $root_url);
if (!defined('URBEM_ROOT_PATH')) define('URBEM_ROOT_PATH', $path);

include_once URBEM_ROOT_PATH . "gestaoAdministrativa/fontes/PHP/framework/URBEM/Sessao.class.php" ;

$sName = 'sw'.md5(URBEM_ROOT_URL);
Sessao::open($sName);
if (!(array_key_exists('action',$_GET) && $_GET['action'] == 'sair')) { // se não for ação de saida

    if (!isset($_SESSION['urbemoot'])) {

        include 'gestaoAdministrativa/fontes/PHP/framework/yaml/spyc.php';
        $urbem_config = Spyc::YAMLLoad(URBEM_ROOT_PATH.'config.yml');

        if (!isset($urbem_config['urbem']['birt'])) {
          $urbem_config['urbem']['birt'] = array('port'=>'8080', 'port_ssl'=>'8443', 'host'=>'');
          @file_put_contents(URBEM_ROOT_PATH.'config.yml', Spyc::YAMLDump($urbem_config));
        }
        Sessao::write('urbemoot',$urbem_config,true);

    } else {

        $urbem_config = Sessao::read('urbemoot');

    }

    # grava definições
    if (!defined('BD_HOST'))       define('BD_HOST'       , $urbem_config['urbem']['connection']['host']);
    if (!defined('BD_NAME'))       define('BD_NAME'       , $urbem_config['urbem']['connection']['database']);
    if (!defined('BD_PORT'))       define('BD_PORT'       , $urbem_config['urbem']['connection']['port']);
    if (!defined('BD_USER'))       define('BD_USER'       , $urbem_config['urbem']['connection']['username']);
    if (!defined('BD_PASS'))       define('BD_PASS'       , $urbem_config['urbem']['connection']['password']);
    if (!defined('ENV_TYPE'))      define('ENV_TYPE'      , $urbem_config['urbem']['env']['type']);
    if (!defined('BIRT_PORT'))     define('BIRT_PORT'     , $urbem_config['urbem']['birt']['port']);
    if (!defined('BIRT_PORT_SSL')) define('BIRT_PORT_SSL' , $urbem_config['urbem']['birt']['port_ssl']);
    if (!defined('BIRT_HOST'))     define('BIRT_HOST'     , $urbem_config['urbem']['birt']['host']);

}
