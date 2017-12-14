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
    * Página de Filtro de Relatório de Despesas Mensais Fixas
    * Data de Criação : 04/09/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-01-02 08:44:54 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso: uc-02.03.33
*/

/**

$Log$
Revision 1.3  2007/04/05 20:26:15  luciano
#8853#

Revision 1.2  2006/09/08 10:23:00  tonismar
relatório de despesas fixas

Revision 1.1  2006/09/05 11:50:26  tonismar
desenvolvendo relatório de despesas fixas

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php" );
include_once( CAM_GF_ORC_COMPONENTES."IPopUpDotacao.class.php" );
include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoTipoDespesaFixa.class.php" );
include_once( CAM_GF_EMP_COMPONENTES."IPopUpCredor.class.php" );

$stPrograma = "ManterDespesasMensaisFixas";
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$obTEmpenhoTipoDespesaFixa = new TEmpenhoTipoDespesaFixa();
$obTEmpenhoTipoDespesaFixa->recuperaTodos( $rsTipo );

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget ( "oculto"   );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_EMP_INSTANCIAS."relatorio/OCManterDespesasMensaisFixas.php" );

$obITextBoxSelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
$obITextBoxSelectEntidadeUsuario->obTextBox->obEvento->setOnChange( 'getIMontaAssinaturas()' );
$obITextBoxSelectEntidadeUsuario->obSelect->obEvento->setOnChange( 'getIMontaAssinaturas()' );

$obExercicio = new Exercicio;
$obExercicio->setTitle( "Informe o Exercício." );

$obPeriodicidade = new Periodo();
$obPeriodicidade->setRotulo          ( 'Vigência'      );
$obPeriodicidade->setExercicio       ( Sessao::getExercicio() );
$obPeriodicidade->setValidaExercicio ( true            );
$obPeriodicidade->setValue           ( 4               );
$obPeriodicidade->setNull			  ( false           );

$obTxtTipo = new TextBox;
$obTxtTipo->setName   ( "inCodTipo"         );
$obTxtTipo->setId     ( "inCodTipo"         );
$obTxtTipo->setValue  ( 0          );
$obTxtTipo->setRotulo ( "Tipo"              );
$obTxtTipo->setTitle  ( "Selecione o Tipo de Despesa." );
$obTxtTipo->setInteiro( true                    );
$obTxtTipo->setNull   ( false );

$obCmbTipo = new Select;
$obCmbTipo->setName      ( "stNomeTipo"    );
$obCmbTipo->setId        ( "stNomeTipo"    );
$obCmbTipo->setValue     ( $inCodTipo      );
$obCmbTipo->addOption    ( 0, "Todos" );
$obCmbTipo->setCampoId   ( "cod_tipo"      );
$obCmbTipo->setCampoDesc ( "descricao"     );
$obCmbTipo->preencheCombo( $rsTipo         );
$obCmbTipo->setNull      ( false );

$obTxtContrato = new TextBox;
$obTxtContrato->setName   ( "inContrato"         );
$obTxtContrato->setId     ( "inContrato"         );
$obTxtContrato->setValue  ( $inContrato          );
$obTxtContrato->setRotulo ( "Nr. Contrato"       );
$obTxtContrato->setTitle  ( "Digite o Número do Contrato." );
$obTxtContrato->setInteiro( true                    );

$obBscLocal = new BuscaInner;
$obBscLocal->setRotulo                      ( "Local"                               );
$obBscLocal->setTitle                       ( "Informe o Local ( prédio,escola,secretaria,etc...)." );
$obBscLocal->setId                          ( "stLocal"                             );
$obBscLocal->obCampoCod->setName            ( "inCodLocal"                          );
$obBscLocal->obCampoCod->setValue           ( $inCodLocal                           );
$obBscLocal->obCampoCod->setSize            ( 10                                    );
$obBscLocal->obCampoCod->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodLocal='+this.value,'buscarLocal');");
$obBscLocal->setFuncaoBusca                 ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/LSProcurarLocal.php','frm','inCodLocal','stLocal','','".Sessao::getId()."','800','550')" );

$obPopUpDotacao = new IPopUpDotacao($obITextBoxSelectEntidadeUsuario->obSelect );

$obPopUpCredor = new IPopUpCredor($obForm );
$obPopUpCredor->setNull    ( true );

// Instanciação do objeto Lista de Assinaturas
include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addTitulo( "Dados para Filtro de Emissão de Relatório Despesas Mensais Fixas" );
$obFormulario->addComponente( $obITextBoxSelectEntidadeUsuario );
$obFormulario->addComponente( $obExercicio );
$obFormulario->addComponente( $obPeriodicidade );
$obFormulario->addComponenteComposto( $obTxtTipo, $obCmbTipo );
$obFormulario->addComponente( $obTxtContrato );
$obFormulario->addComponente( $obBscLocal );
$obFormulario->addComponente( $obPopUpDotacao );
$obFormulario->addComponente( $obPopUpCredor );

$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
