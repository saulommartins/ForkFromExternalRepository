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
    * Data de Criação: 18/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26727 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-11-12 16:31:31 -0200 (Seg, 12 Nov 2007) $

    * Casos de uso: uc-03.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_COMPONENTES.'IPopUpBem.class.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioTipoBaixa.class.php';

$stPrograma = "ManterBaixarBem";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get("stAcao");

include_once( $pgJs );

Sessao::remove('bens');

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

$obTPatrimonioTipoBaixa = new TPatrimonioTipoBaixa();
$obTPatrimonioTipoBaixa->recuperaTodos($rsTipoBaixa, " WHERE cod_tipo NOT IN (0, 7, 8)"," ORDER BY cod_tipo");

$obCmbTipoBaixa = new Select();
$obCmbTipoBaixa->setName       ( "inTipoBaixa"   );
$obCmbTipoBaixa->setRotulo     ( "Tipo da baixa" );
$obCmbTipoBaixa->setId         ( "inTipoBaixa"   );
$obCmbTipoBaixa->setCampoId    ( "cod_tipo"      );
$obCmbTipoBaixa->setCampoDesc  ( "[cod_tipo] - [descricao]"     );
$obCmbTipoBaixa->setNull       ( false           );
$obCmbTipoBaixa->addOption     ( '','Selecione'  );
$obCmbTipoBaixa->preencheCombo ( $rsTipoBaixa    ); 

//instancia um IPopUPBem Inicial
$obIPopUpBemInicio = new IPopUpBem( $obForm );
$obIPopUpBemInicio->setId    ( 'stNomBemInicio' );
$obIPopUpBemInicio->setRotulo( 'Bem Inicial' );
$obIPopUpBemInicio->setTitle ( 'Informe o código inicial do bem.' );
$obIPopUpBemInicio->setNull  ( true );
$obIPopUpBemInicio->setObrigatorioBarra( true );
$obIPopUpBemInicio->obCampoCod->setName( 'inCodBemInicio' );
$obIPopUpBemInicio->obCampoCod->setId  ( 'inCodBemInicio' );
$obIPopUpBemInicio->setTipoBusca       ( 'bemNaoBaixado' );
$obIPopUpBemInicio->obCampoCod->obEvento->setOnChange("ajaxJavaScript('".CAM_GP_PAT_POPUPS.'bem/OCManterBem.php?'.Sessao::getId()."&boBemBaixado=false&stNomCampoCod=".$obIPopUpBemInicio->obCampoCod->getName()."&boMostrarDescricao=".$obIPopUpBemInicio->getMostrarDescricao()."&stIdCampoDesc=".$obIPopUpBemInicio->getId()."&stNomForm=".$obIPopUpBemInicio->obForm->getName()."&inCodigo='+this.value+'&inTipoBaixa='+inTipoBaixa.value, 'buscaPopupTipoBaixa');". $obIPopUpBemInicio->obCampoCod->obEvento->getOnChange());

//instancia um IPopUpBem Final
$obIPopUpBemFim = new IPopUpBem( $obForm );
$obIPopUpBemFim->setId    ( 'stNomBemFim' );
$obIPopUpBemFim->setRotulo( 'Bem Final' );
$obIPopUpBemFim->setTitle ( 'Informe o código final do bem.' );
$obIPopUpBemFim->setNull  ( true );
$obIPopUpBemFim->obCampoCod->setName( 'inCodBemFim' );
$obIPopUpBemFim->obCampoCod->setId  ( 'inCodBemFim' );
$obIPopUpBemFim->setTipoBusca       ( 'bemNaoBaixado' );
$obIPopUpBemFim->obCampoCod->obEvento->setOnChange("ajaxJavaScript('".CAM_GP_PAT_POPUPS.'bem/OCManterBem.php?'.Sessao::getId()."&boBemBaixado=false&stNomCampoCod=".$obIPopUpBemFim->obCampoCod->getName()."&boMostrarDescricao=".$obIPopUpBemFim->getMostrarDescricao()."&stIdCampoDesc=".$obIPopUpBemFim->getId()."&stNomForm=".$obIPopUpBemFim->obForm->getName()."&inCodigo='+this.value+'&inTipoBaixa='+inTipoBaixa.value, 'buscaPopupTipoBaixa');". $obIPopUpBemFim->obCampoCod->obEvento->getOnChange());

//cria os botões de acoes para os bens
$obBtnOk = new Ok;
$obBtnOk->setName ( "btnOk" );
$obBtnOk->setValue( "Incluir" );
$obBtnOk->setTipo ( "button" );
$obBtnOk->obEvento->setOnClick( "montaParametrosGET( 'incluirBaixaBem', 'inCodBemInicio,inCodBemFim,inTipoBaixa' );" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName ( "btnOk" );
$obBtnLimpar->setValue(  "Limpar" );
$obBtnLimpar->obEvento->setOnClick( "LimparCodigos();" );

//instancia componente para a data de baixa
$obDtBaixa = new Data();
$obDtBaixa->setRotulo( 'Data de Baixa' );
$obDtBaixa->setTitle ( 'Informe a data de baixa do bem.' );
$obDtBaixa->setName  ( 'dtBaixa' );
$obDtBaixa->setId    ( 'dtBaixa' );
$obDtBaixa->setNull  ( false );

//instancia componente para o motivo
$obTxtMotivo = new TextArea();
$obTxtMotivo->setRotulo( 'Motivo' );
$obTxtMotivo->setTitle ( 'Informe o motivo da baixa do bem.' );
$obTxtMotivo->setName  ( 'stMotivo' );
$obTxtMotivo->setId    ( 'stMotivo' );
$obTxtMotivo->setNull  ( false );

//cria um span para os bens a serem baixados
$obSpnBem = new Span();
$obSpnBem->setId( 'spnBem' );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.01.06');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo( 'Baixa de Bem' );
$obFormulario->addComponente( $obCmbTipoBaixa );
$obFormulario->addComponente( $obIPopUpBemInicio );
$obFormulario->addComponente( $obIPopUpBemFim );

$obFormulario->defineBarra( array( $obBtnOk, $obBtnLimpar ) );

$obFormulario->addComponente( $obDtBaixa );
$obFormulario->addComponente( $obTxtMotivo );
$obFormulario->addSpan      ( $obSpnBem );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>