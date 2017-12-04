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
    * Arquivo de Oculto
    * Data de Criação: 30/07/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-09-13 13:11:28 -0300 (Qui, 13 Set 2007) $

    * Casos de uso: uc-04.05.59
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterDescontoExterno";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function comparaDatasMenorIgual($stData1,$stData2)
{
   list( $dia1,$mes1,$ano1 ) = explode( '/', $stData1 );
   list( $dia2,$mes2,$ano2 ) = explode( '/', $stData2 );

   if ("$ano1$mes1$dia1" >= "$ano2$mes2$dia2") {
        return true;
   } else {
        return false;
   }

}

function validaDataVigencia()
{
    ;

    include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao( $rsUltimaMovimentacao, $boTransacao    );
    $stJs = '';

    $stDataVigencia = $_REQUEST['dtVigencia'];
    $stDataInicialPeriodMovimentacao = $rsUltimaMovimentacao->getCampo("dt_inicial");

    $obErro = new Erro;
    if ( !comparaDatasMenorIgual( $stDataVigencia,$stDataInicialPeriodMovimentacao ) ) {
        $obErro->setDescricao( "Data de vigência deve ser igual ou maior que a data inicial da competência." );
        $stJs .= "f.dtVigencia.value = ''; 																	\n";
        $stJs .= "f.dtVigencia.focus(); 																	\n";
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');       		    \n";
    }

    return $stJs;
}

function montaValoresAlteracao()
{
    $stJs = '';
    if (!empty($_REQUEST['inContrato'])) {
        include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoPrevidencia.class.php");
        include_once (CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");

        $stFiltro = ' WHERE registro = '.$_REQUEST['inContrato'];
        $obTPessoalContrato = new TPessoalContrato;
        $obTPessoalContrato->recuperaTodos($rsContrato, $stFiltro);
        $inCodContrato = $rsContrato->getCampo("cod_contrato");

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

        $obTFolhaPagamentoDescontoExternoPrevidencia = new TFolhaPagamentoDescontoExternoPrevidencia;
        $obTFolhaPagamentoDescontoExternoPrevidencia->setDado ("registro", $_REQUEST['inContrato']);
        $obTFolhaPagamentoDescontoExternoPrevidencia->setDado("vigencia",$rsPeriodoMovimentacao->getCampo("dt_inicial"));
        $obTFolhaPagamentoDescontoExternoPrevidencia->recuperaRelacionamento ($rsDescontoExternoPrevidencia);

        if ($rsDescontoExternoPrevidencia->getNumLinhas() != -1) {
           $stDataVigencia = SistemaLegado::dataToBr($rsDescontoExternoPrevidencia->getCampo("vigencia"));
           $inValor = $rsDescontoExternoPrevidencia->getCampo("valor_previdencia");
           $inValorBase = $rsDescontoExternoPrevidencia->getCampo("vl_base_previdencia_formatado");

           $stJs  = "f.inValor.value = '".$inValor."';        \n";
           $stJs .= "f.inValorBase.value = '".$inValorBase."';  \n";
           $stJs .= "f.dtVigencia.value = '".$stDataVigencia."';               \n";
           $stJs .= "f.stTimestamp.value = '".$rsDescontoExternoPrevidencia->getCampo("timestamp")."';            \n";
           $stJs .= "f.btnOk.disabled = false;                                                                    \n";
        } else {
           $stJs  = "f.inContrato.value = '';        \n";
           $stJs .= "document.getElementById('inNomCGM').innerHTML = '&nbsp;&nbsp;';        \n";
           $stJs .= "f.inValor.value = '';        \n";
           $stJs .= "f.inValorBase.value = '';  \n";
           $stJs .= "f.dtVigencia.value = '';               \n";
           $stJs .= "f.stTimestamp.value = '';           \n";
           $stJs .= "alertaAviso('Matrícula não possui dados para alteração.','form','erro','".Sessao::getId()."');               \n";
           $stJs .= "f.btnOk.disabled = true;              \n";
        }
    }

    return $stJs;
}

function submeter()
{
    $stJs = "parent.frames[2].Salvar();  \n";

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "validaDataVigencia":
        $stJs .= validaDataVigencia();
    break;
    case "montaValoresAlteracao":
        $stJs .= montaValoresAlteracao();
    break;
    case "submeter":
        $stJs .= submeter();
    break;
}
if ($stJs) {
  echo $stJs;
}

?>
