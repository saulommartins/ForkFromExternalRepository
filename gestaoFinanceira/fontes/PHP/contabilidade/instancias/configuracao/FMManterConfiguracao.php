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
    * Página de Formulário Tipo de Norma
    * Data de Criação   : 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-08-15 11:15:55 -0300 (Qua, 15 Ago 2007) $

    * Casos de uso: uc-02.02.01
*/

/*
$Log$
Revision 1.7  2007/08/15 14:15:13  hboaventura
Bug#9914#

Revision 1.6  2007/07/19 14:58:26  vitor
Bug#9670#

Revision 1.5  2006/07/17 20:23:23  leandro.zis
Bug #6367#

Revision 1.4  2006/07/05 20:50:46  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO . "RContabilidadeConfiguracao.class.php");
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoConta.class.php" );

$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRegra = new RContabilidadeConfiguracao;
$obRegra->Consultar();

$dtDataImplantacao         = $obRegra->getDataImplantacao();
$stMascaraPlanoContas      = $obRegra->getMascaraPlanoContas();
$inMesCorrente             = $obRegra->getMesCorrente();
$boUtilizarEncerramentoMes = $obRegra->getUtilizarEncerramentoMes() == "true" ? "S" : "N";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) || $stAcao=="incluir") {
    $stAcao = "alterar";

}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obDtImplantacao = new Data;
$obDtImplantacao->setRotulo        ( "Data de Implantação" );
$obDtImplantacao->setName          ( "dtDataImplantacao" );
$obDtImplantacao->setValue         ( $dtDataImplantacao );
$obDtImplantacao->setSize          ( 10 );
$obDtImplantacao->setMaxLength     ( 10 );
$obDtImplantacao->setNull          ( false );

$obCmbMesCorrente = new SelectMeses;
$obCmbMesCorrente->setRotulo        ( "Mês de Processamento" );
$obCmbMesCorrente->setName          ( "inMesCorrente" );
$obCmbMesCorrente->setValue         ( $inMesCorrente  );
$obCmbMesCorrente->setStyle         ( "width: 200px");
$obCmbMesCorrente->setNull          ( false );

$obRegra = new RContabilidadePlanoConta;
$obRegra->setExercicio( Sessao::getExercicio() );
$obRegra->listar( $rsLista, "" );

if ( ($rsLista->getNumLinhas() > 0) && (!Sessao::getExercicio() > '2012' ) ) {
    $boExiste = true;
} else {
    $boExiste = false;
}

$obTxtMascaraPlanoContas = new TextBox;
$obTxtMascaraPlanoContas->setRotulo        ( "Máscara de Classificação do Plano de Contas" );
$obTxtMascaraPlanoContas->setName          ( "stMascaraPlanoContas" );
$obTxtMascaraPlanoContas->setValue         ( $stMascaraPlanoContas);
$obTxtMascaraPlanoContas->setSize          ( 35 );
$obTxtMascaraPlanoContas->setMaxLength     ( 160 );
$obTxtMascaraPlanoContas->setNull          ( false );
$obTxtMascaraPlanoContas->obEvento->setOnKeyPress( "return validaExpressao( this, event, '[9.]')" );

$obTxtMascaraPlanoContas->setReadOnly      ( $boExiste );

$obRdnUtilizarEncerramentoMes = new SimNao;
$obRdnUtilizarEncerramentoMes->setRotulo ( "Utilizar Encerramento de Mês"   );
$obRdnUtilizarEncerramentoMes->setName   ( "boUtilizarEncerramentoMes" );
$obRdnUtilizarEncerramentoMes->setTitle  ( "Informe se será utilizado encerramento de mês na contabilidade." );
$obRdnUtilizarEncerramentoMes->setChecked( $boUtilizarEncerramentoMes );
$obRdnUtilizarEncerramentoMes->obRadioSim->setValue  ("true");
$obRdnUtilizarEncerramentoMes->obRadioNao->setValue  ("false");

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->setAjuda             ('UC-02.02.01');
$obFormulario->addTitulo            ( "Dados para Configuração" );

$obFormulario->addComponente        ( $obDtImplantacao         );
$obFormulario->addComponente        ( $obCmbMesCorrente        );
$obFormulario->addComponente        ( $obTxtMascaraPlanoContas );
$obFormulario->addComponente        ( $obRdnUtilizarEncerramentoMes );

$obFormulario->OK                   ();
$obFormulario->show                 ();

//include_once($pgJs);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
