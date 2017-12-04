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
    * Página de Filtro de Despesas Mensais Fixas
    * Data de Criação   : 30/08/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2007-03-07 15:47:39 -0300 (Qua, 07 Mar 2007) $

    * Casos de uso: uc-02.03.29
*/

/**

$Log$
Revision 1.5  2007/03/07 18:47:39  luciano
#8398#

Revision 1.4  2007/02/05 18:41:03  rodrigo_sr
Bug #7914#

Revision 1.3  2006/11/25 16:51:18  cleisson
Bug #7518#

Revision 1.2  2006/09/08 10:18:23  tonismar
ajusto no combo de tipo

Revision 1.1  2006/09/01 17:35:03  tonismar
Manter Despesas Fixas Mensais

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php" );
include_once( CAM_GF_ORC_COMPONENTES."IPopUpDotacao.class.php" );
include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoTipoDespesaFixa.class.php" );
include_once( CAM_GF_EMP_COMPONENTES."IPopUpCredor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterDespesasMensaisFixas";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

Sessao::remove('filtro');
Sessao::remove('link');

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$obTEmpenhoTipoDespesaFixa = new TEmpenhoTipoDespesaFixa();
$obTEmpenhoTipoDespesaFixa->recuperaTodos( $rsTipo );

$obForm = new Form;
$obForm->setAction ( $pgList    );
$obForm->setTarget ( "telaPrincipal"   );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao"   );
$obHdnAcao->setValue( $stAcao   );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl"   );
$obHdnCtrl->setValue( ""        );

$obITextBoxSelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();

$obExercicio = new Exercicio;

$obTxtTipo = new TextBox;
$obTxtTipo->setName   ( "inCodTipo"         );
$obTxtTipo->setId     ( "inCodTipo"         );
$obTxtTipo->setValue  ( 0          );
$obTxtTipo->setRotulo ( "Tipo"              );
$obTxtTipo->setTitle  ( "Selecione o Tipo de Despesa a Cadastrar." );
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

$obTxtIdentificacao = new TextBox;
$obTxtIdentificacao->setName   ( "inIdentificacao"         );
$obTxtIdentificacao->setId     ( "inIdentificacao"         );
$obTxtIdentificacao->setValue  ( $inIdentificacao          );
$obTxtIdentificacao->setRotulo ( "Nr. Identificação"         );
$obTxtIdentificacao->setTitle  ( "Informe o Número de Identificação ( telefone, hidrômetro, nr. prédio, etc...)." );
$obTxtIdentificacao->setInteiro( true                    );

$obTxtContrato = new TextBox;
$obTxtContrato->setName   ( "inContrato"         );
$obTxtContrato->setId     ( "inContrato"         );
$obTxtContrato->setValue  ( $inContrato          );
$obTxtContrato->setRotulo ( "Nr. Contrato"       );
$obTxtContrato->setTitle  ( "Digite o Número do Contrato." );
$obTxtContrato->setInteiro( true                    );

$obBscLocal = new BuscaInner;
$obBscLocal->setRotulo                      ( "Local"                               );
$obBscLocal->setTitle                       ( "Informe o Local (prédio, escola, secretaria, etc...)." );
$obBscLocal->setId                          ( "stLocal"                             );
$obBscLocal->obCampoCod->setName            ( "inCodLocal"                          );
$obBscLocal->obCampoCod->setValue           ( $inCodLocal                           );
$obBscLocal->obCampoCod->setSize            ( 10                                    );
$obBscLocal->obCampoCod->obEvento->setOnBlur("buscaValor('buscaLocal',".$pgOcul.");");
$obBscLocal->setFuncaoBusca                 ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/LSProcurarLocal.php','frm','inCodLocal','stLocal','','".Sessao::getId()."','800','550')" );

$obPopUpDotacao = new IPopUpDotacao($obITextBoxSelectEntidadeUsuario->obSelect );

$obPopUpCredor = new IPopUpCredor($obForm );
$obPopUpCredor->setNull    ( true            );

$obDtVigencia = new Periodo();
$obDtVigencia->setRotulo          ( 'Vigência'      );
$obDtVigencia->setExercicio       ( Sessao::getExercicio() );
$obDtVigencia->setValidaExercicio ( true            );
$obDtVigencia->setValue           ( 4               );

$obCmbStatus = new Select;
$obCmbStatus->setName      ( "inCodStatus"   );
$obCmbStatus->setId        ( "inCodStatus"   );
$obCmbStatus->setRotulo    ( "Status"        );
$obCmbStatus->addOption    ( "", "Todos"     );
$obCmbStatus->addOption    ( "1", "Ativo"    );
$obCmbStatus->addOption    ( "2", "Inativo"  );
$obCmbStatus->setCampoDesc ( "nom_unidade"   );
$obCmbStatus->setNull      ( true            );

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo ( "Dados para Filtro de Alteração de Despesas Mensais Fixas" );
$obFormulario->addComponente( $obITextBoxSelectEntidadeUsuario );
$obFormulario->addComponente( $obExercicio );
$obFormulario->addComponenteComposto( $obTxtTipo, $obCmbTipo );
$obFormulario->addComponente( $obTxtIdentificacao );
$obFormulario->addComponente( $obTxtContrato );
$obFormulario->addComponente( $obBscLocal );
$obFormulario->addComponente( $obPopUpDotacao );
$obFormulario->addComponente( $obPopUpCredor );
$obFormulario->addComponente( $obDtVigencia );
$obFormulario->addComponente( $obCmbStatus );
$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
