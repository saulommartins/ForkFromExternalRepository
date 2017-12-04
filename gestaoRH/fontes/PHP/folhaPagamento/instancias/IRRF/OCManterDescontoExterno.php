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
    * @author Desenvolvedor: Tiago Finger

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-09-25 13:01:26 -0300 (Ter, 25 Set 2007) $

    * Casos de uso: uc-04.05.60
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

function verificaVigencia()
{
    ;

    include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao( $rsUltimaMovimentacao, $boTransacao    );
    $stJs = '';

    $stDataVigencia = $_REQUEST['stDataVigencia'];
    $stDataInicialPeriodMovimentacao = $rsUltimaMovimentacao->getCampo("dt_inicial");

    $obErro = new Erro;
    if ( !comparaDatasMenorIgual( $stDataVigencia,$stDataInicialPeriodMovimentacao ) ) {
        $obErro->setDescricao( "Data de vigência deve ser igual ou maior que a data inicial da competência." );
        $stJs .= "f.stDataVigencia.value = ''; 																	\n";
        $stJs .= "f.stDataVigencia.focus(); 																	\n";
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');       		    \n";
    }

    return $stJs;
}

function montaValoresAlteracao()
{
    ;

    $stJs = '';
    if ( !empty($_REQUEST['inContrato']) ) {

        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoIRRF.class.php"                     );
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"										 );

        $stFiltro = ' WHERE registro = '.$_REQUEST['inContrato'];
        $obTPessoalContrato = new TPessoalContrato;
        $obTPessoalContrato->recuperaTodos( $rsContrato, $stFiltro );
        $inCodContrato = $rsContrato->getCampo("cod_contrato");

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

        $obTFolhaPagamentoDescontoIRRF = new TFolhaPagamentoDescontoExternoIRRF;
        $obTFolhaPagamentoDescontoIRRF->setVigenciaIni		( $rsPeriodoMovimentacao->getCampo("dt_inicial"));

        $obTFolhaPagamentoDescontoIRRF->setVigenciaFinal	( $rsPeriodoMovimentacao->getCampo("dt_final"));

        $stFiltro = " AND desconto_externo_irrf.cod_contrato = ".$inCodContrato;
        $obTFolhaPagamentoDescontoIRRF->recuperaRelacionamento ( $rsDescontoExternoIRRF,$stFiltro);
      
        if ( $rsDescontoExternoIRRF->getNumLinhas() != -1 ) {
         
            //formatando os campos para colocar na tela
            $stDataVigencia = SistemaLegado::dataToBr($rsDescontoExternoIRRF->getCampo("vigencia"));
            $fiValorBaseIRRF = number_format($rsDescontoExternoIRRF->getCampo("vl_base_irrf"), 2, ',', '.') ;
            $fiValorDescontoIRRF = $rsDescontoExternoIRRF->getCampo("valor_irrf");

            $stJs  = "f.fiValorBaseIRRF.value = '".$fiValorBaseIRRF."';  				  								  \n";
            $stJs .= "f.fiValorDescontoIRRF.value = '".$fiValorDescontoIRRF."';  			 	  						  \n";
            $stJs .= "f.stDataVigencia.value = '".$stDataVigencia."'; 													  \n";
            $stJs .= "f.stTimestamp.value = '".$rsDescontoExternoIRRF->getCampo("timestamp")."'; 	     				  \n";
            $stJs .= "f.btnOk.disabled = false;						 													  \n";
        } else {
            $obErro = new Erro;
            $obErro->setDescricao( "Matrícula não possui dados para alteração." );

            $stJs  = "f.fiValorBaseIRRF.value = '';  																	  \n";
            $stJs .= "f.fiValorDescontoIRRF.value = '';  			 													  \n";
            $stJs .= "f.stDataVigencia.value = ''; 																		  \n";
            $stJs .= "f.stTimestamp.value = ''; 	     				  												  \n";
            $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');       	    		  \n";
            $stJs .= "f.btnOk.disabled = true;     				 													 	  \n";
        }

    }

    return $stJs;
}

function submeter()
{
    ;

    $stJs = "parent.frames[2].Salvar();																	    			  \n";

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "verificaVigencia":
        $stJs .= verificaVigencia();
        break;
    case "submeter":
        $stJs .= submeter();
        break;
    case "montaValoresAlteracao":
        $stJs .= montaValoresAlteracao();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
