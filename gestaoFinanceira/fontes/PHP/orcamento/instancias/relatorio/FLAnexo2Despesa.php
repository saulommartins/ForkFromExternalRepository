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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 23/09/2004

    * @author Desenvolvedor: Gustavo Passos Tourinho
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 30762 $
    $Name$
    $Author: bruce $
    $Date:

    * Casos de uso: uc-02.01.11
*/

/*
$Log$
Revision 1.13  2007/08/14 15:36:57  bruce
Bug#9896#

Revision 1.12  2007/03/14 21:18:04  vitor
#6420#

Revision 1.11  2006/07/19 18:18:55  leandro.zis
Bug #6389#

Revision 1.10  2006/07/14 20:10:59  leandro.zis
Bug #6389#

Revision 1.9  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoOrgaoOrcamentario.class.php"         );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"       );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoDespesa.class.php"  );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                   );
include_once ( CAM_FW_HTML."MontaOrgaoUnidade.class.php"      );

//Define o nome dos arquivos PHP
$stPrograma = "Anexo2Despesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRPrevisaoDespesa              = new ROrcamentoPrevisaoDespesa;
$obROrcamentoOrgaoOrcamentario   = new ROrcamentoOrgaoOrcamentario;
$obMontaOrgaoUnidade             = new MontaOrgaoUnidade;
$obROrcamentoUnidadeOrcamentaria = new ROrcamentoUnidadeOrcamentaria;
$obROrcamentoDespesa             = new ROrcamentoDespesa;

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
$arNomFiltro = Sessao::read('filtroNomRelatorio');
while ( !$rsEntidades->eof() ) {
    $arNomFiltro['entidade'][$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
$rsRecordset = new RecordSet;

Sessao::write('filtroNomRelatorio',$arNomFiltro);

//filtro de entidades
$stEntidades = " AND cod_entidade IN(";

while ( !$rsEntidades->eof() ) {
    $stEntidades .= $rsEntidades->getCampo('cod_entidade') . ",";
    $rsEntidades->proximo();
}
$stEntidades = substr( $stEntidades, 0, strlen($stEntidades) - 1 ) . ")";
$rsEntidades->setPrimeiroElemento();

Sessao::remove('filtroRelatorio');
Sessao::remove('link');

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
$pgRelatorio = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
$obForm->setAction( $pgRelatorio );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_ORC_INSTANCIAS."relatorio/PRAnexo2Despesa.php" );

$obHdnTodasEntidades = new Hidden;
$obHdnTodasEntidades->setName("stTodasEntidades");
$obHdnTodasEntidades->setValue( $stEntidades );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o objeto INNER para armazenar o ENTIDADE

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName   ('inNumCGM');
$obHdnNumCGM->setValue  ( $obREntidade->obRCGM->getNumCGM() );

//Define o objeto TEXT para armazenar o CÓDIGO DO CGM
$obTxtOrgaoUnidade = new TextBox;
$obTxtOrgaoUnidade->setName     ( "stNomOrgaoUnidade" );
$obTxtOrgaoUnidade->setId       ( "stNomOrgaoUnidade" );
$obTxtOrgaoUnidade->setRotulo   ( "Órgão ou Unidade" );
$obTxtOrgaoUnidade->setTitle    ( "Selecione o órgão ou unidade orçamentária." );
$obTxtOrgaoUnidade->setSize     ( 80 );
$obTxtOrgaoUnidade->setMaxLength( 80 );
$obTxtOrgaoUnidade->setNull     ( true );

$obMontaOrgaoUnidade->setActionAnterior ( $pgOcul );
$obMontaOrgaoUnidade->setActionPosterior( $pgRelatorio );
$obMontaOrgaoUnidade->setTarget( 'oculto' );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades." );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

$obCmbSituacao= new Select;
$obCmbSituacao->setRotulo              ( "Situação"                      );
$obCmbSituacao->setTitle               ( "Selecione a situação."         );
$obCmbSituacao->setName                ( "stSituacao"                    );
$obCmbSituacao->setValue               ( 3                               );
$obCmbSituacao->setStyle               ( "width: 200px"                  );
$obCmbSituacao->addOption              ( "", "Selecione"                 );
$obCmbSituacao->addOption              ( "empenhados", "Empenhados"      );
$obCmbSituacao->addOption              ( "liquidados", "Liquidados"      );
$obCmbSituacao->addOption              ( "pagos", "Pagos"                );
$obCmbSituacao->setNull                ( true                            );

//Radios de Tipo de Relatório
$obRdbTipoResumo = new Radio;
$obRdbTipoResumo->setRotulo ( "Tipo de Relatório" );
$obRdbTipoResumo->setTitle  ( "Selecione o tipo de relatório." );
$obRdbTipoResumo->setChecked( true );
$obRdbTipoResumo->setName   ( "stTipoRelatorio" );
$obRdbTipoResumo->setValue  ( "resumo" );
$obRdbTipoResumo->setLabel  ( "Resumo Geral" );
$obRdbTipoResumo->setNull   ( false );

$obRdbTipoOrgao = new Radio;
$obRdbTipoOrgao->setName   ( "stTipoRelatorio" );
$obRdbTipoOrgao->setValue  ( "orgao" );
$obRdbTipoOrgao->setLabel  ( "Por Órgão" );
$obRdbTipoOrgao->setNull   ( false );

$obRdbTipoOrgaoUnidade = new Radio;
$obRdbTipoOrgaoUnidade->setName   ( "stTipoRelatorio" );
$obRdbTipoOrgaoUnidade->setValue  ( "orgao_unidade" );
$obRdbTipoOrgaoUnidade->setLabel  ( "Por Órgão e Unidade" );
$obRdbTipoOrgaoUnidade->setNull   ( false );

$obRdbTipoUnidadeCategoria = new Radio;
$obRdbTipoUnidadeCategoria->setName   ( "stTipoRelatorio" );
$obRdbTipoUnidadeCategoria->setValue  ( "unidade_categoria_economica" );
$obRdbTipoUnidadeCategoria->setLabel  ( "Por Unidade Segundo Categorias Econômicas" );
$obRdbTipoUnidadeCategoria->setNull   ( false );

$obCmbDemonstrarValores= new Select;
$obCmbDemonstrarValores->setRotulo  ( "Demonstrar Valores"      );
$obCmbDemonstrarValores->setTitle   ( "Selecione demonstrar valores." );
$obCmbDemonstrarValores->setName    ( "stDemonstrarValores"     );
$obCmbDemonstrarValores->setValue   ( 3                         );
$obCmbDemonstrarValores->setStyle   ( "width: 200px"            );
$obCmbDemonstrarValores->addOption  ( "balanco", "Balanço"      );
$obCmbDemonstrarValores->addOption  ( "orcamento", "Orçamento"  );
$obCmbDemonstrarValores->setNull    ( false                     );
$obCmbDemonstrarValores->obEvento->setOnChange( "if (document.frm.stDemonstrarValores.value=='orcamento') {document.frm.stSituacao.value='';document.frm.stSituacao.disabled=true;} else {document.frm.stSituacao.disabled=false;}");

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValue          ( 4                 );
$obPeriodicidade->setValidaExercicio (true);

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( 'UC-02.01.11' );

$obFormulario->addHidden        ( $obHdnNumCGM );
$obFormulario->addHidden        ( $obHdnCtrl );
$obFormulario->addHidden        ( $obHdnAcao );
$obFormulario->addHidden        ( $obHdnCaminho );
$obFormulario->addHidden        ( $obHdnTodasEntidades );
$obFormulario->addTitulo        ( "Dados para Filtro" );
$obFormulario->addComponente    ( $obCmbEntidades );
$obFormulario->addComponente    ( $obPeriodicidade );
$obFormulario->agrupaComponentes( array($obRdbTipoResumo, $obRdbTipoOrgao, $obRdbTipoOrgaoUnidade, $obRdbTipoUnidadeCategoria) );
$obFormulario->addComponente    ( $obCmbDemonstrarValores );
$obFormulario->addComponente    ( $obCmbSituacao );
$obMontaOrgaoUnidade->geraFormulario( $obFormulario );

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

//***************************************
// Preenche combos e campos Inner
//***************************************

//if ($stAcao == 'alterar') {
//    $js .= "buscaValor( 'preencheInner' ,'".$pgOcul."' , '".$pgProc."' , 'oculto' ,'".Sessao::getId()."');";
//    SistemaLegado::executaFramePrincipal($js);
//}
include_once ($pgJS);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
