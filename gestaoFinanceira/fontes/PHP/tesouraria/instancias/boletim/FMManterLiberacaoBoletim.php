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
    * Filtro para Liberação de Boletim
    * Data de Criação   : 14/12/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore
    * Casos de uso: uc-02.04.08    , uc-02.04.25
    $Id: FMManterLiberacaoBoletim.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CLA_IAPPLETTERMINAL                                                                        );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"                                          );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterLiberacaoBoletim";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

// DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl"                );
$obHdnCtrl->setValue    ( $request->get("stCtrl") );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obApplet = new IAppletTerminal( $obForm );

include_once ( CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeUsuario.class.php' );
$obCmbEntidade = new ITextBoxSelectEntidadeUsuario;

$inCodEntidade = $request->get('inCodEntidade');

if ($inCodEntidade) {
    $obCmbEntidade->inCodEntidade = $inCodEntidade;
    $obCmbEntidade->obTextBox->obEvento->setOnChange("montaParametrosGET('Limpar');");
}

$obMes = new Mes;
$obMes->setExercicio ( Sessao::getExercicio() );
$obMes->setPeriodo   ( true );
$obMes->setNull ( false );

if ($request->get('inMes')) {
    $obMes->obMes->setValue( $request->get('inMes') );
    $obMes->obDataInicial->setValue($request->get('stDataInicial'));
    $obMes->obDataFinal->setValue($request->get('stDataFinal'));
}

$obListar = new Button;
$obListar->setValue( "Listar" );
$obListar->obEvento->setOnClick( "montaParametrosGET('montaLista');");

$jsOnload="";
if ($request->get('inCodEntidade') && $request->get('inMes')) {
    $jsOnload = "montaParametrosGET('montaLista');";
}

$obSpan = new Span();
$obSpan->setId( "spnBoletins" );

$stHdnValor = "
    erroCheck = true;
    for (i=0;i<document.frm.elements.length;i++) {
        if (document.frm.elements[i].type == 'checkbox' && document.frm.elements[i].checked == true) {
            erroCheck = false;
        }
    }
    if (erroCheck == true) {
        erro = true;
        mensagem = '@Selecione pelo menos um boletim!';
    }
";

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( $stHdnValor );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm            );
$obFormulario->addHidden    ( $obHdnCtrl         );
$obFormulario->addHidden    ( $obHdnAcao         );
$obFormulario->addHidden    ( $obApplet          );
$obFormulario->addHidden    ( $obHdnEval, true);
$obFormulario->addTitulo    ( "Dados Para Liberação / Processamento de Boletim" );
$obFormulario->addComponente( $obCmbEntidade     );
$obFormulario->addComponente( $obMes );
$obFormulario->addComponente( $obListar );
$obFormulario->addSpan      ( $obSpan           );

$obOk = new Ok();
$obOk->setId ('Ok');
$obOk->obEvento->setOnClick( "if (Valida()) { BloqueiaFrames(true,false); Salvar(); }");
$obLimpar = new Limpar();
$obFormulario->defineBarra ( array( $obOk, $obLimpar) );
$jsOnload .= 'document.frm.Ok.disabled = true;';

$obFormulario->show();
SistemaLegado::LiberaFrames();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
