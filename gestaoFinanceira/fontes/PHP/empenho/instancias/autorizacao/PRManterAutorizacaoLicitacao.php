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
    * Página de Processamento de Autorização
    * Data de Criação   : 24/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: gelson $
    $Date: 2007-02-23 13:15:05 -0200 (Sex, 23 Fev 2007) $

    * Casos de uso: uc-02.03.02
                    uc-02.03.15
                    uc-02.01.08
*/

/*
$Log$
Revision 1.5  2007/02/23 15:15:05  gelson
Sempre que for autorização tem que ir a reserva. Adicionado em todos arquivos o caso de uso da reserva.

Revision 1.4  2006/07/05 20:47:28  cleisson
Adicionada tag Log aos arquivos

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_EMP_NEGOCIO."REmpenhoLicitacaoAutorizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacaoLicitacao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stCaminho = CAM_GF_EMP_INSTANCIAS."autorizacao/OCRelatorioAutorizacao.php";

$obREmpenhoLicitacaoAutorizacao = new REmpenhoLicitacaoAutorizacao;
$obREmpenhoLicitacaoAutorizacao->addAutorizacao();
$obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->setExercicio( Sessao::getExercicio() );
$obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->setNumLicitacao( $_POST['inCodLicitacao'] );
$obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->setTipoLicitacao( $_POST['stTipoModalidade'] );
$obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->listarLicitacao( $rsLicitacao );

$obREmpenhoLicitacaoAutorizacao->setAutorizacao( array() );
$stNomLicitacao = trim( $rsLicitacao->getCampo( 'descricao') );

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "incluir":
        $rsItens = Sessao::read('arItens');
        $x = 1;
        while ( !$rsItens->eof() ) {

            if ( $rsItens->getCampo( 'numcgm' ) != $inNumCgmOld OR $rsItens->getCampo( 'dotacao' ) != $inDotacaoOld ) {

                $obREmpenhoLicitacaoAutorizacao->addAutorizacao();

                // Seta valores comuns para todas as autorizações
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->setExercicio( Sessao::getExercicio() );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->setDescricao( $stNomLicitacao );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->setDtAutorizacao( date('d/m/'.Sessao::getExercicio()) );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->setNumLicitacao( $_POST['inCodLicitacao'] );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->setTipoLicitacao( $_POST['stTipoModalidade'] );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obREmpenhoTipoEmpenho->setCodTipo( 0 );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obREmpenhoHistorico->setCodHistorico( 0 );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obROrcamentoReserva->setDtValidadeInicial( date('d/m/Y') );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obROrcamentoReserva->setDtValidadeFinal( "31/12/".Sessao::getExercicio() );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obROrcamentoReserva->setDtInclusao( date('d/m/'.Sessao::getExercicio()) );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );

                // Seta valores que variam de autorização para autorização
//                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obROrcamentoDespesa->setCodDespesa( $rsItens->getCampo('dotacao') );
//                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obROrcamentoClassificacaoDespesa->setMascClassificacao( $_POST['stDesdobramento_'.$x] );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obRCGM->setNumCGM( $rsItens->getCampo( 'numcgm' ) );

                // Pega numero do orgão e da unidade da despesa
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa, '', $boTransacao );
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($rsDespesa->getCampo('num_orgao'));
                $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($rsDespesa->getCampo('num_unidade'));

                //Atributos Dinâmicos
                foreach ($arChave as $key=>$value) {
                    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                    $inCodAtributo = $arChaves[0];
                    if ( is_array($value) ) {
                        $value = implode(",",$value);
                    }
                    $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                }

                $x++;

                // Seta Itens das autorizações
                do {
                    $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->addItemPreEmpenho();
                    $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->roUltimoItemPreEmpenho->setQuantidade ( $rsItens->getCampo( 'quantidade'      ) );
                    $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->roUltimoItemPreEmpenho->setNomUnidade ( 'Unidade'                               );
                    $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->roUltimoItemPreEmpenho->setValorTotal ( $rsItens->getCampo( 'vl_total'        ) );
                    $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->roUltimoItemPreEmpenho->setNomItem    ( $rsItens->getCampo( 'nom_item'        ) );
                    $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->roUltimoItemPreEmpenho->setComplemento( $rsItens->getCampo( 'complemento'     ) );
                    $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade( 1 );
                    $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza( 7 );
                    $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->roUltimoItemPreEmpenho->setSiglaUnidade( 'un' );

                    $nuVlReserva = bcadd( $nuVlReserva, $rsItens->getCampo( 'vl_total' ), 4 );
                    $obREmpenhoLicitacaoAutorizacao->roUltimaAutorizacao->obROrcamentoReserva->setVlReserva( $nuVlReserva );

                    $inDotacaoOld = $rsItens->getCampo( 'dotacao' );
                    $inNumCgmOld  = $rsItens->getCampo( 'numcgm'  );

                    $rsItens->proximo();

                } while ( $rsItens->getCampo( 'numcgm' ) == $inNumCgmOld and $rsItens->getCampo( 'dotacao' ) == $inDotacaoOld );
            }
        }

        $obErro = $obREmpenhoLicitacaoAutorizacao->incluir( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $stCodAutorizacao = null;
            foreach ( $obREmpenhoLicitacaoAutorizacao->getAutorizacao() as $obREmpenhoAutorizacaoEmpenho ) {
                $arCodAutorizacao[]    = $obREmpenhoAutorizacaoEmpenho->getCodAutorizacao();
            }
            $stCodAutorizacao = implode( ',', $arCodAutorizacao );
            $stCodAutorizacao = str_pad( $stCodAutorizacao, 0, strlen( $stCodAutorizacao )-1 );
            $pgProx = 'LSManterAutorizacao.php?'.Sessao::getId().'&stAcao=imprimir';
            Sessao::write('paginando', true);
            $arFiltro = Sessao::read('filtro');
            $arFiltro['inCodEntidade']           = array( $_POST['inCodEntidade'] );
            $arFiltro['inCodAutorizacaoInicial'] = $arCodAutorizacao[0];
            $arFiltro['inCodAutorizacaoFinal']   = $arCodAutorizacao[count($arCodAutorizacao)-1];
            Sessao::write('filtro', $arFiltro);
            SistemaLegado::alertaAviso($pgProx, str_pad( $stCodAutorizacao, 0, strlen($stCodAutorizacao)-1 )."/".Sessao::getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
        } else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;
}
?>
