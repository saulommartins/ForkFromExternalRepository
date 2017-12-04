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

    * $Id: FMManterComaaaa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterComaaaa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include( $pgJs );

$stAcao = $request->get('stAcao');

Sessao::write('arContas', array());
Sessao::write('arExcluidas', array());

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

$obCmbTipoLancamento = new Select();
$obCmbTipoLancamento->setName   ('inTipoLancamento');
$obCmbTipoLancamento->setId     ('inTipoLancamento');
$obCmbTipoLancamento->setRotulo ('Tipo de Lançamento');
$obCmbTipoLancamento->setNull   ( false );
$obCmbTipoLancamento->setValue  ('');
$obCmbTipoLancamento->addOption ('','Selecione');
$obCmbTipoLancamento->addOption ('1','Ativo');
$obCmbTipoLancamento->addOption ('2','Passivo');
$obCmbTipoLancamento->obEvento->setOnChange("montaParametrosGET('preencheLista','inTipoLancamento','true');");

$obBscConta = new BuscaInner;
$obBscConta->setRotulo ( "Conta"         );
$obBscConta->setTitle  ( "Informe a conta."    );
$obBscConta->setObrigatorioBarra  ( true );
$obBscConta->setNull( true );
$obBscConta->setId     ( "stConta"             );
$obBscConta->setName   ( "stConta"             );
$obBscConta->setValue  ( $stConta              );
$obBscConta->obCampoCod->setName       ( "inCodConta"      );
$obBscConta->obCampoCod->setId         ( "inCodConta"      );
$obBscConta->obCampoCod->setNull       ( true      );
$obBscConta->obCampoCod->setValue      ( $inCodConta       );
$obBscConta->obCampoCod->setAlign      ( "left"            );
$obBscConta->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaEstrutural','inTipoLancamento,inCodConta','true');");
$obBscConta->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodConta','stConta','conta_analitica_estrutural','".Sessao::getId()."&inCodIniEstrutural='+document.getElementById('inTipoLancamento').value+'&tipoBusca2=Comaaaa','800','550');");

$obBtnOk = new Button();
$obBtnOk->setValue('Incluir');
$obBtnOk->obEvento->setOnClick("montaParametrosGET('incluirConta','inTipoLancamento,inCodConta','true');");

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
$obFormulario->addTitulo            ( "Conta" );
$obFormulario->addComponente        ($obBscConta);
$obFormulario->agrupaComponentes    (array($obBtnOk,$obBtnLimpar));
$obFormulario->addSpan              ($obSpnContas);
$obFormulario->OK      ();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
