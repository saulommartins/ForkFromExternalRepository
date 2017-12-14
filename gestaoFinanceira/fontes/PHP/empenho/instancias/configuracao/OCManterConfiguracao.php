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
  * Formulário oculto
  * Data de criação :

    * @author Analista:
    * @author Programador:

    $Id: OCManterConfiguracao.php 65471 2016-05-24 18:58:44Z michel $

    Caso de uso: uc-02.03.01
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function montaSpanContaCaixa()
{
    $rsRecordSet = new RecordSet;

    if ( count ( Sessao::read('arItens') ) > 0 ) {
        $rsRecordSet->preenche( Sessao::read('arItens') );
    }

    $obLista = new Lista;

    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( 'Conta Caixa das Entidades');

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código Entidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Conta Caixa");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome");
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inCodEntidade" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inCodConta" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento ( 'ESQUERDA' );
    $obLista->ultimoDado->setCampo( "stNomConta" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('delContaCaixa');" );
    $obLista->ultimaAcao->addCampo("","&inId=[inId]&inCodPlano=[inCodConta]&inCodEntidade=[inCodEntidade]");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnContaCaixa').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnContaCaixa').innerHTML = '".$html."';\n";

    return $stJs;
}

function addContaCaixa($inCodEntidade, $stNomEntidade, $inCodContaAnalitica, $stNomContaAnalitica)
{
    $stJs = '';

    $arItens = Sessao::read('arItens');
    if ( is_array($arItens) ) {
        $stErro = '';
        foreach ($arItens as $registro) {
            if ($registro['inCodEntidade'] == $inCodEntidade) {
                $stErro = 'Já existe uma conta caixa para esta entidade.';
            }
        }
    }

    if ($stErro) {
        $stJs = "alertaAviso('$stErro','form','erro','".Sessao::getId()."');\n  ";
    } else {
        $inId = count(Sessao::read('arItens'));

        $arItens[$inId]['inId'            ] = $inId;
        $arItens[$inId]['inCodEntidade'   ] = $inCodEntidade;
        $arItens[$inId]['inCodConta'      ] = $inCodContaAnalitica;
        $arItens[$inId]['stNomConta'      ] = $stNomContaAnalitica;

        Sessao::write('arItens', $arItens);

        $stJs = montaSpanContaCaixa();
    }

    return $stJs;
}

function validaDtFixa(Request $request)
{
    $stJs = "";
    $stTipo = "";
    $stRequest = "";
    
    foreach( $request->getAll() AS $key => $value ){
        if(strpos($key, 'stDtAutorizacao')!==FALSE){
            list ( $stRequest, $inCodEntidade, $inLinha ) = explode("_", $key);
        }
        if(strpos($key, 'stDtEmpenho')!==FALSE){
            list ( $stRequest, $inCodEntidade, $inLinha ) = explode("_", $key);
        }
        if(strpos($key, 'stDtLiquidacao')!==FALSE){
            list ( $stRequest, $inCodEntidade, $inLinha ) = explode("_", $key);
        }

        if($stRequest != ''){
            $inNumCgm = SistemaLegado::pegaDado('numcgm','orcamento.entidade', "where cod_entidade =".$inCodEntidade." and exercicio = '".Sessao::getExercicio()."'");
            $stNomEntidade = SistemaLegado::pegaDado('nom_cgm','sw_cgm', "where numcgm =".$inNumCgm);

            $request->set($stRequest     , $value);
            $request->set('inCodEntidade', $inCodEntidade);
            $request->set('stNomEntidade', $stNomEntidade);
            $request->set('inLinha'      , $inLinha);
            $request->set('stId'         , $key);

            break;
        }
    }

    if($request->get('stDtAutorizacao')){
        list ( $dia, $mes, $ano ) = explode("/", $request->get('stDtAutorizacao'));
        if($ano == Sessao::getExercicio()){
            include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php";

            $obTEmpenhoAutorizacaoEmpenho = new TEmpenhoAutorizacaoEmpenho();
            $stFiltro  = " WHERE ae.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro .= " AND ae.cod_entidade = ".$request->get('inCodEntidade');
            $obTEmpenhoAutorizacaoEmpenho->recuperaRelacionamentoPorPreEmpenho($rsAutorizacao, $stFiltro , " ORDER BY ae.dt_autorizacao DESC LIMIT 1 ");

            if (!$rsAutorizacao->eof()) {
                $stMaxDtAutorizacao = $rsAutorizacao->getCampo('dt_autorizacao');

                if(!SistemaLegado::comparaDatas($request->get('stDtAutorizacao'), $stMaxDtAutorizacao, TRUE))
                    $stMensagem = "A Data Fixa para Autorização não pode ser inferior a data: ".$stMaxDtAutorizacao." (data da última autorização), para a Entidade (".$request->get('inCodEntidade')." - ".$request->get('stNomEntidade').")";
            }
        }else
            $stMensagem = "A Data Fixa para Autorização deve ser do exercício de ".Sessao::getExercicio()."!";
    }

    if($request->get('stDtEmpenho')){
        list ( $dia, $mes, $ano ) = explode("/", $request->get('stDtEmpenho'));
        if($ano == Sessao::getExercicio()){
            include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";

            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $stFiltro  = " AND e.cod_entidade = ".$request->get('inCodEntidade');
            $obTEmpenhoEmpenho->recuperaMaiorDataEmpenho($rsEmpenho, $stFiltro);

            if (!$rsEmpenho->eof()) {
                $stMaxDtEmpenho = $rsEmpenho->getCampo('dataempenho');

                if(!SistemaLegado::comparaDatas($request->get('stDtEmpenho'), $stMaxDtEmpenho, TRUE))
                    $stMensagem = "A Data Fixa para Empenho não pode ser inferior a data: ".$stMaxDtEmpenho." (data da última empenho), para a Entidade (".$request->get('inCodEntidade')." - ".$request->get('stNomEntidade').")";
            }
        }else
            $stMensagem = "A Data Fixa para Empenho deve ser do exercício de ".Sessao::getExercicio()."!";
    }

    if($request->get('stDtLiquidacao')){
        list ( $dia, $mes, $ano ) = explode("/", $request->get('stDtLiquidacao'));
        if($ano == Sessao::getExercicio()){
            include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacao.class.php";

            $obTEmpenhoNotaLiquidacao = new TEmpenhoNotaLiquidacao;
            $obTEmpenhoNotaLiquidacao->setDado( "stDataEmpenho", "01/01/".Sessao::getExercicio());
            $obTEmpenhoNotaLiquidacao->setDado( "stExercicio"  , Sessao::getExercicio());
            $stFiltro  = " WHERE nota_liquidacao.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro .= " AND nota_liquidacao.cod_entidade = ".$request->get('inCodEntidade');
            $obTEmpenhoNotaLiquidacao->recuperaMaiorDataLiquidacao($rsLiquidacao, $stFiltro);

            if (!$rsLiquidacao->eof()) {
                $stMaxDtLiquidacao = $rsLiquidacao->getCampo('data_liquidacao');

                if(!SistemaLegado::comparaDatas($request->get('stDtLiquidacao'), $stMaxDtLiquidacao, TRUE))
                    $stMensagem = "A Data Fixa para Liquidação não pode ser inferior a data: ".$stMaxDtLiquidacao." (data da última liquidação), para a Entidade (".$request->get('inCodEntidade')." - ".$request->get('stNomEntidade').")";
            }
        }else
            $stMensagem = "A Data Fixa para Liquidação deve ser do exercício de ".Sessao::getExercicio()."!";
    }

    if($stMensagem){
        $stJs .= "jQuery('#".$request->get('stId')."').val('');                       \n";
        $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."'); \n";
    }

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case 'limpaPopUpContaAnalitica':
        $stJs .= "document.frm.inCodContaAnalitica.value = '';\n";
        $stJs .= "document.getElementById('stNomContaAnalitica').innerHTML = '&nbsp';\n";
        echo $stJs;
    break;

    case "recuperaFormularioAlteracao":

    break;

    case 'incluircontaCaixa':
        $stJs = addContaCaixa( $request->get('inCodEntidade'), $request->get('stNomEntidade'), $request->get('inCodContaAnalitica'), $request->get('stNomContaAnalitica') ) ;
    break;

    case 'delContaCaixa':
        if ($request->get('inCodPlano')) {
            include_once(CAM_GF_EMP_MAPEAMENTO."TEmpenhoConfiguracao.class.php");
            $obErro = new Erro();
            $obTEmpenhoConfiguracao = new TEmpenhoConfiguracao;
            $obTEmpenhoConfiguracao->setDado('cod_plano', $request->get('inCodPlano'));
            $obTEmpenhoConfiguracao->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obTEmpenhoConfiguracao->setDado('exercicio', Sessao::getExercicio() );
            $obErro = $obTEmpenhoConfiguracao->verificaUtilizacaoContaCaixa( $rsRecordSet );
            if (!$obErro->ocorreu() && $rsRecordSet->getNumLinhas() < 0) {
                $arTMP = array();
                $id = $request->get('inId');
                $inCount = 0;

                $arItensSessao = array();
                $arItensSessao = Sessao::read('arItens');
                $arItens = array();
                foreach ($arItensSessao as $array) {
                    if ($array['inId'] != $id) {

                        $arItens[$inCount]['inId'            ] = $array['inId'];
                        $arItens[$inCount]['inCodEntidade'   ] = $array['inCodEntidade'];
                        $arItens[$inCount]['inCodConta'      ] = $array['inCodConta'   ];
                        $arItens[$inCount]['stNomConta'      ] = $array['stNomConta'   ];

                        $inCount = $inCount + 1;
                    }
                    Sessao::write('arItens', $arItens);
                    $stJs = montaSpanContaCaixa();
                }
            } else {
                $stJs = "alertaAviso('Erro ao excluir conta: A Conta Caixa ".$request->get('inCodPlano')." já possui movimentação de Retenções.','form','erro','".Sessao::getId()."');\n  ";
            }
        }

    break;

    case 'validaDtFixa':
        $stJs = validaDtFixa( $request );
    break;
}

if ($stJs) {
    echo $stJs;
}
