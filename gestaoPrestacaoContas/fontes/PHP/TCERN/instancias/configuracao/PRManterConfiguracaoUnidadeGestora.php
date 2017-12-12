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
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeGestora.class.php");
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeGestoraResponsavel.class.php");
include_once(CAM_GA_NORMAS_NEGOCIO."RNorma.class.php");
//Define o nome dos arquivos PHP

$stPrograma = "ManterConfiguracaoUnidadeGestora";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obErro = new Erro();

$stAcao = $request->get('stAcao');
$intEntidade = $_REQUEST['hdnEntidade'];

$arResponsavel = Sessao::read('arResponsavel');

//Inclusão dos elementos da Unidade Gestora
if ( count($arResponsavel) > 0 ) {

    $obTGestora = new TTCERNUnidadeGestora();
    $obTGestoraResponsavel = new TTCERNUnidadeGestoraResponsavel();

    $inIdUnidade = $_REQUEST['hdnIdUnidade'];

    //Configura a Unidade Gestora
    $obTGestora->setDado( 'id', $inIdUnidade);
    $obTGestora->setDado( 'exercicio', Sessao::getExercicio() );
    $obTGestora->setDado( 'cgm_unidade', $_REQUEST['hdnCgmUnidade'] );
    $obTGestora->setDado( 'cod_institucional', $_REQUEST['stInstitucional'] );
    $obTGestora->setDado( 'personalidade', $_REQUEST['stPersonalidade'] );
    $obTGestora->setDado( 'administracao', $_REQUEST['stAdministracao'] );
    $obTGestora->setDado( 'natureza', $_REQUEST['stNatureza'] );
    if ($_REQUEST['stSituacao'] == 1) {
    $obTGestora->setDado( 'situacao', true );
    } else {
    $obTGestora->setDado( 'situacao', false );
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
    $obTGestora->setDado( 'cod_norma', $stCodNorma );

    $obTGestora->recuperaTodos( $rsGestora );
    if ( $rsGestora->getNumLinhas() > 0 ) {
    $obTGestora->alteracao();
    } else {
    $obTGestora->proximoCod($inIdUnidade);
    $obTGestora->setDado( 'id', $inIdUnidade);
    $obTGestora->inclusao();
    }

    // Configura o Responsável
    $obTGestoraResponsavel->excluirTodos();

    foreach ($arResponsavel as $arResponsavel) {
    $obTGestoraResponsavel->setDado( 'id',       	     ($arResponsavel['id'] + 1));
    $obTGestoraResponsavel->setDado( 'id_unidade',       $inIdUnidade);
    $obTGestoraResponsavel->setDado( 'cgm_responsavel' , $arResponsavel['inNumCGM']);
    $obTGestoraResponsavel->setDado( 'cargo',     	     $arResponsavel['stCargo'] );
    $obTGestoraResponsavel->setDado( 'cod_funcao',       $arResponsavel['stFuncao'] );
    $obTGestoraResponsavel->setDado( 'dt_inicio', 	     $arResponsavel['stDtInicio'] );
    $obTGestoraResponsavel->setDado( 'dt_fim'   , 	     $arResponsavel['stDtFim']  );
    $obTGestoraResponsavel->inclusao();
    }

    if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Dados alterados ", "alterar", "aviso", Sessao::getId(), "../");
    } else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    }
} else {
    sistemaLegado::exibeAviso(urlencode('É necessário cadastrar pelo menos um responsável!'),"n_incluir","erro");
}
SistemaLegado::LiberaFrames();

?>
