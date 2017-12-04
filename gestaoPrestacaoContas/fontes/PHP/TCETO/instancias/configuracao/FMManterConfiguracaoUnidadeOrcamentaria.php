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
    * Pacote de configuração do TCETO - Formulário Configurar Unidade Orçamentária
    * Data de Criação   : 05/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: FMManterConfiguracaoUnidadeOrcamentaria.php 60654 2014-11-06 13:18:49Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoEmpenhoAutorizacao.class.php';
include_once CAM_GPC_TCETO_NEGOCIO.'RExportacaoTCETOArqUniOrcam.class.php';
include_once CAM_FW_HTML.'MontaAtributos.class.php';

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
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) )
    $stAcao = "incluir";

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
$obSpnUnidadeOrcamentaria = new Span;
$obSpnUnidadeOrcamentaria->setId ( "spnUniOrcam" );
$obSpnUnidadeOrcamentariaConversao = new Span;
$obSpnUnidadeOrcamentariaConversao->setId ( "spnUniOrcamConversao" );

$obRExportacaoTCETOArqUniOrcam= new RExportacaoTCETOArqUniOrcam();
$obRExportacaoTCETOArqUniOrcam->obRExportacaoTCETOUniOrcam->setExercicio(Sessao::getExercicio());
$obRExportacaoTCETOArqUniOrcam->obRExportacaoTCETOUniOrcam->listar($rsUnidadeOrcamento);
$obRExportacaoTCETOArqUniOrcam->obRExportacaoTCETOUniOrcam->listarDadosConversao($rsUnidadeOrcamentoConversao);

if ($rsUnidadeOrcamento->getNumLinhas() != 0) {
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );

    $obLista->setRecordSet( $rsUnidadeOrcamento );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Orgão" );
    $obLista->ultimoCabecalho->setWidth( 21 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Unidade" );
    $obLista->ultimoCabecalho->setWidth( 23 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Identificador" );
    $obLista->ultimoCabecalho->setWidth( 7 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "CGM" );
    $obLista->ultimoCabecalho->setWidth( 15 );
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
    $obRExportacaoTCETOArqUniOrcam->listarIdentificador($rsIdentificador);
    $obCmbIdentificador = new Select();
    $obCmbIdentificador->setName        ("inIdentificador_[num_orgao]_[num_unidade]_"   );
    $obCmbIdentificador->setRotulo      (""                                             );
    $obCmbIdentificador->addOption      ("","Selecione"                                 );
    $obCmbIdentificador->setCampoId     ("identificador"                                );
    $obCmbIdentificador->setCampoDesc   ("[cod_identificador] - [nom_identificador]"    );
    $obCmbIdentificador->preencheCombo  ($rsIdentificador                               );
    $obCmbIdentificador->setNull        ( false                                         );
    $obCmbIdentificador->setTitle       ("Selecione o identificador"                    );
    $obCmbIdentificador->setValue       ("identificador");

    $obLista->addDadoComponente( $obCmbIdentificador );
    $obLista->ultimoDado->setCampo( "identificador" );
    $obLista->commitDadoComponente();

    //Define objeto BuscaInner para cgm
    $obBscCGM = new BuscaInner;
    $obBscCGM->setRotulo            ( "CGM"                     );
    $obBscCGM->setTitle             ( "Selecione o CGM"         );
    $obBscCGM->setNull              ( false                     );
    $obBscCGM->setName              ( 'stNomCGM'                );
    $obBscCGM->setValue             ( "nom_cgm"                 );
    $obBscCGM->obCampoCod->setId    ( "inNumCGM_[cod_entidade]" );
    $obBscCGM->obCampoCod->setSize  ( 8                         );
    $obBscCGM->obCampoCod->setName  ( "inNumCGM_[cod_entidade]" );
    $obBscCGM->obCampoCod->setValue ( "numcgm"                  );
     $obBscCGM->setValoresBusca(CAM_GPC_TCETO_INSTANCIAS.'configuracao/OCManterConfiguracaoUnidadeOrcamentaria.php?'.Sessao::getId(),$obForm->getName(),'validaCGM' );
    $obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','stNomCGM','juridica','".Sessao::getId()."','800','550');"   );
     
    $obLista->addDadoComponente( $obBscCGM );
    $obLista->commitDadoComponente();

    $obLista->montaHTML();

    $obSpnUnidadeOrcamentaria->setValue($obLista->getHTML());

    if ($rsUnidadeOrcamentoConversao->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );

        $obLista->setRecordSet( $rsUnidadeOrcamentoConversao );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Exercício" );
        $obLista->ultimoCabecalho->setWidth( 4 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Orgão" );
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Unidade" );
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Identificador" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "CGM" );
        $obLista->ultimoCabecalho->setWidth( 15 );
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
        $obRExportacaoTCETOArqUniOrcam->listarIdentificador($rsIdentificador);
        $obCmbIdentificador = new Select();
        $obCmbIdentificador->setName        ( "inIdentificadorConversao_[num_orgao]_[num_unidade]_[exercicio]_");
        $obCmbIdentificador->setRotulo      ( ""                                            );
        $obCmbIdentificador->addOption      ( "","Selecione"                                );
        $obCmbIdentificador->setCampoId     ( "identificador"                               );
        $obCmbIdentificador->setCampoDesc   ( "[cod_identificador] - [nom_identificador]"   );
        $obCmbIdentificador->preencheCombo  ( $rsIdentificador                              );
        $obCmbIdentificador->setNull        ( false                                         );
        $obCmbIdentificador->setTitle       ( "Selecione o identificador"                   );
        $obCmbIdentificador->setValue       ( "identificador"                               );

        $obLista->addDadoComponente( $obCmbIdentificador );
        $obLista->ultimoDado->setCampo( "identificador" );
        $obLista->commitDadoComponente();

        //Define objeto BuscaInner para cgm
        $obBscCGM = new BuscaInner;
        $obBscCGM->setRotulo            ( "CGM"                 );
        $obBscCGM->setTitle             ( "Selecione o CGM"     );
        $obBscCGM->setNull              ( false                 );
        $obBscCGM->setName              ( 'stNomCGMConversao'   );
        $obBscCGM->setValue             ( "nom_cgm"             );
        $obBscCGM->obCampoCod->setSize  ( 5                     );
        $obBscCGM->obCampoCod->setName  ( "inNumCGMConversao_"  );
        $obBscCGM->obCampoCod->setId    ( "inNumCGMConversao_"  );
        $obBscCGM->obCampoCod->setValue ( "numcgm"              );
        $obBscCGM->setValoresBusca(CAM_GPC_TCETO_INSTANCIAS.'configuracao/OCManterConfiguracaoUnidadeOrcamentaria.php?'.Sessao::getId(),$obForm->getName(),'validaCGM'  );
        $obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGMConversao','','juridica','".Sessao::getId()."','800','550')"   );

        $obLista->addDadoComponente( $obBscCGM );
        $obLista->commitDadoComponente();

        $obLista->montaHTML();

        $obSpnUnidadeOrcamentariaConversao->setValue($obLista->getHTML());
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

$obFormulario->addSpan( $obSpnUnidadeOrcamentaria );

$obFormulario->addTitulo( "Dados de Unidades Orçamentárias da Conversão de Dados" );
$obFormulario->addSpan( $obSpnUnidadeOrcamentariaConversao );

$obFormulario->defineBarra( array( new Ok(true) ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
