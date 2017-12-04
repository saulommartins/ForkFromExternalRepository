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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_BEN_MAPEAMENTO."TBeneficioLayoutPlanoSaude.class.php");
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");

$stPrograma = "ConfiguracaoPlanoSaude";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include $pgJs;

//Esvazia array que acumula lista de dívidas
Sessao::remove('arLista');

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

$spnLista = new Span();
$spnLista->setId ("spnLista");

// aqui é montado campo para se buscar o fornecedor do plano que será vinculado com o layout.
$obCGMFornecedor = new IPopUpCGMVinculado($obForm);
$obCGMFornecedor->setTabelaVinculo('compras.fornecedor');
$obCGMFornecedor->setCampoVinculo('cgm_fornecedor');
$obCGMFornecedor->setNomeVinculo('Fornecedor do plano');
$obCGMFornecedor->setRotulo('Fornecedor do plano');
$obCGMFornecedor->setName('stCGMFornecedor');
$obCGMFornecedor->setId('stCGMFornecedor');
$obCGMFornecedor->obCampoCod->setName('inCGMFornecedor');
$obCGMFornecedor->obCampoCod->setId('inCGMFornecedor');
$obCGMFornecedor->setNull(true);

$obTBeneficioLayoutPlanoSaude = new TBeneficioLayoutPlanoSaude();
$obTBeneficioLayoutPlanoSaude->recuperaTodos($rsBeneficioLayoutPlanoSaude);

// aqui é montado o combobox para relacionar os layouts.
$obCmbBeneficioLayoutPlanoSaude = new Select();
$obCmbBeneficioLayoutPlanoSaude->setRotulo('Layout de Importação');
$obCmbBeneficioLayoutPlanoSaude->setTitle('Selecione o Layout de Importação');
$obCmbBeneficioLayoutPlanoSaude->setName('inLayout');
$obCmbBeneficioLayoutPlanoSaude->setId('inLayout');
$obCmbBeneficioLayoutPlanoSaude->addOption('', 'Selecione');
$obCmbBeneficioLayoutPlanoSaude->setCampoId('cod_layout');
$obCmbBeneficioLayoutPlanoSaude->setCampoDesc('padrao');
$obCmbBeneficioLayoutPlanoSaude->setStyle('width: 300');
$obCmbBeneficioLayoutPlanoSaude->preencheCombo($rsBeneficioLayoutPlanoSaude);
$obCmbBeneficioLayoutPlanoSaude->setNull(true);

$obSpnLista = new Span();
$obSpnLista->setId( 'spnLista' );

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

$obHdnId = new Hidden;
$obHdnId->setName  ( "id" );
$obHdnId->setValue ( $inId );

$obBtOk = new Button();
$obBtOk->setValue('Incluir');
$obBtOk->setId('btIncluir');
$obBtOk->obEvento->setOnCLick("montaParametrosGET('incluiVinculo', 'inCGMFornecedor,inLayout,id');");

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();

$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnId);

$obFormulario->addTitulo     ("Layout de importação do Plano de Saúde");
$obFormulario->addComponente ($obCGMFornecedor);
$obFormulario->addComponente ($obCmbBeneficioLayoutPlanoSaude);

$obLimpar = new Button();
$obLimpar->setValue('Limpar');
$obLimpar->setId('limpar');
$obLimpar->obEvento->setOnCLick("montaParametrosGET('limparCampos');");
/*$obLimpar = new Limpar();
$obLimpar->obEvento->setOnClick("montaParametrosGET('limparCampos');");*/

$obFormulario->defineBarra(array($obBtOk, $obLimpar));

$obFormulario->addSpan       ($obSpnLista);

$obOk = new Ok();
$obFormulario->defineBarra(array($obOk));

$obFormulario->show();

$jsOnLoad = "montaParametrosGET('montaLista');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>