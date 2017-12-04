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
    * Página de Formulario de Inclusão/Alteração de Metas de Arrecadação de Receita
    * Data de Criação   : 27/07/2004

    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.15  2007/05/21 18:58:13  melo
Bug #9229#

Revision 1.14  2007/03/15 17:58:48  vitor
#8632#

Revision 1.13  2007/02/28 13:23:23  luciano
#7317#

Revision 1.12  2007/01/30 15:31:20  luciano
#7317#

Revision 1.11  2007/01/30 11:41:38  luciano
#7317#

Revision 1.10  2006/10/19 14:13:53  cako
Bug #7242#

Revision 1.9  2006/07/14 19:50:12  leandro.zis
Bug #6382#

Revision 1.8  2006/07/05 20:43:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoOrgaoOrcamentario.class.php"         );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"       );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoDespesa.class.php"  );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                   );
include_once ( CAM_FW_HTML."MontaOrgaoUnidade.class.php"      );

//Define o nome dos arquivos PHP
$stPrograma = "MetasDespesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

$obRPrevisaoDespesa             = new ROrcamentoPrevisaoDespesa;
$obROrcamentoOrgaoOrcamentario  = new ROrcamentoOrgaoOrcamentario;
$obMontaOrgaoUnidade            = new MontaOrgaoUnidade;
$obROrcamentoUnidadeOrcamentaria= new ROrcamentoUnidadeOrcamentaria;
$obROrcamentoDespesa            = new ROrcamentoDespesa;
$obRConfiguracaoOrcamento   	= new ROrcamentoConfiguracao;
Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

/* Removido a pedido da Carina #7317#
$stEval .= " if (document.frm.stDotacaoOrcamentaria.value == '' || document.frm.stDotacaoOrcamentaria.value == '00.00') { \n";
$stEval .= "    mensagem = '@Informe ao menos um órgao.'; \n";
$stEval .= "    erro = true; \n";
$stEval .= " }    \n";
*/

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval"            );
$obHdnEval->setValue ( $stEval             );

//Define o objeto TEXT para armazenar a DESCRICAO DO ORGAO
$obTxtDesc = new TextBox;
$obTxtDesc->setName     ( "stDescricao" );
$obTxtDesc->setRotulo   ( "Descrição" );
$obTxtDesc->setSize     ( 80 );
$obTxtDesc->setMaxLength( 80 );
$obTxtDesc->setNull     ( true );
$obTxtDesc->setTitle    ( 'Informe a descrição.' );

// Rubrica Despesa
$stMascaraRubrica    = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();

//Define o objeto TEXT para Codigo do Rubrica Despesa Inicial
$obTxtCodRubricaDespesaInicial = new TextBox;
$obTxtCodRubricaDespesaInicial->setRotulo   ( "Rubrica de Despesa"   );
$obTxtCodRubricaDespesaInicial->setTitle    ( "Informe o número da rubrica de despesa." );
$obTxtCodRubricaDespesaInicial->setName     ( "inCodRubricaDespesaInicial" );
$obTxtCodRubricaDespesaInicial->setId     	( "inCodRubricaDespesaInicial" );
$obTxtCodRubricaDespesaInicial->setValue    ( $inCodRubricaDespesaInicial  );
$obTxtCodRubricaDespesaInicial->setMascara  ( $stMascaraRubrica  );
$obTxtCodRubricaDespesaInicial->setPreencheComZeros('D');
$obTxtCodRubricaDespesaInicial->setNull     ( true                  );
$obTxtCodRubricaDespesaInicial->setSize     ( strlen($stMascaraRubrica) );
$obTxtCodRubricaDespesaInicial->setMaxLength( strlen($stMascaraRubrica) );
$obTxtCodRubricaDespesaInicial->obEvento->setOnFocus("selecionaValorCampo( this );");
$obTxtCodRubricaDespesaInicial->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");

//Define objeto Label
$obLblCodRubricaDespesa = new Label;
$obLblCodRubricaDespesa->setValue( "a" );

//Define o objeto TEXT para Codigo do Rubrica Despesa Final
$obTxtCodRubricaDespesaFinal = new TextBox;
$obTxtCodRubricaDespesaFinal->setRotulo   ( "Rubrica de Despesa" );
$obTxtCodRubricaDespesaFinal->setTitle    ( "Informe o número da rubrica de despesa." );
$obTxtCodRubricaDespesaFinal->setName     ( "inCodRubricaDespesaFinal" );
$obTxtCodRubricaDespesaFinal->setId       ( "inCodRubricaDespesaFinal" );
$obTxtCodRubricaDespesaFinal->setValue    ( $inCodRubricaDespesaFinal  );
$obTxtCodRubricaDespesaFinal->setMascara  ( $stMascaraRubrica  );
$obTxtCodRubricaDespesaFinal->setPreencheComZeros('D');
$obTxtCodRubricaDespesaFinal->setNull     ( true                );
$obTxtCodRubricaDespesaFinal->setSize     ( strlen($stMascaraRubrica) );
$obTxtCodRubricaDespesaFinal->setMaxLength( strlen($stMascaraRubrica) );
$obTxtCodRubricaDespesaFinal->obEvento->setOnFocus("selecionaValorCampo( this );");
$obTxtCodRubricaDespesaFinal->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");

//Define o objeto INNER para armazenar o ENTIDADE
$obROrcamentoDespesa->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obROrcamentoDespesa->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade , " ORDER BY cod_entidade" );

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName   ('inNumCGM');
$obHdnNumCGM->setValue  ( $obROrcamentoDespesa->obROrcamentoEntidade->obRCGM->getNumCGM() );

$obCmbEntidade = new Select;
$obCmbEntidade->setName      ( 'inCodEntidade'   );
$obCmbEntidade->setValue     (  $inCodEntidade   );
$obCmbEntidade->setTitle     (  "Selecione a entidade."   );
$obCmbEntidade->setRotulo    ( 'Entidade'        );
$obCmbEntidade->setStyle     ( "width: 400px"    );
$obCmbEntidade->setNull      ( false             );
$obCmbEntidade->setCampoId   ( "cod_entidade"    );
$obCmbEntidade->setCampoDesc ( "[cod_entidade] - [nom_cgm]" );
if ($rsEntidade->getNumLinhas()>1) {
    $obCmbEntidade->addOption              ( "", "Selecione"               );
}
$obCmbEntidade->preencheCombo( $rsEntidade       );

// Pop Uo de intervalo de Dotação
include_once(CAM_GF_ORC_COMPONENTES."IIntervaloPopUpDotacao.class.php"                                         );
$obPopUpIntervaloDotacao = new IIntervaloPopUpDotacao ( serialize($obCmbEntidade )) ;

//Define o objeto TEXT para armazenar o CÓDIGO DO CGM
$obTxtOrgaoUnidade = new TextBox;
$obTxtOrgaoUnidade->setName     ( "stNomOrgaoUnidade" );
$obTxtOrgaoUnidade->setValue    ( $stNomOrgaoUnidade  );
$obTxtOrgaoUnidade->setRotulo   ( "Órgão ou Unidade" );
$obTxtOrgaoUnidade->setTitle    ( "Selecione o órgão ou unidade orçamentária." );
$obTxtOrgaoUnidade->setSize     ( 80 );
$obTxtOrgaoUnidade->setMaxLength( 80 );
$obTxtOrgaoUnidade->setNull     ( true );

$obMontaOrgaoUnidade->setActionAnterior ( $pgOcul );
$obMontaOrgaoUnidade->setActionPosterior( $pgList );
$obMontaOrgaoUnidade->setTarget( 'telaPrincipal' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda         ( "UC-02.01.06" );

$obFormulario->addHidden        ( $obHdnNumCGM  );
$obFormulario->addHidden        ( $obHdnCtrl    );
$obFormulario->addHidden        ( $obHdnAcao    );
$obFormulario->addHidden        ( $obHdnEval, true    );
$obFormulario->addTitulo        ( "Dados para filtro" );
$obFormulario->addComponente    ( $obCmbEntidade      );
$obFormulario->addComponente    ( $obPopUpIntervaloDotacao );
$obFormulario->agrupaComponentes( array( $obTxtCodRubricaDespesaInicial,$obLblCodRubricaDespesa, $obTxtCodRubricaDespesaFinal ));
$obFormulario->addComponente    ( $obTxtDesc    );
$obMontaOrgaoUnidade->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

//***************************************
// Preenche combos e campos Inner
//***************************************

//if ($stAcao == 'alterar') {
//    $js .= "buscaValor( 'preencheInner' ,'".$pgOcul."' , '".$pgProc."' , 'oculto' ,'".Sessao::getId()."');";
//    SistemaLegado::executaFramePrincipal($js);
//}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
