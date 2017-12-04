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
    * Data de Criação   : 22/01/2007

    * @author Diego Barbosa Victoria

    * @ignore

    * $Id: FMManterTipoConta.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );
include_once(TTGO."TTGOTipoLancamento.class.php" );

$stPrograma = "ManterTipoConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include( $pgJs );

$stAcao = $request->get('stAcao');

Sessao::write('arContas', array());

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

$obTTGOTipoLancamento = new TTGOTipoLancamento();
$obTTGOTipoLancamento->recuperaTodos( $rsTipoLancamento );

$obCmbTipoLancamento = new Select();
$obCmbTipoLancamento->setName   ('inTipoLancamento');
$obCmbTipoLancamento->setId     ('inTipoLancamento');
$obCmbTipoLancamento->setRotulo ('Tipo do Lançamento');
$obCmbTipoLancamento->setNull   ( false );
$obCmbTipoLancamento->setValue  ('');
$obCmbTipoLancamento->setCampoId('cod_tipo_lancamento');
$obCmbTipoLancamento->setCampoDesc('descricao');
$obCmbTipoLancamento->addOption ('','Selecione');
$obCmbTipoLancamento->preencheCombo( $rsTipoLancamento );
$obCmbTipoLancamento->obEvento->setOnChange("montaParametrosGET('preencheTipoConta','inTipoLancamento','true');");

$obCmbTipoConta = new Select();
$obCmbTipoConta->setName   ('inTipoConta');
$obCmbTipoConta->setId     ('inTipoConta');
$obCmbTipoConta->setRotulo ('Tipo de Conta');
$obCmbTipoConta->setNull   ( false );
$obCmbTipoConta->setValue  ('');
$obCmbTipoConta->addOption ('','Selecione');
$obCmbTipoConta->obEvento->setOnChange("montaParametrosGET('preencheLista','inTipoLancamento,inTipoConta','true');");

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;

//Recupera Mascara
$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

$obBscContaSintetica = new BuscaInner;
$obBscContaSintetica->setRotulo ( "Conta"         );
$obBscContaSintetica->setTitle  ( "Informe a conta."    );
$obBscContaSintetica->setObrigatorioBarra  ( true );
$obBscContaSintetica->setNull( true );
$obBscContaSintetica->setId     ( "stConta"             );
$obBscContaSintetica->setName   ( "stConta"             );
$obBscContaSintetica->setValue  ( $stConta              );
$obBscContaSintetica->obCampoCod->setName       ( "inCodConta"      );
$obBscContaSintetica->obCampoCod->setId         ( "inCodConta"      );
$obBscContaSintetica->obCampoCod->setNull         ( true      );
//$obBscContaSintetica->obCampoCod->setSize       ( 10                );
//$obBscContaSintetica->obCampoCod->setMaxLength  ( 5                 );
$obBscContaSintetica->obCampoCod->setValue      ( $inCodConta       );
$obBscContaSintetica->obCampoCod->setMascara    ( $stMascara        );
$obBscContaSintetica->obCampoCod->setPreencheComZeros( 'D'          );
$obBscContaSintetica->obCampoCod->obEvento->setOnKeyPress( "return validaExpressao( this, event, '[0-9.]');" );
$obBscContaSintetica->obCampoCod->setAlign      ( "left"            );
$obBscContaSintetica->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaEstrutural','inCodConta,inTipoLancamento','true');");
$obBscContaSintetica->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLContaSintetica.php','frm','inCodConta','stConta','ativoPassivo&tipoLancamento='+document.getElementById('inTipoLancamento').value,'".Sessao::getId()."&inTipoLancamento='+document.getElementById('inTipoLancamento').value,'800','550');");

$obBtnOk = new Button();
$obBtnOk->setValue('Incluir');
$obBtnOk->obEvento->setOnClick("montaParametrosGET('incluirConta','inTipoLancamento,inTipoConta,inCodConta','true');");

$obBtnLimpar = new Button();
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->obEvento->setOnClick("limpaConta();");

$obSpnContas = new Span();
$obSpnContas->setId('spnContas');

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);
$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addTitulo            ( "Tipo de Conta" );
$obFormulario->addComponente        ($obCmbTipoLancamento);
$obFormulario->addComponente        ($obCmbTipoConta);
$obFormulario->addTitulo            ( "Conta" );
$obFormulario->addComponente        ($obBscContaSintetica);
$obFormulario->agrupaComponentes    (array($obBtnOk,$obBtnLimpar));
$obFormulario->addSpan              ($obSpnContas);
$obFormulario->OK      ();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
