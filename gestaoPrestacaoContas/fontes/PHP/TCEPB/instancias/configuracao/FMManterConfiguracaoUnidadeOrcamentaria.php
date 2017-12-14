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
 * Data de Criação   : 22/07/2014

 * @author Analista: Silvia Martins
 * @author Desenvolvedor: Lisiane Morais

 * @ignore
 *
 * $Id: FMManterConfiguracaoUnidadeOrcamentaria.php 59612 2014-09-02 12:00:51Z gelson $
 * $Name:$
 * $Revision:$
 * $Author:$
 * $Date:$
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EXP_NEGOCIO . "RExportacaoTCEPBArqUniOrcam.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoUnidadeOrcamentaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

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
$obRExportacaoTCEPBArqUniOrcam = new RExportacaoTCEPBArqUniOrcam();
$obRExportacaoTCEPBArqUniOrcam->obRExportacaoTCEPBUniOrcam->setExercicio(Sessao::getExercicio());
$obRExportacaoTCEPBArqUniOrcam->obRExportacaoTCEPBUniOrcam->listar($rsUnidadeOrcamento);

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
    $obLista->ultimoCabecalho->addConteudo( "Natureza Jurídica" );
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
    
    // Define Objeto Combo para Natureza Jurídica
    $obRExportacaoTCEPBArqUniOrcam->listarNaturezaJuridica($rsNaturezaJuridica);
    $obCmbNaturezaJuridica = new Select();
    $obCmbNaturezaJuridica->setName       ( "inNaturezaJuridica_"   );
    $obCmbNaturezaJuridica->setRotulo     ( ""                                             );
    $obCmbNaturezaJuridica->addOption     ( 0,"Selecione"                                  );
    $obCmbNaturezaJuridica->setCampoId    ( "natureza_juridica"                            );
    $obCmbNaturezaJuridica->setCampoDesc  ( "[cod_natureza_juridica] - [nom_natureza_juridica]" );
    $obCmbNaturezaJuridica->preencheCombo ( $rsNaturezaJuridica                               );
    $obCmbNaturezaJuridica->setNull       ( false                                          );
    $obCmbNaturezaJuridica->setStyle      ( 'width:150'                                    );
    $obCmbNaturezaJuridica->setTitle      ( "Selecione o tipo da natureza jurídica"        );
    $obCmbNaturezaJuridica->setValue      ( "natureza_juridica"                            );

    $obLista->addDadoComponente( $obCmbNaturezaJuridica );
    $obLista->ultimoDado->setCampo( "natureza_juridica" );
    $obLista->commitDadoComponente();

    //Define objeto BuscaInner para cgm
    $obBscCGM = new BuscaInner;
    $obBscCGM->setRotulo              ( "CGM"                         );
    $obBscCGM->setTitle               ( "Selecione o CGM"             );
    $obBscCGM->setNull                ( false                         );
    $obBscCGM->setName                ( 'stNomCGM'                    );
    $obBscCGM->setValue               ( "nom_cgm_responsavel"         );
    $obBscCGM->obCampoCod->setId      ( "inNumCGM_[num_orgao]_[num_unidade]"     );
    $obBscCGM->obCampoCod->setSize(8);
    $obBscCGM->obCampoCod->setName    ( "inNumCGM_[num_orgao]_[num_unidade]_"     );
    $obBscCGM->obCampoCod->setValue   ( "num_cgm"                     );
    $obBscCGM->setValoresBusca($pgOcul.'?'.Sessao::getId(),$obForm->getName(),'validaCGM');
    $obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','stNomCGM','fisica','".Sessao::getId()."','800','550');" );
    $obLista->addCabecalho('Ordenador de Despesa', 2);
    $obLista->addDadoComponente( $obBscCGM );
    $obLista->commitDadoComponente();

    $obLista->montaHTML();

    $obSpnUniOrcam->setValue($obLista->getHTML());
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

$obBtnOK = new Ok(true);

$obFormulario->defineBarra(array($obBtnOK));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
