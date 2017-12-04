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
    * Data de Criação: 15/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Revision: 28101 $
    $Name$
    $Author: luiz $
    $Date: 2008-02-20 09:08:34 -0300 (Qua, 20 Fev 2008) $

    * Casos de uso : uc-03.05.29
*/

/*
$Log:
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( TLIC."TLicitacaoConvenio.class.php" );
include_once( TLIC."TLicitacaoConvenioAditivos.class.php" );
include_once( TLIC."TLicitacaoConvenioAditivosAnulacao.class.php" );
include_once( TLIC."TLicitacaoPublicacaoConvenioAditivo.class.php" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stPrograma = "ManterAditivoConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
Sessao::setTrataExcecao( true );
$stMensagem = "";

$arValores = Sessao::read('arValores');

// validação dos dados caso ação seje diferente de 'anular'
if ($stAcao != "anular") {
    $obTLicitacaoConvenio = new TLicitacaoConvenio();
    $obTLicitacaoConvenio->setDado('num_convenio', $_REQUEST['inNumConvenio']);
    $obTLicitacaoConvenio->setDado('exercicio', $_REQUEST['stExercicio']);
    $obTLicitacaoConvenio->recuperaPorChave( $rsLicitacaoConvenio );

    if ( implode(array_reverse(explode('/',$_REQUEST['dtAssinatura']))) < implode(array_reverse(explode('/',$rsLicitacaoConvenio->getCampo("dt_assinatura"))))) {
        $stMensagem .= "<br />A data de assinatura do aditivo não pode ser anterior que a data de assinatura do convênio.";
    }

    if ( implode(array_reverse(explode('/',$_REQUEST['dtInicioExcucao']))) < implode(array_reverse(explode('/',$_REQUEST['dtAssinatura'])))) {
        $stMensagem .= "<br />A data de início de execução não pode ser anterior que a data de assinatura do aditivo.";
    }

    if ( implode(array_reverse(explode('/',$_REQUEST['dtFinalVigencia']))) < implode(array_reverse(explode('/',$_REQUEST['dtInicioExcucao'])))) {
        $stMensagem .= "<br />A data de final de vigência não pode ser anterior que a data de início de execução.";
    }

} else {
    $obTLicitacaoConvenioAditivo = new TLicitacaoConvenioAditivos();
    $obTLicitacaoConvenioAditivo->setDado("exercicio_convenio", $_REQUEST["stExercicio"]);
    $obTLicitacaoConvenioAditivo->setDado("num_convenio", $_REQUEST["inNumConvenio"]);
    $obTLicitacaoConvenioAditivo->setDado("exercicio", $_REQUEST["stExercicioAditivo"]);
    $obTLicitacaoConvenioAditivo->setDado("num_aditivo", $_REQUEST["inNumeroAditivo"]);
    $obTLicitacaoConvenioAditivo->recuperaPorChave( $rsLicitacaoConvenioAditivos );

    if ( implode(array_reverse(explode('/',$_REQUEST['dtAnulacao']))) < implode(array_reverse(explode('/',$rsLicitacaoConvenioAditivos->getCampo("dt_assinatura"))))) {
        $stMensagem .= "<br />A data de anulação não pode ser anterior que a data de assinatura do aditivo.";
    }
}

if ($stMensagem != "") {
    SistemaLegado::exibeAviso(urlencode($stMensagem), "n_incluir", "erro" );

} else {

    switch ($stAcao) {
    case "incluir":
        $obTLicitacaoContratoAditivo = new TLicitacaoConvenioAditivos();
        setaDados($obTLicitacaoContratoAditivo, $stAcao);
        $obTLicitacaoContratoAditivo->inclusao();
        $obTLicitacaoContratoAditivo->debug();

        $obTLicitacaoPublicacaoConvenioAditivo = new TLicitacaoPublicacaoConvenioAditivo();

        foreach ($arValores as $arTemp) {
            $obTLicitacaoPublicacaoConvenioAditivo->setDado('num_convenio'   	 , $obTLicitacaoContratoAditivo->getDado('num_convenio'));
            $obTLicitacaoPublicacaoConvenioAditivo->setDado('num_aditivo'    	 , $obTLicitacaoContratoAditivo->getDado('num_aditivo'));
            $obTLicitacaoPublicacaoConvenioAditivo->setDado('exercicio_convenio' , $obTLicitacaoContratoAditivo->getDado('exercicio_convenio'));
            $obTLicitacaoPublicacaoConvenioAditivo->setDado('numcgm'         	 , $arTemp['inVeiculo'] );
            $obTLicitacaoPublicacaoConvenioAditivo->setDado('dt_publicacao'  	 , $arTemp['dtDataPublicacao'] );
            $obTLicitacaoPublicacaoConvenioAditivo->setDado('num_publicacao' 	 , $arTemp['inNumPublicacao'] );
            $obTLicitacaoPublicacaoConvenioAditivo->setDado('exercicio'      	 , Sessao::getExercicio() );
            $obTLicitacaoPublicacaoConvenioAditivo->setDado('observacao'     	 , $arTemp['_stObservacao'] );
            $obTLicitacaoPublicacaoConvenioAditivo->inclusao();
        }

        SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Convênio: ".$_REQUEST['inNumConvenio']."/".$_REQUEST['stExercicio'],"incluir", "aviso", Sessao::getId(),"");
        break;

    case "alterar":
        $obTLicitacaoContratoAditivo = new TLicitacaoConvenioAditivos();
        setaDados($obTLicitacaoContratoAditivo, $stAcao);
        $obTLicitacaoContratoAditivo->alteracao();

        $obTLicitacaoPublicacaoConvenioAditivo = new TLicitacaoPublicacaoConvenioAditivo();
        $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'num_aditivo'  , $_REQUEST['inNumeroAditivo']);
        $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'num_convenio' , $_REQUEST['inNumConvenio']);
        $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'exercicio'    , $_REQUEST['stExercicioAditivo']);
        $obTLicitacaoPublicacaoConvenioAditivo->exclusao();

        foreach ($arValores as $arTemp) {
            $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'num_convenio'   	  , $obTLicitacaoContratoAditivo->getDado('num_convenio'));
            $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'num_aditivo'    	  , $obTLicitacaoContratoAditivo->getDado('num_aditivo'));
            $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'exercicio_convenio' , $obTLicitacaoContratoAditivo->getDado('exercicio_convenio'));
            $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'numcgm'         	  , $arTemp['inVeiculo'] );
            $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'dt_publicacao'  	  , $arTemp['dtDataPublicacao'] );
            $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'num_publicacao' 	  , $arTemp['inNumPublicacao'] );
            $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'exercicio'      	  , Sessao::getExercicio() );
            $obTLicitacaoPublicacaoConvenioAditivo->setDado( 'observacao'     	  , $arTemp['_stObservacao'] );
            $obTLicitacaoPublicacaoConvenioAditivo->inclusao();
        }

        SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Convênio: ".$_REQUEST['inNumConvenio']."/".$_REQUEST['stExercicioAditivo'],"alterar", "aviso", Sessao::getId(),"");
    break;

    case "anular":
        $obTLicitacaoContratoAditivo = new TLicitacaoConvenioAditivosAnulacao();
        $obTLicitacaoContratoAditivo->setDado("exercicio_convenio", $_REQUEST["stExercicio"]);
        $obTLicitacaoContratoAditivo->setDado("num_convenio", $_REQUEST["inNumConvenio"]);
        $obTLicitacaoContratoAditivo->setDado("exercicio", $_REQUEST["stExercicioAditivo"]);
        $obTLicitacaoContratoAditivo->setDado("num_aditivo", $_REQUEST["inNumeroAditivo"]);
        $obTLicitacaoContratoAditivo->setDado("dt_anulacao", $_REQUEST["dtAnulacao"]);
        $obTLicitacaoContratoAditivo->setDado("motivo", $_REQUEST["stMotivoAnulacao"]);
        $obTLicitacaoContratoAditivo->inclusao();
        SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","Convênio: ".$_REQUEST['inNumConvenio']."/".$_REQUEST['stExercicioAditivo'],"anular", "aviso", Sessao::getId(),"");
        break;
    }
}

Sessao::encerraExcecao();

// método para setar os dados do objeto passado por parâmetro.
function setaDados(&$obTLicitacaoContratoAditivo, $stAcao)
{
    include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"  );
    $obTNorma = new TNorma;
    $obTNorma->setDado('cod_norma' , $_REQUEST['inCodLei']);
    $obTNorma->recuperaPorChave($rsNormaAlteracao);
    $obTLicitacaoContratoAditivo->setDado('exercicio_convenio', $_REQUEST['stExercicio']);
    $obTLicitacaoContratoAditivo->setDado('num_convenio', $_REQUEST['inNumConvenio']);

    if ($stAcao == "incluir") {
        $obTLicitacaoContratoAditivo->setDado('exercicio', Sessao::getExercicio());
        $obTLicitacaoContratoAditivo->proximoCod($inCodNumAditivo);
        $obTLicitacaoContratoAditivo->setDado('num_aditivo', $inCodNumAditivo);
    } else {
        $obTLicitacaoContratoAditivo->setDado('exercicio', $_REQUEST['stExercicioAditivo']);
        $obTLicitacaoContratoAditivo->setDado('num_aditivo', $_REQUEST['inNumeroAditivo']);
    }

    $obTLicitacaoContratoAditivo->setDado('responsavel_juridico', $_REQUEST['inCodRespJuridico']);
    $obTLicitacaoContratoAditivo->setDado('dt_vigencia', $_REQUEST['dtFinalVigencia']);
    $obTLicitacaoContratoAditivo->setDado('dt_assinatura', $_REQUEST['dtAssinatura']);
    $obTLicitacaoContratoAditivo->setDado('inicio_execucao', $_REQUEST['dtInicioExcucao']);
    $vlValorConvenio = number_format(str_replace(".", "", $_REQUEST['vlValorConvenio']), 2, ".", "");
    $obTLicitacaoContratoAditivo->setDado('valor_convenio', $vlValorConvenio);
    $obTLicitacaoContratoAditivo->setDado('objeto', $_REQUEST['stObjeto']);
    $obTLicitacaoContratoAditivo->setDado('observacao', $_REQUEST['stObservacao']);
    $obTLicitacaoContratoAditivo->setDado('fundamentacao', $rsNormaAlteracao->getCampo('nom_norma'));
    $obTLicitacaoContratoAditivo->setDado( "cod_norma_autorizativa", $_REQUEST [ "inCodLei" ]);

}
