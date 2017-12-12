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
    * Data de Criação: 21/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: PRManterMotorista.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaMotorista.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaMotoristaVeiculo.class.php" );
include_once( CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php" );

$stPrograma = "ManterMotorista";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTFrotaMotorista = new TFrotaMotorista();
$obTFrotaMotoristaVeiculo = new TFrotaMotoristaVeiculo();
$obTCGMPessoaFisica = new TCGMPessoaFisica();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTFrotaMotorista );
Sessao::getTransacao()->setMapeamento( $obTFrotaMotoristaVeiculo );
Sessao::getTransacao()->setMapeamento( $obTCGMPessoaFisica );

switch ($stAcao) {
    case 'incluir' :
        if ( implode(array_reverse(explode('/',$_REQUEST['dtValidade']))) < date('Ymd') ) {
                $stMensagem = 'A data de validade do CNH deve ser maior ou igual a data de hoje';
        }
        if (!$stMensagem) {
            //altera os dados do motorista na table sw_cgm_pessoa_fisica
            $obTCGMPessoaFisica->setDado( 'numcgm', $_REQUEST['inCodMotorista']);
            $obTCGMPessoaFisica->recuperaPorChave( $rsCGM );
            $obTCGMPessoaFisica->setDado( 'cod_categoria_cnh', $_REQUEST['hdnHabilitacao'] );
            $obTCGMPessoaFisica->setDado( 'rg', $rsCGM->getCampo('rg') );
            $obTCGMPessoaFisica->setDado( 'orgao_emissor', $rsCGM->getCampo('orgao_emissor') );
            $obTCGMPessoaFisica->setDado( 'cod_nacionalidade', $rsCGM->getCampo('cod_nacionalidade') );
            $obTCGMPessoaFisica->setDado( 'num_cnh', $_REQUEST['stNumCNH'] );
            $obTCGMPessoaFisica->setDado( 'dt_validade_cnh', $_REQUEST['dtValidade'] );
            $obTCGMPessoaFisica->alteracao();

            //insere na table frota.motorista
            $obTFrotaMotorista->setDado('cgm_motorista', $_REQUEST['inCodMotorista'] );
            $obTFrotaMotorista->recuperaPorChave( $rsMotorista );
            $obTFrotaMotorista->setDado('ativo', ( $_REQUEST['boStatus'] == 1 ) ? true : false );
            if ( $rsMotorista->getNumLinhas() > 0 ) {
                $obTFrotaMotorista->alteracao();
            } else {
                $obTFrotaMotorista->inclusao();
            }

            //se tiver veiculos autorizados para o motorista, inclui na table frota.motorista_veiculp
            if ( count( Sessao::read('veiculosMotorista') ) > 0 ) {
                foreach ( Sessao::read('veiculosMotorista') AS $arTemp ) {
                    $obTFrotaMotoristaVeiculo->setDado('cod_veiculo', $arTemp['cod_veiculo'] );
                    $obTFrotaMotoristaVeiculo->setDado('cgm_motorista', $_REQUEST['inCodMotorista'] );
                    $obTFrotaMotoristaVeiculo->setDado('padrao', $arTemp['padrao'] );
                    $obTFrotaMotoristaVeiculo->inclusao();
                }
            }

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,'Motorista - '.$_REQUEST['inCodMotorista'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;

    case 'alterar' :
        if ( implode(array_reverse(explode('/',$_REQUEST['dtValidade']))) < date('Ymd') ) {
                $stMensagem = 'A data de validade do CNH deve ser maior ou igual a data de hoje';
        }
        if (!$stMensagem) {
            //altera os dados do motorista na table sw_cgm_pessoa_fisica
            $obTCGMPessoaFisica->setDado( 'numcgm', $_REQUEST['inCodMotorista']);
            $obTCGMPessoaFisica->recuperaPorChave( $rsCGM );
            $obTCGMPessoaFisica->setDado( 'cod_categoria_cnh', $_REQUEST['hdnHabilitacao'] );
            $obTCGMPessoaFisica->setDado( 'rg', $rsCGM->getCampo('rg') );
            $obTCGMPessoaFisica->setDado( 'orgao_emissor', $rsCGM->getCampo('orgao_emissor') );
            $obTCGMPessoaFisica->setDado( 'cod_nacionalidade', $rsCGM->getCampo('cod_nacionalidade') );
            $obTCGMPessoaFisica->setDado( 'num_cnh', $_REQUEST['stNumCNH'] );
            $obTCGMPessoaFisica->setDado( 'dt_validade_cnh', $_REQUEST['dtValidade'] );
            $obTCGMPessoaFisica->alteracao();

            //insere na table frota.motorista
            $obTFrotaMotorista->setDado('cgm_motorista', $_REQUEST['inCodMotorista'] );
            $obTFrotaMotorista->setDado('ativo', ( $_REQUEST['boStatus'] == 1 ) ? true : false );
            $obTFrotaMotorista->alteracao();

            //remove todos os veiculos dos quais ele era motorista
            $obTFrotaMotoristaVeiculo->setDado( 'cgm_motorista', $_REQUEST['inCodMotorista'] );
            $obTFrotaMotoristaVeiculo->exclusao();

            //se tiver veiculos autorizados para o motorista, inclui na table frota.motorista_veiculp
            if ( count( Sessao::read('veiculosMotorista') ) > 0 ) {
                foreach ( Sessao::read('veiculosMotorista') AS $arTemp ) {
                    $obTFrotaMotoristaVeiculo->setDado('cod_veiculo', $arTemp['cod_veiculo'] );
                    $obTFrotaMotoristaVeiculo->setDado('cgm_motorista', $_REQUEST['inCodMotorista'] );
                    $obTFrotaMotoristaVeiculo->setDado('padrao', $arTemp['padrao'] );
                    $obTFrotaMotoristaVeiculo->inclusao();
                }
            }
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Motorista - '.$_REQUEST['inCodMotorista'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;

    case 'excluir' :
            //exclui da table frota.motorista_veiculo
            $obTFrotaMotoristaVeiculo->setDado( 'cgm_motorista', $_REQUEST['inCodMotorista'] );

            $obTFrotaMotoristaVeiculo->exclusao();

            //seta os dados da exclusao
            $obTFrotaMotorista->setDado( 'cgm_motorista', $_REQUEST['inCodMotorista'] );
            $obTFrotaMotorista->exclusao();
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Motorista - '.$_REQUEST['inCodMotorista'],"excluir","aviso", Sessao::getId(), "../");
        break;
}

Sessao::encerraExcecao();
