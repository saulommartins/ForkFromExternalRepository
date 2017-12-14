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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 13/09/2007

    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 26518 $
    $Name$
    $Autor: $
    $Date: 2007-10-30 12:26:25 -0200 (Ter, 30 Out 2007) $

    * Casos de uso: uc-03.05.31
*/

/*
$Log$
Revision 1.1  2007/09/19 14:56:49  bruce
Ticket#10105#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_COM_COMPONENTES."IPopUpFornecedor.class.php"             );
include_once ( CAM_GP_LIC_COMPONENTES."IPopUpContrato.class.php"               );
include_once ( CAM_GP_COM_COMPONENTES."IPopUpObjeto.class.php"                 );
include_once ( CAM_GF_ORC_MAPEAMENTO ."TOrcamentoOrgao.class.php"              );
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeGeral.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Contrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//DEFINICAO DOS COMPONENTES DO FORMULARIO

$obForm = new Form;
$obForm->setAction ( "OCRelatorio".$stPrograma.".php" );
$obForm->setTarget ( 'telaPrincipal'     );

$obHdnStCtrl = new Hidden;
$obHdnStCtrl->setName ( "stCtrl" );
$obHdnStCtrl->setValue( $stCtrl );

$obHdnAcao = new Hidden();
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obISelectMultiploEntidadeGeral = new ISelectMultiploEntidadeGeral;
$obISelectMultiploEntidadeGeral->setNull ( true );

$obFornecedor = new IPopUpFornecedor($obForm);
$obFornecedor->setId ( "stNomFornecedor" );
$obFornecedor->setTitle( "Selecione o Fornecedor." );

$obContrato = new IPopUpContrato( $obForm );

$obObjeto = new IPopUpObjeto($obForm);
$obObjeto->setRotulo ("Objeto");

$obPeriodicidade = new Periodicidade;
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setIdComponente ('Assinatura');
$obPeriodicidade->setValue          ( 4                 );
$obPeriodicidade->setValidaExercicio( true              );
$obPeriodicidade->obDataInicial->setName    ( "stDtInicial" );
$obPeriodicidade->obDataFinal->setName      ( "stDtFinal" );
$obPeriodicidade->setRotulo('Assinatura');

$obPeriodicidadeInicialExecucao = new Periodicidade;
$obPeriodicidadeInicialExecucao->setExercicio      ( Sessao::getExercicio());
$obPeriodicidadeInicialExecucao->setValue          ( 4                 );
$obPeriodicidadeInicialExecucao->setValidaExercicio( true              );
$obPeriodicidadeInicialExecucao->setIdComponente ('InicioExec');
$obPeriodicidadeInicialExecucao->obDataInicial->setName    ( "stDtInicial" );
$obPeriodicidadeInicialExecucao->obDataFinal->setName      ( "stDtFinal" );
$obPeriodicidadeInicialExecucao->setRotulo('Inicio de Execução');

$obPeriodicidadeFimExecucao = new Periodicidade;
$obPeriodicidadeFimExecucao->setExercicio      ( Sessao::getExercicio());
$obPeriodicidadeFimExecucao->setValue          ( 4                 );
$obPeriodicidadeFimExecucao->setValidaExercicio( true              );
$obPeriodicidadeFimExecucao->setIdComponente ('FimExec');
$obPeriodicidadeFimExecucao->obDataInicial->setName    ( "stDtInicial" );
$obPeriodicidadeFimExecucao->obDataFinal->setName      ( "stDtFinal");
$obPeriodicidadeFimExecucao->setRotulo('Fim de Execução');

$obTOrcamentoOrgao = new TOrcamentoOrgao;
$stFiltro = "and OO.exercicio = '" . Sessao::getExercicio() ."'";
$obTOrcamentoOrgao->recuperaRelacionamento( $rsOrgao, $stFiltro );

$rsRecordSet = new RecordSet;

$obSelOrgao = new SelectMultiplo();
$obSelOrgao->setName             ( 'inCodOrgao'                         );
$obSelOrgao->setRotulo           ( "Orgãos"                             );
$obSelOrgao->setTitle            ( "Selecione um ou mais orgãos para o filtro " );

$obSelOrgao->SetNomeLista1       ( 'inCodOrgaoDisponiveis'      );
$obSelOrgao->setCampoId1         ( 'num_orgao'                  );
$obSelOrgao->setCampoDesc1       ( 'nom_orgao'                  );
$obSelOrgao->setStyle1           ( "width: 300px"               );
$obSelOrgao->SetRecord1          ( $rsOrgao                     );

$obSelOrgao->SetNomeLista2       ( 'inCodOrgaoSelecionados'     );
$obSelOrgao->setCampoId2         ( 'num_orgao'                  );
$obSelOrgao->setCampoDesc2       ( 'nom_orgao'                  );
$obSelOrgao->setStyle2           ( "width: 300px"               );
$obSelOrgao->SetRecord2          ( $rsRecordSet                 );

$obSNAnuladosRescindidos = new SimNao;
$obSNAnuladosRescindidos->setRotulo ( 'Demonstrar Anulados/Rescindindos' );
$obSNAnuladosRescindidos->setName   ( 'snAnulados'                       );
$obSNAnuladosRescindidos->setId     ( 'snAnulados'                       );

$obRadTipoContratoTodos = new Radio;
$obRadTipoContratoTodos->setRotulo('Tipo de Contrato');
$obRadTipoContratoTodos->setId('rdTodos');
$obRadTipoContratoTodos->setName('tipoContrato');
$obRadTipoContratoTodos->setNull(false);
$obRadTipoContratoTodos->setChecked(true);
$obRadTipoContratoTodos->setLabel('Todos');
$obRadTipoContratoTodos->setValue('todos');
$obRadTipoContratoTodos->setTitle( 'Selecione o tipo de contrato' );

$obRadTipoContratoCompraDireta = new Radio;
$obRadTipoContratoCompraDireta->setId('rdCompraDireta');
$obRadTipoContratoCompraDireta->setName('tipoContrato');
$obRadTipoContratoCompraDireta->setNull(false);
$obRadTipoContratoCompraDireta->setChecked(false);
$obRadTipoContratoCompraDireta->setLabel('Compra Direta');
$obRadTipoContratoCompraDireta->setValue('compraDireta');
$obRadTipoContratoCompraDireta->setTitle( 'Selecione o tipo de contrato' );

$obRadTipoContratoLicitacao = new Radio;
$obRadTipoContratoLicitacao->setId('rdLicitacao');
$obRadTipoContratoLicitacao->setName('tipoContrato');
$obRadTipoContratoLicitacao->setNull(false);
$obRadTipoContratoLicitacao->setChecked(false);
$obRadTipoContratoLicitacao->setLabel('Licitação');
$obRadTipoContratoLicitacao->setValue('licitacao');
$obRadTipoContratoLicitacao->setTitle( 'Selecione o tipo de contrato' );

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnStCtrl );
$obFormulario->addHidden( $obHdnAcao );

$obContrato->geraFormulario  ( $obFormulario                   );
$obFormulario->addComponente ( $obISelectMultiploEntidadeGeral );
$obFormulario->addComponente ( $obSelOrgao                     );
$obFormulario->addComponente ( $obFornecedor                   );
$obFormulario->addComponente ( $obObjeto                       );
$obFormulario->addComponente ( $obPeriodicidade                );
$obFormulario->addComponente ( $obPeriodicidadeInicialExecucao );
$obFormulario->addComponente ( $obPeriodicidadeFimExecucao     );
$obFormulario->addComponente ( $obSNAnuladosRescindidos        );
$obFormulario->agrupaComponentes ( array($obRadTipoContratoTodos,$obRadTipoContratoCompraDireta,$obRadTipoContratoLicitacao ));

$obFormulario->Ok();
$obFormulario->Show();

?>
