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
    * Página de Processamento de Anulação de Empenho
    * Data de Criação   : 06/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2007-10-31 15:55:22 -0200 (Qua, 31 Out 2007) $

    * Casos de uso: uc-02.03.24,uc-02.03.18,uc-02.03.04

*/

/*
$Log$
Revision 1.14  2006/07/27 19:36:31  cako
Bug #6606#

Revision 1.13  2006/07/19 19:34:45  jose.eduardo
Bug #6521#

Revision 1.12  2006/07/05 20:48:41  cleisson
Adicionada tag Log aos arquivos

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php" );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoConta.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AnularLiquidacao";
$pgFilt 	= "FL".$stPrograma.".php";
$pgList 	= "LS".$stPrograma.".php?stAcao=".$_REQUEST["stAcao"];
$pgForm 	= "FM".$stPrograma.".php";
$pgProc 	= "PR".$stPrograma.".php";
$pgOcul 	= "OC".$stPrograma.".php";

$obREmpenhoEmpenho = new REmpenhoEmpenho;

$stAcao = $request->get('stAcao');

//valida a utilização da rotina de encerramento do mês contábil
$arDtAutorizacao = explode('/', $_POST['stDtEstorno']);
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
    SistemaLegado::LiberaFrames(true,true);
    SistemaLegado::exibeAviso(urlencode("Mês da Anulação encerrado!"),"n_incluir","erro");
    exit;
}

switch ($stAcao) {
    case "anular":
        SistemaLegado::BloqueiaFrames(true, true);
        Sessao::setTrataExcecao(true);

        $obREmpenhoEmpenho->setExercicio ( $_REQUEST["dtExercicioEmpenho"] );
        $obREmpenhoEmpenho->setCodEmpenho ( $_REQUEST["inCodEmpenho"] );
        $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
        $obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST["inCodDespea"] );
        $obErro = $obREmpenhoEmpenho->consultar($boTransacao);
        if (SistemaLegado::comparaDatas( $_POST['stDtEstorno'], date('d/m/Y'))) {
            $obErro->setDescricao("Campo Data de Anulação deve ser menor ou igual a data de hoje.");
        }

        if ( !$obErro->ocorreu() ) {
            $obREmpenhoNotaLiquidacao = new REmpenhoNotaLiquidacao( $obREmpenhoEmpenho );
            $obREmpenhoNotaLiquidacao->setExercicio ( $_REQUEST['dtExercicioNota'] );
            $obREmpenhoNotaLiquidacao->setExercRP ( Sessao::getExercicio() );
            $obREmpenhoNotaLiquidacao->setDtEstornoLiquidacao ( $_POST['stDtEstorno'] );
            $obREmpenhoNotaLiquidacao->setDtVencimento ( $_REQUEST["dtValidadeFinal"] );
            $obREmpenhoNotaLiquidacao->setCodNota ( $_REQUEST["inCodNota"] );
            $obREmpenhoNotaLiquidacao->setCodContaContabilFinanc ( $_REQUEST["inCodContaContabilFinanc"] );
            $obREmpenhoNotaLiquidacao->setCodHistorico ( $_REQUEST["inCodHistoricoPatrimon"] );
            $obREmpenhoNotaLiquidacao->setComplemento ( $_REQUEST["stComplemento"] );
            $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $_POST['inNumOrgao'] );

            $arItens = array();
            foreach ($_POST as $stChave => $stValor) {
                if ( strstr( $stChave, "nuValor_" ) ) {
                    $arCampoItem = explode( "_", $stChave );
                    $arItemPreEmpenho[$arCampoItem[1]] = $stValor;
                    $arItens[$arCampoItem[1]]['num_item'] = $arCampoItem[1];
                    $arItens[$arCampoItem[1]]['valor']    = $stValor;
                }
            }
            Sessao::write('arItens', $arItens);
            for ( $inContItens = 0; $inContItens <  count( $obREmpenhoEmpenho->arItemPreEmpenho ); $inContItens++ ) {
                $obREmpenhoEmpenho->arItemPreEmpenho[$inContItens]->setValorAAnular( $arItemPreEmpenho[$obREmpenhoEmpenho->arItemPreEmpenho[$inContItens]->getNumItem()] );
            }
        }
        
        if ( !$obErro->ocorreu() ) {
            $obErro = $obREmpenhoNotaLiquidacao->anularItens($boTransacao );
        }
        
        if ( !$obErro->ocorreu() ) {
            $stLink = "&pg=".Sessao::read('pg')."&pos=".Sessao::read('pos');
            SistemaLegado::alertaAviso($pgList.$stLink, "Nota n. ".$obREmpenhoNotaLiquidacao->getCodNota()."/".$obREmpenhoNotaLiquidacao->getExercicio() , "incluir", "aviso", Sessao::getId(), "../");
            $stCaminho = CAM_GF_EMP_INSTANCIAS."liquidacao/OCRelatorioNotaLiquidacaoEmpenhoAnulado.php";
            $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodNota=".$obREmpenhoNotaLiquidacao->getCodNota();
            $stCampos .= "&stExercicioNota=".$_REQUEST['dtExercicioNota']."&inCodEntidade=".$_POST['inCodEntidade']."&boImplantado=";
            $stCampos .= $obREmpenhoEmpenho->getImplantado().'&stTimestamp='.$obREmpenhoNotaLiquidacao->getTimestamp()."&dtExercicioEmpenho=".$_REQUEST["dtExercicioEmpenho"];
            SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
        } else {
            SistemaLegado::exibeAviso( urlencode( $obErro->getDescricao()), "n_incluir", "erro" );
            SistemaLegado::LiberaFrames(true, true);
        }
        Sessao::encerraExcecao();
    break;

}
?>
