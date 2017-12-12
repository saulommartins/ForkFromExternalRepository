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
    * Página de Processamento de Credito especial/suplementar por Excesso de arrecadação
    * Data de Criação   : 02/03/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31951 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.07
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterExcesso";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obROrcamentoSuplementacao = new ROrcamentoSuplementacao();

$stAcao = $request->get('stAcao');

//valida a utilização da rotina de encerramento do mês contábil
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);

include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";

$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

$arDtAutorizacao = explode('/', $request->get('stData'));
if ($boUtilizarEncerramentoMes == 'true' && $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
    SistemaLegado::exibeAviso(urlencode("Mês do Crédito encerrado!"),"n_incluir","erro");
    exit;
}

switch ($stAcao) {
    case "Suplementa":
    case "Especial":
        $nuVlNorma = str_replace( '.','' , $request->get('nuVlTotal') );
        $nuVlNorma = str_replace( ',','.', $nuVlNorma );

        $obROrcamentoSuplementacao->setExercicio( Sessao::getExercicio() );
        $obROrcamentoSuplementacao->setCodTipo( $request->get('inCodTipo') );
        $obROrcamentoSuplementacao->obRNorma->setCodNorma( $request->get('inCodNorma') );
        $obROrcamentoSuplementacao->setVlTotal( $nuVlNorma );
        $obROrcamentoSuplementacao->setDecreto( $stDecreto );
        $obROrcamentoSuplementacao->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );
        $obROrcamentoSuplementacao->setCredSuplementar( 'Excesso' );
        $obROrcamentoSuplementacao->setMotivo( $request->get('stMotivo') );
        $obROrcamentoSuplementacao->setDtLancamento( $request->get('stData') );

        $arDespesaSuplementarSessao = Sessao::read('arDespesaSuplementar');
        $inCount = count( $arDespesaSuplementarSessao );
        if ($inCount) {
            foreach ($arDespesaSuplementarSessao as $arDespesaSuplementar) {
                $obROrcamentoSuplementacao->addDespesaSuplementada();
                $obROrcamentoSuplementacao->roUltimoDespesaSuplementada->setCodDespesa   ( $arDespesaSuplementar['cod_despesa'] );
                $obROrcamentoSuplementacao->roUltimoDespesaSuplementada->setValorOriginal( $arDespesaSuplementar['valor']       );
            }
        }

        $obErro = $obROrcamentoSuplementacao->incluir();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=".$stAcao, $obROrcamentoSuplementacao->getDecreto() , "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}

?>