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
    * Página de Formulário Vínculo das Contas Dedutoras de Impostos
    * Data de Criação   : 21/05/2013

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."IPopUpEstruturalReceita.class.php" );
include_once ( CAM_GPC_STN_MAPEAMENTO."TSTNTributoAnexo8.class.php" );

$stPrograma = "VincularContasDeducoesReceitaImpostos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$obTSTNTributoAnexo8 = new TSTNTributoAnexo8();
$obTSTNTributoAnexo8->listarTributoAnexo8($rsTributos);

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
foreach ($rsTributos->arElementos as $tributo) {
    $stPopUpReceita = 'obPopUpReceita_'.$tributo['cod_tributo'];
    $stSpnLista = 'obSpnLista_'.$tributo['cod_tributo'];
    $stBtnIncluir = 'obBtnIncluir_'.$tributo['cod_tributo'];
    $stBtnLimpar = 'obBtnLimpar_'.$tributo['cod_tributo'];

    $$stPopUpReceita = new BuscaInner;
    $$stPopUpReceita->setRotulo ( "Contas para ".$tributo['descricao'] );
    $$stPopUpReceita->setTitle  ( "Digite o Reduzido");
    $$stPopUpReceita->setId ( "stNomReceita_".$tributo['cod_tributo'] );
    $$stPopUpReceita->obCampoCod->setName ( "inCodReceita_".$tributo['cod_tributo'] );
    $$stPopUpReceita->obCampoCod->setId   ( "inCodReceita_".$tributo['cod_tributo'] );
    $$stPopUpReceita->obCampoCod->setSize ( 10 );
    $$stPopUpReceita->obCampoCod->setMaxLength( 5 );
    $$stPopUpReceita->obCampoCod->setAlign ("left");
    $$stPopUpReceita->obCampoCod->obEvento->setOnBlur("montaParametrosGET('buscaReceita', 'inCodReceita_".$tributo['cod_tributo']."');");
    $$stPopUpReceita->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."receita/FLReceita.php','frm','inCodReceita_".$tributo['cod_tributo']."','stNomReceita_".$tributo['cod_tributo']."','receitaDedutoraExportacao','".Sessao::getId()."','800','550');");

    $$stBtnIncluir = new Button();
    $$stBtnIncluir->setId('btnIncluir_'.$tributo['cod_tributo']);
    $$stBtnIncluir->setName('btnIncluir_'.$tributo['cod_tributo']);
    $$stBtnIncluir->setValue('Incluir');
    $$stBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluiReceita', 'inCodReceita_".$tributo['cod_tributo']."')");

    //botão de limpar lista
    $$stBtnLimpar = new Button();
    $$stBtnLimpar->setId('btnLimpar_'.$tributo['cod_tributo']);
    $$stBtnLimpar->setName('btnLimpar_'.$tributo['cod_tributo']);
    $$stBtnLimpar->setValue('Limpar');
    $$stBtnLimpar->obEvento->setOnClick("montaParametrosGET('limparCampos', 'btnLimpar_".$tributo['cod_tributo']."')");

    $$stSpnLista = new Span();
    $$stSpnLista->setId('spnLista_'.$tributo['cod_tributo']);

    //cria o array na sessão para a lista
    Sessao::write('arReceitaTributo_'.$tributo['cod_tributo'], array());

    $obFormulario->addTitulo($tributo['descricao']);
    $obFormulario->addComponente($$stPopUpReceita);
    $obFormulario->agrupaComponentes(array($$stBtnIncluir, $$stBtnLimpar));
    $obFormulario->addSpan($$stSpnLista);
}

$obFormulario->OK();
$obFormulario->show();

SistemaLegado::executaFrameOculto('montaListas();');

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
