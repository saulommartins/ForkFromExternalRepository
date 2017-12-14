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
    * Página de Filtro para Relatório Despesas SIOPS
    * Data de Criação  : 12/06/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso : uc-02.01.01

    * $Id: FLDespesasSIOPS.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );
include_once CAM_GF_ORC_COMPONENTES."ISelectOrgao.class.php";

$stPrograma = "DespesasSIOPS";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma."php";

Sessao::remove('filtroRelatorio');

$obForm = new Form;
$obForm->setTarget ( 'telaPrincipal' );
$obForm->setAction ( 'OCGeraDespesasSIOPS.php' );

//Definição dos componentes
$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval"            );
$obHdnEval->setValue ( $stEval             );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnStCtrl = new Hidden;
$obHdnStCtrl->setName ( "stCtrl" );
$obHdnStCtrl->setValue( $stCtrl );

$stJs = "montaParametrosGET( 'inCodEntidade, stCodEntidade, inCodOrgao, stAcao' )";

$obISelectEntidade = new ISelectMultiploEntidadeUsuario();
$obISelectEntidade->SetNomeLista2("inCodEntidade");

$rsOrgao = new RecordSet;
require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php" ;
$obTOrcamentoOrgao = new TOrcamentoOrgao;
$obTOrcamentoOrgao->setDado('exercicio', Sessao::getExercicio() );
$obTOrcamentoOrgao->recuperaDadosExercicio( $rsOrgao, '', 'orcamento.orgao.num_orgao' );

$obInCodOrgao = new SelectMultiplo();
$obInCodOrgao->setRotulo    ( 'Órgao' );
$obInCodOrgao->setTitle     ( 'Selecione o órgao.' );
$obInCodOrgao->setNull      ( false );

$obInCodOrgao->SetNomeLista1 ('inCodOrgaoDisponivel');
$obInCodOrgao->setCampoId1   ( 'num_orgao' );
$obInCodOrgao->setCampoDesc1 ( '[num_orgao] - [nom_orgao]' );
$obInCodOrgao->SetRecord1 ( $rsOrgao );

$obInCodOrgao->SetNomeLista2 ('inCodOrgao');
$obInCodOrgao->setCampoId2   ('num_orgao');
$obInCodOrgao->setCampoDesc2 ('[num_orgao] - [nom_orgao]');
$obInCodOrgao->SetRecord2 ( new RecordSet );

$obCmbTipoRelatorio = new Select;
$obCmbTipoRelatorio->setRotulo        ( "Período"      );
$obCmbTipoRelatorio->setName          ( "stTipoRelatorio"             );
$obCmbTipoRelatorio->addOption        ( "", "Selecione"               );
$obCmbTipoRelatorio->addOption        ( "Bimestre", "Bimestre"        );
$obCmbTipoRelatorio->addOption        ( "Trimestre", "Trimestre"      );
$obCmbTipoRelatorio->addOption        ( "Semestre", "Semestre"        );
$obCmbTipoRelatorio->setNull          ( false                         );
$obCmbTipoRelatorio->setStyle         ( "width: 220px"                );
$obCmbTipoRelatorio->obEvento->setOnChange ( "montaParametrosGET( 'preencheSpan' );"  );

$obSpnTipoRelatorio = new Span();
$obSpnTipoRelatorio->setId( 'spnTipoRelatorio' );

// Define Objeto Radio Para Tipo de conta
$obRdEstiloContaSintetica = new Radio;
$obRdEstiloContaSintetica->setName   ( "stEstiloConta"  );
$obRdEstiloContaSintetica->setId     ( "stEstiloContaAnalitica"  );
$obRdEstiloContaSintetica->setValue  ( "S"              );
$obRdEstiloContaSintetica->setRotulo ( "*Tipo de Relatório" );
$obRdEstiloContaSintetica->setLabel  ("Sintético"       );

$obRdEstiloContaAnalitica = new Radio;
$obRdEstiloContaAnalitica->setName ( "stEstiloConta" );
$obRdEstiloContaAnalitica->setId   ( "stEstiloContaSintetica" );
$obRdEstiloContaAnalitica->setValue( "A"           );
$obRdEstiloContaAnalitica->setLabel( "Analítico"   );
$obRdEstiloContaAnalitica->setChecked( true );

include_once CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obISelectEntidade );

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnStCtrl );
$obFormulario->addTitulo( "Dados para o filtro" );
$obFormulario->addComponente( $obISelectEntidade );
$obFormulario->addComponente( $obInCodOrgao );
$obFormulario->addComponente ( $obCmbTipoRelatorio );
$obFormulario->addSpan( $obSpnTipoRelatorio );
$obFormulario->agrupaComponentes( array($obRdEstiloContaSintetica, $obRdEstiloContaAnalitica) );

$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
