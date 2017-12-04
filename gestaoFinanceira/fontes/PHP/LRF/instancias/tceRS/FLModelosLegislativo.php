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
    * Página de Filtro para Relatório LRF
    * Data de Criação   : 24/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso  uc-02.05.11
                    uc-02.05.12

    * @ignore
*/

/*
$Log$
Revision 1.6  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
if ($_REQUEST['stAcao']) {
   $sessao->filtro['inCodModelo'] = $_REQUEST['stAcao'];
}
include_once( CAM_GF_LRF_NEGOCIO."RLRFRelatorioModelos".$sessao->filtro['inCodModelo'].".class.php"  );

include_once 'JSModelosExecutivo.js';

$sessao = $_SESSION ['sessao'];

$RegraAux           = 'RLRFRelatorioModelos'.$sessao->filtro['inCodModelo'];
$obRegra            = new $RegraAux;

// $obRegra->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
// $obRegra->obROrcamentoEntidade->setVerificaConfiguracao  ( true );
// $obRegra->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

$rsRecordset = new RecordSet;

$js = "buscaValor('listarMes');";

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_LRF_INSTANCIAS."tceRS/OCModelosLegislativo.php" );

$obHdnModelo = new Hidden;
$obHdnModelo->setName("inCodModelo");
$obHdnModelo->setValue( $_REQUEST['stAcao'] );

$obCmbTipoDespesa = new Select;
$obCmbTipoDespesa->setRotulo ( "Demonstrar Despesa"        );
$obCmbTipoDespesa->setName   ( "stTipoDespesa"             );
$obCmbTipoDespesa->setValue  ( $stTipoDespesa              );
$obCmbTipoDespesa->setTitle  ( "Selecione a despesa que deseja demonstrar");
$obCmbTipoDespesa->setNull   ( false                       );
$obCmbTipoDespesa->addOption ( "", "Selecione"             );
$obCmbTipoDespesa->addOption ( "E", "Empenhada"            );
$obCmbTipoDespesa->addOption ( "L", "Liquidada"            );
$obCmbTipoDespesa->addOption ( "P", "Paga"                 );

$obCmbMes = new Select;
$obCmbMes->setRotulo              ( "Mês"                       );
$obCmbMes->setName                ( "inMes"                     );
$obCmbMes->setValue               ( $inMes                      );
$obCmbMes->setStyle               ( "width: 120px"              );
$obCmbMes->setTitle               ( "Informe o  mês para filtro");
$obCmbMes->setNull                ( false                       );
//$obCmbMes->setCampoID             ( "mes"                       );
//$obCmbMes->setCampoDesc           ( "descricao"                 );
$obCmbMes->addOption              ( "", "Selecione"             );
//$obCmbMes->preencheCombo          ( $rsMes                      );

$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm               );
$obFormulario->addHidden        ( $obHdnCaminho         );
$obFormulario->addHidden        ( $obHdnModelo          );
$obFormulario->addTitulo        ( "Dados para filtro"   );
$obFormulario->addComponente    ( $obCmbMes             );
$obFormulario->addComponente    ( $obCmbTipoDespesa     );
$obFormulario->OK();
$obFormulario->show();

SistemaLegado::executaFrameOculto ($js);

?>
