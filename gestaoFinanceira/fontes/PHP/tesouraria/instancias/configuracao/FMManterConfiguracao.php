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
    * Página de Formulário de Configuração do Módulo Tesouraria
    * Data de Criação : 01/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31732 $
    $Name$
    $Author: tonismar $
    $Date: 2008-01-08 15:09:37 -0200 (Ter, 08 Jan 2008) $

    * Casos de uso: uc-02.04.01
*/

/*
$Log$
Revision 1.18  2007/09/26 12:54:49  cako
Ticket#10211#

Revision 1.17  2007/09/21 21:09:44  cako
Ticket#10211#

Revision 1.16  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php" );
include_once(CAM_GF_TES_NEGOCIO."RTesourariaAutenticacao.class.php" );

$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$obRTesourariaConfiguracao = new RTesourariaConfiguracao();
$obRTesourariaConfiguracao->setExercicio( Sessao::getExercicio() );
$obRTesourariaConfiguracao->consultarTesouraria();

//CONSULTA PARA VERIFICAR A MOVIMENTAÇÃO DA TESOURARIA
//ESTA VERIFICAÇÃO SERVIRÁ PARA BLOQUEAR OS CAMPOS FORMA DE COMPROVAÇÃO
//E NUMERAÇÃO DE COMPROVAÇÃO CASO TENHA OCORRIDO ALGUMA MOVIMENTAÇÃO
$obRTesourariaAutenticacao = new RTesourariaAutenticacao();
$obRTesourariaAutenticacao->setDataAutenticacao( Sessao::getExercicio() );
$obRTesourariaAutenticacao->consultarMovimentacao($boMovimentacao);
//FINAL DA VERIFICAÇÃO

$inFormaComprovacao    = $obRTesourariaConfiguracao->getFormaComprovacao();
$inNumeracaoComprovacao= $obRTesourariaConfiguracao->getNumeracaoComprovacao();
$inNumeroVias          = $obRTesourariaConfiguracao->getViasComprovacao();
$boReiniciaComprovacao = $obRTesourariaConfiguracao->getReiniciarNumeracao();
$stDigitos             = $obRTesourariaConfiguracao->getDigitos();
$stOcultarMovimentacoes = ( $obRTesourariaConfiguracao->getOcultarMovimentacoes() ) ? "S" : "N" ;

if( !$inNumeracaoComprovacao ) $inNumeracaoComprovacao = 1;

/*$arAssinatura = array();
$inCount = 0;
$arRTesourariaAssinatura = $obRTesourariaConfiguracao->getAssinatura();
if ($arRTesourariaAssinatura) {
    foreach ($arRTesourariaAssinatura as $obRTesourariaAssinatura) {
        $arAssinatura[$inCount]['id_assinatura' ] = $inCount;
        $arAssinatura[$inCount]['numcgm'        ] = $obRTesourariaAssinatura->obRCGM->getNumCGM();
        $arAssinatura[$inCount]['nom_cgm'       ] = $obRTesourariaAssinatura->obRCGM->getNomCGM();
        $arAssinatura[$inCount]['cargo'         ] = $obRTesourariaAssinatura->getCargo();
        $arAssinatura[$inCount]['situacao'      ] = $obRTesourariaAssinatura->getSituacao();
        $arAssinatura[$inCount]['cod_entidade'  ] = $obRTesourariaAssinatura->obROrcamentoEntidade->getCodigoEntidade();
        $inCount++;
    }
}
Sessao::write('assinaturas', $arAssinatura);

SistemaLegado::executaFramePrincipal( "montaListaAssinatura( '".$inNumeracaoComprovacao."', '".$boReiniciaComprovacao."' );" );
*/
$rsEntidade = new RecordSet;
if (Sessao::read('numCgm')) {
    $obRTesourariaConfiguracao->addAssinatura();
    $obRTesourariaConfiguracao->roUltimaAssinatura->obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
    $obRTesourariaConfiguracao->roUltimaAssinatura->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm'));
    $obRTesourariaConfiguracao->roUltimaAssinatura->obROrcamentoEntidade->listarUsuariosEntidade ( $rsEntidade);
}

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnIdAssinatura = new Hidden;
$obHdnIdAssinatura->setName( "inIdAssinatura" );
$obHdnIdAssinatura->setValue( "" );

$obHdnFormaComprovacao = new Hidden;
$obHdnFormaComprovacao->setName( "inFormaComprovacao" );
$obHdnFormaComprovacao->setValue( $inFormaComprovacao );

$obHdnNumeracaoComprovacao = new Hidden;
$obHdnNumeracaoComprovacao->setName( "inNumeracaoComprovacao" );
$obHdnNumeracaoComprovacao->setValue( $inNumeracaoComprovacao );

// Define Objeto Select para forma de comprovação
$obCmbFormaComprovacao = new Select();
$obCmbFormaComprovacao->setRotulo ( "Forma de Comprovação" );
$obCmbFormaComprovacao->setTitle  ( "Selecione a Forma de Comprovação a ser utilizada" );
$obCmbFormaComprovacao->setName   ( "inFormaComprovacao"   );
$obCmbFormaComprovacao->addOption ( "0","Nenhum"           );
$obCmbFormaComprovacao->addOption ( "1","Comprovante"      );
$obCmbFormaComprovacao->addOption ( "2","Autenticação"     );
$obCmbFormaComprovacao->setValue  ( $inFormaComprovacao    );
$obCmbFormaComprovacao->setStyle  ( "width: 120px"         );
$obCmbFormaComprovacao->setNull   ( false                  );

// Define objeto Select para tipo de númeração de comprovação
$obCmbTipoNumeracao = new Select;
$obCmbTipoNumeracao->setRotulo    ( "Numeração de Comprovação" );
$obCmbTipoNumeracao->setTitle     ( "Selecione a forma de Numeração de Comprovação a ser utilizada" );
$obCmbTipoNumeracao->setName      ( "inNumeracaoComprovacao"   );
$obCmbTipoNumeracao->addOption    ( "1","Diária"               );
$obCmbTipoNumeracao->addOption    ( "2","Anual"                );
$obCmbTipoNumeracao->setValue     ( $inNumeracaoComprovacao     );
$obCmbTipoNumeracao->setStyle     ( "width: 120px"             );
$obCmbTipoNumeracao->setNull      ( false                      );
$obCmbTipoNumeracao->obEvento->setOnChange( "buscaDado( 'montarReiniciarNumeracao', '".$boReiniciaComprovacao."' );" );

// Define objeto Span para
$obSpnResetaNumeracao = new Span();
$obSpnResetaNumeracao->setId( 'spnResetaNumeracao' );

// Define objeto TextBox para numero de vias de comprovação
$obTxtNumeroVias = new TextBox();
$obTxtNumeroVias->setRotulo     ( 'Nr. de Vias de Comprovação' );
$obTxtNumeroVias->setTitle      ( "Defina o número de vias de comprovação a serem impressas" );
$obTxtNumeroVias->setName       ( 'inNumeroVias'               );
$obTxtNumeroVias->setValue      ( $inNumeroVias                );
$obTxtNumeroVias->setInteiro    ( true                         );
$obTxtNumeroVias->setMaxLength  ( 2                            );
$obTxtNumeroVias->setSize       ( 3                            );
$obTxtNumeroVias->setNull       ( false                        );

// Define objeto TextBox para Dígitos de Autenticação
$obTxtDigitos = new TextBox();
$obTxtDigitos->setRotulo     ( 'Dígitos de Autenticação' );
$obTxtDigitos->setTitle      ( "Informe as letras iniciais da linha de autenticação" );
$obTxtDigitos->setName       ( 'stDigitos'               );
$obTxtDigitos->setValue      ( $stDigitos                );
$obTxtDigitos->setMaxLength  ( 10                        );
$obTxtDigitos->setSize       ( 9                         );
$obTxtDigitos->setNull       ( false                     );

// Define objeto SimNao para ocultar movimentaçoes de conciliaçao
$obRdOcultarMovimentacao = new SimNao();
$obRdOcultarMovimentacao->setRotulo     ( 'Ocultar Movimentações Conciliadas' );
$obRdOcultarMovimentacao->setName       ( 'stOcultarMovimentacoes' );
$obRdOcultarMovimentacao->setNull       ( false                    );
$obRdOcultarMovimentacao->setChecked    ( $stOcultarMovimentacoes  );
$obRdOcultarMovimentacao->setDefinicao  ( "radio"                  );

// Define Objeto Select para Entidade
$obCmbEntidade = new Select();
$obCmbEntidade->setRotulo    ( "*Entidade"                 );
$obCmbEntidade->setName      ( "inCodEntidade"            );
$obCmbEntidade->setTitle     ( "Selecione a Entidade para definir as assinaturas do Boletim"     );
$obCmbEntidade->setCampoId   ( "cod_entidade"             );
$obCmbEntidade->setCampoDesc ( "nom_cgm"                  );
$obCmbEntidade->setValue     ( $inCodEntidade             );
$obCmbEntidade->setNull      ( true                       );
if ($rsEntidade->getNumLinhas() > 1) {
    $obCmbEntidade->addOption    ( ""            ,"Selecione" );
    $obCmbEntidade->obEvento->setOnChange( "montaListaAssinatura( '".$inNumeracaoComprovacao."', '".$boReiniciaComprovacao."' );");
} else $jsSL = "montaListaAssinatura( '".$inNumeracaoComprovacao."', '".$boReiniciaComprovacao."' );";
$obCmbEntidade->preencheCombo( $rsEntidade                );

// Define objeto BuscaInner para cgm
$obBscCGM = new BuscaInner();
$obBscCGM->setRotulo                 ( "*CGM"            );
$obBscCGM->setTitle                  ( "Informe o CGM que deseja pesquisar"                );
$obBscCGM->setId                     ( "stNomCgm"        );
$obBscCGM->setValue                  ( $stNomCgm         );
$obBscCGM->setNull                   ( true              );
$obBscCGM->obCampoCod->setName       ( "inNumCgm"        );
$obBscCGM->obCampoCod->setSize       ( 10                );
$obBscCGM->obCampoCod->setMaxLength  ( 8                 );
$obBscCGM->obCampoCod->setValue      ( $inNumCgm         );
$obBscCGM->obCampoCod->setAlign      ( "left"            );
$obBscCGM->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCgm','stNomCgm','fisica','".Sessao::getId()."','800','550');");
$obBscCGM->setValoresBusca           ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() ,'fisica');

// Define objeto TextBox para cargo
$obTxtCargo = new TextBox();
$obTxtCargo->setRotulo     ( '*Cargo'     );
$obTxtCargo->setTitle      ( 'Informe o Cargo referente ao CGM pesquisado'     );
$obTxtCargo->setName       ( 'stCargo'    );
$obTxtCargo->setValue      ( $stCargo     );
$obTxtCargo->setSize       ( 75           );
$obTxtCargo->setMaxLength  ( 1000         );
$obTxtCargo->setNull       ( true         );

// Define objeto Select para situação
$obCmbSituacao = new Select();
$obCmbSituacao->setRotulo ( "*Situação"     );
$obCmbSituacao->setName   ( "boSituacao"    );
$obCmbSituacao->setTitle  ( "Informe a Situação da assinatura referente ao CGM pesquisado"    );
$obCmbSituacao->addOption ( "t","Ativo"     );
$obCmbSituacao->addOption ( "f","Inativo"   );
$obCmbSituacao->setValue  ( "t"             );
$obCmbSituacao->setNull   ( true            );

// Define objeto Button para incluir assinatura
$obBtnIncluir = new Button;
$obBtnIncluir->setValue( "Incluir" );
$obBtnIncluir->obEvento->setOnClick( "incluirAssinatura();" );

// Define Objeto Button para limpar
$obBtnLimpar = new Button;
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick( "limparAssinatura();" );

// Define objeto span para lista de assinaturas
$obSpnLista = new Span();
$obSpnLista->setId( "spnLista" );

$obOk = new Ok;

//DEFINICAO DOS COMPONENTES

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm            ( $obForm                 );
$obFormulario->addHidden          ( $obHdnAcao              );
$obFormulario->addHidden          ( $obHdnCtrl              );
$obFormulario->addHidden          ( $obHdnIdAssinatura      );

$obFormulario->addTitulo          ( "Configuração Inicial"   );
$obFormulario->addComponente      ( $obCmbFormaComprovacao   );
$obFormulario->addComponente      ( $obCmbTipoNumeracao      );
$obFormulario->addSpan            ( $obSpnResetaNumeracao    );
$obFormulario->addComponente      ( $obTxtNumeroVias         );
$obFormulario->addComponente      ( $obTxtDigitos            );
$obFormulario->addComponente      ( $obRdOcultarMovimentacao );

$obFormulario->addTitulo          ( "Assinatura do Boletim da Tesouraria"   );
$obFormulario->addComponente      ( $obCmbEntidade                          );
$obFormulario->addComponente      ( $obBscCGM                               );
$obFormulario->addComponente      ( $obTxtCargo                             );
$obFormulario->addComponente      ( $obCmbSituacao                          );
$obFormulario->agrupaComponentes  ( array( $obBtnIncluir, $obBtnLimpar )    );
$obFormulario->addSpan            ( $obSpnLista                             );

$obFormulario->defineBarra( array( $obOk ) );

$obFormulario->show();
if ($jsSL) SistemaLegado::executaFrameOculto($jsSL);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
