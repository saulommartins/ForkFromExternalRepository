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
    * Página de Processamento - Dedutoras
    * Data de Criação   : 10/10/2007

    * @author Desenvolvedor: Anderson cAko Konze

    * Casos de uso: uc-02.01.06

    $Id: PRDedutora.php 60942 2014-11-25 18:42:22Z carlos.silva $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"          );

//Define o nome dos arquivos PHP
$stPrograma = "Dedutora";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obROrcamentoReceita = new ROrcamentoReceita;

$boFlagTransacao = false;
$obErro = $obROrcamentoReceita->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

function verificaValorConta($stClassificacao)
{
    global $obROrcamentoReceita;
    $arClassReceita = preg_split( "/[^a-zA-Z0-9]/", $stClassificacao );
    $inCount        = count( $arClassReceita );

    //busca a posicao do ultimo valor na string de classificacao
    for ($inPosicao = $inCount; $inPosicao >= 0; $inPosicao--) {
        if ($arClassReceita[$inPosicao] != 0) {
            break;
        }
    }

    for ($i = 0; $i <= $inPosicao; $i++) {
        $stClassFilha .= $arClassReceita[$i].".";
    }
    $stClassFilha = substr( $stClassFilha, 0, strlen( $stClassFilha ) - 1 );
    $stFiltro .= " AND classificacao like publico.fn_mascarareduzida('".$stClassFilha."') || '%' ";
    $obROrcamentoReceita->verificaValorConta( $inSumConta, $stFiltro );

    return $inSumConta;
}

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php"        );
$obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
$obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
$obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
$obTOrcamentoConfiguracao->consultar();
if($obTOrcamentoConfiguracao->getDado("valor") == 'true') // Utilização da Destinação de Recursos || 2008 em diante
    $boDestinacao = true;

//Sessao::setTrataExcecao( true );

switch ($stAcao) {
    case "incluir":

        $obErro = new Erro;
        $inSumConta = verificaValorConta( $_REQUEST['inCodReceita'] );
        if ($inSumConta > 0.00) {
            $obErro->setDescricao('Já houveram movimentações na classificação informada ('.$_REQUEST['inCodReceita'].')');
        } else {
            //busca o codigo da conta da Classificação de Receita informada
            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDedutora ( true );
            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $_REQUEST['inCodReceita'] );
            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->consultar( $rsRubrica );

            $inCodConta = $rsRubrica->getCampo('cod_conta');

            if ($_POST['nuValorOriginal']) {
                $obROrcamentoReceita->setValorOriginal                         ( $_POST['nuValorOriginal']);
            } else {
                $obROrcamentoReceita->setValorOriginal                         ( 0.00);
            }
            $obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade      ( $_POST['inCodEntidade']  );
            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setCodConta( $inCodConta			  );

            if ($boDestinacao) {
                include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
                $obTOrcamentoRecurso = new TOrcamentoRecurso;
                $obTOrcamentoRecurso->setDado("exercicio", Sessao::getExercicio() );
                $obTOrcamentoRecurso->proximoCod( $inCodRecurso );
                $obTOrcamentoRecurso->setDado("cod_recurso", $inCodRecurso );
                $obErro = $obTOrcamentoRecurso->inclusao( $boTransacao );
                if (!$obErro->ocorreu()) {
                    $arDestinacaoRecurso = explode('.',$_REQUEST['stDestinacaoRecurso']);

                    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php" );
                    $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                    $obTOrcamentoRecursoDestinacao->setDado("exercicio",        Sessao::getExercicio()      );
                    $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",      $inCodRecurso           );
                    $obTOrcamentoRecursoDestinacao->setDado("cod_uso",          $arDestinacaoRecurso[0] );
                    $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao",   $arDestinacaoRecurso[1] );
                    $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2] );
                    $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3] );
                    $obErro = $obTOrcamentoRecursoDestinacao->inclusao( $boTransacao );

                    $obROrcamentoReceita->obROrcamentoRecurso->setCodRecurso ( $inCodRecurso );
                }

            } else {

                if ($_POST['inCodRecurso']) {
                    $obROrcamentoReceita->obROrcamentoRecurso->setCodRecurso( $_POST['inCodRecurso'] );
                   }

                include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php" );
                $obTOrcamentoReceita = new TOrcamentoReceita;
                $stFiltro .= " WHERE cod_conta    = ".$inCodConta;
                $stFiltro .= "   AND exercicio    = '".Sessao::getExercicio()."' ";
                $stFiltro .= "   AND cod_entidade = ".$_POST['inCodEntidade'];
                $obTOrcamentoReceita->recuperaTodos($rsReceita,$stFiltro);

                if ($rsReceita->getNumLinhas() >= 1 ) {
                    $obErro->setDescricao("A Classificação da Dedutora informada já foi cadastrada no exercício de (".Sessao::getExercicio().")");
                }
            }

            if (!$obErro->ocorreu()) {
                $obErro = $obROrcamentoReceita->salvar();
                $inCodReceita = $obROrcamentoReceita->getCodReceita();

                if ( !$obErro->ocorreu() ) {
                    $obErro = lancarMetasReceita();
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm."?inCodEntidade=".$_POST['inCodEntidade'], $inCodReceita."/".$obROrcamentoReceita->getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $obErro = new Erro;
        include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceita.class.php");
 
            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDedutora ( true );
            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $_POST['inCodEstrutural'] );
            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->consultar( $rsRubrica );
            $inCodConta = $rsRubrica->getCampo( 'cod_conta' );

            $obROrcamentoReceita->setCodReceita                                ( $_POST['inCodFixacaoReceita'] );
            $obROrcamentoReceita->setValorOriginal                             ( $_POST['nuValorOriginal']     );
            $obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade      ( $_POST['inCodEntidade']       );
            if ($boDestinacao) {
                $arDestinacaoRecurso = explode('.',$_REQUEST['stDestinacaoRecurso']);

                include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php" );
                $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                $obTOrcamentoRecursoDestinacao->setDado("exercicio",        Sessao::getExercicio()        );
                $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",      $_REQUEST['inCodRecurso'] );
                $obTOrcamentoRecursoDestinacao->setDado("cod_uso",          $arDestinacaoRecurso[0]   );
                $obTOrcamentoRecursoDestinacao->setDado("cod_destinacao",   $arDestinacaoRecurso[1]   );
                $obTOrcamentoRecursoDestinacao->setDado("cod_especificacao",$arDestinacaoRecurso[2]   );
                $obTOrcamentoRecursoDestinacao->setDado("cod_detalhamento", $arDestinacaoRecurso[3]   );
                $obTOrcamentoRecursoDestinacao->alteracao( $boTransacao );

                $obROrcamentoReceita->obROrcamentoRecurso->setCodRecurso ( $_REQUEST['inCodRecurso'] );

            } else {
                $obROrcamentoReceita->obROrcamentoRecurso->setCodRecurso      ( $_POST['inCodRecurso']        );
            }

            $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setCodConta( $inCodConta                   );
            $obErro = $obROrcamentoReceita->salvar($boTransacao);

            $stFiltro = "";
            $arFiltro = Sessao::read('filtro');
            
            if(is_array($arFiltro['inCodEntidade'])) {
                $arFiltro['inCodEntidade'] = implode(",", $arFiltro['inCodEntidade']);
            } else {
                $arFiltro['inCodEntidade'] = array();
            }
            
            foreach ($arFiltro as $stCampo => $stValor) {
                if(is_string($stValor))
                    $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
            }
            $stFiltro .= "pg=".Sessao::read('pg')."&";
            $stFiltro .= "pos=".Sessao::read('pos')."&";
            $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if (!$obErro->ocorreu() ) {
            $obErro = lancarMetasReceita();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList, $_POST['inCodFixacaoReceita']."/".$obROrcamentoReceita->getExercicio(), "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":
        $obErro = new Erro;

        include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceita.class.php");
        $obRContablidadeLancamentoReceita   = new RContabilidadeLancamentoReceita;
        $obRContablidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio ( Sessao::getExercicio() );
        $obRContablidadeLancamentoReceita->consultarExistenciaReceita();

        if ( $obRContablidadeLancamentoReceita->getCountReceitaExercicio() == 0) {
            include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeDesdobramentoReceita.class.php" );
            $obTContabilidadeDesdobramentoReceita   = new TContabilidadeDesdobramentoReceita;
            $obTContabilidadeDesdobramentoReceita->setDado( "exercicio", Sessao::getExercicio() );
            $obTContabilidadeDesdobramentoReceita->setDado( "cod_receita", $_GET['inCodReceita'] );

            $obErro = $obTContabilidadeDesdobramentoReceita->verificaReceitaSecundaria( $boSecundaria );

            if (!$obErro->ocorreu() && !$boSecundaria ) {
                $obROrcamentoReceita->setCodReceita( $_GET['inCodReceita'] );
                $obROrcamentoReceita->setExercicio ( Sessao::getExercicio() );
                $obErro = $obROrcamentoReceita->excluir();
                if ($boDestinacao && !$obErro->ocorreu() && $_REQUEST['inCodRecurso'] ) {
                    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php" );
                    $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                    $obTOrcamentoRecursoDestinacao->setDado("exercicio",       Sessao::getExercicio()       );
                    $obTOrcamentoRecursoDestinacao->setDado("cod_recurso",     $_REQUEST['inCodRecurso']);
                    $obTOrcamentoRecursoDestinacao->exclusao();

                    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
                    $obTOrcamentoRecurso = new TOrcamentoRecurso;
                    $obTOrcamentoRecurso->setDado("exercicio", Sessao::getExercicio() );
                    $obTOrcamentoRecurso->setDado("cod_recurso", $_REQUEST['inCodRecurso'] );
                    $obTOrcamentoRecurso->exclusao();

                }

            } else {
                $obErro->setDescricao("Receita Secundária - não pode ser excluída!");
            }
        } else {
            $obErro->setDescricao("Já existe movimentação nas contas.");
        }
        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
        if (isset($arFiltro['inCodEntidade'])) {
            $arFiltro['inCodEntidade'] = implode(",", $arFiltro['inCodEntidade']);
        }
        foreach ($arFiltro as $stCampo => $stValor) {            
            $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];
        
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir", $_GET['inCodReceita']."/".$obROrcamentoReceita->getExercicio() ,"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir", urlencode($obErro->getDescricao()) ,"n_excluir","erro", Sessao::getId(), "../");
        }
    break;

}

/*
 *
 * LANÇAR METAS !!
 *
 */

function lancarMetasReceita()
{
    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoReceita.class.php"      );
    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoOrcamentaria.class.php" );
    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"              );
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php"              );

    $obRPrevisaoReceita                 = new ROrcamentoPrevisaoReceita;
    $obROrcamentoPrevisaoOrcamentaria   = new ROrcamentoPrevisaoOrcamentaria;
    $obRConfiguracaoOrcamento           = new ROrcamentoConfiguracao;
    global $obROrcamentoReceita;
    $obTOrcamentoReceita                = new TOrcamentoReceita;

    $obErro = new Erro;

    $inNumColunas    = $_POST['inNumCampos'];
    $vlValorOriginal = $_POST['nuValorOriginal'];
    $inCodEstrutural = $_POST['inCodReceita'];
    $inCodReceita    = $_POST['inCodFixacaoReceita'];
    $inCodEntidade   = $_POST['inCodEntidade'];

    $obRPrevisaoReceita->setQtdColunas ( $inNumColunas );
    $obRPrevisaoReceita->setQtdLinhas  ( 1 );
    $obRPrevisaoReceita->setExercicio  ( Sessao::getExercicio() );

    if (!$inCodReceita) {
        $obTOrcamentoReceita->setDado( 'cod_estrutural' , $inCodEstrutural );
        $obTOrcamentoReceita->setDado( 'exercicio'      , $obRPrevisaoReceita->getExercicio() );
        $obErro = $obTOrcamentoReceita->recuperaCodReceita( $rsCodReceita, $boTransacao );
        if ( !$rsCodReceita->eof() ) {
            $inCodReceita = $rsCodReceita->getCampo( 'cod_receita' );
            $obROrcamentoReceita->setCodReceita( $inCodReceita );
        }
    }

    $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->setExercicio( $obRPrevisaoReceita->getExercicio() );
    $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->consultar( $rsPrevisaoOrcamentaria, $obTransacao );

    if ( $obRPrevisaoReceita->getExercicio() != $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->getExercicio() ) {
        $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->setExercicio( $obRPrevisaoReceita->getExercicio() );
        $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->salvar($boTransacao);
    }

    for ($inContColunas = 1; $inContColunas <= $inNumColunas; $inContColunas++) {
        $inValor = "vlValor_".$inContColunas;

        $inValor = str_replace( ".", "" , $_POST[$inValor] );
        $inValor = str_replace( ",", ".", $inValor );
        $arValor[$inContColunas] = $inValor;
        $vlTotal += $inValor;
    }

    $vlTotal = $_POST['TotalValor'] ;
    $vlTotal = str_replace( ".", "" , $vlTotal );
    $vlTotal = str_replace( ",", ".", $vlTotal );

    $vlValorOriginal = str_replace( ".", "" , $vlValorOriginal );
    $vlValorOriginal = str_replace( ",", ".", $vlValorOriginal );

    $boSalvar = 0;

    if ($vlTotal <> 0.00) {
        if ( number_format($vlTotal,2,'.','') > number_format($vlValorOriginal,2,'.','')) {
            $obErro->setDescricao( "Valor Total das Metas de Arrecadação ultrapassou o Valor de Previsão da Receita." );
            $boSalvar++;
        } elseif ( number_format($vlTotal,2,'.','') < number_format($vlValorOriginal,2,'.','') ) {
            $obErro->setDescricao( "Valor Total das Metas de Arrecadação é inferior ao Valor de Previsão da Receita." );
            $boSalvar++;
        }
    } else {
        $obRPrevisaoReceita->setCodigoReceita   ( $inCodReceita );
        $obErro = $obRPrevisaoReceita->limparDados($boTransacao);
        $boSalvar++;
    }

    if ($boSalvar == 0) {
        $obRPrevisaoReceita->setCodigoReceita   ( $inCodReceita );
        $obErro = $obRPrevisaoReceita->limparDados($boTransacao);

        for ($inContColunas = 1; $inContColunas <= $inNumColunas; $inContColunas++) {
            $obRPrevisaoReceita->setCodigoReceita   ( $inCodReceita );
            $obRPrevisaoReceita->setPeriodo         ( $inContColunas );
            $inValor = "vlValor_".$inContColunas;

            if ($arValor[$inContColunas] == "") {
                $obRPrevisaoReceita->setValorPeriodo ( 0 );
            } else {
                $valor = str_replace('.','',$$inValor);
                $valor = str_replace(',','.',$valor);
                $obRPrevisaoReceita->setValorPeriodo ( $arValor[$inContColunas] );
            }
            $obErro = $obRPrevisaoReceita->salvar($boTransacao);
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }

    return $obErro;
}
    $obROrcamentoReceita->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRPrevisaoReceita );
?>
