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
    * Filtro para Empenho - Ordem de Pagamento - Gerar Pagamento
    * Data de Criação   : 02/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    $Revision: 31087 $
    $Name$
    $Author: leandro.zis $
    $Date: 2006-07-14 12:44:07 -0300 (Sex, 14 Jul 2006) $

    * Casos de uso: uc-02.03.05
*/

/*
$Log$
Revision 1.10  2006/07/14 15:44:07  leandro.zis
Bug #6191#

Revision 1.9  2006/07/14 14:33:42  leandro.zis
Bug #6193#

Revision 1.8  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoPagamentoLiquidacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "GerarPagamento";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
//include_once( $pgJs );

$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "pagar";
}

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
Sessao::remove('link');

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

// VALIDA INTERVALO DE DATAS
$stValidaData = "if (''+document.frm.stDataInicial.value.substr(6,4)+document.frm.stDataInicial.value.substr(3,2)+document.frm.stDataInicial.value.substr(0,2) >  ''+document.frm.stDataFinal.value.substr(6,4)+document.frm.stDataFinal.value.substr(3,2)+document.frm.stDataFinal.value.substr(0,2)) { erro = true; mensagem += '@Data Final deve ser maior que Data Inicial!'; } ";

$stValidaData .= "";

$stValidaData .= "if (''+document.frm.stDataInicial.value.substr(6,4)+document.frm.stDataInicial.value.substr(3,2)+document.frm.stDataInicial.value.substr(0,2) <=  ''+document.frm.stDataFinal.value.substr(6,4)+document.frm.stDataFinal.value.substr(3,2)+document.frm.stDataFinal.value.substr(0,2)) { erro = false; alertaAviso('Processando ...','form','erro','".Sessao::getId()."'); document.frm.Ok.disabled = true; } ";
$stValidaData .= "";
//

$obHdnValidaData = new HiddenEval;
$obHdnValidaData->setName  ( "hdnValidaData" );
$obHdnValidaData->setValue ( $stValidaData  );

// DEFINE OBJETOS DO FORMULARIO

//Define objeto Label
$obLblOrdemPagamento = new Label;
$obLblOrdemPagamento->setValue( "&nbsp;a&nbsp;" );

// Define objeto Data inicial para Periodo
$obDataInicial = new Data;
$obDataInicial->setName                        ( "stDataInicial"                 );
$obDataInicial->setRotulo                      ( "Período"                       );
$obDataInicial->setTitle                       ( 'Informe o período.'            );
$obDataInicial->setNull                        ( false                           );
// Define Objeto Label
$obLabel = new Label;
$obLabel->setValue( " até " );
// Define objeto Data final para Periodo
$obDataFinal = new Data;
$obDataFinal->setName                          ( "stDataFinal"                   );
$obDataFinal->setRotulo                        ( "Período"                       );
$obDataFinal->setTitle                         ( ''                              );
$obDataFinal->setNull                          ( false                           );

//DEFINICAO DOS COMPONENTES

$obBtnOK = new Ok;
//$obBtnOK->obEvento->setOnClick($obBtnOK->obEvento->getOnClick()."alertaAviso('Processando ...','form','erro','".Sessao::getId()."'); this.disabled = true;");
$obLimpar = new Limpar();

$js .= "document.frm.stDataInicial.focus();";
SistemaLegado::executaFramePrincipal($js);

$obForm = new Form;
$obForm->setAction           ( $pgProc                  );
$obForm->setTarget           ( "oculto"                 );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                  );
$obFormulario->addHidden     ( $obHdnCtrl               );
$obFormulario->addHidden     ( $obHdnAcao               );
$obFormulario->addHidden     ( $obHdnValidaData,true    );

$obFormulario->addTitulo     ( "Dados para Filtro"      );

$obFormulario->agrupaComponentes( array( $obDataInicial,$obLabel, $obDataFinal ) );
$obFormulario->defineBarra(array($obBtnOK,$obLimpar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
