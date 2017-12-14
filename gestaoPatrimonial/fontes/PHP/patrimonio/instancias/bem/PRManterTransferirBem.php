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
    * Data de Criação: 21/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: PRManterTransferirBem.php 61675 2015-02-24 17:00:21Z franver $

    * Casos de uso: uc-03.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioHistoricoBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemResponsavel.class.php";

$stPrograma = "ManterTransferirBem";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get("stAcao");

$obTPatrimonioHistoricoBem = new TPatrimonioHistoricoBem();
$obTPatrimonioBem = new TPatrimonioBem();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioHistoricoBem );;

switch ($stAcao) {
    case 'transferir' :

        $stMensagem = "";
        
        //verifica se foi incluido pelo menos 1 item
        $boTransferir = false;
        foreach ($_POST as $stKey=>$stValue) {
            if ( strstr( $stKey, 'boTransferir_' ) ) {
                $boTransferir = true;
                break;
            }
        }
        if (!$boTransferir) {
            $stMensagem = 'É necessário informar pelo menos 1 item.';
        }

        //transfere todos os itens
        if (empty($stMensagem)) {
            $arLocalizacao = explode('/',$_REQUEST['stCodLocalizacao']);

            $obTPatrimonioHistoricoBem->setDado( 'cod_local', $_REQUEST['inLocalDestino'] );
            $obTPatrimonioHistoricoBem->setDado( 'cod_orgao', $_REQUEST['hdnUltimoOrgaoSelecionado'] );
            //$obTPatrimonioHistoricoBem->setDado( 'ano_exercicio', Sessao::getExercicio() );

            foreach ($_POST as $stKey => $stValue) {
                if ( strstr( $stKey, 'boTransferir_' ) ) {
                    $arBem = explode('_', $stKey);
                    $obTPatrimonioHistoricoBem->setDado( 'cod_bem', $arBem[1] );
                    $obTPatrimonioHistoricoBem->recuperaUltimaLocalizacao( $rsLocalizacao );

                    $boMesmoOrgao = ($rsLocalizacao->getCampo('cod_orgao') == $_REQUEST['hdnUltimoOrgaoSelecionado']) ? true : false;

                    $boMesmoLocal = ($_REQUEST['inLocalOrigem'] == $_REQUEST['inLocalDestino']) ? true : false;

                    $boMesmaLocalizacao = ($boMesmoOrgao && $boMesmoLocal) ? true : false;

                    if ($boMesmaLocalizacao == false) {
                        $obTPatrimonioHistoricoBem->setDado( 'cod_situacao', $_REQUEST['slSituacao_'.$arBem[1].'_'.$arBem[2]] );
                        $obTPatrimonioHistoricoBem->setDado( 'descricao'   , $_REQUEST['stNomBem'] );
                        $obTPatrimonioHistoricoBem->inclusao();
                    }

                    $inCodResponsavelNovo = $_REQUEST['inCodResponsavel'];
                    $inCodBem = $arBem[1];

                    $obTPatrimonioBemResponsavel = new TPatrimonioBemResponsavel();
                    $obTPatrimonioBemResponsavel->setDado('cod_bem', $inCodBem);
                    $obTPatrimonioBemResponsavel->recuperaUltimoResponsavel($rsUltimoResponsavel);
                    $inCodResponsavelAtual = $rsUltimoResponsavel->getCampo('numcgm');

                    if ($inCodResponsavelNovo && ($inCodResponsavelAtual != $inCodResponsavelNovo)) {
                       if ($inCodResponsavelAtual) {
                          $obTPatrimonioBemResponsavel = new TPatrimonioBemResponsavel();
                          $obTPatrimonioBemResponsavel->setDado('cod_bem'  , $inCodBem );
                          $obTPatrimonioBemResponsavel->setDado('timestamp', $rsUltimoResponsavel->getCampo('timestamp') );
                          $obTPatrimonioBemResponsavel->setDado('numcgm'   , $rsUltimoResponsavel->getCampo('numcgm') );
                          $obTPatrimonioBemResponsavel->setDado('dt_fim'   , date('d/m/Y') );
                          $obTPatrimonioBemResponsavel->alteracao();
                       }

                       $obTPatrimonioBemResponsavel = new TPatrimonioBemResponsavel();
                       $obTPatrimonioBemResponsavel->setDado('cod_bem'  , $inCodBem );
                       $obTPatrimonioBemResponsavel->setDado('numcgm'   , $inCodResponsavelNovo );
                       $obTPatrimonioBemResponsavel->inclusao();
                    }
                        $arBemTransferido[] = $arBem[1];
                }
            }

            if (count($arBemNaoTransferido) > 0) {
                $stMensagem = 'Os bens '.implode(',',$arBemNaoTransferido).' já estavam na localização de destino.';
            } else {
                $stMensagem = 'Bens '.implode(',',$arBemTransferido);
            }

            $stListaBens = implode(',',$arBemTransferido);

            if ($_REQUEST['boEmitirTermo'] == 'true') {

                $stCaminho = CAM_GP_PAT_INSTANCIAS."relatorio/OCGeratermoResponsabilidade.php";
                $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&inNumResponsavel=".$_REQUEST['inCodResponsavel']."&stNomResponsavel=".$_REQUEST['stNomResponsavel']."&setPDF=true"."&local_origem=".$_REQUEST['inLocalOrigem']."&local_destino=".$_REQUEST['inLocalDestino']."&lista_bens=".$stListaBens;
                
                if (isset($_REQUEST['demo_valor'])) {
                    $stCampos .= "&demo_valor=1";
                }

                if (isset($_REQUEST['inCodOrganogramaAtivo'])) {
                    $stCampos .= "&inCodOrganogramaAtivo=".$_REQUEST['inCodOrganogramaAtivo'];
                }

                if (isset($_REQUEST['inCodOrganogramaClassificacao'])) {
                    $stCampos .= "&inCodOrganogramaClassificacao=".$_REQUEST['inCodOrganogramaClassificacao'];
                }
                
                if (isset($_REQUEST['hdninCodOrganograma'])) {
                    $stCampos .= "&hdninCodOrganograma=".$_REQUEST['hdninCodOrganograma'];
                }

                $pgRel = $stCaminho.$stCampos;

                $stJS  = " window.parent.frames['oculto'].location = '".$pgRel."'";
            }

            SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
            SistemaLegado::executaFrameOculto($stJS);
        } else {
            SistemaLegado::exibeAviso(urlencode( $stMensagem ).'!',"n_incluir","erro");
        }
        break;
}

Sessao::encerraExcecao();
