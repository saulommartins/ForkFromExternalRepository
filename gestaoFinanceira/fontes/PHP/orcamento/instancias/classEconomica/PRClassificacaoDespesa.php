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
    * Página de Processamento de Comissao de Avaliacao
    * Data de Criação   : 16/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor: $
    $Date: 2008-02-22 10:04:27 -0300 (Sex, 22 Fev 2008) $

    * Casos de uso: uc-02.01.04
*/

/*
$Log$
Revision 1.10  2006/07/25 13:13:21  andre.almeida
Bug #6533#

Revision 1.9  2006/07/10 18:15:52  andre.almeida
Correções na paginação.

Revision 1.8  2006/07/05 20:42:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ClassificacaoDespesa";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obROrcamentoClassificacaoDespesa  = new ROrcamentoClassificacaoDespesa;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "incluir":
        $arClassificacao    = explode( "." , $_POST['inCodClassificacao'] );
        $arClassificacaoBD  = explode( "." , $_POST['inCodClassificacao'] );
        $inCount            = count( $arClassificacao );

        //busca o codigo da Classificacao que sera inserido e coloca zero na sua posição para verificar se a Classificacao pai existe
        for ($inPosicao = $inCount; $inPosicao >= 0; $inPosicao--) {
            if ($arClassificacao[$inPosicao] != 0) {
                $inTamPos = strlen( $arClassificacao[$inPosicao] );
                $arClassificacao[$inPosicao] = str_pad( '0' , $inTamPos, 0 , STR_PAD_LEFT );
                break;
            }
        }

        //remonta a Classificacao de Despesa, colocanco '0' na ultima casa com valor
        for ($inPosicaoNew = 0; $inPosicaoNew < $inCount; $inPosicaoNew++) {
                $stVerificaClassDespesa .= $arClassificacao[$inPosicaoNew].".";
        }
        $stVerificaClassDespesa = substr( $stVerificaClassDespesa, 0, strlen( $stVerificaClassDespesa ) - 1 );

        //verifica se existe uma Classificacao pai para a Classificacao de Despesa informada
        $checkClass = 'false';
        $obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
        $obROrcamentoClassificacaoDespesa->listar      ( $rsClass );
        if ($inPosicao > 0) {
            while ( !$rsClass->eof() ) {
                if ( $rsClass->getCampo('mascara_classificacao') == $stVerificaClassDespesa ) {
                    $checkClass = 'true';
                    break;
                }
                $rsClass->Proximo();
            }
            if ($checkClass == 'false') {
                $checkClass = 'SemPai';
            }
        } else {
            $checkClass = 'true';
        }

        //verifica se a Classificação informada já não foi inserida
        if ($checkClass == 'true') {
            $rsClass->setPrimeiroElemento();
            while ( !$rsClass->eof() ) {
                if ( $rsClass->getCampo('mascara_classificacao') == $_POST['inCodClassificacao'] ) {
                    $checkClass = 'false';
                    break;
                }
                $rsClass->Proximo();
            }
        }

        if ($checkClass == 'true') {
            include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoContaDespesa.class.php"         );
            $obTOrcamentoContaDespesa         = new TOrcamentoContaDespesa;
            $boFlagTransacao = false;
            $obErro = $obROrcamentoClassificacaoDespesa->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

            if ( !$obErro->ocorreu() ) {

                //Se a conta ainda nao foi inserida insere ela na tabela ORCAMENTO.CONTA_DESPESA
                if ( !$obROrcamentoClassificacaoDespesa->getCodConta() ) {
                    $obErro = $obTOrcamentoContaDespesa->proximoCod( $inCodConta , $boTransacao );
                    $obROrcamentoClassificacaoDespesa->setCodConta( $inCodConta );

                    $obTOrcamentoContaDespesa->setDado( "cod_conta"       , $obROrcamentoClassificacaoDespesa->getCodConta()  );
                    $obTOrcamentoContaDespesa->setDado( "exercicio"       , $obROrcamentoClassificacaoDespesa->getExercicio() );
                    $obTOrcamentoContaDespesa->setDado( "descricao"       , $_POST['stDescricao'] );
                    $obTOrcamentoContaDespesa->setDado( "cod_estrutural"  , $_POST['inCodClassificacao']);
                    $obErro = $obTOrcamentoContaDespesa->inclusao( $boTransacao );
                }

                foreach ($arClassificacaoBD as $key => $valor) {
                    $inCodPosicao = $key + 1;
                    $obROrcamentoClassificacaoDespesa->setCodClassificacao( $valor                );
                    $obROrcamentoClassificacaoDespesa->setCodPosicao      ( $inCodPosicao         );
                    $obROrcamentoClassificacaoDespesa->setDescricao       ( $_POST['stDescricao'] );
                    $obROrcamentoClassificacaoDespesa->setCodEstrutural   ( implode(".",$arClassificacaoBD) );
                    $obErro = $obROrcamentoClassificacaoDespesa->incluir( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
                $obROrcamentoClassificacaoDespesa->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoContaDespesa );
            }
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgForm, $_POST['inCodClassificacao']." - ".$_POST['stDescricao'], "incluir", "aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } elseif ($checkClass == 'false') {
            SistemaLegado::exibeAviso('Classificação de Despesa já cadastrada. - '.$_POST['inCodClassificacao'], "n_incluir" , "erro" );
        } elseif ($checkClass == 'SemPai') {
            SistemaLegado::exibeAviso("Classificação de Despesa - ".$_POST['inCodClassificacao']." - não possui uma Classificação Pai.", "n_incluir", "erro");
        }
     //     else if ($checkClass == 'SemConta') {
        //     SistemaLegado::exibeAviso("Rubrica não está cadastrada na contabilidade ou não é Analítica! (".$_POST['inCodClassificacao'].")", "n_incluir", "erro");
        // }
    break;
    case "alterar":
        $obROrcamentoClassificacaoDespesa->setCodConta ( $_POST['inCodConta']   );
        $obROrcamentoClassificacaoDespesa->setDescricao( $_POST['stDescricao']  );
        $obErro = $obROrcamentoClassificacaoDespesa->alterar();

        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');

        foreach ($arFiltro['filtro'] as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=". $stValor ."&";
        }

        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".$stFiltro, $_POST['inCodClassificacao']." - ".$_POST['stDescricao'], "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir":
        $obROrcamentoClassificacaoDespesa->setCodConta( $_GET['inCodConta'] );
        $obROrcamentoClassificacaoDespesa->setMascClassificacao( $_GET['stMascClassDespesaReduzida'] );
        $obErro = $obROrcamentoClassificacaoDespesa->excluir();

        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');

        foreach ($arFiltro['filtro'] as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=".htmlentities(urlencode( $stValor ), ENT_NOQUOTES, 'UTF-8')."&";
        }

        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro, $_GET['stMascClassDespesa']." - ".$_GET['stDescricao'] ,"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro, urlencode($obErro->getDescricao()) ,"n_excluir","erro", Sessao::getId(), "../");
            //SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;

}
?>
