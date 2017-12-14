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
    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNObraContrato.class.php");
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNObraAditivo.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoObraAditivo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

if ($_REQUEST['stAcao'] == 'manter') {
    $obTTCERNAditivo = new TTCERNObraAditivo;
    $obTTCERNAditivo->setDado('id', $_REQUEST['id']);
    $obTTCERNAditivo->recuperaPorChave($rsObraAditivo);

    $inId           = $rsObraAditivo->getCampo('id');
    $stContrato     = $rsObraAditivo->getCampo('obra_contrato_id');
    $stAditivo      = $rsObraAditivo->getCampo('num_aditivo');
    $dtAditivo      = $rsObraAditivo->getCampo('dt_aditivo');
    $stPrazo        = $rsObraAditivo->getCampo('prazo');
    $stPrazoAditado = $rsObraAditivo->getCampo('prazo_aditado');
    $vlValor        = number_format($rsObraAditivo->getCampo('valor'), '2', ',', '.');
    $vlValorAditado = number_format($rsObraAditivo->getCampo('valor_aditado'), '2', ',', '.');
    $inART          = $rsObraAditivo->getCampo('num_art');
    $stMotivo       = $rsObraAditivo->getCampo('motivo');
}

$obTObraContrato = new TTCERNObraContrato();
$obTObraContrato->recuperaTodos( $rsObraContrato );

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( ""       );

if ($_REQUEST['stAcao'] == 'manter') {
    $obHdnId = new Hidden;
    $obHdnId->setName ( "InId" );
    $obHdnId->setValue( $inId );
}

$obCmbContrato = new Select();
$obCmbContrato->setRotulo    ( 'Contrato de obra' );
$obCmbContrato->setTitle     ( 'Selecione a Obra' );
$obCmbContrato->setName      ( 'stContrato'       );
$obCmbContrato->setId        ( 'stContrato'       );
$obCmbContrato->addOption    ( '', 'Selecione'    );
$obCmbContrato->setCampoId   ( 'id'               );
$obCmbContrato->setCampoDesc ( 'num_contrato'     );
$obCmbContrato->setStyle     ( 'width: 350px'     );
$obCmbContrato->preencheCombo( $rsObraContrato    );
$obCmbContrato->setValue     ( $stContrato        );
$obCmbContrato->setNull      ( false              );

$obTxtNumAditivo = new TextBox;
$obTxtNumAditivo->setName  ( "inNumAditivo"      );
$obTxtNumAditivo->setRotulo( "Número do aditivo" );
$obTxtNumAditivo->setTitle ( ''                  );
$obTxtNumAditivo->setValue ( $stAditivo          );
$obTxtNumAditivo->setMaxLength( 10               );
$obTxtNumAditivo->setStyle( 'width: 350px'        );
$obTxtNumAditivo->setNull  ( false               );

$obTxtDtAditivo = new Data;
$obTxtDtAditivo->setName  ( "dtAditivo"       );
$obTxtDtAditivo->setRotulo( "Data do aditivo" );
$obTxtDtAditivo->setTitle ( ''                );
$obTxtDtAditivo->setValue ( $dtAditivo        );
$obTxtDtAditivo->setNull  ( false             );

$obTxtPrazo = new TextBox;
$obTxtPrazo->setName  ( "stPrazo"      );
$obTxtPrazo->setRotulo( "Prazo"        );
$obTxtPrazo->setTitle ( ''             );
$obTxtPrazo->setValue ( $stPrazo       );
$obTxtPrazo->setMaxLength( 100         );
$obTxtPrazo->setStyle ( 'width: 350px' );
$obTxtPrazo->setNull  ( false          );

$obTxtPrazoAditado = new TextBox;
$obTxtPrazoAditado->setName  ( "stPrazoAditado" );
$obTxtPrazoAditado->setRotulo( "Prazo Aditado"  );
$obTxtPrazoAditado->setTitle ( ''               );
$obTxtPrazoAditado->setValue ( $stPrazoAditado  );
$obTxtPrazoAditado->setMaxLength( 100           );
$obTxtPrazoAditado->setStyle( 'width: 350px'    );
$obTxtPrazoAditado->setNull  ( false            );

$obTxtValor = new Moeda;
$obTxtValor->setName  ( "vlValor" );
$obTxtValor->setRotulo( "Valor"  );
$obTxtValor->setTitle ( ''               );
$obTxtValor->setValue ( $vlValor         );
$obTxtValor->setNull  ( false            );

$obTxtValorAditado = new Moeda;
$obTxtValorAditado->setName  ( "vlValorAditado" );
$obTxtValorAditado->setRotulo( "Valor Aditado"  );
$obTxtValorAditado->setTitle ( ''               );
$obTxtValorAditado->setValue ( $vlValorAditado  );
$obTxtValorAditado->setNull  ( false            );

$obTxtNumART = new TextBox;
$obTxtNumART->setName  ( "inART"         );
$obTxtNumART->setRotulo( "Número da ART" );
$obTxtNumART->setTitle ( ''              );
$obTxtNumART->setValue ( $inART          );
$obTxtNumART->setMaxLength( 50           );
//$obTxtNumART->setSize  ( 'width: 350px'  );
$obTxtNumART->setNull  ( false           );

$obTxtMotivo = new TextArea;
$obTxtMotivo->setName  ( "stMotivo"     );
$obTxtMotivo->setRotulo( "Motivo"       );
$obTxtMotivo->setTitle ( ''             );
$obTxtMotivo->setValue ( $stMotivo      );
$obTxtMotivo->setNull  ( false          );
$obTxtMotivo->setMaxCaracteres (255     );
$obTxtMotivo->setStyle ( 'width: 350px' );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

if ($_REQUEST['stAcao'] == 'manter') {
    $obFormulario->addHidden( $obHdnId );
}

$obFormulario->addComponente( $obCmbContrato      );
$obFormulario->addComponente( $obTxtNumAditivo      );
$obFormulario->addComponente( $obTxtDtAditivo   );
$obFormulario->addComponente( $obTxtPrazo      );
$obFormulario->addComponente( $obTxtPrazoAditado );
$obFormulario->addComponente( $obTxtValor );
$obFormulario->addComponente( $obTxtValorAditado );
$obFormulario->addComponente( $obTxtNumART );
$obFormulario->addComponente( $obTxtMotivo );

$obOk = new Ok();
$obLimpar = new Limpar();
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
