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

    *$Id: FMManterContaTipo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTGO.'TTGOOrgaoPlanoBanco.class.php');
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php" );

$stPrograma = "ManterContaOrgao";
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

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if ($inCodigo) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTOrcamentoOrgao = new TOrcamentoOrgao();
$stFiltro = " and OO.exercicio = ".Sessao::getExercicio()." ";
$obTOrcamentoOrgao->recuperaRelacionamento( $rsOrgao, $stFiltro );

$obCmbOrgaos = new Select();
$obCmbOrgaos->setRotulo('Orgão');
$obCmbOrgaos->setCampoID('num_orgao');
$obCmbOrgaos->setCampoDesc('nom_orgao');
$obCmbOrgaos->setId('inOrgao');
$obCmbOrgaos->setName('inOrgao');
$obCmbOrgaos->addOption('','Selecione');
$obCmbOrgaos->setNull( false );
$obCmbOrgaos->preencheCombo( $rsOrgao );
$obCmbOrgaos->obEvento->setOnChange("montaParametrosGET('preencheLista','inOrgao','true');");

$obTTGOOrgaoPlanoBanco = new TTGOOrgaoPlanoBanco();
$obTTGOOrgaoPlanoBanco->recuperaBanco( $rsBanco );

$obCmbBanco = new Select();
$obCmbBanco->setRotulo('Banco');
$obCmbBanco->setId('inCodBanco');
$obCmbBanco->setName('inCodBanco');
$obCmbBanco->setValue( '' );
$obCmbBanco->setCampoId('cod_banco');
$obCmbBanco->setCampoDesc('[num_banco] - [nom_banco]');
$obCmbBanco->addOption('','Selecione');
$obCmbBanco->preencheCombo( $rsBanco );
$obCmbBanco->setObrigatorioBarra( true );
$obCmbBanco->obEvento->setOnChange("montaParametrosGET('preencheAgencia','inCodBanco','true');");

$obCmbAgencia = new Select();
$obCmbAgencia->setRotulo('Agência');
$obCmbAgencia->setId('inCodAgencia');
$obCmbAgencia->setName('inCodAgencia');
$obCmbAgencia->addOption('','Selecione');
$obCmbAgencia->setObrigatorioBarra( true );
$obCmbAgencia->obEvento->setOnChange("montaParametrosGET('preencheConta','inCodBanco,inCodAgencia','true');");

$obCmbConta = new Select();
$obCmbConta->setRotulo('Conta Corrente');
$obCmbConta->setId('inCodConta');
$obCmbConta->setName('inCodConta');
$obCmbConta->addOption('','Selecione');
$obCmbConta->setObrigatorioBarra( true );

$obBtnOk = new Button();
$obBtnOk->setValue('Incluir');
$obBtnOk->obEvento->setOnClick("montaParametrosGET('incluirConta','inOrgao,inCodBanco,inCodAgencia,inCodConta','true');");

$obBtnLimpar = new Button();
$obBtnLimpar->setValue('Limpar');

$obSpnContas = new Span();
$obSpnContas->setId('spnContas');

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);
$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addTitulo            ( "Órgãos" );
$obFormulario->addComponente        ($obCmbOrgaos);
$obFormulario->addTitulo            ( "Contas" );
$obFormulario->addComponente        ($obCmbBanco);
$obFormulario->addComponente        ($obCmbAgencia);
$obFormulario->addComponente        ($obCmbConta);
$obFormulario->agrupaComponentes    (array($obBtnOk,$obBtnLimpar));
$obFormulario->addSpan              ($obSpnContas);
$obFormulario->OK      ();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
