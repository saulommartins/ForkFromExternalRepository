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
    * Data de Criação: 10/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Id: PRManterAditivoContrato.php 66086 2016-07-18 20:07:47Z michel $

    * Casos de uso : uc-03.05.24
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( TLIC."TLicitacaoContrato.class.php" );
include_once( TLIC."TLicitacaoContratoAditivos.class.php" );
include_once( TLIC."TLicitacaoContratoAditivosAnulacao.class.php" );
include_once( TLIC."TLicitacaoPublicacaoContratoAditivos.class.php" );

Sessao::getExercicio();
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stPrograma = "ManterAditivoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$arValores = Sessao::read('arValores');

Sessao::setTrataExcecao( true );
$stMensagem = "";

// validação dos dados caso ação seje diferente de 'anular'
if ($stAcao != "anular") {
    $obTLicitacaoContrato = new TLicitacaoContrato();
    $obTLicitacaoContrato->setDado('exercicio_contrato', $request->get('stExercicio'));
    $obTLicitacaoContrato->setDado('num_contrato', $request->get('inNumContrato'));
    $obTLicitacaoContrato->setDado('cod_entidade', $request->get('inCodEntidade'));
    $obTLicitacaoContrato->recuperaPorChave( $rsLicitacaoContrato );

    if ( implode(array_reverse(explode('/',$request->get('dtAssinatura')))) < implode(array_reverse(explode('/',$rsLicitacaoContrato->getCampo("dt_assinatura"))))) {
        $stMensagem .= "<br />A data de assinatura do aditivo não pode ser anterior que a data de assinatura do contrato.";
    }

    if ( implode(array_reverse(explode('/',$request->get('dtInicioExcucao')))) < implode(array_reverse(explode('/',$request->get('dtAssinatura'))))) {
        $stMensagem .= "<br />A data de início de execução não pode ser anterior que a data de assinatura do aditivo.";
    }

    if ( implode(array_reverse(explode('/',$request->get('dtFinalVigencia')))) < implode(array_reverse(explode('/',$request->get('dtInicioExcucao'))))) {
        $stMensagem .= "<br />A data de final de vigência não pode ser anterior que a data de início de execução.";
    }
}

if ($stMensagem != "") {
    SistemaLegado::exibeAviso(urlencode($stMensagem), "n_incluir", "erro" );
} else {

    $obTLicitacaoPublicacaoContratoAditivos = new TLicitacaoPublicacaoContratoAditivos;

    switch ($stAcao) {
    case "incluir":
        $obTLicitacaoContratoAditivo = new TLicitacaoContratoAditivos();
        setaDados($obTLicitacaoContratoAditivo, $stAcao);
        $obTLicitacaoContratoAditivo->inclusao();

        //inclui os dados da publicacao do contrato
        foreach ($arValores as $arTemp) {
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_contrato'       , $obTLicitacaoContratoAditivo->getDado('num_contrato') );
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_aditivo'        , $obTLicitacaoContratoAditivo->getDado('num_aditivo') );
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'numcgm'             , $arTemp['inVeiculo']);
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'dt_publicacao'      , $arTemp['dtDataPublicacao']);
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_publicacao'     , $arTemp['inNumPublicacao']);
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'exercicio'          , Sessao::getExercicio());
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'exercicio_contrato' , $obTLicitacaoContratoAditivo->getDado('exercicio_contrato') );
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'cod_entidade'       , $request->get('inCodEntidade'));
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'observacao'         , $arTemp['stObservacao']);
            $obTLicitacaoPublicacaoContratoAditivos->inclusao();
        }

        SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Contrato: ".$request->get('inNumContrato')."/".$request->get('stExercicioContrato'),"incluir", "aviso", Sessao::getId(),"");
        break;

    case "alterar":
        $obTLicitacaoContratoAditivo = new TLicitacaoContratoAditivos();
        setaDados($obTLicitacaoContratoAditivo, $stAcao);
        $obTLicitacaoContratoAditivo->alteracao();

        //exclui os veiculos de publicidade existentes
        $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_contrato' , $request->get('inNumContrato'));
        $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_aditivo'  , $request->get('inNumAditivo'));
        $obTLicitacaoPublicacaoContratoAditivos->setDado( 'exercicio'    , Sessao::getExercicio());
        $obTLicitacaoPublicacaoContratoAditivos->setDado( 'cod_entidade' , $request->get('inCodEntidade'));
        $obTLicitacaoPublicacaoContratoAditivos->exclusao();

        //inclui os veiculos que estao na sessao
        foreach ($arValores as $arTemp) {
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_contrato'       , $obTLicitacaoContratoAditivo->getDado('num_contrato'));
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_aditivo'        , $obTLicitacaoContratoAditivo->getDado('num_aditivo'));
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'numcgm'             , $arTemp['inVeiculo']);
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'dt_publicacao'      , $arTemp['dtDataPublicacao']);
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_publicacao'     , $arTemp['inNumPublicacao']);
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'exercicio'          , Sessao::getExercicio());
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'exercicio_contrato' , $obTLicitacaoContratoAditivo->getDado('exercicio_contrato'));
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'cod_entidade'       , $request->get('inCodEntidade'));
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'observacao'         , $arTemp['stObservacao']);
            $obTLicitacaoPublicacaoContratoAditivos->inclusao();
        }

        SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Contrato: ".$request->get('inNumContrato')."/".$request->get('stExercicioContrato'),"alterar", "aviso", Sessao::getId(),"");
        break;

    case "anular":
        $obTLicitacaoContratoAditivoAnulacao = new TLicitacaoContratoAditivosAnulacao();
        $obTLicitacaoContratoAditivoAnulacao->setDado("exercicio_contrato", $request->get("stExercicioContrato"));
        $obTLicitacaoContratoAditivoAnulacao->setDado("cod_entidade", $request->get("inCodEntidade"));
        $obTLicitacaoContratoAditivoAnulacao->setDado("num_contrato", $request->get("inNumContrato"));
        $obTLicitacaoContratoAditivoAnulacao->setDado("exercicio", $request->get("stExercicioAditivo"));
        $obTLicitacaoContratoAditivoAnulacao->setDado("num_aditivo", $request->get("inNumeroAditivo"));
        $obTLicitacaoContratoAditivoAnulacao->setDado("dt_anulacao", $request->get("dtAnulacao"));
        $obTLicitacaoContratoAditivoAnulacao->setDado("motivo", $request->get("stMotivoAnulacao"));
        $vlValorAnulacao = number_format(str_replace(".", "", $request->get('vlValorAnulacao')), 2, ".", "");
        $obTLicitacaoContratoAditivoAnulacao->setDado('valor_anulacao', $vlValorAnulacao);
        
        $obTLicitacaoContratoAditivo = new TLicitacaoContratoAditivos;
        $obTLicitacaoContratoAditivo->setDado("exercicio_contrato", $request->get("stExercicioContrato"));
        $obTLicitacaoContratoAditivo->setDado("cod_entidade", $request->get("inCodEntidade"));
        $obTLicitacaoContratoAditivo->setDado("num_contrato", $request->get("inNumContrato"));
        $obTLicitacaoContratoAditivo->setDado("exercicio", $request->get("stExercicioAditivo"));
        $obTLicitacaoContratoAditivo->setDado("num_aditivo", $request->get("inNumeroAditivo"));
        $obTLicitacaoContratoAditivo->recuperaPorChave($rsContratoAditivos);
        
        if($vlValorAnulacao > $rsContratoAditivos->getCampo('valor_contratado')) {
            SistemaLegado::exibeAviso('O valor da anulação não pode ser maior que o valor do aditivo.', "n_incluir", "erro" );
            break;
        }
        
        $obTLicitacaoContratoAditivoAnulacao->inclusao();
        
        SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Contrato: ".$request->get('inNumContrato')."/".$request->get('stExercicioContrato'),"anular", "aviso", Sessao::getId(),"");
        break;
    }
}
Sessao::encerraExcecao();

// método para setar os dados do objeto passado por parâmetro.
function setaDados(&$obTLicitacaoContratoAditivo, $stAcao)
{
    global $request;
    
    $obTLicitacaoContratoAditivo->setDado('exercicio_contrato', $request->get('stExercicioContrato'));
    $obTLicitacaoContratoAditivo->setDado('num_contrato', $request->get('inNumContrato'));
    $obTLicitacaoContratoAditivo->setDado('cod_entidade', $request->get('inCodEntidade'));
    if ($stAcao == "incluir") {
        $obTLicitacaoContratoAditivo->setDado('exercicio', Sessao::getExercicio());
        $obTLicitacaoContratoAditivo->proximoCod($inCodNumAditivo);
        $obTLicitacaoContratoAditivo->setDado('num_aditivo', $inCodNumAditivo);
    } else {
        $obTLicitacaoContratoAditivo->setDado('exercicio', $request->get('stExercicioAditivo'));
        $obTLicitacaoContratoAditivo->setDado('num_aditivo', $request->get('inNumeroAditivo'));
    }
    $obTLicitacaoContratoAditivo->setDado('responsavel_juridico', $request->get('inCodResponsavelJuridico'));
    $obTLicitacaoContratoAditivo->setDado('tipo_termo_aditivo', $request->get('inCodTipoTermoAditivo'));
    $obTLicitacaoContratoAditivo->setDado('tipo_valor', $request->get('inCodTipoAlteracaoValor'));
    $obTLicitacaoContratoAditivo->setDado('dt_vencimento', $request->get('dtFinalVigencia'));
    $obTLicitacaoContratoAditivo->setDado('dt_assinatura', $request->get('dtAssinatura'));
    $obTLicitacaoContratoAditivo->setDado('inicio_execucao', $request->get('dtInicioExcucao'));
    $obTLicitacaoContratoAditivo->setDado('fim_execucao', $request->get('dtFimExecucao'));
    $vlValorContratado = number_format(str_replace(".", "", $request->get('vlValorContratado')), 2, ".", "");
    $obTLicitacaoContratoAditivo->setDado('valor_contratado', $vlValorContratado);
    $obTLicitacaoContratoAditivo->setDado('objeto', $_REQUEST['stObjeto']);
    $obTLicitacaoContratoAditivo->setDado('justificativa', $_REQUEST['stJustificativa']);
    $obTLicitacaoContratoAditivo->setDado('fundamentacao', $_REQUEST['stFundamentacaoLegal']);
    if($request->get('stDescricao')){
        $obTLicitacaoContratoAditivo->setDado('descricao_alteracao', $_REQUEST['stDescricao']);
    }
}
