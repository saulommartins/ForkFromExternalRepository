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
    * Data de Criação   : 04/09/2006

    * @author Analista: CLeisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 32235 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
include_once(CAM_GF_TES_NEGOCIO."RTesourariaCheque.class.php" );
include_once(CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterPagamentoExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgAutenticacao = "../autenticacao/FMManterAutenticacao.php";

list($inCodBoletimAberto,$stDtBoletimAberto) = explode ( ':' , $_REQUEST['inCodBoletim']);
$stDtBoletim = $stDtBoletimAberto;

list($stDia, $stMes, $stAno) = explode( '/', $stDtBoletim );

//valida a utilização da rotina de encerramento do mês contábil
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $stMes) {
    SistemaLegado::exibeAviso(urlencode("Mês do Boletim encerrado!"),"n_incluir","erro");
    SistemaLegado::LiberaFrames();
    exit;
}

$obErro = new Erro;

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

//pegando a data do boletim para enviar de volta ao formulario
$stFiltros  = "?stAcao=".$stAcao."&inCodEntidade=".$_REQUEST['inCodEntidade']."&stNomEntidade=".$_REQUEST['stNomEntidade']
            ."&inCodBoletim=".$_REQUEST['inCodBoletim']."&stDtBoletim=".$stDtBoletim."&inCodHistorico=".$_REQUEST['inCodHistorico']
            ."&stNomHistorico=".$_REQUEST['stNomHistorico']."&inCodPlanoCredito=".$_REQUEST['inCodPlanoCredito']."&stNomContaCredito=".$_REQUEST['stNomContaCredito'];

//Implementacao do pagamento extra via cheque
$arCheques = Sessao::read('arCheques');
if (is_array($arCheques)) {
    foreach ($arCheques as $arCheque) {
        if (SistemaLegado::comparaDatas($arCheque['data_emissao'],$stDtBoletim)) {
            $obErro->setDescricao("Data do(s) cheque(s) igual ou superior a data do pagamento");
        }
    }
}

switch ($stAcao) {
    case 'incluir':
        
        $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf');
        
        if ($inCodUf == 16) {
            if (isset($_POST['inCodEntidadeBeneficio']) && $_POST['inCodEntidadeBeneficio'] == '') {
                SistemaLegado::LiberaFrames(true,false);
                $obErro->setDescricao("Deve ser informado o campo Entidade Beneficiada!");
            } else if (isset($_POST['inCodTransferencia']) && $_POST['inCodTransferencia'] == ''){
                SistemaLegado::LiberaFrames(true,false);
                $obErro->setDescricao("Deve ser informado o campo Tipo de Transferência!");
            }
        }
        
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio ( Sessao::getExercicio() );
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade ( $_POST['inCodEntidade'] );
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setContaDebito ( $_POST['inCodPlanoDebito']  );
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setContaCredito ( $_POST['inCodPlanoCredito']  );
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setValor ( str_replace(',','.',str_replace('.','',$_POST['nuValor'] )));
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico ( $_POST['inCodHistorico'] );
        $obRTesourariaBoletim->roUltimaTransferencia->setObservacaoTransferencia ( $_POST['stObservacoes'] );
        $obRTesourariaBoletim->roUltimaTransferencia->setCodRecurso ( $_POST['inCodRecurso'] );
        $obRTesourariaBoletim->roUltimaTransferencia->setDestinacaoRecurso ( $_POST['stDestinacaoRecurso']  );
        $obRTesourariaBoletim->roUltimaTransferencia->setCodCredor ( $_POST['inCodCredor']  );
        $obRTesourariaBoletim->roUltimaTransferencia->setTipoRecibo ( $_POST['stTipoRecibo']  );
        $obRTesourariaBoletim->roUltimaTransferencia->setCodRecibo ( $_POST['inCodRecibo']  );
        $obRTesourariaBoletim->roUltimaTransferencia->setTipoTransferencia ( 1 ); // Pagamento Extra
        
        if ($_POST['stDtRecibo'] <> "") {
            
            if (sistemaLegado::comparaDatas ($_POST['stDtRecibo'], $stDtBoletim )) {
                $obErro->setDescricao("Impossível realizar Pagamento em Boletim com data anterior a Emissão do Recibo.");
            }
            
        }
        
        if (!$obErro->ocorreu()) {
            
            if ( $stDtBoletim == date( 'd/m/Y' ) ) {
                $obRTesourariaBoletim->roUltimaTransferencia->setTimestampTransferencia( date( 'Y-m-d H:i:s.ms' ) );
            } else {
                list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletim );
                $obRTesourariaBoletim->roUltimaTransferencia->setTimestampTransferencia( $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms') );
            }
            
            $obErro = $obRTesourariaBoletim->roUltimaTransferencia->transferir( $boTransacao );
            
            if ( !$obErro->ocorreu() ) {
                if ($obRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao()) {
                    $arDescricao = Sessao::read('stDescricao');
                    $inCount = count($arDescricao);
                    if ($inCount > 0) {
                        $arDescricao[$inCount]['stDescricao'] = $obRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao();
                    } else {
                        $arDescricao['stDescricao'] = $obRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao();
                    }
                    Sessao::write('stDescricao', $arDescricao);
                }
                
                //Faz a baixa do cheque caso exista algum vinculado a ele.
                if (is_array($arCheques)) {
                    $obRTesourariaCheque = new RTesourariaCheque();
                    foreach ($arCheques as $arCheque) {
                        $obRTesourariaCheque->stNumCheque                                                 = $arCheque['num_cheque'        ];
                        $obRTesourariaCheque->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco = $arCheque['cod_banco'         ];
                        $obRTesourariaCheque->obRMONContaCorrente->obRMONAgencia->inCodAgencia            = $arCheque['cod_agencia'       ];
                        $obRTesourariaCheque->obRMONContaCorrente->inCodigoConta                          = $arCheque['cod_conta_corrente'];
                        $obErro = $obRTesourariaCheque->baixarChequeEmissao($boTransacao);
                    }
                }
            }
        }
        
        if ( !$obErro->ocorreu() ) {
            
            if ( $obRTesourariaConfiguracao->getFormaComprovacao() ) {
                SistemaLegado::alertaAviso($pgAutenticacao.$stFiltros."&pg_volta=../pagamentos/".$pgForm,"Lote " . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() . "/" . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ,"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($pgForm.$stFiltros,"Lote " . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() . "/" . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ,"incluir","aviso", Sessao::getId(), "../");
            }
            
        } else {
            $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));
            SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"","erro");
        }

    break;

    case 'alterar': // Estorno
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $_POST['inCodLote'] );
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->setContaDebito   ( $_POST['inCodPlanoDebito']  );
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->setContaCredito  ( $_POST['inCodPlanoCredito'] );
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->setValor         ( str_replace(',','.',str_replace('.','',$_POST['nuValorEstorno'] )) );
        $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( $_POST['inCodHistorico'] );
        $obRTesourariaBoletim->roUltimaTransferencia->setObservacaoEstorno( $_POST['stObservacoes'] );
        $obRTesourariaBoletim->roUltimaTransferencia->setTipoTransferencia  ( 1 ); // Pagamento Extra

        if (sistemaLegado::comparaDatas ($_POST['dtBoletimPagamento'],  $stDtBoletim)) {
            $obErro->setDescricao("A data do Boletim do Estorno (".$stDtBoletim.") não pode ser inferior a data do Pagamento (".$_POST['dtBoletimPagamento'].")");
        }
        
        if (!$obErro->ocorreu()) {
            
            if ( $stDtBoletim == date( 'd/m/Y' ) ) {
                $obRTesourariaBoletim->roUltimaTransferencia->setTimestampEstornada( date( 'Y-m-d H:i:s.ms' ) );
            } else {
                list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletim );
                $obRTesourariaBoletim->roUltimaTransferencia->setTimestampEstornada( $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms') );
            }
            
            $obErro = $obRTesourariaBoletim->roUltimaTransferencia->estornar( $boTransacao );
            
            if (!$obErro->ocorreu()) {
                
                if ($obRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao()) {
                    $arDescricao = Sessao::read('stDescricao');
                    $inCount = count($arDescricao);
                    
                    if ($inCount > 0) {
                        $arDescricao[$inCount]['stDescricao'] = $obRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao();
                    } else {
                        $arDescricao['stDescricao'] = $obRTesourariaBoletim->roUltimaTransferencia->obRTesourariaAutenticacao->getDescricao();
                    }
                    
                    Sessao::write('stDescricao', $arDescricao);
                    
                }
                
                //Faz a anulacao da baixa do cheque caso exista algum vinculado a ele.
                if (is_array($arCheques)) {
                    $obRTesourariaCheque = new RTesourariaCheque();
                    
                    foreach ($arCheques as $arCheque) {
                        $obRTesourariaCheque->stNumCheque                                                 = $arCheque['num_cheque'        ];
                        $obRTesourariaCheque->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco = $arCheque['cod_banco'         ];
                        $obRTesourariaCheque->obRMONContaCorrente->obRMONAgencia->inCodAgencia            = $arCheque['cod_agencia'       ];
                        $obRTesourariaCheque->obRMONContaCorrente->inCodigoConta                          = $arCheque['cod_conta_corrente'];
                        $obErro = $obRTesourariaCheque->anularBaixaChequeEmissao($boTransacao);
                    }
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            if( $obRTesourariaConfiguracao->getFormaComprovacao() ) {
                SistemaLegado::alertaAviso($pgAutenticacao."?pg_volta=../pagamentos/".$pgFilt,"Lote " . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() . "/" . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ,"excluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($pgFilt,"Lote " . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() . "/" . $obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValorEstornada->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() ,"excluir","aviso", Sessao::getId(), "../");
            }
        } else {
            $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));
            SistemaLegado::exibeAviso(urlencode("Erro ao executar ação ".$nomAcao.": ".$obErro->getDescricao()),"","erro");
        }
        
    break;
}

?>
