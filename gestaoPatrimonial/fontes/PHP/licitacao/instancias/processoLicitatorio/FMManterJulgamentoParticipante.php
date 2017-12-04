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
    * Página de Formulário para incluir processo licitatório
    * Data de Criação   : 04/10/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * Casos de uso : uc-03.05.26
*/

/*
$Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Definições padrões do framework
$stPrograma = "ManterJulgamentoProposta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once ( $pgJs );

$arFornecedores = Sessao::read('arFornecedores');
$arFornecedores = $arFornecedores[$_GET['inCodItem']]['dados'] ;
$inPos = 0;
while (( $inPos <= count( $arFornecedores )) and ($arFornecedores[$inPos]['ordem'] != $inOrdem)) {
    $inPos++;
}

$arRegistro = $arFornecedores[$inPos];
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obForm = new Form;
$obForm->setAction ( $pgOcul );
$obForm->setTarget ( "window.parent.frames['oculto']" );

$obHdnCodItem = new Hidden;
$obHdnCodItem->setName  ( 'inCodItem'        );
$obHdnCodItem->setId    ( 'inCodItem'        );
$obHdnCodItem->setValue ( $_GET['inCodItem'] );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden();
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$lblParticipante = new Label;
$lblParticipante->setId     ( 'lblParticipante' );
$lblParticipante->setRotulo ( 'Participante'    );
$lblParticipante->setValue  ( $arRegistro['nom_cgm']);

$lblItem = new Label;
$lblItem->setId     ( 'lblItem' );
$lblItem->setRotulo ( 'Item'    );
$lblItem->setValue  ( $arRegistro['item'] );

$lblLote = new Label;
$lblLote->setId     ( 'lblLote' );
$lblLote->setRotulo ( 'Lote'    );
$lblLote->setValue  ( $arRegistro['lote']    );

$lblQuantidade = new Label;
$lblQuantidade->setId     ( 'lblQuantidade' );
$lblQuantidade->setRotulo ( 'Quantidade'    );
$lblQuantidade->setValue  ( $arRegistro['quantidade']   );

$lblValorReferencia = new Label;
$lblValorReferencia->setId     ( 'lblvalorRef' );
$lblValorReferencia->setRotulo ( 'Valor de Referência'    );
$lblValorReferencia->setValue  ( 0   );

$lblValorUnitario = new Label;
$lblValorUnitario->setId     ( 'lblvalorUni' );
$lblValorUnitario->setRotulo ( 'Valor Unitário'    );
$lblValorUnitario->setValue  ( $arRegistro['vl_unitario'] );

$lblValorTotal = new Label;
$lblValorTotal->setId     ( 'lblvalorTot' );
$lblValorTotal->setRotulo ( 'Valot Total'    );
$lblValorTotal->setValue  ( $arRegistro['vl_total']   );

$inCodStatus = ($arRegistro['status'] == 'classificado')? 0 : 1  ;
$obCmbStatus = new Select;
$obCmbStatus->setName       ( "stStatus"             );
$obCmbStatus->setRotulo     ( "Status"               );
$obCmbStatus->setValue      ( $inCodStatus           );
$obCmbStatus->setNull       ( false                  );
$obCmbStatus->addOption     ( "0", "Classificado"    );
$obCmbStatus->addOption     ( "1", "Desclassificado" );

$obTxtJustificativa = new TextArea;
$obTxtJustificativa->setId     ( 'txtJustificativa' );
$obTxtJustificativa->setRotulo ( 'Justificativa'    );
$obTxtJustificativa->setNull   ( false              );
$obTxtJustificativa->setValue  ( $arRegistro['obs'] );

$obFormulario = new Formulario();
$obFormulario->addForm  ( $obForm    );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addComponente( $lblParticipante    );
$obFormulario->addComponente( $lblItem            );
$obFormulario->addComponente( $lblLote            );
$obFormulario->addComponente( $lblQuantidade      );
$obFormulario->addComponente( $lblValorReferencia );
$obFormulario->addComponente( $lblValorUnitario   );
$obFormulario->addComponente( $lblValorTotal      );
$obFormulario->addComponente( $obCmbStatus        );
$obFormulario->addComponente( $obTxtJustificativa );

$obOk = new Button;
$obOk->setName ( 'btoOk' );
$obOk->setValue ( 'Ok' );
$obOk->obEvento->setOnClick( "javaScript:fecha( $inOrdem,". $_GET['inCodItem'] . "  , stStatus.value, txtJustificativa.value );" );

$obFormulario->defineBarra( array( $obOk ) );

$obFormulario->show();

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem" );
$obIFrame2->setWidth  ( "100%"         );
$obIFrame2->setHeight ( "50"           );
$obIFrame2->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
