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
  * Página de Formulario de Configuração de Consideracoes dos Arquivos
  * Data de Criação: 25/02/2014

  * @author Analista:      Sergio Santos
  * @author Desenvolvedor: Lisiane Morais
  *
  * @ignore
  * $Id: FMManterConsideracao.php 64510 2016-03-08 14:05:56Z jean $
  * $Date: $
  * $Author: $
  * $Rev: $
  *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConsideracao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once ($pgJs);

$rsEntidades = new RecordSet();
$boTransacao = new Transacao();

$stAcao   = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );
$obForm->setName('frm');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "" );

$obSpnCodigos = new Span();
$obSpnCodigos->setId('spnCodigos');

$obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidadeGeral->setNull(false);
$obITextBoxSelectEntidadeGeral->obSelect->obEvento->setOnChange(" limpaSpan();");
$obITextBoxSelectEntidadeGeral->obTextBox->obEvento->setOnChange(" limpaSpan();");

$obSlcTipoExportacao = new Select();
$obSlcTipoExportacao->setRotulo("Módulo de exportação");
$obSlcTipoExportacao->setId('stTipoExportacao');
$obSlcTipoExportacao->setNull(false);
$obSlcTipoExportacao->setName('stTipoExportacao');
$obSlcTipoExportacao->addOption('mensal','Acompanhamento Mesal');
$obSlcTipoExportacao->addOption('planejamento','Arquivos Planejamento');
$obSlcTipoExportacao->addOption('balancete','Balancete Contabil');
$obSlcTipoExportacao->addOption('inclusao',' Inclusão Programas');
$obSlcTipoExportacao->addOption('folha',' Folha de Pagamento');
$obSlcTipoExportacao->obEvento->setOnChange(" document.getElementById('inMes').value = ''; ");

$obPeriodoMes = new Mes;
$obPeriodoMes->obMes->setId ('inMes');
$obPeriodoMes->setExercicio ( Sessao::getExercicio() );
$obPeriodoMes->setNull      ( false );
$obPeriodoMes->obMes->obEvento->setOnChange ("if ( validaCampos() ){ montaParametrosGET('montaSpanCodigos'); } ");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Considerações por arquivo" );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addComponente ( $obITextBoxSelectEntidadeGeral );
$obFormulario->addComponente ( $obSlcTipoExportacao );
$obFormulario->addComponente ( $obPeriodoMes );
$obFormulario->addSpan       ( $obSpnCodigos);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
