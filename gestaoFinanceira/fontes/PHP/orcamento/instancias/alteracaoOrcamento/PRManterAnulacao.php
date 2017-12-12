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
    * Página de Processamento de Suplementacao
    * Data de Criação   : 16/03/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30813 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.07
*/

/*
$Log$
Revision 1.4  2006/07/05 20:42:23  cleisson
Adicionada tag Log aos arquivos

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAnulacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$obRegra = new ROrcamentoSuplementacao;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//valida a utilização da rotina de encerramento do mês contábil
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

$arDtAutorizacao = explode('/', $_POST['stData']);
if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
    SistemaLegado::exibeAviso(urlencode("Mês da Anulação encerrado!"),"n_incluir","erro");
    exit;
}

switch ($stAcao) {

    case "incluir":
        $obErro = new Erro;

        $nuVlTotal = str_replace( '.' , '' , $_POST['nuVlTotal'] );
        $nuVlTotal = str_replace( ',' ,'.' , $nuVlTotal          );

        $obRegra->setExercicio         ( Sessao::getExercicio()      );
        $obRegra->setCodTipo           ( $_POST['inCodTipo']     );
        $obRegra->obRNorma->setCodNorma( $_POST['inCodNorma']    );
        $obRegra->setVlTotal           ( $nuVlTotal              );
        $obRegra->setDecreto           ( $stDecreto              );
        $obRegra->setCredSuplementar   ( 'Reducao'               );
        $obRegra->setMotivo            ( $_POST['stMotivo']      );
        $obRegra->setDtLancamento      ( $_POST['stData']        );

        if ($_POST['inCodEntidadeReducao'] == $_POST['inCodEntidadeSuplementada']) {
            $obErro->setDescricao( 'As entidades das dotações devem ser diferentes' );
        }

        $arSuplementada = Sessao::read('arSuplementada');
        $inCount = count( $arSuplementada );
        if ($inCount) {
            foreach ($arSuplementada as $arDespesaSuplementar) {
                $obRegra->addDespesaSuplementada();
                $obRegra->roUltimoDespesaSuplementada->setCodDespesa   ( $arDespesaSuplementar['cod_reduzido']);
                $obRegra->roUltimoDespesaSuplementada->setValorOriginal( $arDespesaSuplementar['vl_valor']    );
                $obRegra->roUltimoDespesaSuplementada->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidadeSuplementada'] );
            }
        }

        $arRedutoras = Sessao::read('arRedutoras');
        $inCount = count( $arRedutoras );
        if ($inCount) {
            foreach ($arRedutoras as $arDespesaReducao) {
                $obRegra->addDespesaReducao();
                $obRegra->roUltimoDespesaReducao->setCodDespesa   ( $arDespesaReducao['cod_reduzido']);
                $obRegra->roUltimoDespesaReducao->setValorOriginal( $arDespesaReducao['vl_valor']    );
                $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidadeReducao'] );
            }
        } else {
            $obErro->setDescricao( "É necessário cadastrar pelo menos uma Redução" );
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRegra->anular();

            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=".$stAcao, $obRegra->getDecreto() , "incluir", "aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}
?>
