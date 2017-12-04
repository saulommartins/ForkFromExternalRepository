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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: PRManterTipoVeiculo.php 63195 2015-08-03 20:45:09Z carlos.silva $

    * Casos de uso: uc-03.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaTipoVeiculo.class.php" );

$stPrograma = "ManterTipoVeiculo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTFrotaTipoVeiculo = new TFrotaTipoVeiculo();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTFrotaTipoVeiculo );

switch ($stAcao) {
    case 'incluir':
        //verifica se nao existe ja no cadastro a descricao do tipo do veiculo
        $obTFrotaTipoVeiculo->recuperaTodos( $rsTipoVeiculo, " WHERE nom_tipo ILIKE '".$_REQUEST['stTipoVeiculo']."' " );
        if ( $rsTipoVeiculo->getNumLinhas() > 0 ) {
            $stMensagem = 'Já existe um tipo de veículo com esta descrição';
        }
        
        //verifica se nao existe ja no cadastro o cod_tipo da tipo
        $obTFrotaTipoVeiculo->recuperaTodos( $rsTipoVeiculo, " WHERE cod_tipo = ".$_REQUEST['stCodigoTipoVeiculo']." ");
        if ( $rsTipoVeiculo->getNumLinhas() > 0 ) {
            $stMensagem = 'Já existe um tipo de veículo com este código';
        }

        if (!$stMensagem) {
            //seta os dados e cadastra no sistema
            $obTFrotaTipoVeiculo->setDado( 'cod_tipo', $_REQUEST['stCodigoTipoVeiculo'] );
            $obTFrotaTipoVeiculo->setDado( 'nom_tipo', $_REQUEST['stTipoVeiculo'] );
            $obTFrotaTipoVeiculo->setDado( 'placa', ( $_REQUEST['boPlaca'] == 1 ) ? true : false );
            $obTFrotaTipoVeiculo->setDado( 'prefixo', ( $_REQUEST['boPrefixo'] == 1 ) ? true : false );
            $obTFrotaTipoVeiculo->setDado( 'controlar_horas_trabalhadas', ( $_REQUEST['boHoras'] == 1 ) ? true : false );
            $obTFrotaTipoVeiculo->inclusao();

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,'Tipo de Veículo - '.$_REQUEST['stCodigoTipoVeiculo'].' - '.$_REQUEST['stTipoVeiculo'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;

    case 'alterar':
        //verifica se nao existe ja no cadastro a descricao do tipo do veiculo
        $obTFrotaTipoVeiculo->recuperaTodos( $rsTipoVeiculo, " WHERE nom_tipo ILIKE '".$_REQUEST['stTipoVeiculo']."' AND cod_tipo <> ".$_REQUEST['stCodigoTipoVeiculo']." " );
        if ( $rsTipoVeiculo->getNumLinhas() > 0 ) {
            $stMensagem = 'Já existe um tipo de veículo com esta descrição';
        }

        if (!$stMensagem) {
            //seta os dados e cadastra no sistema
            $obTFrotaTipoVeiculo->setDado( 'cod_tipo', $_REQUEST['stCodigoTipoVeiculo'] );
            $obTFrotaTipoVeiculo->setDado( 'nom_tipo', $_REQUEST['stTipoVeiculo'] );
            $obTFrotaTipoVeiculo->setDado( 'placa', ( $_REQUEST['boPlaca'] == 1 ) ? true : false );
            $obTFrotaTipoVeiculo->setDado( 'prefixo', ( $_REQUEST['boPrefixo'] == 1 ) ? true : false );
            $obTFrotaTipoVeiculo->setDado( 'controlar_horas_trabalhadas', ( $_REQUEST['boHoras'] == 1 ) ? true : false );
            $obTFrotaTipoVeiculo->alteracao();

            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Tipo de Veículo - '.$_REQUEST['stCodigoTipoVeiculo'].' - '.$_REQUEST['stTipoVeiculo'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;

    CASE 'excluir':
        //seta os dados e exclui da base
        $obTFrotaTipoVeiculo->setDado( 'cod_tipo', $_REQUEST['inCodTipoVeiculo'] );
        $obTFrotaTipoVeiculo->exclusao();
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Tipo de Veículo - '.$_REQUEST['inCodTipoVeiculo'].' - '.$_REQUEST['stNomTipoVeiculo'],"excluir","aviso", Sessao::getId(), "../");

        break;

}

Sessao::encerraExcecao();
