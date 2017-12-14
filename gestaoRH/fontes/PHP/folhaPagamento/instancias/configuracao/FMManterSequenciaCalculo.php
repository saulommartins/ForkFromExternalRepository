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
* Página de Formulario de Inclusao de Sequência de Cálculo
* Data de Criação: 05/01/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30711 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.05.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

//Define a função do arquivo, ex: incluir ou alterar
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$stLink = "";
foreach ($_GET as $stCampo=>$stValor) {
    if ($stCampo != 'PHPSESSID' and $stCampo != 'iURLRandomica' and $stCampo != 'stAcao') {
        $stLink .= "&".$stCampo."=".$stValor;
    }
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterSequenciaCalculo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once( $pgJS );

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

//Define o código da sequência
$obHdnInCodSequencia = new Hidden;
$obHdnInCodSequencia->setName ( "inCodSequencia" );
$obHdnInCodSequencia->setValue( $_GET["inCodSequencia"] );

if ($stAcao == "incluir") {
    //Define o objeto TEXTBOX para o número da sequência para o cálculo
    $obTxtSequencia = new TextBox;
    $obTxtSequencia->setRotulo            ( "Número"                     );
    $obTxtSequencia->setTitle             ( "Informe o número da sequência para o cálculo" );
    $obTxtSequencia->setName              ( "inSequencia"        );
    $obTxtSequencia->setId                ( "inSequencia"        );
    $obTxtSequencia->setValue             ( $inSequencia );
    $obTxtSequencia->setSize              ( 10 );
    $obTxtSequencia->setMaxLength         ( 10 );
    $obTxtSequencia->setNull              ( false );
    $obTxtSequencia->setInteiro           ( true  );
    $obTxtSequencia->obEvento->setOnBlur  ( "buscaValor('verificaSequencia');" );
} else {
    $obHdnInSequencia = new Hidden;
    $obHdnInSequencia->setName ( "inSequencia" );
    $obHdnInSequencia->setValue( $_GET["inSequencia"] );

    $obLblSequencia = new Label;
    $obLblSequencia->setRotulo ( "Número"      );
    $obLblSequencia->setName   ( "inLblSequencia" );
    $obLblSequencia->setId     ( "inLblSequencia" );
    $obLblSequencia->setValue  ( $_GET["inSequencia"]  );
}

if ($inSequencia != 1 and $inSequencia != 100 and $inSequencia != 200 and $inSequencia != 300 and $inSequencia != 400) {
    //Define objeto TEXTBOX para armazenar a DESCRICAO da sequência
    $obTxtDescricao = new TextBox;
    $obTxtDescricao->setRotulo            ( "Descrição"                        );
    $obTxtDescricao->setTitle             ( "Informe a descrição da sequência" );
    $obTxtDescricao->setName              ( "stDescricao"                      );
    $obTxtDescricao->setId                ( "stDescricao"                      );
    $obTxtDescricao->setValue             ( $_GET["stDescricao"]                       );
    $obTxtDescricao->setStyle             ( "width:300px"                   );
    $obTxtDescricao->setMaxLength         ( 80 );
    $obTxtDescricao->setNull              ( false );
    $obTxtDescricao->setCaracteresAceitos ( '[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ/-]' );
    $obTxtDescricao->setEspacosExtras       ( false );
} else {
    $obHdnDescricao = new Hidden;
    $obHdnDescricao->setName ( "stDescricao" );
    $obHdnDescricao->setValue( $_GET["stDescricao"]  );

    $obLblDescricao = new Label;
    $obLblDescricao->setRotulo            ( "Descrição"                        );
    $obLblDescricao->setName              ( "stLblDescricao"                      );
    $obLblDescricao->setId                ( "stLblDescricao"                      );
    $obLblDescricao->setValue             ( $_GET["stDescricao"]                       );
}

//Define objeto TEXTAREA para o complemento da sequência
$obTxtComplemento = new TextArea;
$obTxtComplemento->setRotulo ( "Complemento"                        );
$obTxtComplemento->setTitle  ( "Informe um complemento para a sequência" );
$obTxtComplemento->setName   ( "stComplemento"                      );
$obTxtComplemento->setId     ( "stComplemento"                      );
$obTxtComplemento->setValue  ( $_GET["stComplemento"]                       );
$obTxtComplemento->setStyle  ( "width:300px"                        );
$obTxtComplemento->setNull   ( false                                );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm              );
$obFormulario->addTitulo        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden        ( $obHdnAcao           );
$obFormulario->addHidden        ( $obHdnCtrl           );
$obFormulario->addHidden        ( $obHdnInCodSequencia );
$obFormulario->addTitulo        ( "Dados da sequência" );
if ($stAcao == "incluir") {
    $obFormulario->addComponente    ( $obTxtSequencia );
} else {
    $obFormulario->addHidden        ( $obHdnInSequencia );
    $obFormulario->addComponente    ( $obLblSequencia   );
}
if ($inSequencia != 1 and $inSequencia != 100 and $inSequencia != 200 and $inSequencia != 300 and $inSequencia != 400) {
    $obFormulario->addComponente    ( $obTxtDescricao );
} else {
    $obFormulario->addHidden        ( $obHdnDescricao );
    $obFormulario->addComponente    ( $obLblDescricao );
}
$obFormulario->addComponente    ( $obTxtComplemento );

//Botões
if ( $stAcao == "incluir" )
    $obFormulario->OK();
else
    $obFormulario->Cancelar( $pgList );

// Define o "focus"
if ($stAcao == "incluir") {
    $obFormulario->setFormFocus($obTxtSequencia->getId() );
} else {
    if ($inSequencia != 1 and $inSequencia != 100 and $inSequencia != 200 and $inSequencia != 300 and $inSequencia != 400) {
        $obFormulario->setFormFocus($obTxtDescricao->getId() );
    } else {
        $obFormulario->setFormFocus($obTxtComplemento->getId() );
    }
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
