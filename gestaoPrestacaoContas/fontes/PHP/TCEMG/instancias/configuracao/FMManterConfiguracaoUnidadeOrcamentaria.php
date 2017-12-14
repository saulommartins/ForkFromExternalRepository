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
 * Página Formulário - Configuração Unidade Orçamentária
 * Data de Criação   : 16/01/2014

 * @author Analista: Eduardo Schitz
 * @author Desenvolvedor: Franver Sarmento de Moraes

 * @ignore
 *
 * $Id: FMManterConfiguracaoUnidadeOrcamentaria.php 60484 2014-10-23 18:43:36Z lisiane $
 * $Name: $
 * $Revision: 60484 $
 * $Author: lisiane $
 * $Date: 2014-10-23 16:43:36 -0200 (Thu, 23 Oct 2014) $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EXP_NEGOCIO . "RExportacaoTCEMGArqUniOrcam.class.php";
include_once( CAM_GF_ORC_NEGOCIO. "ROrcamentoDespesa.class.php"                      );


//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoUnidadeOrcamentaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once $pgJS;
include_once ($pgOcul);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "incluir";
}

//SistemaLegado::executaFramePrincipal( "BloqueiaFrames(true); buscaDado('MontaListaUniOrcam');" );

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto da ação stAcao
$obHdnCodOrgao = new Hidden;
$obHdnCodOrgao->setName ( "stCodOrgaoUnidade" );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define Span para DataGrid
$obSpnUniOrcam = new Span;
$obSpnUniOrcam->setId ( "spnUniOrcam" );
$obSpnUniOrcamConversao = new Span;
$obSpnUniOrcamConversao->setId ( "spnUniOrcamConversao" );

//Monta Lista de Orgão - Unidade
$obRExportacaoTCEMGArqUniOrcam = new RExportacaoTCEMGArqUniOrcam();
$obRExportacaoTCEMGArqUniOrcam->obRExportacaoTCEMGUniOrcam->setExercicio(Sessao::getExercicio());
$obRExportacaoTCEMGArqUniOrcam->obRExportacaoTCEMGUniOrcam->listar($rsUnidadeOrcamento);
$obRExportacaoTCEMGArqUniOrcam->obRExportacaoTCEMGUniOrcam->listarDadosConversao($rsUnidadeOrcamentoConversao);

if ($rsUnidadeOrcamento->getNumLinhas() != 0) {
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );

    $obLista->setRecordSet( $rsUnidadeOrcamento );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 1 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Orgão" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Unidade" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Identificador" );
    $obLista->ultimoCabecalho->setWidth( 1 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[num_orgao] - [nom_orgao]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[num_unidade] - [nom_unidade]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    // Define Objeto Combo para identificadores
    $obRExportacaoTCEMGArqUniOrcam->listarIdentificador($rsIdentificador);
    $obCmbIdentificador = new Select();
    $obCmbIdentificador->setName       ("inIdentificador_[num_orgao]_[num_unidade]_"   );
    $obCmbIdentificador->setRotulo     (""                                             );
    $obCmbIdentificador->addOption     (0,"Selecione"                                 );
    $obCmbIdentificador->setCampoId    ("identificador"                                );
    $obCmbIdentificador->setCampoDesc  ("[cod_identificador] - [nom_identificador]"    );
    $obCmbIdentificador->preencheCombo ($rsIdentificador                               );
    $obCmbIdentificador->setNull       ( false                                         );
    $obCmbIdentificador->setStyle('width:250');
    $obCmbIdentificador->setTitle      ("Selecione o identificador"                    );
    $obCmbIdentificador->setValue      ("identificador");

    $obLista->addDadoComponente( $obCmbIdentificador );
    $obLista->ultimoDado->setCampo( "identificador" );
    $obLista->commitDadoComponente();
    
    //Define objeto BuscaInner para cgm
    $obBscCGM = new BuscaInner;
    $obBscCGM->setRotulo              ( "CGM"                         );
    $obBscCGM->setTitle               ( "Selecione o CGM"             );
    $obBscCGM->setNull                ( false                         );
    $obBscCGM->setName                ( 'stNomCGM'                    );
    $obBscCGM->setValue               ( "nom_cgm_responsavel"         );
    $obBscCGM->obCampoCod->setId      ( "inNumCGM_[cod_entidade]"     );
    $obBscCGM->obCampoCod->setSize(8);
    $obBscCGM->obCampoCod->setName    ( "inNumCGM_[cod_entidade]"     );
    $obBscCGM->obCampoCod->setValue   ( "num_cgm"                     );
    $obBscCGM->setValoresBusca(CAM_GPC_TCEMG_INSTANCIAS.'configuracao/OCManterConfiguracaoUnidadeOrcamentaria.php?'.Sessao::getId(),$obForm->getName(),'validaCGM');
    $obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','stNomCGM','geral','".Sessao::getId()."','800','550');" );
    
    $obLista->addCabecalho('Ordenador de Despesa', 12);
    $obLista->addDadoComponente( $obBscCGM );
    $obLista->commitDadoComponente();
    
  

    $obLista->montaHTML();

    $obSpnUniOrcam->setValue($obLista->getHTML());
    
    if ($rsUnidadeOrcamentoConversao->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsUnidadeOrcamentoConversao );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 1 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Exercício" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Orgão" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Unidade" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Identificador" );
        $obLista->ultimoCabecalho->setWidth( 1 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "exercicio" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "num_orgao" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "num_unidade" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        // Define Objeto Combo para identificadores
        $obRExportacaoTCEMGArqUniOrcam->listarIdentificador($rsIdentificador);
        $obCmbIdentificador = new Select();
        $obCmbIdentificador->setName("inIdentificadorConversao_[num_orgao]_[num_unidade]_[exercicio]_");
        $obCmbIdentificador->setRotulo("");
        $obCmbIdentificador->addOption(0,"Selecione");
        $obCmbIdentificador->setCampoId("identificador");
        $obCmbIdentificador->setCampoDesc("[cod_identificador] - [nom_identificador]");
        $obCmbIdentificador->preencheCombo($rsIdentificador);
        $obCmbIdentificador->setNull( false );
        $obCmbIdentificador->setStyle('width:250');
        $obCmbIdentificador->setTitle("Selecione o identificador");
        $obCmbIdentificador->setValue("identificador");

        $obLista->addDadoComponente( $obCmbIdentificador );
        $obLista->ultimoDado->setCampo( "identificador" );
        $obLista->commitDadoComponente();
        
        //Define objeto BuscaInner para cgm
        $obBscCGM = new BuscaInner;
        $obBscCGM->setRotulo              ( "CGM"                         );
        $obBscCGM->setTitle               ( "Selecione o CGM"             );
        $obBscCGM->setNull                ( false                         );
        $obBscCGM->setName                ( 'stNomCGMConversao'           );
        $obBscCGM->setValue               ( "nom_cgm_responsavel"         );
        $obBscCGM->obCampoCod->setId      ( "inNumCGMConversao_[num_orgao]_[num_unidade]_[exercicio]_"     );
        $obBscCGM->obCampoCod->setSize(8);
        $obBscCGM->obCampoCod->setName    ( "inNumCGMConversao_[num_orgao]_[num_unidade]_[exercicio]_"     );
        $obBscCGM->obCampoCod->setValue   ( "cgm_ordenador"               );
        $obBscCGM->setValoresBusca(CAM_GPC_TCEMG_INSTANCIAS.'configuracao/OCManterConfiguracaoUnidadeOrcamentaria.php?'.Sessao::getId(),$obForm->getName(),'validaCGM');
        $obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGMConversao','stNomCGMConversao','geral','".Sessao::getId()."','800','550');" );
        
        $obLista->addCabecalho('Ordenador de Despesa', 50);
        $obLista->addDadoComponente( $obBscCGM );
        $obLista->commitDadoComponente();
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Órgão Atual" );
        $obLista->ultimoCabecalho->setWidth( 1 );
        $obLista->commitCabecalho();

        $obRDespesa = new ROrcamentoDespesa;
        
        //Monta combo para seleção de ORGÃO ORCAMENTARIO
        $obRDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio());
        $obRDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar($rsOrgao, "ORDER BY num_orgao");
       
        $obCmbOrgao = new Select;
        $obCmbOrgao->setName('inCodOrgao');
        $obCmbOrgao->setValue('[cod_orgao]-[num_orgao_atual]-[exercicio_atual]');
        $obCmbOrgao->setRotulo('Orgão');
        $obCmbOrgao->setStyle("width: 320px");
        $obCmbOrgao->setNull(true);
        $obCmbOrgao->setCampoId("[cod_orgao]-[num_orgao]-[exercicio]");
        $obCmbOrgao->setCampoDesc("[num_orgao] - [nom_orgao]");
        $obCmbOrgao->addOption("", "Selecione");
        $obCmbOrgao->obEvento->setOnBlur("preencheUnidadeOrcamentaria( 'buscaValoresUnidade',(this.name));");
        $obCmbOrgao->preencheCombo($rsOrgao);
    
        //Monta combo para seleção de UNIDADE ORCAMENTARIA
        $obCmbUnidade = new Select;
        $obCmbUnidade->setName('inMontaCodUnidadeM');
        $obCmbUnidade->setValue('[num_orgao_atual]-[num_unidade_atual]-[exercicio_atual]');
        $obCmbUnidade->setRotulo('Unidade');
        $obCmbUnidade->setStyle("width: 320px");
        $obCmbUnidade->setCampoId("num_unidade");
        $obCmbUnidade->setCampoDesc("[num_unidade] - [nom_nom_unidade]");
        $obCmbUnidade->addOption("", "Selecione");
        $obCmbUnidade->setNull(true);
        
        $obLista->addDadoComponente( $obCmbOrgao );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->ultimoDado->setCampo( 'num_orgao_atual' );
        $obLista->commitDadoComponente();
        $obLista->addCabecalho();
        
        $obLista->ultimoCabecalho->addConteudo( "Unidade Orçamentária Atual" );
        $obLista->ultimoCabecalho->setWidth( 1 );
        $obLista->commitCabecalho();
        
        $obLista->addDadoComponente( $obCmbUnidade );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->ultimoDado->setCampo( 'num_unidade_atual' );
        $obLista->commitDadoComponente();
        
        $obHdnCodOrgaoAtual = new Hidden;
        $obHdnCodOrgaoAtual->setName ( "hdnInCodOrgaoAtual_" );
        $obHdnCodOrgaoAtual->setValue( '[cod_orgao]-[num_orgao_atual]-[exercicio_atual]' );
        
        $obHdnUnidadeAtual = new Hidden;
        $obHdnUnidadeAtual->setName ( "hdnInCodUnidadeAtual_" );
        $obHdnUnidadeAtual->setValue( '[num_orgao_atual]-[num_unidade_atual]-[exercicio_atual]' );
        
        $obLista->addDadoComponente( $obHdnCodOrgaoAtual );
        $obLista->commitDadoComponente();
        
        $obLista->addDadoComponente( $obHdnUnidadeAtual );
        $obLista->commitDadoComponente();
        
        $obLista->montaHTML();

        $obSpnUniOrcamConversao->setValue($obLista->getHTML());
    }
    
}


//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados de Unidade Orçamentária do Exercício Atual" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addSpan( $obSpnUniOrcam );

$obFormulario->addTitulo( "Dados de Unidades Orçamentárias da Conversão de Dados" );
$obFormulario->addSpan( $obSpnUniOrcamConversao );

$obBtnOK = new Ok(true);

$obFormulario->defineBarra(array($obBtnOK));
$obFormulario->show();

processarForm(true,"Form",$stAcao,$rsUnidadeOrcamentoConversao);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
