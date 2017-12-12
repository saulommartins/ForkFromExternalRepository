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
    * Página de Processamento para Pagamento do módulo Tesouraria
    * Data de Criação   : 29/08/2006

    * @author Analista: CLeisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 30835 $
    $Name$
    $Author: luciano $
    $Date: 2007-09-24 17:52:14 -0300 (Seg, 24 Set 2007) $

    * Casos de uso: uc-02.04.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO.'RTesourariaBoletim.class.php';
include_once CAM_GF_TES_NEGOCIO.'RTesourariaConfiguracao.class.php';

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = $_POST['stPrograma'];
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgAutenticacao = '../autenticacao/FMManterAutenticacao.php';

$obErro = new Erro;

if (!$_REQUEST['inCodBoletim']) {
    $obErro->setDescricao('Selecione um boletim');
} else {
    list($inCodBoletim,$stDtBoletim) = explode(':', $_REQUEST['inCodBoletim']);
}

list($stDia, $stMes, $stAno) = explode( '/', $stDtBoletim );

//valida a utilização da rotina de encerramento do mês contábil
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $stMes) {
    SistemaLegado::executaFrameOculto(" window.parent.frames['telaPrincipal'].document.getElementById('Ok').disabled = false; ");
    $nomAcao = SistemaLegado::pegaDado('nom_acao', 'administracao.acao', " where cod_acao = ".Sessao::read('acao'));
    SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (Mês do Boletim encerrado!)"),"","erro");
    exit;
}

if( isset($_REQUEST['inCodTipoTransferenciaTO']) && ($_REQUEST['inCodTipoTransferenciaTO'] == '') ){
    SistemaLegado::exibeAviso(urlencode("Selecione um Tipo de Trânsferência"),"","erro");
    exit;
}

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio  (Sessao::getExercicio());
$obRTesourariaBoletim->setCodBoletim ($inCodBoletim);
$obRTesourariaBoletim->setDataBoletim($stDtBoletim);
$obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade($_POST['inCodEntidade']);
$obRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->setNumCGM(Sessao::read('numCgm'));
$obRTesourariaBoletim->obRTesourariaUsuarioTerminal->setTimestampUsuario($_POST['stTimestampUsuario']);
$obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal($_POST['inCodTerminal']);
$obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal($_POST['stTimestampTerminal']);
$obRTesourariaBoletim->addTransferencia();

$obRTesourariaConfiguracao = new RTesourariaConfiguracao();
$obRTesourariaConfiguracao->setExercicio(Sessao::getExercicio());
$obRTesourariaConfiguracao->consultarTesouraria();

$obRTesourariaBoletim->roUltimaTransferencia->setNomContaDebito ($_POST['stNomContaDebito']);
$obRTesourariaBoletim->roUltimaTransferencia->setNomContaCredito($_POST['stNomContaCredito']);

$stFiltros = "inCodEntidade=".$_POST['inCodEntidade']."&inCodBoletim=".urlencode($_REQUEST['inCodBoletim']);

switch ($stAcao) {
    case 'incluir':
       
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio(Sessao::getExercicio());
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade($_POST['inCodEntidade']);
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setContaDebito ($_POST['inCodPlanoDebito']);
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setContaCredito($_POST['inCodPlanoCredito']);
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setValor       (str_replace(',','.',str_replace('.','',$_POST['nuValor'])));
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico($_POST['inCodHistorico']);
        $obRTesourariaBoletim->roUltimaTransferencia->setObservacaoTransferencia($_POST['stObservacoes']);

        $obRTesourariaBoletim->roUltimaTransferencia->setTipoTransferencia($_POST['inTipoTransferencia']);

        if ($stDtBoletim == date( 'd/m/Y' )) {
            $obRTesourariaBoletim->roUltimaTransferencia->setTimestampTransferencia(date('Y-m-d H:i:s.ms'));
        } else {
            list($stDia, $stMes, $stAno) = explode( '/', $stDtBoletim);
            $obRTesourariaBoletim->roUltimaTransferencia->setTimestampTransferencia($stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms'));
        }
        if($_REQUEST['stExercicioEmpenho'] AND empty($_REQUEST['inCodigoEmpenho'])){
            $obErro->setDescricao('É necessário informar o empenho');
        }elseif(empty($_REQUEST['stExercicioEmpenho']) AND ($_REQUEST['inCodigoEmpenho'])){
            $obErro->setDescricao('É necessário informar o Exercício do Empenho');
        }
        

        if (!$obErro->ocorreu()) {
            $obErro = $obRTesourariaBoletim->roUltimaTransferencia->transferir($boTransacao);
        }

        if ($obRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao()) {
            $arDescricao['stDescricao'] = $obRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao();
            Sessao::write('stDescricao', $arDescricao);
        }

        if (!$obErro->ocorreu()) {
            if ($obRTesourariaConfiguracao->getFormaComprovacao()) {
                SistemaLegado::alertaAviso($pgAutenticacao."?pg_volta=../transferencia/".$pgForm.'&'.$stFiltros,"Lote " . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() . "/" . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ,"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($pgForm.'?'.$stFiltros,"Lote " . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() . "/" . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ,"incluir","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::executaFrameOculto(" window.parent.frames['telaPrincipal'].document.getElementById('Ok').disabled = false; ");
            $nomAcao = SistemaLegado::pegaDado('nom_acao', 'administracao.acao', " where cod_acao = ".Sessao::read('acao'));
            SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"","erro");
        }

    break;
}

?>
