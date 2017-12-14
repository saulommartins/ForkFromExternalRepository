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
    * Página de Formulário Configuração das contas do relatório RGF 2
    * Data de Criação   : 28/05/2013

    * @author Analista: Valtair Lacerda
    * @author Desenvolvedor: Eduardo Paculski Schitz

    * @ignore

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_STN_MAPEAMENTO."TSTNContasRGF2.class.php" );

$stPrograma = "ConfigurarRGF2";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$obTSTNContasRGF2 = new TSTNContasRGF2();
$obTSTNContasRGF2->listarContasRGF2($rsContasRGF);

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$inCount = 1;
foreach ($rsContasRGF->arElementos as $arConta) {
    $stPopUpConta = 'obPopUpConta_'.$arConta['cod_conta'];
    $stSpnLista = 'obSpnLista_'.$arConta['cod_conta'];
    $stBtnIncluir = 'obBtnIncluir_'.$arConta['cod_conta'];
    $stBtnLimpar = 'obBtnLimpar_'.$arConta['cod_conta'];

    $$stPopUpConta = new BuscaInner;
    $$stPopUpConta->setRotulo ( "Contas para ".$arConta['descricao'] );
    $$stPopUpConta->setTitle  ( "Digite o Reduzido");
    $$stPopUpConta->setId ( "stNomConta_".$arConta['cod_conta'] );
    $$stPopUpConta->obCampoCod->setName ( "inCodPlano_".$arConta['cod_conta'] );
    $$stPopUpConta->obCampoCod->setId   ( "inCodPlano_".$arConta['cod_conta'] );
    $$stPopUpConta->obCampoCod->setSize ( 10 );
    $$stPopUpConta->obCampoCod->setMaxLength( 5 );
    $$stPopUpConta->obCampoCod->setAlign ("left");
    $$stPopUpConta->obCampoCod->obEvento->setOnBlur("montaParametrosGET('buscaPlanoConta', 'inCodPlano_".$arConta['cod_conta']."');");
    $$stPopUpConta->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodPlano_".$arConta['cod_conta']."','inCodPlano_".$arConta['cod_conta']."','','".Sessao::getId()."','800','550');");

    $$stBtnIncluir = new Button();
    $$stBtnIncluir->setId('btnIncluir_'.$arConta['cod_conta']);
    $$stBtnIncluir->setName('btnIncluir_'.$arConta['cod_conta']);
    $$stBtnIncluir->setValue('Incluir');
    $$stBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluiPlanoConta', 'inCodPlano_".$arConta['cod_conta']."')");

    //botão de limpar lista
    $$stBtnLimpar = new Button();
    $$stBtnLimpar->setId('btnLimpar_'.$arConta['cod_conta']);
    $$stBtnLimpar->setName('btnLimpar_'.$arConta['cod_conta']);
    $$stBtnLimpar->setValue('Limpar');
    $$stBtnLimpar->obEvento->setOnClick("montaParametrosGET('limparCampos', 'btnLimpar_".$arConta['cod_conta']."')");

    $$stSpnLista = new Span();
    $$stSpnLista->setId('spnLista_'.$arConta['cod_conta']);

    //cria o array na sessão para a lista
    Sessao::write('arVinculoContas_'.$arConta['cod_conta'], array());

    $obFormulario->addTitulo($arConta['descricao']);
    $obFormulario->addComponente($$stPopUpConta);
    $obFormulario->agrupaComponentes(array($$stBtnIncluir, $$stBtnLimpar));
    $obFormulario->addSpan($$stSpnLista);
    $inCount++;
}

$obFormulario->OK();
$obFormulario->show();

SistemaLegado::executaFrameOculto('montaListas();');

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
