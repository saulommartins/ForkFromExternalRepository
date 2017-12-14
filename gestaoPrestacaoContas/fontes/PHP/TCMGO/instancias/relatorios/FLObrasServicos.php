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
* Página de filtro do relatório
* Data de Criação   : 30/07/2008

* @author Analista: Tonismar Bernardo
* @author Desenvolvedor: Eduardo Schitz

    $Id: FLObrasServicos.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioExtratoBancario.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ObrasServicos";
$pgFilt = "FL".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma.".php";

$rsUsuariosDisponiveis = $rsUsuariosSelecionados = new recordSet;

$obRTesourariaRelatorioExtratoBancario  = new RTesourariaRelatorioExtratoBancario;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$rsUsuariosDisponiveis = $rsUsuariosSelecionados = new recordSet;
$stOrdem               = " ORDER BY C.nom_cgm";

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRTesourariaRelatorioExtratoBancario->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade($rsUsuariosDisponiveis, " ORDER BY cod_entidade");

while ( !$rsUsuariosDisponiveis->eof() ) {
    $arFiltroNom['entidade'][$rsUsuariosDisponiveis->getCampo( 'cod_entidade' )] = $rsUsuariosDisponiveis->getCampo( 'nom_cgm' );
    $rsUsuariosDisponiveis->proximo();
}
Sessao::write('filtroNomRelatorio', $arFiltroNom);

$rsUsuariosDisponiveis->setPrimeiroElemento();

//Recupera Mascara da Classificao de Despesa
$obROrcamentoDespesa = new ROrcamentoDespesa;
$obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setExercicio(Sessao::getExercicio());
$mascClassificacao = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgGera );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//insere o máscara como hidden
$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $mascClassificacao );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodigoEntidadesSelecionadas');
$obCmbEntidades->setRotulo ( "Entidade" );
$obCmbEntidades->setTitle  ( "Selecione a Entidade." );
$obCmbEntidades->setNull   ( true );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsUsuariosDisponiveis->getNumLinhas()==1) {
       $rsUsuariosSelecionados = $rsUsuariosDisponiveis;
       $rsUsuariosDisponiveis  = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodigoEntidadesDisponiveis');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsUsuariosDisponiveis );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodigoEntidadesSelecionadas');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsUsuariosSelecionados );

$obMes = new Mes;
$obMes->setNull            ( false );
$obMes->setTitle           ( "Informe o mês de movimentação." );
$obMes->setPeriodo         ( true );
$obMes->setExercicio       ( Sessao::getExercicio() );

//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtCodEmpenhoInicial = new TextBox;
$obTxtCodEmpenhoInicial->setName     ( "inCodEmpenhoInicial" );
$obTxtCodEmpenhoInicial->setValue    ( $inCodEmpenhoInicial  );
$obTxtCodEmpenhoInicial->setRotulo   ( "Número do Empenho"   );
$obTxtCodEmpenhoInicial->setTitle    ( "Informe o número do empenho." );
$obTxtCodEmpenhoInicial->setInteiro  ( true                  );
$obTxtCodEmpenhoInicial->setNull     ( true                  );

//Define objeto Label
$obLblEmpenho = new Label;
$obLblEmpenho->setValue( "a" );

//Define o objeto TEXT para Codigo do Empenho Final
$obTxtCodEmpenhoFinal = new TextBox;
$obTxtCodEmpenhoFinal->setName     ( "inCodEmpenhoFinal" );
$obTxtCodEmpenhoFinal->setValue    ( $inCodEmpenhoFinal  );
$obTxtCodEmpenhoFinal->setRotulo   ( "Número do Empenho" );
$obTxtCodEmpenhoFinal->setTitle    ( "Informe o número do empenho." );
$obTxtCodEmpenhoFinal->setInteiro  ( true                );
$obTxtCodEmpenhoFinal->setNull     ( true                );

$obCodObraInicial = new Inteiro();
$obCodObraInicial->setRotulo('Número da Obra');
$obCodObraInicial->setName  ('inCodObraInicial');
$obCodObraInicial->setId    ('inCodObraInicial');
$obCodObraInicial->setValue ($inCodObraInicial);
$obCodObraInicial->setMaxLength( 4 );
$obCodObraInicial->setSize  ( 5 );

//Define objeto Label
$obLblObra = new Label;
$obLblObra->setValue( "a" );

$obCodObraFinal = new Inteiro();
$obCodObraFinal->setRotulo('Número da Obra');
$obCodObraFinal->setName  ('inCodObraFinal');
$obCodObraFinal->setId    ('inCodObraFinal');
$obCodObraFinal->setValue ($inCodObraFinal);
$obCodObraFinal->setMaxLength( 4 );
$obCodObraFinal->setSize  ( 5 );

$obTxtCodEstruturalInicial = new TextBox;
$obTxtCodEstruturalInicial->setRotulo            ( "Código Estrutural Inicial" );
$obTxtCodEstruturalInicial->setTitle             ( "Informe o código estrutural inicial para filtro." );
$obTxtCodEstruturalInicial->setName              ( "stCodEstruturalInicial" );
$obTxtCodEstruturalInicial->setValue             ( $stCodEstruturalInicial  );
$obTxtCodEstruturalInicial->setSize              ( 20 );
$obTxtCodEstruturalInicial->setMaxLength         ( 50 );
$obTxtCodEstruturalInicial->setMascara           ($mascClassificacao);

$obTxtCodEstruturalFinal = new TextBox;
$obTxtCodEstruturalFinal->setRotulo            ( "Código Estrutural Final" );
$obTxtCodEstruturalFinal->setTitle             ( "Informe o código estrutural final para filtro." );
$obTxtCodEstruturalFinal->setName              ( "stCodEstruturalFinal" );
$obTxtCodEstruturalFinal->setValue             ( $stCodEstruturalFinal  );
$obTxtCodEstruturalFinal->setSize              ( 20 );
$obTxtCodEstruturalFinal->setMaxLength         ( 50 );
$obTxtCodEstruturalFinal->setMascara           ($mascClassificacao);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden         ( $obHdnCtrl            );
$obFormulario->addHidden         ( $obHdnAcao            );

$obFormulario->addTitulo    ( "Dados para Filtro"   );
$obFormulario->addComponente( $obCmbEntidades       );
$obFormulario->addComponente( $obMes            );
$obFormulario->agrupaComponentes    ( array( $obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal ) );
$obFormulario->agrupaComponentes    ( array( $obCodObraInicial, $obLblObra, $obCodObraFinal ) );
$obFormulario->addComponente( $obTxtCodEstruturalInicial      );
$obFormulario->addComponente( $obTxtCodEstruturalFinal      );

$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
