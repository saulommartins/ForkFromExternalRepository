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
    * Pagina de processamento para Incluir Penalidade a Fornecedores
    * Data de Criação   : 10/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * Casos de uso: uc-03.05.28

    $Id: PRManterPenalidadeFornecedor.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( TLIC."TLicitacaoParticipanteCertificacaoPenalidade.class.php" );
include_once( TLIC."TLicitacaoPenalidadesCertificacao.class.php" );

$stPrograma = "ManterPenalidadeFornecedor";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao( true );

$obTLicitacaoParticipanteCertificacaoPenalidade = new TLicitacaoParticipanteCertificacaoPenalidade();
$obTLicitacaoPenalidadesCertificacao = new TLicitacaoPenalidadesCertificacao();

Sessao::getTransacao()->setMapeamento( $obTLicitacaoParticipanteCertificacaoPenalidade );

switch ($_REQUEST['stAcao']) {

    case 'incluir':

            $arPen = Sessao::read('arPen');

            if ( count($arPen) > 0 ) {
                $obTLicitacaoPenalidadesCertificacao->obTLicitacaoParticipanteCertificacaoPenalidade = $obTLicitacaoParticipanteCertificacaoPenalidade;

                $obTLicitacaoParticipanteCertificacaoPenalidade->setDado('cgm_fornecedor', $_REQUEST['inCodFornecedor']);
                $obTLicitacaoParticipanteCertificacaoPenalidade->setDado('exercicio', $_REQUEST['stExercicio'] );
                $obTLicitacaoParticipanteCertificacaoPenalidade->setDado('ano_exercicio', $_REQUEST['stExercicio'] );
                $obTLicitacaoParticipanteCertificacaoPenalidade->setDado('num_certificacao', $_REQUEST['inCertificacao'] );
                $obTLicitacaoParticipanteCertificacaoPenalidade->setDado('cod_documento',0 );
                $obTLicitacaoParticipanteCertificacaoPenalidade->setDado('cod_tipo_documento',0 );
                $rsCertificacao = new RecordSet();

                $stFiltro ="\n WHERE participante_certificacao_penalidade.num_certificacao = ".$_REQUEST['inCertificacao']." \n";
                $stFiltro.="     AND participante_certificacao_penalidade.exercicio        = '".$_REQUEST['stExercicio']."'  \n";
                $stFiltro.="     AND participante_certificacao_penalidade.cgm_fornecedor   = ".$_REQUEST['inCodFornecedor']."\n";

                $obTLicitacaoParticipanteCertificacaoPenalidade->recuperaTodos($rsCertificacao,$stFiltro);
                if ($rsCertificacao->getNumLinhas()<1) {
                    $obTLicitacaoParticipanteCertificacaoPenalidade->inclusao();
                }

                // Limpa os registros da tabela para fazer uma nova inserção.
                $obTLicitacaoPenalidadesCertificacao->setDado('cgm_fornecedor', $_REQUEST['inCodFornecedor']);
                $obTLicitacaoPenalidadesCertificacao->setDado('exercicio', $_REQUEST['stExercicio'] );
                $obTLicitacaoPenalidadesCertificacao->setDado('num_certificacao', $_REQUEST['inCertificacao'] );
                $obTLicitacaoPenalidadesCertificacao->exclusao();

                foreach ($arPen as $chave => $valor) {
                    $obTLicitacaoPenalidadesCertificacao->setDado('ano_exercicio', $valor['ano_exercicio']);
                    $obTLicitacaoPenalidadesCertificacao->setDado('cod_processo', intval($valor['processo']));
                    $obTLicitacaoPenalidadesCertificacao->setDado('cod_penalidade', $valor['cod_penalidade']);
                    if ($valor['valor']) {
                        $obTLicitacaoPenalidadesCertificacao->setDado('valor', $valor['valor']);
                    } else {
                        $obTLicitacaoPenalidadesCertificacao->setDado('valor', '' );
                    }
                    $obTLicitacaoPenalidadesCertificacao->setDado('dt_publicacao', $valor['dt_publicacao']);
                    $obTLicitacaoPenalidadesCertificacao->setDado('dt_validade', $valor['dt_validade']);
                    $obTLicitacaoPenalidadesCertificacao->setDado('observacao', $valor['observacao']);
                    $obTLicitacaoPenalidadesCertificacao->inclusao();
                }
            } else {
                $stMensagem = "Ao menos uma Penalidade deve ser incluída.";
            }

            if (!$stMensagem) {
                SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], "Número da Certificação: ".$_REQUEST['inCertificacao'], "incluir", "aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($stMensagem),"n_alterar","erro");
            }
    break;

    case 'alterar':

        $arPen = Sessao::read('arPen');

        if ( count($arPen) > 0 ) {
            //$obTLicitacaoParticipanteCertificacaoPenalidade->setDado('cgm_fornecedor', $_REQUEST['inCodFornecedor']);
            //$obTLicitacaoParticipanteCertificacaoPenalidade->setDado('exercicio', $_REQUEST['stExercicio'] );
            //$obTLicitacaoParticipanteCertificacaoPenalidade->setDado('num_certificacao', $_REQUEST['inCertificacao'] );
            //$obTLicitacaoParticipanteCertificacaoPenalidade->setDado('cod_documento',0 );
            //$obTLicitacaoParticipanteCertificacaoPenalidade->setDado('cod_tipo_documento',0 );
            //$obTLicitacaoParticipanteCertificacaoPenalidade->alteracao();
            //$obTLicitacaoPenalidadesCertificacao->obTLicitacaoParticipanteCertificacaoPenalidade = & $obTLicitacaoParticipanteCertificacaoPenalidade;

            // Limpa os registros da tabela para fazer uma nova inserção.
            $obTLicitacaoPenalidadesCertificacao->setDado('cgm_fornecedor', $_REQUEST['inCodFornecedor']);
            $obTLicitacaoPenalidadesCertificacao->setDado('exercicio', $_REQUEST['stExercicio'] );
            $obTLicitacaoPenalidadesCertificacao->setDado('num_certificacao', $_REQUEST['inCertificacao'] );
            $obTLicitacaoPenalidadesCertificacao->exclusao();

            foreach ($arPen as $chave => $valor) {
                $obTLicitacaoPenalidadesCertificacao->setDado('ano_exercicio', $valor['ano_exercicio']);
                $obTLicitacaoPenalidadesCertificacao->setDado('cod_processo', intval($valor['processo']));
                $obTLicitacaoPenalidadesCertificacao->setDado('cod_penalidade', $valor['cod_penalidade']);
                if ($valor['valor']) {
                    $obTLicitacaoPenalidadesCertificacao->setDado('valor', $valor['valor']);
                } else {
                    $obTLicitacaoPenalidadesCertificacao->setDado('valor', '' );
                }
                $obTLicitacaoPenalidadesCertificacao->setDado('dt_publicacao', $valor['dt_publicacao']);
                $obTLicitacaoPenalidadesCertificacao->setDado('dt_validade', $valor['dt_validade']);
                $obTLicitacaoPenalidadesCertificacao->setDado('observacao', $valor['observacao']);
                $obTLicitacaoPenalidadesCertificacao->inclusao();
            }
        } else {
            $stMensagem = "Ao menos uma Penalidade deve ser incluída.";
        }

        if (!$stMensagem) {
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao", "Número da Certificação: ".$_REQUEST['inCertificacao'], "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem),"n_alterar","erro");
        }
    break;
    case 'excluir':
        $obTLicitacaoPenalidadesCertificacao->setDado('cgm_fornecedor', $_REQUEST['inCodFornecedor']);
        $obTLicitacaoPenalidadesCertificacao->setDado('exercicio', $_REQUEST['stExercicio'] );
        $obTLicitacaoPenalidadesCertificacao->setDado('num_certificacao', $_REQUEST['inCertificacao'] );

        $obErro = $obTLicitacaoPenalidadesCertificacao->exclusao();

        if ( !$obErro->ocorreu() ) {
            $obTLicitacaoParticipanteCertificacaoPenalidade->setDado('cgm_fornecedor', $_REQUEST['inCodFornecedor']);
            $obTLicitacaoParticipanteCertificacaoPenalidade->setDado('exercicio', $_REQUEST['stExercicio'] );
            $obTLicitacaoParticipanteCertificacaoPenalidade->setDado('num_certificacao', $_REQUEST['inCertificacao'] );

            $obErro = $obTLicitacaoParticipanteCertificacaoPenalidade->exclusao();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao", "Número da Certificação: ".$_REQUEST['inCertificacao'], "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
}

Sessao::encerraExcecao();
