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
    * Filtro para consultar saldos

    * Data de Criação: 04/07/2008

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * $Id: FMConsultarSaldo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.40
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";
require_once CAM_GF_TES_NEGOCIO."RTesourariaSaldoTesouraria.class.php";
require_once CAM_GF_TES_MAPEAMENTO."FTesourariaExtratoBancario.class.php";

//Define o nome dos arquivos PHP
$stPrograma      = "ConsultarSaldo";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

// DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( $pgFilt );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl"            );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

// Monta Label contendo as informações da Entidade
$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
$obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

$obLblEntidade = new Label;
$obLblEntidade->setId('entidade');
$obLblEntidade->setName('entidade');
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setValue($_REQUEST['inCodEntidade']." - ".$rsEntidade->getCampo('nom_cgm'));

// Monta Label contendo as informações da Conta
$obRegra = new RTesourariaSaldoTesouraria();
$obRegra->obRContabilidadePlanoBanco->setCodPlano ( $_REQUEST["inCodConta"] );
$obRegra->obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$obRegra->obRContabilidadePlanoBanco->setDtSaldo( $_REQUEST['dtSaldo'] );
$obRegra->obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade ( $_REQUEST["inCodEntidade"]);
$obRegra->obRContabilidadePlanoBanco->consultar();

$obLblConta = new Label;
$obLblConta->setId('conta');
$obLblConta->setName('conta');
$obLblConta->setRotulo('Conta');
$obLblConta->setValue($_REQUEST['inCodConta']." - ".$obRegra->obRContabilidadePlanoBanco->getNomConta());

// Monta Label contendo aa data do saldo
$obLblDtSaldo = new Label;
$obLblDtSaldo->setId('dtSaldo');
$obLblDtSaldo->setName('dtSaldo');
$obLblDtSaldo->setRotulo('Data do Saldo');
$obLblDtSaldo->setValue($_REQUEST['dtSaldo']);

// Busca o saldo atual
$obFTesourariaExtratoBancario = new FTesourariaExtratoBancario;
$obFTesourariaExtratoBancario->setDado("inCodPlano"    , $_REQUEST['inCodConta'] );
$obFTesourariaExtratoBancario->setDado("stExercicio"   , Sessao::getExercicio());
$obFTesourariaExtratoBancario->setDado("stDtInicial" , '01/01/'.Sessao::getExercicio());
$obFTesourariaExtratoBancario->setDado("stDtFinal"   , $_REQUEST['dtSaldo']);
$obFTesourariaExtratoBancario->setDado("boMovimentacao", "true" );
$obErro = $obFTesourariaExtratoBancario->recuperaSaldoAnteriorAtual( $rsSaldoAnteriorAtual, $stFiltro, $stOrder );
$saldoAtual = $rsSaldoAnteriorAtual->getCampo("fn_saldo_conta_tesouraria");

// Monta Label contendo o valor do saldo até a data indicada
$obLblVlSaldo = new Label;
$obLblVlSaldo->setId('vlSaldo');
$obLblVlSaldo->setName('vlSaldo');
$obLblVlSaldo->setRotulo('Saldo');
$obLblVlSaldo->setValue(number_format($saldoAtual, 2, ',', '.'));

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm               );
$obFormulario->addHidden    ( $obHdnCtrl            );
$obFormulario->addHidden    ( $obHdnAcao            );
$obFormulario->addTitulo    ( "Dados do Valor do Saldo" );
$obFormulario->addComponente($obLblEntidade);
$obFormulario->addComponente($obLblConta);
$obFormulario->addComponente($obLblDtSaldo);
$obFormulario->addComponente($obLblVlSaldo);

$obVoltar = new Voltar;
$obFormulario->defineBarra( array($obVoltar) );
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
