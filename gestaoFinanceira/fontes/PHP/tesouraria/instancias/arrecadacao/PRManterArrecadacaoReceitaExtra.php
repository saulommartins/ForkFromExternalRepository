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
    * Página de Processo de Arrecadação Extra Orçamentária
    * Data de Criação   : 14/09/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    * $Id: PRManterArrecadacaoReceitaExtra.php 60328 2014-10-14 14:31:02Z jean $

    * Casos de uso: uc-02.04.26

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoReceitaExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgAutenticacao = "../autenticacao/FMManterAutenticacao.php";

$stAcao = $request->get('stAcao');

if (!$_REQUEST['inCodBoletim']) {
    SistemaLegado::exibeAviso( 'Não há boletins abertos para esta entidade!'  ,"","erro");
    sistemaLegado::LiberaFrames();
    die;
}
$obErro = new erro;
if (trim($_REQUEST['inCodPlanoDebito']) == '') {
    $obErro->setDescricao("Impossível realizar Arrecadação em Boletim com data anterior a Emissão do Recibo.");
}

if ( $obErro->ocorreu() ) {
    SistemaLegado::exibeAviso( 'Campo Conta Caixa/Bancos inválido!'  ,"","alerta");
} else {

    list($inCodBoletimAberto,$stDtBoletimAberto) = explode ( ':' , $_REQUEST['inCodBoletim']);
    list($stDia, $stMes, $stAno) = explode( '/', $stDtBoletimAberto );

    //valida a utilização da rotina de encerramento do mês contábil
    $boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
    $obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
    $obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
    $obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
    $obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

    if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $stMes) {
        SistemaLegado::exibeAviso(urlencode("Mês do Boletim encerrado!"),"n_incluir","erro");
        sistemaLegado::LiberaFrames();
        exit;
    }
    $obRTesourariaBoletim = new RTesourariaBoletim();
    $obRTesourariaBoletim->setExercicio  ( Sessao::getExercicio() );
    $obRTesourariaBoletim->setCodBoletim ( $inCodBoletimAberto );
    $obRTesourariaBoletim->setDataBoletim( $stDtBoletimAberto );
    $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->setTimestampUsuario( $_POST['stTimestampUsuario'] );
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal( $_POST['inCodTerminal'] );
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal( $_POST['stTimestampTerminal'] );
    $obRTesourariaBoletim->addTransferencia();

    $obRTesourariaConfiguracao = new RTesourariaConfiguracao();
    $obRTesourariaConfiguracao->setExercicio( Sessao::getExercicio() );
    $obRTesourariaConfiguracao->consultarTesouraria();

    $obRTesourariaBoletim->roUltimaTransferencia->setNomContaDebito     ( $_POST['stNomContaDebito']   );
    $obRTesourariaBoletim->roUltimaTransferencia->setNomContaCredito    ( $_POST['stNomContaCredito']  );

    if($request->get('inCodEntidade'))
        $arFiltro['inCodEntidade'] = $_REQUEST['inCodEntidade'];

    $stFiltros  = "?stAcao=".$stAcao."&inCodEntidade=".$_REQUEST['inCodEntidade']."&stNomEntidade=".$_REQUEST['stNomEntidade']."&inCodBoletim=".$_REQUEST['inCodBoletim']."&inCodHistorico=".$_REQUEST['inCodHistorico'];
    $stFiltros .= "&stNomHistorico=".$_REQUEST['stNomHistorico']."&inCodPlanoDebito=".$_REQUEST['inCodPlanoDebito']."&stNomContaDebito=".$_REQUEST['stNomContaDebito'];

    switch ($stAcao) {
        case 'incluir':
            $obErro = new erro;
            
            $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf');
            
            if ($inCodUf == 16) {
                if (isset($_POST['inCodEntidadeTransferidora']) && $_POST['inCodEntidadeTransferidora'] == ''){
                    $obErro->setDescricao("Deve ser informado o  campo Entidade Transferidora!");
                } else if (isset($_POST['inCodTransferencia']) && $_POST['inCodTransferencia'] == '') {
                    $obErro->setDescricao("Deve ser informado o campo Tipo de Transferência!");
                }
            }
            
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setContaDebito   ( $_POST['inCodPlanoDebito']  );
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setContaCredito  ( $_POST['inCodPlanoCredito']  );
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setValor         ( str_replace(',','.',str_replace('.','',$_POST['nuValor'] )));
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( $_POST['inCodHistorico'] );
            $obRTesourariaBoletim->roUltimaTransferencia->setObservacaoTransferencia( $_POST['stObservacoes'] );
            $obRTesourariaBoletim->roUltimaTransferencia->setCodRecurso ( $_POST['inCodRecurso'] );
            $obRTesourariaBoletim->roUltimaTransferencia->setCodCredor  ( $_POST['inCodCredor']  );
            $obRTesourariaBoletim->roUltimaTransferencia->setDestinacaoRecurso  ( $_POST['stDestinacaoRecurso']  );

            $obRTesourariaBoletim->roUltimaTransferencia->setTipoRecibo         ( $_POST['stTipoRecibo']  );
            $obRTesourariaBoletim->roUltimaTransferencia->setCodRecibo          ( $_POST['inCodRecibo']  );
            $obRTesourariaBoletim->roUltimaTransferencia->setTipoTransferencia  ( 2 ); // Arrecadação Extra

            if ($_POST['stDtRecibo'] <> "") {
                if (sistemaLegado::comparaDatas ( $_POST['stDtRecibo'],$stDtBoletimAberto)) {
                    $obErro->setDescricao("Impossível realizar Arrecadação em Boletim com data anterior a Emissão do Recibo.");
                }
            }
            
            if (!$obErro->ocorreu()) {
                if ( $stDtBoletimAberto == date( 'd/m/Y' ) ) {
                    $obRTesourariaBoletim->roUltimaTransferencia->setTimestampTransferencia( date( 'Y-m-d H:i:s.ms' ) );
                } else {
                    list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletimAberto );
                    $obRTesourariaBoletim->roUltimaTransferencia->setTimestampTransferencia( $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms') );
                }

                $obErro = $obRTesourariaBoletim->roUltimaTransferencia->transferir( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ($obRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao()) {
                        Sessao::write('stDescricao', array('stDescricao' => $obRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao()));
                    }
                }
            }
            if ( !$obErro->ocorreu() ) {
                if ( $obRTesourariaConfiguracao->getFormaComprovacao() ) {
                    SistemaLegado::alertaAviso($pgAutenticacao.$stFiltros."&pg_volta=../arrecadacao/".$pgForm,"Lote " . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() . "/" . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ,"incluir","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::alertaAviso($pgForm.$stFiltros,"Lote " . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() . "/" . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ,"incluir","aviso", Sessao::getId(), "../");
                }
            } else {
                $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));
                SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"","erro");
            }

        break;

        case 'alterar': // Estorno
            $obErro = new erro;
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $_POST['inCodLote'] );
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->setContaDebito   ( $_POST['inCodPlanoDebito']  );
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->setContaCredito  ( $_POST['inCodPlanoCredito'] );
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->setValor         ( str_replace(',','.',str_replace('.','',$_POST['nuValorEstorno'] )) );
            $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( $_POST['inCodHistorico'] );
            $obRTesourariaBoletim->roUltimaTransferencia->setObservacaoEstorno( $_POST['stObservacoes'] );
            $obRTesourariaBoletim->roUltimaTransferencia->setTipoTransferencia  ( 2 ); // Arrecadação Extra

            if (sistemaLegado::comparaDatas ($_POST['dtBoletimArrecadacao'], $stDtBoletimAberto)) {
                $obErro->setDescricao("A data do Boletim do Estorno (".$stDtBoletimAberto.") não pode ser inferior a data da Arrecadação (".$_POST['dtBoletimArrecadacao'].").");
            }
            if (!$obErro->ocorreu()) {
                if ( $stDtBoletimAberto == date( 'd/m/Y' ) ) {
                    $obRTesourariaBoletim->roUltimaTransferencia->setTimestampEstornada( date( 'Y-m-d H:i:s.ms' ) );
                } else {
                    list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletimAberto );
                    $obRTesourariaBoletim->roUltimaTransferencia->setTimestampEstornada( $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms') );
                }

                $obErro = $obRTesourariaBoletim->roUltimaTransferencia->estornar( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ($obRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao()) {
                        Sessao::write('stDescricao', array('stDescricao' => $obRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao()));
                    }
                }
            }
            if ( !$obErro->ocorreu() ) {
                if ( $obRTesourariaConfiguracao->getFormaComprovacao() ) {
                    Sessao::write('filtro',$arFiltro);
                    SistemaLegado::alertaAviso($pgAutenticacao."?pg_volta=../arrecadacao/".$pgList.'?'.Sessao::getId().'&stAcao='.$stAcao,"Lote " . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() . "/" . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ,"excluir","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId().'&stAcao='.$stAcao,"Lote " . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() . "/" . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ,"excluir","aviso", Sessao::getId(), "../");
                }
            } else {
                $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));
                SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"","erro");
            }

        break;
    }
}
sistemaLegado::LiberaFrames();
?>
