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
    * Página de Processamento - Parâmetros do Arquivo
    * Data de Criação   : 30/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 25762 $
    $Name$
    $Autor: $
    $Date: 2007-10-02 15:20:03 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeOrcamentaria.class.php");
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeGestora.class.php");
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeOrcamentariaResponsavel.class.php");
include_once(CAM_GA_NORMAS_NEGOCIO."RNorma.class.php");
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php" );
//Define o nome dos arquivos PHP

$stPrograma = "ManterConfiguracaoUnidadeOrcamentaria";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obErro = new Erro();

$stAcao = $request->get('stAcao');

$arResponsavel = Sessao::read('arResponsavel');

//Inclusão dos elementos da Unidade Gestora
if ( count($arResponsavel) > 0 ) {
    $obTUnidade = new TTCERNUnidadeOrcamentaria();
    $obTUnidadeResponsavel = new TTCERNUnidadeOrcamentariaResponsavel();

    $inIdUnidade = $_REQUEST['HdnIdOrcamentaria'];

    //Configura a Unidade Orcamentaria
    $obTUnidade->setDado( 'id', $inIdUnidade);
    $obTUnidade->setDado( 'exercicio', Sessao::getExercicio() );
    $obTUnidade->setDado( 'cgm_unidade_orcamentaria', $_REQUEST['inNumCGMUnidade'] );
    $obTUnidade->setDado( 'cod_institucional', $_REQUEST['stInstitucional'] );
    $obTUnidade->setDado( 'num_unidade', $_REQUEST['hdnNumUnidade'] );
    $obTUnidade->setDado( 'num_orgao', $_REQUEST['hdnNumOrgao'] );
    $obTUnidade->setDado( 'id_unidade_gestora', $_REQUEST['hdnIdGestora']);

    if ($_REQUEST['stSituacao'] == 1) {
        $obTUnidade->setDado( 'situacao', true );
    } else {
        $obTUnidade->setDado( 'situacao', false );
    }

    //Verifica Norma
    $arCodNorma = explode("/",$_REQUEST['stCodNorma']);

    if (count($arCodNorma) > 0) {
        $stNumNorma = ltrim($arCodNorma[0],'0');
        if ($stNumNorma == "") {
            $stNumNorma = "0";
        }
        $obRNorma = new RNorma();
        $obRNorma->setNumNorma( $stNumNorma );
        $obRNorma->setExercicio( $arCodNorma[1] );
        $obRNorma->listar($rsNorma);
        $stCodNorma = $rsNorma->getCampo('cod_norma');
    }
    $obTUnidade->setDado( 'cod_norma', $stCodNorma );

    //$obTUnidade->recuperaTodos( $rsUnidade );
    $stFiltro = " WHERE unidade.num_unidade = ".$_REQUEST['hdnNumUnidade']." AND unidade.num_orgao = ".$_REQUEST['hdnNumOrgao']." AND unidade.exercicio = '".Sessao::getExercicio()."'";
    $obTUnidade->recuperaRelacionamento($rsUnidade, $stFiltro);

    if ( $rsUnidade->getNumLinhas() > 0 ) {

        foreach ($rsUnidade->arElementos as $arUnidade) {

            if ( $arUnidade['num_orgao'] == $obTUnidade->getDado('num_orgao') ) {
                $obTUnidade->alteracao();
                $boTeste = true;
            }
        }
        if ($boTeste != true) {
            $obTUnidade->proximoCod($inIdUnidade);
            $obTUnidade->setDado( 'id', $inIdUnidade);
            $obTUnidade->inclusao();
        }
    } else {
        $obTUnidade->proximoCod($inIdUnidade);
        $obTUnidade->setDado( 'id', $inIdUnidade);
        $obTUnidade->inclusao();

//        $inIdUnidade = 0;
//        $inIdUnidade++;
//
//        $obTUnidade->setDado( 'id', $inIdUnidade);
//        $obTUnidade->inclusao();
    }

    // Configura o Responsável
    $obTUnidadeResponsavel->setDado('id_unidade', $inIdUnidade);
    $obTUnidadeResponsavel->excluir();
    //$obTUnidadeResponsavel->excluirTodos();

    foreach ($arResponsavel as $arResponsavel) {
        //$obTUnidadeResponsavel->setDado( 'id',       	     ($arResponsavel['id'] + 1));
        $obTUnidadeResponsavel->proximoCod($inIdResp);
        $obTUnidadeResponsavel->setDado( 'id',       	     $inIdResp);
        $obTUnidadeResponsavel->setDado( 'id_unidade',       $inIdUnidade);
        $obTUnidadeResponsavel->setDado( 'cgm_responsavel',  $arResponsavel['inNumCGM'] );
        $obTUnidadeResponsavel->setDado( 'cargo',     	     $arResponsavel['stCargo'] );
        $obTUnidadeResponsavel->setDado( 'cod_funcao',       $arResponsavel['stFuncao'] );
        $obTUnidadeResponsavel->setDado( 'dt_inicio', 	     $arResponsavel['stDtInicio'] );
        $obTUnidadeResponsavel->setDado( 'dt_fim', 	         $arResponsavel['stDtFim']  );
        $obTUnidadeResponsavel->inclusao();
    }

    if ( !$obErro->ocorreu() ) {
        SistemaLegado::alertaAviso($pgFilt."?".$stFiltro, " ".$cont." Dados alterados ", "alterar", "aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    }
} else {
    sistemaLegado::exibeAviso(urlencode('É necessário cadastrar pelo menos um responsável!'),"n_incluir","erro");
}
SistemaLegado::LiberaFrames();

?>
