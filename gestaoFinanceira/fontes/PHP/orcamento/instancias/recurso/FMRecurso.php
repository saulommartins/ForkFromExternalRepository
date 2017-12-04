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
    * Página de Formulario de Inclusao/Alteracao de Fornecedores
    * Data de Criação   : 14/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor: $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.01.05
*/

/*
$Log$
Revision 1.12  2007/05/21 18:58:43  melo
Bug #9229#

Revision 1.11  2006/08/17 18:46:45  jose.eduardo
Bug #6739#

Revision 1.10  2006/07/17 18:32:29  andre.almeida
Bug #6380#

Revision 1.9  2006/07/05 20:43:20  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GF_INCLUDE."validaGF.inc.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"           );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"      );
include_once ( CAM_GF_ORC_COMPONENTES."ISelectFonteRecurso.class.php"     );
include_once ( CAM_GF_ORC_MAPEAMENTO."TTCEPECodigoFonteTCE.class.php"     );
include_once ( CAM_GF_ORC_MAPEAMENTO."TTCEPECodigoFonteRecurso.class.php" );

//Define o nome dos arquivos PHP
$stRecurso = "Recurso";
$pgFilt = "FL".$stRecurso.".php";
$pgList = "LS".$stRecurso.".php";
$pgForm = "FM".$stRecurso.".php";
$pgProc = "PR".$stRecurso.".php";
$pgOcul = "OC".$stRecurso.".php";
$pgJS   = "JS".$stRecurso.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obRConfiguracaoOrcamento->consultarConfiguracao();

// Pega o código do estado para saber se é Pernambuco
$inCodUf = SistemaLegado::pegaConfiguracao('cod_uf');

if ($stAcao == 'alterar') {
    $obROrcamentoRecurso = new ROrcamentoRecurso;
    if( $_GET['inCodRecurso'] !== "" )
        $obROrcamentoRecurso->setCodRecurso( $_GET['inCodRecurso'] );

    if( $_GET['stExercicio'] )
        $obROrcamentoRecurso->setExercicio ( $_GET['stExercicio']  );

    $inCodRecurso = $_GET['inCodRecurso'];

    $obROrcamentoRecurso->consultarRecursoDireto( $rsRecurso );

    $utilizado = $obROrcamentoRecurso->verificaUtilizacao();

    $stNome            = $rsRecurso->getCampo( 'nom_recurso'  );
    $stFinalidade      = $rsRecurso->getCampo( 'finalidade'   );
    $stTipo            = $rsRecurso->getCampo( 'tipo'         );
    $inCodigoTC        = $rsRecurso->getCampo( 'codigo_tc'    );
    $inCodFonteRecurso = $rsRecurso->getCampo( 'cod_fonte'    );
    $stNomFonteRecurso = $obROrcamentoRecurso->getNomFonteRecurso();
    $inTipoEsfera      = $rsRecurso->getCampo( 'cod_tipo_esfera'  );
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
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

$obHdnCodRecurso = new Hidden;
$obHdnCodRecurso->setName ( "inCodRecurso" );
$obHdnCodRecurso->setValue( $inCodRecurso  );

$obHdnMascRecurso = new Hidden;
$obHdnMascRecurso->setName ( "stMascRecurso" );
$obHdnMascRecurso->setValue( $obRConfiguracaoOrcamento->getMascRecurso()  );

$obLblCodRecurso = new Label;
$obLblCodRecurso->setRotulo( "Código" );
$obLblCodRecurso->setName  ( "lblCodRecurso"   );
$obLblCodRecurso->setValue ( $inCodRecurso );

$obTxtCodRecurso = new TextBox;
$obTxtCodRecurso->setName     ( "inCodRecurso"   );
$obTxtCodRecurso->setValue    ( $inCodRecurso    );
$obTxtCodRecurso->setRotulo   ( "Código" );
$obTxtCodRecurso->setSize     ( 10 );
$obTxtCodRecurso->setMaxLength( strlen($obRConfiguracaoOrcamento->getMascRecurso()) );
$obTxtCodRecurso->setNull     ( false );
$obTxtCodRecurso->setTitle    ( "Código do Recurso" );
$obTxtCodRecurso->obEvento->setOnKeyUp("mascaraDinamico('".$obRConfiguracaoOrcamento->getMascRecurso()."', this, event);");
$obTxtCodRecurso->obEvento->setOnChange("buscaValor('mascaraRecurso','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."')");

$obTxtDescRecurso = new TextBox;
$obTxtDescRecurso->setName     ( "stNome"   );
$obTxtDescRecurso->setValue    ( $stNome    );
$obTxtDescRecurso->setRotulo   ( "Descrição" );
$obTxtDescRecurso->setSize     ( 80 );
$obTxtDescRecurso->setMaxLength( 80 );
$obTxtDescRecurso->setNull     ( false );
$obTxtDescRecurso->setTitle    ( "Descrição do Recurso" );

$obCmbTipo = new Select;
$obCmbTipo->setRotulo        ( "Tipo de Recurso");
$obCmbTipo->setName          ( "stTipo"         );
$obCmbTipo->setStyle         ( "width: 100px"   );
$obCmbTipo->setValue         ( $stTipo          );
$obCmbTipo->setNull          ( false            );
$obCmbTipo->setTitle         ( 'Informe o tipo do Recurso' );
$obCmbTipo->addOption        ( "", "Selecione"  );
$obCmbTipo->addOption        ( "V","Vinculado"  );
$obCmbTipo->addOption        ( "L","Livre"      );

// testa se estado configurado é Pernambuco (16)
if ($inCodUf == 16) {
    $obTTCEPECodigoFonteTCE = new TTCEPECodigoFonteTCE;
    $obTTCEPECodigoFonteTCE->recuperaTodos ($rsCodigoFonteTCE);
    
    $obCmbFonteTCE = new Select;
    $obCmbFonteTCE->setRotulo     ( "Código Fonte TCE" );
    $obCmbFonteTCE->setId         ( "inCodTCE" );
    $obCmbFonteTCE->setName       ( "inCodTCE" );
    $obCmbFonteTCE->setTitle      ( "Selecione o Código Fonte TCE" );
    $obCmbFonteTCE->setCampoID    ( "cod_fonte" );
    $obCmbFonteTCE->setCampoDesc  ( "[cod_fonte] - [descricao]" );
    $obCmbFonteTCE->addOption     ( "", "Selecione" );
    $obCmbFonteTCE->setNull       ( false );
    if ($stAcao == "alterar") {
        $obTTCEPECodigoFonteRecurso = new TTCEPECodigoFonteRecurso;
        $obTTCEPECodigoFonteRecurso->setDado('cod_recurso', $request->get('inCodRecurso'));
        $obTTCEPECodigoFonteRecurso->setDado('exercicio'  , $request->get('stExercicio'));
        $obTTCEPECodigoFonteRecurso->recuperaPorChave($rsCodigoFonteRecurso);
           
        $obCmbFonteTCE->setValue ( $rsCodigoFonteRecurso->getCampo('cod_fonte') );
    }
    $obCmbFonteTCE->preencheCombo ( $rsCodigoFonteTCE );
}

$obCampoTipo = $obCmbTipo;

$obISelectFonteRecurso = new ISelectFonteRecurso;
$obISelectFonteRecurso->setValue( $inCodFonteRecurso );

$obCampoFonteRecurso = $obISelectFonteRecurso;

$obTxtCodigoTC = new TextBox;
$obTxtCodigoTC->setName     ( "inCodigoTC" );
$obTxtCodigoTC->setValue    ( $inCodigoTC  );
$obTxtCodigoTC->setRotulo   ( "Código TC"  );
$obTxtCodigoTC->setSize     ( 6 );
$obTxtCodigoTC->setMaxLength( 6 );
$obTxtCodigoTC->setNull     ( true );
$obTxtCodigoTC->setTitle    ( "Informe o Código TC" );
$obTxtCodigoTC->setInteiro  ( true );

$obCmbTipoEsfera = new Select;
$obCmbTipoEsfera->setRotulo        ( "Tipo");
$obCmbTipoEsfera->setName          ( "inTipoEsfera" );
$obCmbTipoEsfera->setStyle         ( "width: 100px" );
$obCmbTipoEsfera->setValue         ( $inTipoEsfera );
$obCmbTipoEsfera->setNull          ( true );
$obCmbTipoEsfera->setTitle         ( 'Informe o tipo de esfera do Recurso' );
$obCmbTipoEsfera->addOption        ( "", "Selecione" );
$obCmbTipoEsfera->addOption        ( "1","Federal" );
$obCmbTipoEsfera->addOption        ( "2","Estadual" );
$obCmbTipoEsfera->addOption        ( "3","Municipal" );

$obTxtDetalhamento = new TextArea;
$obTxtDetalhamento->setName     ( "stFinalidade"   );
$obTxtDetalhamento->setValue    ( $stFinalidade    );
$obTxtDetalhamento->setRotulo   ( "Finalidade" );
$obTxtDetalhamento->setNull     ( false );
$obTxtDetalhamento->setTitle    ( "Finalidade do Recurso" );

if (Sessao::getExercicio() > '2008') {
    // Define Objeto Radio Para Tipo de conta
    $obRdCriarContaContabilSim = new Radio;
    $obRdCriarContaContabilSim->setName   ('boCriarContaContabil');
    $obRdCriarContaContabilSim->setId     ('boCriarContaContabil');
    $obRdCriarContaContabilSim->setRotulo('Criar Conta Contábil');
    $obRdCriarContaContabilSim->setValue  ('Sim'  );
    $obRdCriarContaContabilSim->setLabel  ('Sim'  );
    $obRdCriarContaContabilSim->setChecked(false);

    $obRdCriarContaContabilNao = new Radio;
    $obRdCriarContaContabilNao->setName ('boCriarContaContabil');
    $obRdCriarContaContabilNao->setId   ('boCriarContaContabil');
    $obRdCriarContaContabilNao->setRotulo('Criar Conta Contábil');
    $obRdCriarContaContabilNao->setValue('Nao');
    $obRdCriarContaContabilNao->setLabel('Não');
    $obRdCriarContaContabilNao->setChecked(true);
}

//****************************************//
//Monta FORMULARIO
//****************************************//

$boDestinacao = $obRConfiguracaoOrcamento->getDestinacaoRecurso();

if ($boDestinacao == 'true' && $stAcao == 'alterar') {
    $obTxtDescRecurso->setLabel ( true );
    if(!$utilizado) $obCampoFonteRecurso->setLabel ( true );
    $obTxtCodigoTC->setLabel ( true );
    $obTxtDetalhamento->setLabel ( true );
    if(!$utilizado) $obCampoTipo->setLabel ( true );
}

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-02.01.05"                );

$obFormulario->addTitulo( "Dados para o Recurso" );
$obFormulario->addHidden( $obHdnCtrl                   );
$obFormulario->addHidden( $obHdnAcao                   );
$obFormulario->addHidden( $obHdnMascRecurso            );

if ($stAcao == 'alterar') {
    $obFormulario->addComponente( $obLblCodRecurso     );
    $obFormulario->addHidden    ( $obHdnCodRecurso     );
} else {
    $obFormulario->addComponente( $obTxtCodRecurso     );
}
$obFormulario->addComponente( $obTxtDescRecurso        );
$obFormulario->addComponente( $obCampoTipo             );
$obFormulario->addComponente( $obCampoFonteRecurso     );
$obFormulario->addComponente( $obCmbTipoEsfera         );
$obFormulario->addComponente( $obTxtCodigoTC           );
if ($inCodUf == 16) {
    $obFormulario->addComponente( $obCmbFonteTCE           );
}
$obFormulario->addComponente( $obTxtDetalhamento       );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

if ($boDestinacao == 'true') {
    if ($stAcao == 'alterar') {
        $obFormulario->addTitulo( "Dados para a Destinação de Recursos" );
        include_once( CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
        $obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
        $obIMontaRecursoDestinacao->setCodRecurso  ( $inCodRecurso );
        $obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
        $obFormulario->Cancelar( $stLocation );
    }
    if ($stAcao =='incluir') {
        SistemaLegado::exibeAviso("Cadastro não permitido. O sistema está configurado para utilizar a Destinação de Recursos.","","erro");
        $obFormulario = new Formulario;
    }
} else {
    $obFormulario->Cancelar( $stLocation );
}
    $obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
