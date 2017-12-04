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
    * Data de Criação   : 25/07/2011

    * @author Davi Ritter Aroldi
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEMS_MAPEAMENTO."TTCEMSDespesasNaoComputadas.class.php");
include_once(CAM_GPC_TCEMS_MAPEAMENTO."TTCEMSReceitaCorrenteLiquida.class.php");

$stPrograma = "ManterRGFAnexo1";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//recupera lista de despesas não computadas
$stFiltroDepesas = " where exercicio = '".Sessao::read('exercicio')."' ";
$obTTCEMSDespesasNaoComputadas = new TTCEMSDespesasNaoComputadas();
$obTTCEMSDespesasNaoComputadas->recuperaTodos($rsDespesasNaoComputadas, $stFiltroDepesas);

Sessao::write('arListaDespesa', array());
$rsDespesasNaoComputadas->addFormatacao('quadrimestre1','NUMERIC_BR');
$rsDespesasNaoComputadas->addFormatacao('quadrimestre2','NUMERIC_BR');
$rsDespesasNaoComputadas->addFormatacao('quadrimestre3','NUMERIC_BR');
if ($rsDespesasNaoComputadas->getNumLinhas() > 0) {
    $arListaDespesas = array();
    while (!$rsDespesasNaoComputadas->eof()) {
        $arListaDespesasTMP['stExercicio'] = $rsDespesasNaoComputadas->getCampo('exercicio');
        $arListaDespesasTMP['inId'] = $rsDespesasNaoComputadas->getCampo('id');
        $arListaDespesasTMP['stDescricao'] = $rsDespesasNaoComputadas->getCampo('descricao');
        $arListaDespesasTMP['nuQuadrimestreValor1'] = $rsDespesasNaoComputadas->getCampo('quadrimestre1');
        $arListaDespesasTMP['nuQuadrimestreValor2'] = $rsDespesasNaoComputadas->getCampo('quadrimestre2');
        $arListaDespesasTMP['nuQuadrimestreValor3'] = $rsDespesasNaoComputadas->getCampo('quadrimestre3');

        $arListaDespesas[] = $arListaDespesasTMP;
        $rsDespesasNaoComputadas->proximo();
    }

    Sessao::write('arListaDespesa', $arListaDespesas);
}

//recupera valores da receita corrente líquida
$obTTCEMSReceitaCorrenteLiquida = new TTCEMSReceitaCorrenteLiquida();
$obTTCEMSReceitaCorrenteLiquida->recuperaValorQuadrimestre1($rsQuadrimestre1);
$obTTCEMSReceitaCorrenteLiquida->recuperaValorQuadrimestre2($rsQuadrimestre2);
$obTTCEMSReceitaCorrenteLiquida->recuperaValorQuadrimestre3($rsQuadrimestre3);
$rsQuadrimestre1->addFormatacao('vl_quadrimestre', 'NUMERIC_BR');
$rsQuadrimestre2->addFormatacao('vl_quadrimestre', 'NUMERIC_BR');
$rsQuadrimestre3->addFormatacao('vl_quadrimestre', 'NUMERIC_BR');

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setId( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setId( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName( "stExercicio" );
$obHdnExercicio->setId( "stExercicio" );
$obHdnExercicio->setValue( Sessao::read('exercicio') );

$obQuadrimestre1 = new Numerico;
$obQuadrimestre1->setName( "nuQuadrimestre1" );
$obQuadrimestre1->setId( "nuQuadrimestre1" );
$obQuadrimestre1->setTitle( "Quadrimestre 1" );
$obQuadrimestre1->setRotulo( "Quadrimestre 1" );
$obQuadrimestre1->setValue($rsQuadrimestre1->getCampo('vl_quadrimestre'));
$obQuadrimestre1->setMaxLength( 19 );
$obQuadrimestre1->setSize( 21 );
$obQuadrimestre1->setNegativo( false );
//$obQuadrimestre1->setNull( false );

$obQuadrimestre2 = new Numerico;
$obQuadrimestre2->setName( "nuQuadrimestre2" );
$obQuadrimestre2->setId( "nuQuadrimestre2" );
$obQuadrimestre2->setTitle( "Quadrimestre 2" );
$obQuadrimestre2->setRotulo( "Quadrimestre 2" );
$obQuadrimestre2->setValue($rsQuadrimestre2->getCampo('vl_quadrimestre'));
$obQuadrimestre2->setMaxLength( 19 );
$obQuadrimestre2->setSize( 21 );
$obQuadrimestre2->setNegativo( false );
//$obQuadrimestre2->setNull( false );

$obQuadrimestre3 = new Numerico;
$obQuadrimestre3->setName( "nuQuadrimestre3" );
$obQuadrimestre3->setId( "nuQuadrimestre3" );
$obQuadrimestre3->setTitle( "Quadrimestre 3" );
$obQuadrimestre3->setRotulo( "Quadrimestre 3" );
$obQuadrimestre3->setValue($rsQuadrimestre3->getCampo('vl_quadrimestre'));
$obQuadrimestre3->setMaxLength( 19 );
$obQuadrimestre3->setSize( 21 );
$obQuadrimestre3->setNegativo( false );
//$obQuadrimestre3->setNull( false );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName('stDescricao');
$obTxtDescricao->setId('stDescricao');
$obTxtDescricao->setValue('');
$obTxtDescricao->setRotulo('Descrição da Despesa');
$obTxtDescricao->setTitle('Informe a Descrição da Despesa');
$obTxtDescricao->setMaxLength(100);
$obTxtDescricao->setSize(100);

$obQuadrimestreValor1 = new Numerico;
$obQuadrimestreValor1->setName( "nuQuadrimestreValor1" );
$obQuadrimestreValor1->setId( "nuQuadrimestreValor1" );
$obQuadrimestreValor1->setTitle( "Valor Quadrimestre 1" );
$obQuadrimestreValor1->setRotulo( "Valor Quadrimestre 1" );
$obQuadrimestreValor1->setMaxLength( 19 );
$obQuadrimestreValor1->setSize( 21 );
$obQuadrimestreValor1->setNegativo( false );
//$obQuadrimestreValor1->setNull( false );

$obQuadrimestreValor2 = new Numerico;
$obQuadrimestreValor2->setName( "nuQuadrimestreValor2" );
$obQuadrimestreValor2->setId( "nuQuadrimestreValor2" );
$obQuadrimestreValor2->setTitle( "Valor Quadrimestre 2" );
$obQuadrimestreValor2->setRotulo( "Valor Quadrimestre 2" );
$obQuadrimestreValor2->setMaxLength( 19 );
$obQuadrimestreValor2->setSize( 21 );
$obQuadrimestreValor2->setNegativo( false );
//$obQuadrimestreValor2->setNull( false );

$obQuadrimestreValor3 = new Numerico;
$obQuadrimestreValor3->setName( "nuQuadrimestreValor3" );
$obQuadrimestreValor3->setId( "nuQuadrimestreValor3" );
$obQuadrimestreValor3->setTitle( "Valor Quadrimestre 3" );
$obQuadrimestreValor3->setRotulo( "Valor Quadrimestre 3" );
$obQuadrimestreValor3->setMaxLength( 19 );
$obQuadrimestreValor3->setSize( 21 );
$obQuadrimestreValor3->setNegativo( false );
//$obQuadrimestreValor3->setNull( false );

$obBtnIncluir = new Button();
$obBtnIncluir->setValue('Incluir');
$obBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluirDespesa','stDescricao,nuQuadrimestreValor1,nuQuadrimestreValor2,nuQuadrimestreValor3,stExercicio','true');");

$obBtnLimpar = new Button();
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->obEvento->setOnClick("montaParametrosGET('limparDespesa');");

$obSpnListaDespesas = new Span();
$obSpnListaDespesas->setId('spnListaDespesas');

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnExercicio );
$obFormulario->addTitulo( "Receita Corrente Líquida" );
$obFormulario->addComponente( $obQuadrimestre1 );
$obFormulario->addComponente( $obQuadrimestre2 );
$obFormulario->addComponente( $obQuadrimestre3 );
$obFormulario->addTitulo( "Despesas não Computadas" );
$obFormulario->addComponente( $obTxtDescricao );
$obFormulario->addComponente( $obQuadrimestreValor1 );
$obFormulario->addComponente( $obQuadrimestreValor2 );
$obFormulario->addComponente( $obQuadrimestreValor3 );
$obFormulario->agrupaComponentes( array($obBtnIncluir,$obBtnLimpar) );
$obFormulario->addSpan( $obSpnListaDespesas );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
SistemaLegado::executaFrameOculto("executaFuncaoAjax('montaListaDespesas')");
