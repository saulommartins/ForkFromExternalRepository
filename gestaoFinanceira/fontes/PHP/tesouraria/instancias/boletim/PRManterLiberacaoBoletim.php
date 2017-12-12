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
    * Página de Processamento de Configuração do módulo Tesouraria
    * Data de Criação   : 15/12/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: PRManterLiberacaoBoletim.php 64692 2016-03-22 13:36:45Z michel $

    * Casos de uso: uc-02.04.08 , uc-02.04.25
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterLiberacaoBoletim";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stNow = date( 'Y-m-d H:i:s.ms' );

$obErro = new Erro();

foreach ($request->getAll() as $campo => $valor) {
    if (substr($campo,0,9) == 'boLiberar' && !$obErro->ocorreu()) {
        $inCodBoletim = str_replace('boLiberar_','',$campo);
        $inCodBoletim = substr($inCodBoletim,0,strpos($inCodBoletim,'_'));
        $arBoletim[] = $inCodBoletim;
    }
}

//valida a utilização da rotina de encerramento do mês contábil
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);

$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $request->get('inMes')) {
    if ($stAcao == 'excluir') {
        SistemaLegado::exibeAviso(urlencode("Mês do Cancelamento encerrado!"),"n_incluir","erro");
    } else {
        SistemaLegado::exibeAviso(urlencode("Mês da Liberação encerrado!"),"n_incluir","erro");
    }
    SistemaLegado::LiberaFrames();
    exit;
}

$rsBoletim = new RecordSet;
$rsBoletim->preenche( $arBoletim );

$inCount = 0;
$obRegra = new RTesourariaBoletim();
$obRegra->obRTesourariaUsuarioTerminal->obRCGM->setNumCGM  ( Sessao::read('numCgm')              			);
$obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal( Sessao::read('inCodTerminal'));
$obRegra->obRTesourariaUsuarioTerminal->listar($userTerminal);
$obRegra->obRTesourariaUsuarioTerminal->setTimestampUsuario( $userTerminal->getCampo('timestamp_usuario')  	);
$obRegra->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal( $userTerminal->getCampo('timestamp_terminal') );
$obRegra->setExercicio   ( Sessao::getExercicio() );
$obRegra->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );

$boTransacao="";
while ( !$obErro->ocorreu() && !$rsBoletim->eof() ) {

    $obRegra->setDataBoletim ('');
    $obRegra->setCodBoletim  ( $rsBoletim->arElementos[$inCount] );

    $boArrecadar = ($inCount < 1 ? true : false);
    if ($stAcao == 'incluir') {
        $obErro = $obRegra->liberar( $boTransacao, $boArrecadar );
    }
    if ($stAcao == 'excluir') {
        $obErro = $obRegra->cancelarLiberacao( $boTransacao );
    }
    if (!$obErro->ocorreu()) {
        $inCount++;
    } else {
        break;
    }

    $rsBoletim->proximo();
}
if (!$obErro->ocorreu()) {
    if($stAcao == 'incluir')
        $stMensagem = $inCount > 1 ? "$inCount Boletins Liberados" : "Boletim ".$inCodBoletim."/".Sessao::getExercicio();
    if($stAcao == 'excluir')
        $stMensagem = $inCount > 1 ? "$inCount Liberações de Boletim Canceladas" : "Boletim ".$inCodBoletim."/".Sessao::getExercicio();

    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao."&inCodEntidade=".$request->get('inCodEntidade')."&inMes=".$request->get('inMes')."&stDataInicial=".$request->get('stDataInicial')."&stDataFinal=".$request->get('stDataFinal'), $stMensagem,$stAcao,"aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
    SistemaLegado::LiberaFrames();
}

?>
