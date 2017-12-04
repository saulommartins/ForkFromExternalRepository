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
    * Página de processamento oculto para o cadastro de bairro
    * Data de Criação   : 14/10/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: OCProcurarBairro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.05
*/

/*
$Log$
Revision 1.5  2006/09/15 15:03:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php" );

    $stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

// Instancia Objeto
$obRCIMBairro = new RCIMBairro;
$rsMunicipios = new RecordSet;

switch ($_REQUEST ["stCtrl"]) {
    case "preencheMunicipio":
        $js .= "f.inCodMunicipio.value = '';\n";
        $js .= "limpaSelect(f.cmbMunicipio,0); \n";
        $js .= "f.cmbMunicipio[0] = new Option('Selecione','', 'selected');\n";
        if ($_REQUEST["inCodUF"]) {
            $obRCIMBairro->setCodigoUF( $_REQUEST["inCodUF"]);
            $obRCIMBairro->listarMunicipios( $rsMunicipios );
            $inContador = 1;
        }
        while ( !$rsMunicipios->eof() ) {
            $inCodMunicipio = $rsMunicipios->getCampo( "cod_municipio" );
            $stNomMunicipio = $rsMunicipios->getCampo( "nom_municipio" );
            $js .= "f.cmbMunicipio.options[$inContador] = new Option('".$stNomMunicipio."','".$inCodMunicipio."'); \n";
            $inContador++;
            $rsMunicipios->proximo();
        }
        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inCodMunicipio.value='".$_REQUEST["inCodMunicipio"]."'; \n";
            $js .= "f.cmbMunicipio.options[".$_REQUEST["inCodMunicipio"]."].selected = true; \n";
        }
        sistemaLegado::executaIFrameOculto($js);
        break;

    case "buscaLegalAliquota":
        include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php" );
        $obRARRDesoneracao = new RARRDesoneracao;
        $obRARRDesoneracao->addNorma();
        $obRARRDesoneracao->roUltimaNorma->setCodNorma( $_REQUEST['inCodigoFundamentacaoAliquota'] );
        $obRARRDesoneracao->roUltimaNorma->listar( $rsNorma );

        if ( !$rsNorma->eof() ) {
            $stJs = "d.getElementById('stFundamentacaoAliquota').innerHTML = '".$rsNorma->getCampo( "nom_norma" )."';\n";
        } else {
            $stMsg = "Fundamentação inválida! ";
            $stJs = "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoFundamentacaoAliquota"].")','form','erro','".Sessao::getId()."', '../');";

            $stJs .= "d.getElementById('stFundamentacaoAliquota').innerHTML = '&nbsp;';\n";
            $stJs .= 'f.inCodigoFundamentacaoAliquota.value = "";';
            $stJs .= 'f.inCodigoFundamentacaoAliquota.focus();';
        }

        SistemaLegado::executaIFrameOculto($stJs);
        break;

    case "buscaLegal":
        include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php" );
        $obRARRDesoneracao = new RARRDesoneracao;
        $obRARRDesoneracao->addNorma();
        $obRARRDesoneracao->roUltimaNorma->setCodNorma( $_REQUEST['inCodigoFundamentacao'] );
        $obRARRDesoneracao->roUltimaNorma->listar( $rsNorma );

        if ( !$rsNorma->eof() ) {
            $stJs = "d.getElementById('stFundamentacao').innerHTML = '".$rsNorma->getCampo( "nom_norma" )."';\n";
        } else {
            $stMsg = "Fundamentação inválida! ";
            $stJs = "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoFundamentacao"].")','form','erro','".Sessao::getId()."', '../');";

            $stJs .= "d.getElementById('stFundamentacao').innerHTML = '&nbsp;';\n";
            $stJs .= 'f.inCodigoFundamentacao.value = "";';
            $stJs .= 'f.inCodigoFundamentacao.focus();';
        }

        SistemaLegado::executaIFrameOculto($stJs);
        break;
}
?>
