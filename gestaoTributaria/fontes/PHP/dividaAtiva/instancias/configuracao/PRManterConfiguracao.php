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
    * Página de Processamento Configuracao Divida Ativa
    * Data de Criação   : 08/05/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: PRManterConfiguracao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_DAT_NEGOCIO."RDATConfiguracao.class.php" );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFormLivro = "FM".$stPrograma."Livro.php";
$pgFormInscricao = "FM".$stPrograma."Inscricao.php";
$pgFormDocumento = "FM".$stPrograma."Documento.php";
$pgFormRemissao = "FM".$stPrograma."Remissao.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

switch ($stAcao) {
    case "documento":
        $obRDATConfiguracao = new RDATConfiguracao;
        $obRDATConfiguracao->setDocumentoNroLeiInscricaoDA ( $_REQUEST["stLeiDA"] );
        $obRDATConfiguracao->setDocumentoMetodologiaCalculo ( $_REQUEST["stMetodologiaCalculo"] );
        $obRDATConfiguracao->setDocumentoSetorArrecadacao ( $_REQUEST["stSetorArrecadacao"] );
        $obRDATConfiguracao->setDocumentoSecretaria ( $_REQUEST["stSecretaria"] );
        $obRDATConfiguracao->setDocumentoCoordenador ( $_REQUEST["stCoordenador"] );
        $obRDATConfiguracao->setDocumentoChefeDepartamento ( $_REQUEST["stChefeDepartamento"] );
        $obRDATConfiguracao->setDocumentoMensagem ( $_REQUEST["stMensagem"] );

        $obRDATConfiguracao->setDocumentoUtilizarMetCalc ( $_REQUEST["boMetCalc"]?true:false );
        $obRDATConfiguracao->setDocumentoUtilizarIncidValDA ( $_REQUEST["boIncidValDA"]?true:false );
        $obRDATConfiguracao->setDocumentoUtilizarLeiDA ( $_REQUEST["boLeiDA"]?true:false );
        $obRDATConfiguracao->setDocumentoUtilizarMsg ( $_REQUEST["boMsg"]?true:false );
        $obRDATConfiguracao->setDocumentoUtilizarResp2 ( $_REQUEST["boResp2"]?true:false );

        $obErro = $obRDATConfiguracao->salvarDocumento( $_REQUEST["stDocumento"] );
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFormDocumento, "Configuração Documentos", "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), "n_alterar", "erro");
        }
        break;

    case "remissao":
        $obRDATConfiguracao = new RDATConfiguracao;

        $obRDATConfiguracao->setLancamentoAtivo( $_REQUEST["stLancamentoAtivo"] );
        $obRDATConfiguracao->setInscricaoAutomatica( $_REQUEST["stInscricaoAutomatica"] );
        $obRDATConfiguracao->setValidacao( $_REQUEST["stValidacaoRemissao"] );
        $obRDATConfiguracao->setLimites( $_REQUEST["cmbValoresLimites"] );
        $obRDATConfiguracao->setCodModalidade( $_REQUEST["inCodModalidade"] );

        $obErro = $obRDATConfiguracao->salvarRemissao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFormRemissao, "Configuração Remissão", "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), "n_alterar", "erro");
        }
        break;

    case "livro": //salvar configuracao da configuracao livro
        $obRDATConfiguracao = new RDATConfiguracao;
        $stLivroFolha = $_REQUEST['inNumIniLivro'].';'.$_REQUEST['stSeqLivro'].';'.
                        $_REQUEST['inNumFolLivro'].';'.$_REQUEST['stNumFolSeq'].';'.Sessao::read('exercicio');

        $obRDATConfiguracao->setLivroFolha ( $stLivroFolha );
        $obErro = $obRDATConfiguracao->salvarLivro();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFormLivro, "Configuração Livro", "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), "n_alterar", "erro");
        }
        break;

    case "inscricao":
        $obRDATConfiguracao = new RDATConfiguracao;
//        $obRDATConfiguracao->setVerificarEmissao( $_REQUEST["stEmissaoNotificacao"] );
        $obRDATConfiguracao->setUtilizarValorReferencia( $_REQUEST["stValorReferencia"] );
        $obRDATConfiguracao->setTipoValorReferencia( $_REQUEST["stTipoValorReferencia"] );
        if ($_REQUEST["stValorReferencia"] == "sim") {
            if ($_REQUEST["stTipoValorReferencia"] == "moeda") { //verificando se os campos obrigatorios foram preenxidos
                if (!$_REQUEST["inMoeda"]) {
                    SistemaLegado::exibeAviso("Campo 'Moeda' deve ser preenchido!", "n_alterar", "erro");
                    exit();
                }

                if (!$_REQUEST["inValorReferenciaMoeda"] || !$_REQUEST["cmbReferenciaMoeda"]) {
                    SistemaLegado::exibeAviso("Campo 'Valor de Referência' deve ser preenchido!", "n_alterar", "erro");
                    exit();
                }

                //moeda (codMoeda)
                $obRDATConfiguracao->setMoedaValorReferencia( $_REQUEST["inMoeda"] );

                //limite valor referencia (max / min)
                $obRDATConfiguracao->setLimiteValorReferencia( $_REQUEST["cmbReferenciaMoeda"] );

                //valor referencia (qualquer valor numerico)
                $obRDATConfiguracao->setValorReferencia( $_REQUEST["inValorReferenciaMoeda"] );
            } else {
                if (!$_REQUEST["inIndicadorEconomico"]) {
                    SistemaLegado::exibeAviso("Campo 'Indicador Econômico' deve ser preenchido!", "n_alterar", "erro");
                    exit();
                }

                if (!$_REQUEST["inValorReferenciaIndicador"] || !$_REQUEST["cmbReferenciaIndicador"]) {
                    SistemaLegado::exibeAviso("Campo 'Valor de Referência' deve ser preenchido!", "n_alterar", "erro");
                    exit();
                }

                //indicador (codIndicador)
                $obRDATConfiguracao->setIndicadorValorReferencia( $_REQUEST["inIndicadorEconomico"] );

                //limite valor referencia (max / min)
                $obRDATConfiguracao->setLimiteValorReferencia( $_REQUEST["cmbReferenciaIndicador"] );

                //valor referencia (qualquer valor numerico)
                $obRDATConfiguracao->setValorReferencia( $_REQUEST["inValorReferenciaIndicador"] );
            }
        }

        //utilizar credito divida (sim / nao)
        $obRDATConfiguracao->setUtilizarCreditoDivida( $_REQUEST["stUtilizarCreditoDividaAtiva"] );

        if ($_REQUEST["stUtilizarCreditoDividaAtiva"] == "sim") {
            if (!$_REQUEST["inCreditoDivida"]) {
                SistemaLegado::exibeAviso("Campo 'Crédito de Dívida' deve ser preenchido!", "n_alterar", "erro");
                exit();
            }

            //credito divida (codCredito)
            $obRDATConfiguracao->setCreditoDivida( $_REQUEST["inCreditoDivida"] );
        }

        //numero inscricao (sequencial / exercicio)
        $obRDATConfiguracao->setNumeracaoInscricao( $_REQUEST["stNumeracaoInscricao"] );
        $obErro = $obRDATConfiguracao->salvarInscricao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFormInscricao, "Configuração Inscrição", "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), "n_alterar", "erro");
        }
        break;
}
?>
