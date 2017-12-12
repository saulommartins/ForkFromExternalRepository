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
* Arquivo de instância para relatório de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 15580 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 08:26:31 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.04.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_NORMAS_NEGOCIO."RNorma.class.php");
include_once(CAM_GA_NORMAS_NEGOCIO."RTipoNorma.class.php");

$stPrograma = "RelatorioNorma";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRegra = new RNorma;
$rsTipoNorma = new RecordSet;

$obRegra->obRTipoNorma->listar( $rsTipoNorma );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

Sessao::remove('link');

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GA_NORMAS_INSTANCIAS."relatorio/OCRelatorioNorma.php" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrll" );
$obHdnCtrl->setValue( "" );

$obTxtTipoNorma = new TextBox;
$obTxtTipoNorma->setRotulo        ( "Tipo Norma" );
$obTxtTipoNorma->setTitle         ( "Tipo de Norma" );
$obTxtTipoNorma->setName          ( "inCodTipoNormaTxt" );
$obTxtTipoNorma->setValue         ( $inCodTipoNorma     );
$obTxtTipoNorma->setSize          ( 5 );
$obTxtTipoNorma->setMaxLength     ( 5 );
$obTxtTipoNorma->setInteiro       ( true  );
$obTxtTipoNorma->setNull          ( true );
$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setRotulo        ( "Tipo Norma" );
$obCmbTipoNorma->setName          ( "inCodTipoNorma" );
$obCmbTipoNorma->setValue         ( $inCodTipoNorma );
$obCmbTipoNorma->setStyle         ( "width: 200px");
$obCmbTipoNorma->setCampoID       ( "cod_tipo_norma" );
$obCmbTipoNorma->setCampoDesc     ( "nom_tipo_norma" );
$obCmbTipoNorma->addOption        ( "", "Selecione" );
$obCmbTipoNorma->setNull          ( true );
$obCmbTipoNorma->preencheCombo    ( $rsTipoNorma );

$obDtDataInicial = new Data;
$obDtDataInicial->setName      ( "dtDataInicial" );
$obDtDataInicial->setValue     ( $dtDataInicial );
$obDtDataInicial->setRotulo    ( "Periodicidade de Publicação" );
$obDtDataInicial->setTitle     ( "Informe a periodicidade de publicação da norma" );
$obDtDataInicial->setValue     ( "01/01/" . date(Y) );
$obDtDataInicial->setNull      ( false );

$obLblA = new Label;
$obLblA->setRotulo (" Intervalo ");
$obLblA->setValue  ("&nbsp;&nbsp;a&nbsp;&nbsp;");

$obDtDataFinal  = new Data;
$obDtDataFinal->setName      ( "dtDataFinal" );
$obDtDataFinal->setValue     ( $dtDataFinal );
$obDtDataFinal->setValue     ( "31/12/" . date(Y) );
$obDtDataFinal->setNull      ( false );

$obDtPerAssiInicial = new Data;
$obDtPerAssiInicial->setName      ( "dtAssInicial" );
$obDtPerAssiInicial->setValue     ( $dtAssInicial );
$obDtPerAssiInicial->setRotulo    ( "Periodicidade de Assinatura" );
$obDtPerAssiInicial->setTitle     ( "Informe a periodicidade de assinatura da norma" );
$obDtPerAssiInicial->setNull      ( true );

$obDtPerAssiFinal  = new Data;
$obDtPerAssiFinal->setName      ( "dtAssFinal" );
$obDtPerAssiFinal->setValue     ( $dtAssFinal );
$obDtPerAssiFinal->setNull      ( true );

$obRdbVigente = new Radio;
$obRdbVigente->setRotulo     ( "Status");
$obRdbVigente->setName       ( "stCtrl" );
$obRdbVigente->setId         ( "stCtrl" );
$obRdbVigente->setTitle      ("Selecione o status da norma: Vigente, Revogada ou Vigente até a data informada");
$obRdbVigente->setLabel      ( "Vigente");
$obRdbVigente->setValue      ( "vigente");
$obRdbVigente->setNull       ( false );
$obRdbVigente->setChecked    ( true );
$obRdbVigente->obEvento->setOnClick  ("montaParametrosGET('check_vigente');");

$obRdbRevogada = new Radio;
$obRdbRevogada->setRotulo     ( "Status" );
$obRdbRevogada->setName       ( "stCtrl" );
$obRdbRevogada->setId         ( "stCtrl" );
$obRdbRevogada->setLabel      ( "Revogada" );
$obRdbRevogada->setValue      ( "revogada" );
$obRdbRevogada->setNull       ( false );
$obRdbRevogada->setChecked    ( false );
$obRdbRevogada->obEvento->setOnClick  ("montaParametrosGET('check_revogada');");

$obRdbVigenteAte = new Radio;
$obRdbVigenteAte->setRotulo     ( "Status" );
$obRdbVigenteAte->setName       ( "stCtrl" );
$obRdbVigenteAte->setId         ( "stCtrl" );
$obRdbVigenteAte->setLabel      ( "Vigente até" );
$obRdbVigenteAte->setValue      ( "vigente_ate" );
$obRdbVigenteAte->setNull       ( false );
$obRdbVigenteAte->setChecked    ( false );
$obRdbVigenteAte->obEvento->setOnClick  ("montaParametrosGET('check_vigente_ate', 'stStatus');");

$obRdbVigenteAteDtTermino = new Span();
$obRdbVigenteAteDtTermino->setId('addCmpDtTermino');

/*
 *Este código foi feito assim por que usando uma função não funciona
*/
echo "<script type='text/javascript'>
function validaDtTermino()
{
    if(jQuery('#dtTermino') == 'undefined')

        return false;

    if (jQuery('#dtTermino').val() != '') {
        return true;
    } else {
        return false;
    }
}
</script>
";

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo               ( "Filtro do relatório" );
$obFormulario->setAjuda                ( 'UC-01.04.03' );
$obFormulario->addForm                 ( $obForm );
$obFormulario->addHidden               ( $obHdnAcao );
$obFormulario->addHidden               ( $obHdnCtrl );
$obFormulario->addHidden               ( $obHdnCaminho );
$obFormulario->addComponenteComposto   ( $obTxtTipoNorma, $obCmbTipoNorma );
$obFormulario->agrupaComponentes       ( array( $obDtDataInicial, $obLblA ,$obDtDataFinal ));
$obFormulario->agrupaComponentes       ( array( $obDtPerAssiInicial, $obLblA ,$obDtPerAssiFinal ));
$obFormulario->agrupaComponentes       ( array( $obRdbVigente, $obRdbRevogada ,$obRdbVigenteAte ));
$obFormulario->addSpan                 ($obRdbVigenteAteDtTermino);

$obOk = new Ok();

$obOk->setId('Ok');
$obOk->obEvento->setOnClick("if (validaDtTermino()) { Salvar(); } else { alertaAviso('Informe a data de término obrigatória!','n_incluir','aviso','".Sessao::getId()."'); }");

$obLimpar = new Limpar();
$obLimpar->obEvento->setOnClick('limparTodos();');
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
