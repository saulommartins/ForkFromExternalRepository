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
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-10-27 16:37:56 -0300 (Sex, 27 Out 2006) $

    * Casos de uso  uc-02.05.03
                    uc-02.05.04
                    uc-02.05.05
                    uc-02.05.06
                    uc-02.05.07
                    uc-02.05.08
                    uc-02.05.10
                    uc-02.01.35

    * @ignore
*/

/*
$Log$
Revision 1.9  2006/10/27 19:37:33  cako
Bug #6773#

Revision 1.8  2006/08/25 17:50:06  fernando
Bug #6773#

Revision 1.7  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
if ($_REQUEST['stAcao']) {
   $sessao->filtro['inCodModelo'] = $_REQUEST['stAcao'];
}
include_once( CAM_GF_LRF_NEGOCIO."RLRFRelatorioModelos".$sessao->filtro['inCodModelo'].".class.php"  );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php");

include_once 'JSModelosExecutivo.js';
$sessao = $_SESSION ['sessao'];
$RegraAux           = 'RLRFRelatorioModelos'.$sessao->filtro['inCodModelo'];
$obRegra            = new $RegraAux;

$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->consultarConfiguracao();
$inCodEntidadeRPPS = $obRConfiguracaoOrcamento->getCodEntidadeRPPS();

$obRegra->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
if ( !in_array( $sessao->filtro['inCodModelo'], array( 3, 4 ) ) ) {
    $obRegra->obROrcamentoEntidade->setVerificaConfiguracao  ( true );
}
$obRegra->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

$stEntidades = "";
while (!$rsEntidades->eof()) {
    if ($rsEntidades->getCampo("cod_entidade")<>$inCodEntidadeRPPS) {
        $stEntidades = $stEntidades ."," .$rsEntidades->getCampo("cod_entidade");
    }
    $rsEntidades->proximo();
}
$rsEntidades->setPrimeiroElemento();
$stEntidades=substr($stEntidades,1);

$rsRecordset = new RecordSet;
$js = "buscaValor('listarMes');";

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_LRF_INSTANCIAS."tceRS/OCModelosExecutivo.php" );
$obHdnModelo = new Hidden;
$obHdnModelo->setName("inCodModelo");
$obHdnModelo->setValue( $_REQUEST['stAcao'] );
$obHdnEntidades = new Hidden;
$obHdnEntidades->setName("inCodEntidade");
$obHdnEntidades->setValue( $stEntidades );

$obHdnEntidadeRPPS = new Hidden;
$obHdnEntidadeRPPS->setName("inCodEntidadeRPPS");
$obHdnEntidadeRPPS->setValue( $inCodEntidadeRPPS );

$obRdIndicativos1 = new Radio;
$obRdIndicativos1->setRotulo ( "Indicativos" );
$obRdIndicativos1->setChecked( true );
$obRdIndicativos1->setName   ( "stIndicativo" );
$obRdIndicativos1->setValue  ( "1" );
$obRdIndicativos1->setLabel  ( "I - Executivo/Legislativo e Indiretas Municipais" );
$obRdIndicativos1->setNull   ( false );

$obRdIndicativos2 = new Radio;
$obRdIndicativos2->setRotulo ( "Indicativos" );
$obRdIndicativos2->setChecked( false );
$obRdIndicativos2->setName   ( "stIndicativo" );
$obRdIndicativos2->setValue  ( "2" );
$obRdIndicativos2->setLabel  ( "II - Regime Próprio de Previdência Social do Servidor" );
$obRdIndicativos2->setNull   ( false );
if ($inCodEntidadeRPPS) {
$obRdIndicativos3 = new Radio;
$obRdIndicativos3->setRotulo ( "Indicativos" );
$obRdIndicativos3->setChecked( false );
$obRdIndicativos3->setName   ( "stIndicativo" );
$obRdIndicativos3->setValue  ( "3" );
$obRdIndicativos3->setLabel  ( "Todas" );
$obRdIndicativos3->setNull   ( false );
}

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "" );
$obCmbEntidades->setNull   ( false );

if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

$obCmbMes = new Select;
$obCmbMes->setRotulo              ( "Mês"                       );
$obCmbMes->setName                ( "inMes"                     );
$obCmbMes->setValue               ( $inMes                      );
$obCmbMes->setStyle               ( "width: 120px"              );
$obCmbMes->setTitle               ( "Informe o  mês para filtro");
$obCmbMes->setNull                ( false                       );
//$obCmbMes->setCampoID           ( "mes"                       );
//$obCmbMes->setCampoDesc         ( "descricao"                 );
$obCmbMes->addOption              ( "", "Selecione"             );
//$obCmbMes->preencheCombo        ( $rsMes                      );

if ( in_array( $sessao->filtro['inCodModelo'], array( 2, 7, 9 ) ) ) {
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
}

$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm               );
$obFormulario->addHidden        ( $obHdnCaminho         );
$obFormulario->addHidden        ( $obHdnModelo          );

$obFormulario->addTitulo        ( "Dados para filtro"   );
if ( in_array( $sessao->filtro['inCodModelo'], array( 3 ) ) ) {
    $obFormulario->addComponente( $obRdIndicativos1 );
    $obFormulario->addHidden        ( $obHdnEntidades       );
    if ($inCodEntidadeRPPS) {
        $obFormulario->addHidden    ( $obHdnEntidadeRPPS    );
        $obFormulario->addComponente( $obRdIndicativos2     );
        $obFormulario->addComponente( $obRdIndicativos3     );
    }
} else {
    $obFormulario->addComponente    ( $obCmbEntidades       );
}

$obFormulario->addComponente    ( $obCmbMes             );
if ( in_array( $sessao->filtro['inCodModelo'], array( 2, 7, 9 ) ) ) {
    $obFormulario->addComponente( $obCmbTipoDespesa     );
}
$obFormulario->OK();
$obFormulario->show();

SistemaLegado::executaFrameOculto ($js);

?>
