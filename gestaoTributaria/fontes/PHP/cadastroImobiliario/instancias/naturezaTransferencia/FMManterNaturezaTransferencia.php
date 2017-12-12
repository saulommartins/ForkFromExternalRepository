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
    * Página de Formulário para o cadastro de natureza de transferência
    * Data de Criação   : 07/10/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Vitor Davi Valentini

    * @ignore

    * $Id: FMManterNaturezaTransferencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.16
*/

/*
$Log$
Revision 1.7  2006/09/18 10:31:03  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNaturezaTransferencia.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterNaturezaTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$obRCIMNaturezaTransferencia = new RCIMNaturezaTransferencia;

$arDocumentosSessao =  array();
Sessao::write('Documentos', $arDocumentosSessao);

if ($_REQUEST['stAcao'] == "incluir") {
    $boAutomaticaNatureza = 'Sim';
} else {
    $inCodigoNatureza = $_REQUEST["inCodigoNatureza"];
    $obRCIMNaturezaTransferencia->setCodigoNatureza ( $inCodigoNatureza );
    $obRCIMNaturezaTransferencia->consultarNaturezaTransferencia();

    $stDescricaoNatureza  = $obRCIMNaturezaTransferencia->getDescricaoNatureza();
    $boAutomaticaNatureza = $obRCIMNaturezaTransferencia->getAutomaticaNatureza();

    $obRCIMNaturezaTransferencia->consultarDocumentosNatureza();

    if ($boAutomaticaNatureza == 't') {
        $boAutomaticaNatureza = 'Sim';
    } else {
        $boAutomaticaNatureza = 'Não';
    }

    $obLblCodigoNatureza = new Label;
    $obLblCodigoNatureza->setRotulo ( "Código" );
    $obLblCodigoNatureza->setValue  ( $inCodigoNatureza );
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                   );
$obHdnAcao->setValue                            ( $_REQUEST['stAcao']                                        );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                        );
$obHdnCtrl->setValue                            ( $_REQUEST['stCtrl']                                                         );

$obHdnCodigoNatureza =  new Hidden;
$obHdnCodigoNatureza->setName                   ( "inCodigoNatureza"                                         );
$obHdnCodigoNatureza->setValue                  ( $_REQUEST['inCodigoNatureza']                              );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName  ("campoNum");
$obHdnCampoNum->setValue ($_REQUEST['campoNum']);

$obTxtDescricaoNatureza = new TextBox;
$obTxtDescricaoNatureza->setName                ( "stDescricaoNatureza"                                      );
$obTxtDescricaoNatureza->setId                  ( "stDescricaoNatureza"                                      );
$obTxtDescricaoNatureza->setSize                ( 80                                                         );
$obTxtDescricaoNatureza->setMaxLength           ( 80                                                         );
$obTxtDescricaoNatureza->setNull                ( false                                                      );
$obTxtDescricaoNatureza->setRotulo              ( "Descrição"                                                );
$obTxtDescricaoNatureza->setValue               ( $stDescricaoNatureza                                       );
//$obTxtDescricaoNatureza->setAlfaNumerico        ( 't'                                                        );
$obTxtDescricaoNatureza->obEvento->setOnChange  ( "buscaValor('buscaDescricao');"                            );

$obRdbAutomaticaNatureza = new SimNao;
$obRdbAutomaticaNatureza->setRotulo             ( "*Automático"                                              );
$obRdbAutomaticaNatureza->setName               ( "boAutomaticaNatureza"                                     );
$obRdbAutomaticaNatureza->setChecked            ( $boAutomaticaNatureza                                      );
$obRdbAutomaticaNatureza->setTitle              ( "Define se a transferência será efetivada com o pagamento" );
$obRdbAutomaticaNatureza->obRadioSim->setValue  ( "Sim"                                                      );
$obRdbAutomaticaNatureza->obRadioNao->setValue  ( "Não"                                                      );

$obTxtDescricaoDocumento = new TextBox;
$obTxtDescricaoDocumento->setName               ( "stDescricaoDocumento"                                     );
$obTxtDescricaoDocumento->setId                 ( "stDescricaoDocumento"                                     );
$obTxtDescricaoDocumento->setSize               ( 80                                                         );
$obTxtDescricaoDocumento->setMaxLength          ( 80                                                         );
$obTxtDescricaoDocumento->setRotulo             ( "Nome"                                                     );
$obTxtDescricaoDocumento->setValue              ( $stDescricaoDocumento                                      );
$obTxtDescricaoDocumento->setTitle              ( "Inclui um documento na lista abaixo"                      );
//$obTxtDescricaoDocumento->setAlfaNumerico       ( 't'                                                        );

$obRdbNaoDocumento = new Radio;
$obRdbNaoDocumento->setRotulo                   ( "Obrigatório"                                              );
$obRdbNaoDocumento->setName                     ( "boObrigatorioDocumento"                                   );
$obRdbNaoDocumento->setLabel                    ( "Não"                                                      );
$obRdbNaoDocumento->setValue                    ( "Não"                                                      );
$obRdbNaoDocumento->setChecked                  ( true                                                       );
$obRdbNaoDocumento->setTitle                    ( "Informa se o documento é obrigatório"                     );

$obRdbCadastroDocumento = new Radio;
$obRdbCadastroDocumento->setRotulo              ( "Obrigatório"                                              );
$obRdbCadastroDocumento->setName                ( "boObrigatorioDocumento"                                   );
$obRdbCadastroDocumento->setLabel               ( "Cadastro"                                                 );
$obRdbCadastroDocumento->setValue               ( "Cadastro"                                                 );
$obRdbCadastroDocumento->setChecked             ( false                                                      );

$obRdbEfetivacaoDocumento = new Radio;
$obRdbEfetivacaoDocumento->setRotulo            ( "Obrigatório"                                              );
$obRdbEfetivacaoDocumento->setName              ( "boObrigatorioDocumento"                                   );
$obRdbEfetivacaoDocumento->setLabel             ( "Efetivação"                                               );
$obRdbEfetivacaoDocumento->setValue             ( "Efetivação"                                               );
$obRdbEfetivacaoDocumento->setChecked           ( false                                                      );

$obBtnIncluir = new Button;
$obBtnIncluir->setName                          ( "btnIncluirDocumentos"                                     );
$obBtnIncluir->setValue                         ( "Incluir"                                                  );
//$obBtnIncluir->obEvento->setOnClick             ( "return incluirDocumentos('incluirDocumentos');"                              );
$obBtnIncluir->obEvento->setOnClick             ( "incluirDocumentos();"                              );

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimparDocumentos"                                      );
$obBtnLimpar->setValue                          ( "Limpar"                                                   );
$obBtnLimpar->obEvento->setOnClick              ( "limparDocumentos();"                                      );

$obBtnOk = new Ok;

$obBtnLimparFormulario = new Button;
$obBtnLimparFormulario->setName                 ( "btnLimparFormulario"                                      );
$obBtnLimparFormulario->setValue                ( "Limpar"                                                   );
$obBtnLimparFormulario->obEvento->setOnClick    ( "limparFormulario();"                                      );

$obSpnDocumentos = new Span;
$obSpnDocumentos->setId                         ( "spnDocumentosNatureza"                                    );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( 'oculto' );

if ($_REQUEST['stAcao'] == "alterar") {
    $arDocumentosSessao = $obRCIMNaturezaTransferencia->getDocumentosNatureza();
    Sessao::write('Documentos', $arDocumentosSessao);

    SistemaLegado::executaFramePrincipal("buscaValor('ListaDocumento');");
}

//DEFINICAO DO FORMULARIO
$obFomulario = new Formulario;
$obFomulario->addForm               ( $obForm                                                                         );
$obFomulario->setAjuda ( "UC-05.01.16" );
$obFomulario->addTitulo             ( "Dados para natureza de transferência"                                          );
$obFomulario->addHidden             ( $obHdnAcao                                                                      );
$obFomulario->addHidden             ( $obHdnCtrl                                                                      );
$obFomulario->addHidden             ( $obHdnCodigoNatureza                                                            );
$obFomulario->addHidden             ( $obHdnCampoNum );
if ($_REQUEST['stAcao'] == "alterar") {
    $obFomulario->addComponente     ( $obLblCodigoNatureza                                                            );
}
$obFomulario->addComponente         ( $obTxtDescricaoNatureza                                                         );
$obFomulario->addComponente         ( $obRdbAutomaticaNatureza                                                        );
$obFomulario->addTitulo             ( "Documentos"                                                                    );
$obFomulario->addComponente         ( $obTxtDescricaoDocumento                                                        );
$obFomulario->agrupaComponentes     ( array( $obRdbNaoDocumento, $obRdbCadastroDocumento, $obRdbEfetivacaoDocumento ) );
$obFomulario->defineBarra           ( array( $obBtnIncluir, $obBtnLimpar ), "left", ""                                );
$obFomulario->addSpan               ( $obSpnDocumentos                                                                );
if ($_REQUEST['stAcao'] == "incluir") {
    $obFomulario->defineBarra       ( array( $obBtnOk, $obBtnLimparFormulario )                                       );
} else {
    $obFomulario->Cancelar();
}
$obFomulario->setFormFocus          ( $obTxtDescricaoNatureza->getId()                                                );
$obFomulario->show();

?>
