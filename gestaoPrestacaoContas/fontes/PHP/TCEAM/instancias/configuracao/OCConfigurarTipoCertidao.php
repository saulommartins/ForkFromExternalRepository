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
    * Titulo do arquivo : Oculto do Tipo de Documento Certidao
    * Data de Criação   : 23/05/2014

    * @author Analista      Gelson
    * @author Desenvolvedor Evandro Noguez Melos

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: OCConfigurarTipoCertidao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConfigurarTipoCertidao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case "carregaComboTipoCertidao":
        include_once (CAM_GPC_TCEAM_MAPEAMENTO."TTCEAMTipoCertidao.class.php");
        $obTTCEAMTipoCertidao = new TTCEAMTipoCertidao();
        $obTTCEAMTipoCertidao->recuperaComboDocumentosCertidao($rsDocumentosCertidao, "", "ORDER BY tipo_certidao.cod_tipo_certidao", $boTransacao);
        
        foreach ($rsDocumentosCertidao->getElementos() as $valor) {
            $stJs .= "jQuery(\"select[name~='inCodDocumento_".$valor['cod_documento']."']\").val(".$valor['cod_tipo_certidao'].");";
        }
    break;
}

if ($stJs) {
    echo $stJs;
}

?>