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
    * Data de Criação: 08/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: PRManterBaixarVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculoBaixado.class.php" );

$stPrograma = "ManterBaixarVeiculo";
$pgFilt   = "FLManterVeiculo.php";
$pgList   = "LSManterVeiculo.php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTFrotaVeiculoBaixado = new TFrotaVeiculoBaixado();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTFrotaVeiculoBaixado );

switch ($stAcao) {
    case 'baixar' :
        if ( implode('',array_reverse(explode('/',$_REQUEST['dtBaixa']))) > date('Ymd') ) {
            $stMensagem = 'A data de baixa deve ser menor ou igual ao dia de hoje.';
        }
        if (!$stMensagem) {

            //inclui na table frota.veiculo_baixado
            $obTFrotaVeiculoBaixado->setdado('cod_veiculo', $_REQUEST['inCodVeiculo'] );
            $obTFrotaVeiculoBaixado->setdado('dt_baixa'   , $_REQUEST['dtBaixa'] );
            $obTFrotaVeiculoBaixado->setdado('motivo'     , $_REQUEST['stMotivo'] );
            $obTFrotaVeiculoBaixado->setdado('cod_tipo_baixa', $_REQUEST['inCodTpBaixa'] );
            $obTFrotaVeiculoBaixado->inclusao();

            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Veículo - '.$_REQUEST['inCodVeiculo'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
            echo "<script>LiberaFrames(true,true);</script>";
        }

        break;

    case 'excluir' or 'exc_baixa' :
        //deleta da table frota.veiculo_baixado
        $obTFrotaVeiculoBaixado->setDado('cod_veiculo', $_REQUEST['inCodVeiculo'] );
        $obTFrotaVeiculoBaixado->exclusao();

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=exc_baixa",'Veículo - '.$_REQUEST['inCodVeiculo'],"excluir","excluir", Sessao::getId(), "../");
        break;
}

Sessao::encerraExcecao();
