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
    * Página de Formulário para configuração
    * Data de Criação   : 30/07/2008

    * @author Diego Barbosa Victoria

    * $Id: FMManterConfiguracao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.04.08
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( TALM."TAlmoxarifadoConfiguracao.class.php"                             );

$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//include_once ( $pgOcul );

$obTConfiguracao = new TAlmoxarifadoConfiguracao();
$obTConfiguracao->setDado("parametro","numeracao_lancamento_estoque");
$obTConfiguracao->recuperaPorChave($rsConfiguracao);
$stNumeracao = $rsConfiguracao->getCampo('valor');

$obTConfiguracao = new TAlmoxarifadoConfiguracao();
$obTConfiguracao->setDado("parametro","demonstrar_saldo_estoque");
$obTConfiguracao->recuperaPorChave($rsConfiguracao);
$stDemonstrarSaldo = $rsConfiguracao->getCampo('valor');

$obTConfiguracao = new TAlmoxarifadoConfiguracao();
$obTConfiguracao->setDado("parametro","anular_saldo_pendente");
$obTConfiguracao->recuperaPorChave($rsConfiguracao);
$stAnularSaldo = $rsConfiguracao->getCampo('valor');

$obTConfiguracao = new TAlmoxarifadoConfiguracao();
$obTConfiguracao->setDado("parametro","homologacao_automatica_requisicao");
$obTConfiguracao->recuperaPorChave($rsConfiguracao);
$stHomologacaoAutomatica = $rsConfiguracao->getCampo('valor');

//$jsOnLoad = "executaFuncaoAjax('recuperaFormularioAlteracao')";
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if ($inCodigo) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obNumeracaoGeral = new Radio;
$obNumeracaoGeral->setName('stNumeracao');
$obNumeracaoGeral->setRotulo('Numeração de Entradas/Saídas no Estoque');
$obNumeracaoGeral->setTitle('Define como serão numeradas as entradas e saídas no estoque.');
$obNumeracaoGeral->setLabel('Geral');
$obNumeracaoGeral->setValue('G');
$obNumeracaoGeral->setChecked( $stNumeracao=='G'?true:false );

$obNumeracaoNatureza = new Radio;
$obNumeracaoNatureza->setName('stNumeracao');
//$obNumeracaoNatureza->setRotulo('Numeração de Entradas/Saídas no Estoque');
//$obNumeracaoNatureza->setTitle('Define como serão numeradas as entradas e saídas no estoque.');
$obNumeracaoNatureza->setLabel('Por Natureza');
$obNumeracaoNatureza->setValue('N');
$obNumeracaoNatureza->setChecked( $stNumeracao=='N'||empty($stNumeracao)?true:false );

$obDemonstrarSaldoSim = new Radio;
$obDemonstrarSaldoSim->setName('stDemonstrarSaldo');
$obDemonstrarSaldoSim->setRotulo('Demonstrar Saldo na Requisição');
$obDemonstrarSaldoSim->setTitle('Demonstrar o Saldo em Estoque ao efetuar a Requisição.');
$obDemonstrarSaldoSim->setLabel('Sim');
$obDemonstrarSaldoSim->setValue('true');
$obDemonstrarSaldoSim->setChecked( $stDemonstrarSaldo == 'true'?true:false );

$obDemonstrarSaldoNao = new Radio;
$obDemonstrarSaldoNao->setName('stDemonstrarSaldo');
$obDemonstrarSaldoNao->setLabel('Não');
$obDemonstrarSaldoNao->setValue('false');
$obDemonstrarSaldoNao->setChecked( $stDemonstrarSaldo == 'false' || empty($stDemonstrarSaldo)?true:false );

$obAnularSaldoPendenteSim = new Radio;
$obAnularSaldoPendenteSim->setName('stAnularSaldo');
$obAnularSaldoPendenteSim->setRotulo('Anular Saldo Pendente');
$obAnularSaldoPendenteSim->setTitle('Anular saldo pendente ao efetuar a saída do estoque.');
$obAnularSaldoPendenteSim->setLabel('Sim');
$obAnularSaldoPendenteSim->setValue('true');
$obAnularSaldoPendenteSim->setChecked( $stAnularSaldo == 'true'?true:false );

$obAnularSaldoPendenteNao = new Radio;
$obAnularSaldoPendenteNao->setName('stAnularSaldo');
$obAnularSaldoPendenteNao->setLabel('Não');
$obAnularSaldoPendenteNao->setValue('false');
$obAnularSaldoPendenteNao->setChecked( $stAnularSaldo == 'false' || empty($stAnularSaldo)?true:false );

$obHomologacaoAutomaticaSim = new Radio;
$obHomologacaoAutomaticaSim->setName('stHomologacaoAutomatica');
$obHomologacaoAutomaticaSim->setRotulo('Homologação Automática');
$obHomologacaoAutomaticaSim->setTitle('Homologar automaticamente a requisição para retirada de itens do estoque.');
$obHomologacaoAutomaticaSim->setLabel('Sim');
$obHomologacaoAutomaticaSim->setValue('true');
$obHomologacaoAutomaticaSim->setChecked( $stHomologacaoAutomatica == 'true'?true:false );

$obHomologacaoAutomaticaNao = new Radio;
$obHomologacaoAutomaticaNao->setName('stHomologacaoAutomatica');
$obHomologacaoAutomaticaNao->setLabel('Não');
$obHomologacaoAutomaticaNao->setValue('false');
$obHomologacaoAutomaticaNao->setChecked( $stHomologacaoAutomatica == 'false' || empty($stHomologacaoAutomatica)?true:false );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                   );
$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addTitulo        ( "Dados de Configuração"   );

$obFormulario->agrupaComponentes(array($obNumeracaoGeral, $obNumeracaoNatureza));
$obFormulario->agrupaComponentes(array($obDemonstrarSaldoSim, $obDemonstrarSaldoNao));
$obFormulario->agrupaComponentes(array($obAnularSaldoPendenteSim, $obAnularSaldoPendenteNao));
$obFormulario->agrupaComponentes(array($obHomologacaoAutomaticaSim, $obHomologacaoAutomaticaNao));

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
