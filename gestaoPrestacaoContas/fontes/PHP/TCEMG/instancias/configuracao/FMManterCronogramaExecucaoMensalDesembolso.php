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
  * Página de Filtro de Configuração de Cronograma de Execucao Mensal de Desembolso 
  * Data de Criação   : 29/02/2016

  * @author Analista      Ane Caroline
  * @author Desenvolvedor Lisiane Morais

  * @package URBEM
  * @subpackage

  * $Id:$
  * $Date: $
  * $Author: $
  * $Rev: $
  *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );
require_once CAM_GF_ORC_COMPONENTES."ISelectOrgao.class.php";
require_once CAM_GF_ORC_COMPONENTES."ISelectUnidade.class.php";


//Define o nome dos arquivos PHP
$stPrograma = "ManterCronogramaExecucaoMensalDesembolso";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once ($pgJs);

$rsEntidades           = new RecordSet();
$boTransacao           = new Transacao();

$stAcao   = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "telaPrincipal" );
$obForm->setName('frm');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "" );

$obSpnGruposDespesa = new Span();
$obSpnGruposDespesa->setId('spnGruposDespesa');

$obHdnCodUnidade = new Hidden;
$obHdnCodUnidade->setName( "inCodigosUnidade" );
$obHdnCodUnidade->setValue( "" );

$obHdnVlSaldoTotal = new Hidden;
$obHdnVlSaldoTotal->setName( "hdnVlSaldoTotal" );
$obHdnVlSaldoTotal->setId( "hdnVlSaldoTotal" );

$obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidadeGeral->setNull(false);
$obITextBoxSelectEntidadeGeral->obSelect->obEvento->setOnChange(" limpaSpan();");
$obITextBoxSelectEntidadeGeral->obTextBox->obEvento->setOnChange(" limpaSpan();");

$obInCodOrgao = new ISelectOrgao;
$obInCodOrgao->setExercicio( Sessao::getExercicio() );
$obInCodOrgao->setNull(false);
$obInCodOrgao->obEvento->setOnChange("montaParametrosGET( 'montaDadosUnidade', this.name, true ); ".$stJs);

$obInCodUnidade = new ISelectUnidade;
$obInCodUnidade->setExercicio( Sessao::getExercicio() );
$obInCodUnidade->setNull(false);
$obInCodUnidade->obEvento->setOnChange("montaParametrosGET( 'montaSpanGruposDespesa'); ");



//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm             );
$obFormulario->addTitulo     ( "Grupos de Despesa" );
$obFormulario->addHidden     ( $obHdnCtrl          );
$obFormulario->addHidden     ( $obHdnAcao          );
$obFormulario->addHidden     ( $obHdnCodUnidade    );
$obFormulario->addHidden     ( $obHdnVlSaldoTotal  );

$obFormulario->addComponente ( $obITextBoxSelectEntidadeGeral );
$obFormulario->addComponente ( $obInCodOrgao                  );
$obFormulario->addComponente ( $obInCodUnidade                );
$obFormulario->addSpan       ( $obSpnGruposDespesa            );

$obOk = new Ok;
$obOk->setDisabled(true);
$obLimpar = new Limpar;
$obLimpar->obEvento->setOnClick('LimparForm();');
$obFormulario->defineBarra(array($obOk, $obLimpar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
