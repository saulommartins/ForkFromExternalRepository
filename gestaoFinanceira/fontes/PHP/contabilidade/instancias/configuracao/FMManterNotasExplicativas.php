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
    * Página de Formulario de Incluir Notas Explicativas
    * Data de Criação: 03/09/2007

    * @author Analista      : Gelson Gonçalves
    * @author Desenvolvedor : Rodrigo S. Rodrigues

    * @ignore

    * $Id: FMManterNotasExplicativas.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeNotasExplicativas.class.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeNotasExplicativas.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotasExplicativas";

$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

include_once $pgJS;

Sessao::write('arValores', array());

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obHdnId = new Hidden();
$obHdnId->setName ( 'stHdnId' );
$obHdnId->setId   ( 'stHdnId' );

$rsAnexo = new RecordSet;
$obTContabilidadeNotaExplicativa   = new TContabilidadeNotasExplicativas;
$obTContabilidadeNotaExplicativa->recuperaAnexo ( $rsAnexo ,'');

// Define Objeto Select para Nome da Ação
$obCmbNomAcao = new Select;
$obCmbNomAcao->setName       ( "stNomAcao"                     );
$obCmbNomAcao->setId         ( "stNomAcao"                     );
$obCmbNomAcao->setValue      ( $stNomAcao                      );
$obCmbNomAcao->setRotulo     ( "Anexo"                         );
$obCmbNomAcao->setTitle      ( "Selecione o anexo"             );
$obCmbNomAcao->setCampoID    ( 'cod_acao'                      );
$obCmbNomAcao->setCampoDesc  ( "[nom_acao] [complemento_acao]" );
$obCmbNomAcao->addOption     ( '', 'Selecione'                 );
$obCmbNomAcao->setStyle      ( "width: 300px;"                 );
$obCmbNomAcao->preencheCombo ( $rsAnexo                        );

//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtDtInicial = new Data;
$obTxtDtInicial->setName("stDtInicial");
$obTxtDtInicial->setId("stDtInicial");
$obTxtDtInicial->setTitle("Informe a data inicial e final da nota explicativa");
$obTxtDtInicial->setValue($stDtInicial);
$obTxtDtInicial->setRotulo("*Data da Nota");

//Define objeto Label
$obLblData = new Label;
$obLblData->setValue("a");

//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtDtFinal = new Data;
$obTxtDtFinal->setName("stDtFinal");
$obTxtDtFinal->setId("stDtFinal");
$obTxtDtFinal->setTitle("Informe a data inicial e final da nota explicativa");
$obTxtDtFinal->setValue($stDtFinal);
$obTxtDtFinal->setRotulo("*Data da Nota");

// Define Objeto TextBox para Nota Explicativa
$obTxtNotaExplicativa = new TextArea;
$obTxtNotaExplicativa->setName          ( "stNotaExplicativa"                  );
$obTxtNotaExplicativa->setId            ( "stNotaExplicativa"                  );
$obTxtNotaExplicativa->setValue         ( $stNotaExplicativa                   );
$obTxtNotaExplicativa->setRotulo        ( "*Nota Explicativa"                  );
$obTxtNotaExplicativa->setTitle         ( "Digite o texto da nota explicativa" );
$obTxtNotaExplicativa->setNull          ( true                                 );
$obTxtNotaExplicativa->setRows          ( 5                                    );
$obTxtNotaExplicativa->setCols          ( 40                                    );
//$obTxtNotaExplicativa->setMaxCaracteres ( 400                                  );

// Define Objeto Button para Incluir Item
$obBtnIncluir = new Button;
$obBtnIncluir->setId                ( "incluir" );
$obBtnIncluir->setValue             ( "Incluir" );
$obBtnIncluir->obEvento->setOnClick ( "incluirCadastro('incluirListaCadastro'); $('stNomAcao').focus();" );

// Define Objeto Button para Limpar Item
$obBtnLimpar = new Button;
$obBtnLimpar->setId                ( "limpar" );
$obBtnLimpar->setValue             ( "Limpar" );
$obBtnLimpar->obEvento->setOnClick ( "limparCadastro(); $('stNomAcao').focus();" );

//Span da Listagem de itens de Itens Incluídos
$obSpnLista = new Span;
$obSpnLista->setID ( "spnListaItens" );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm                                     );
$obFormulario->addTitulo         ( "Dados para Cadastro de Notas Explicativas" );
$obFormulario->addHidden         ( $obHdnAcao                                  );
$obFormulario->addHidden         ( $obHdnCtrl                                  );
$obFormulario->addHidden         ( $obHdnId                                    );
$obFormulario->addComponente     ( $obCmbNomAcao                               );
$obFormulario->agrupaComponentes(array($obTxtDtInicial, $obLblData, $obTxtDtFinal));
$obFormulario->addComponente     ( $obTxtNotaExplicativa                       );
$obFormulario->agrupaComponentes ( array( $obBtnIncluir, $obBtnLimpar )        );
/* Dados da Listagem      */
$obFormulario->addSpan           ( $obSpnLista                                 );

$stLocation = $pgForm.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obFormulario->Cancelar( $stLocation );

$obFormulario->show();

$stJs="ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&cod_acao=".$_GET['cod_acao']."','carregarItem');";
$jsOnLoad = $stJs;
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
