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
    * Birt.php
    * Data de Criação   : 30/11/2006

    * @author Analista: Lucas Stephanou
    * @author Desenvolvedor: Lucas Stephanou

    * Casos de uso : uc-01.01.00
*/

error_reporting(E_ERROR);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/Sessao.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/Conexao.inc.php';

/**
 * Alteração para pegar parametros do banco na url , passadas pelo Urbem.
 * @author Lucas Teixeira Stephanou - Urbem - Confedereção Nacional dos Municipios
 */

// captura dados de conexao

if (!$_REQUEST['reportImage']) {
    $stDiretorioGestao = SistemaLegado::pegaDado('nom_diretorio' , 'administracao.gestao' , 'where cod_gestao='.$_REQUEST['inCodGestao'] );
    $stDiretorioGestao = substr($stDiretorioGestao,0, strlen($stDiretorioGestao) - 4).'RPT/';
    $stDiretorioModulo = SistemaLegado::pegaDado('nom_diretorio' , 'administracao.modulo' , 'where cod_modulo='.$_REQUEST['inCodModulo'] );
    $stBirtLayoutsFolder = $stDiretorioGestao.$stDiretorioModulo.'report/design';
}

// BIRT configuration
define("BIRT_ENGINE_HOME", "/var/www/birt_runtime/ReportEngine");
define("BIRT_LOG_FOLDER", session_save_path(). "/birt");
define("BIRT_BASE_IMAGE_URL", $_SERVER['SCRIPT_NAME']."?reportImage=");
define("BIRT_IMAGE_FOLDER", session_save_path() . "/birt/images" );
define("BIRT_LAYOUTS_FOLDER", $stBirtLayoutsFolder );
// not used yet. This will be required for drilldown operations
define("BIRT_BASE_URL", "http://localhost:8080/");

/**
 * Supports BIRT reports generation accessing Java environment.
 * @see br.org.cnm.birt.BIRTFacade (java)
 * @author Luiz Geovani Vier - E-Core Desenvolvimento de Software
 */
class BIRTInvoker
{
    public $birtFacade;

    public function BIRTInvoker()
    {
        // get instance of Java class
        $this->birtFacade = new Java('br.org.cnm.birt.BIRTFacade');
        // setup
        $accessCount = $this->birtFacade->setup(BIRT_ENGINE_HOME, BIRT_LOG_FOLDER, BIRT_BASE_URL, BIRT_BASE_IMAGE_URL, BIRT_IMAGE_FOLDER);
        //echo "accessCount: " . $accessCount . "<br/>";
    }

    public function generateReport($layoutPath, $_parameters, $format, $filename)
    {
        $format = strtolower($format);

        $ctype = '';
        switch ($format) {
            case 'html':
                $ctype = "text/html"; break;
            case 'pdf':
                $ctype = "application/pdf"; break;
            default:
                die ("generateReport: Unknown format: $format");
        }

        // convert parameters to java's HashMap
        $parameters = new Java('java.util.HashMap');
        foreach ($_parameters as $key => $value) {
            $parameters->put($key, $value);
        }

        $parameters->put("db_driver", "org.postgresql.Driver");
        $parameters->put("db_conn_url", "jdbc:postgresql://" . $_GET['db_host'] . ":" .$_GET['db_port'] . "/" . $_GET['db_name'] . "");
        $parameters->put("db_user", 'sw.'.Sessao::getUsername());
        $parameters->put("db_password", Sessao::getPassword());
        $parameters->put("cod_acao",Sessao::read('acao'));
        $parameters->put("exercicio",Sessao::getExercicio());

        //$parameters->put("db_conn_url", "jdbc:postgresql://" . $_GET['db_url'] . ":" .$_GET['db_port'] . "/" . $_GET['db_name'] . "");
        //$parameters->put("db_user", Sessao::getUsername());
        //$parameters->put("db_password", Sessao::getPassword());

        // invoke report generator
        //$tmpfile = $this->birtFacade->generateReport($layoutPath, $parameters, $format) or die ("Error generating report.");// " . java_last_exception_get().getMessage());
        $tmpfile = $this->birtFacade->generateReport($layoutPath, $parameters, $format) or die ("<b>Ocorreu um erro durante a geração do relatório</b><hr/>" . $this->getStackTrace(java_last_exception_get()));
        //echo "file: ".$filename;

        // http headers
        header("Expires: 0");
        header("Content-Type: $ctype");

        $cdisp = ($filename) ? 'attachment; filename="'.$filename.'"' : "inline";
        header("Content-Disposition: $cdisp");

        // write the generated report to the browser
        readfile($tmpfile);

        // delete temp file
        unlink ($tmpfile);
    }

    public function getStackTrace($ex)
    {
        $sw = new Java('java.io.StringWriter');
        $pw = new Java('java.io.PrintWriter', $sw);
        $ex->printStackTrace($pw);
        $stack = $sw->toString();

        return nl2br($stack);
    }

}

// singleton pattern implementation for the BIRTInvoker class
function getBIRTInvokerInstance()
{
    static $instance;
    if (!is_object($instance)) {
        // does not currently exist, so create it
        $instance = new BIRTInvoker();
    }

    return $instance;
}


// here we generate the requested report,
// if reportLayout parameter is specified.
$requestParams = $_POST ? $_POST : $_GET;
if (array_key_exists('reportLayout', $requestParams)) {
    $layout = $requestParams['reportLayout'];
    //echo "layout: $layout";
    $birtInvoker = getBIRTInvokerInstance();

    $params = $requestParams;//array("name" => "Geovani", "gender" => "M", "city" => "Porto Alegre");

    $fmt = array_key_exists('fmt', $requestParams) ? $requestParams["fmt"] : "html";
    $filename = array_key_exists('filename', $requestParams) ? $requestParams["filename"] : null;

    $result = $birtInvoker->generateReport(BIRT_LAYOUTS_FOLDER."/".$layout, $params, $fmt, $filename);

    //echo "<hr/>report result: " . $result;
    // on html reports, images have to be rendered in a new request
    // BIRT creates the images in BIRT_IMAGE_FOLDER
    // After readfile(), we can delete the image
} elseif (array_key_exists('reportImage', $requestParams)) {
    $imgName = $requestParams['reportImage'];
    $imgFile = BIRT_IMAGE_FOLDER."/".$imgName;
    //echo $imgFile;
    readfile($imgFile);
    // delete temp image file
    unlink ($imgFile);
}
?>
