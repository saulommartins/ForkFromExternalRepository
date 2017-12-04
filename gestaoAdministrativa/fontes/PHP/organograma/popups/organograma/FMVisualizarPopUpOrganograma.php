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
* Arquivo de instância para popup
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3480 $
$Name$
$Author: pablo $
$Date: 2005-12-06 14:28:46 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.05.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php");

$stPrograma = "VisualizarPopUpOrganograma";
$pgForm = "FM".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obBtnVoltar = new Button;
$obBtnVoltar->setName( "btnVoltar" );
$obBtnVoltar->setValue( "Voltar" );
$obBtnVoltar->obEvento->setOnClick ( "voltar ();" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgFilt );
$obForm->setTarget                  ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addTitulo            ( "Visualização do Organograma" );

$rsTipoNorma = new RecordSet;
$rsNorma     = new RecordSet;
$obRegra     = new ROrganogramaOrgao;
$arNiveisOrganograma = array ();
$obRegra->obROrganograma->setCodOrganograma( $_REQUEST['inCodOrganograma'] );
$obRegra->setCodOrgao( $_REQUEST['inCodOrgao'] );
$obRegra->obROrganograma->consultar();
$stDataImplantacao = $obRegra->obROrganograma->getDtImplantacao();
$obErro = $obRegra->listaVisualizacaoOrganogramaPopUp ($arNiveisOrganograma);
if ( $obErro->ocorreu () ) {
    SistemaLegado::exibeAviso (urlencode($obErro->getDescricao()),"","erro");
} else {
    $inNumArvores = count($arNiveisOrganograma);
    if ($inNumArvores <= 1) {
        $rsArvore = new RecordSet;
        $rsArvore->preenche ($arNiveisOrganograma[0]);
        $obArvore = new Arvore;
        $obArvore->setRecordSet( $rsArvore );
        $obArvore->setName("orgao");
        $obArvore->setNameReduzido("orgao");
        $obArvore->setValue("[orgao] - [descricao]");
        $obArvore->setRotulo( $stDataImplantacao );
    } else {
        for ($inCount = 0; $inCount < $inNumArvores; $inCount++) {
            $rsArvore = new RecordSet;
            $rsArvore->preenche ($arNiveisOrganograma[$inCount]);
            ${"obArvore".$inCount} = new Arvore;
            ${"obArvore".$inCount}->setRecordSet( $rsArvore );
            ${"obArvore".$inCount}->setName("orgao".$inCount);
            ${"obArvore".$inCount}->setNameReduzido("orgao");
            ${"obArvore".$inCount}->setValue("[orgao] - [descricao]");
            ${"obArvore".$inCount}->setRotulo( $stDataImplantacao );
        }
    }
    if ($inNumArvores <= 1) {
        $obFormulario->addComponente    ( $obArvore );
    } else {
        for ($inCount = 0; $inCount < $inNumArvores; $inCount++) {
            $obFormulario->addComponente        ( ${"obArvore".$inCount} );
        }
    }
}
$obFormulario->defineBarra          ( array( $obBtnVoltar ) ,'','');
$obFormulario->show                 ();

include_once($pgJs);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
