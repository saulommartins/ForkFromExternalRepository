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
    * Data de Criação: 05/02/2009

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * $Id: $

    * Casos de uso: uc-03.02.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaUtilizacao.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaUtilizacaoRetorno.class.php" );

$stPrograma = "ManterUtilizacao";
$pgList   = "LS".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$obErro = new Erro();
$obTrasacao = new Transacao();
$obTFrotaUtilizacao = new TFrotaUtilizacao();
$obTFrotaUtilizacaoRetorno = new TFrotaUtilizacaoRetorno();

$obTrasacao->abreTransacao($boFlagTransacao, $boTransacao);

switch ($stAcao) {

    case 'excluir' :

        if ( ($_REQUEST["stDataRetorno"] != "") && ($_REQUEST["stHoraRetorno"] != "") ) {
            $obTFrotaUtilizacaoRetorno->setDado('cod_veiculo', $_REQUEST['inCodVeiculo']);
            $obTFrotaUtilizacaoRetorno->setDado('dt_saida', $_REQUEST['stDataSaida']);
            $obTFrotaUtilizacaoRetorno->setDado('hr_saida', $_REQUEST['stHoraSaida']);
            $obErro = $obTFrotaUtilizacaoRetorno->exclusao( $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            $obTFrotaUtilizacao->setDado('cod_veiculo', $_REQUEST['inCodVeiculo']);
            $obTFrotaUtilizacao->setDado('dt_saida', $_REQUEST['stDataSaida']);
            $obTFrotaUtilizacao->setDado('hr_saida', $_REQUEST['stHoraSaida']);
            $obErro = $obTFrotaUtilizacao->exclusao( $boTransacao );
        }

        $obTrasacao->fechaTransacao($boFlagTransacao, $boTransacao,$obErro,$obTFrotaUtilizacao);

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Veículo - '.$_REQUEST['inCodVeiculo'],"excluir","excluir", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,$obErro->getDescricao(),"excluir","excluir", Sessao::getId(), "../");
        }
    break;
}

//Sessao::encerraExcecao();
