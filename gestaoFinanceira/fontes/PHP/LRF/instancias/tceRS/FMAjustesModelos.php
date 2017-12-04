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
    * Página de Formulario de Ajustes de modelos
    * Data de Criação   : 18/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-10-27 16:37:56 -0300 (Sex, 27 Out 2006) $

    * Casos de uso uc-02.05.01, uc-02.01.35

    * @ignore
*/

/*
$Log$
Revision 1.7  2006/10/27 19:37:33  cako
Bug #6773#

Revision 1.6  2006/08/25 17:50:22  fernando
Bug #6773#

Revision 1.5  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_LRF_NEGOCIO."RLRFTCERSModelo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AjustesModelos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRLRFTCERSModelo = new RLRFTCERSModelo();
$obRLRFTCERSModelo->setExercicio( Sessao::getExercicio() );
$obRLRFTCERSModelo->setCodModelo( $_POST['inCodModelo'] );
$obRLRFTCERSModelo->addQuadro();
$obRLRFTCERSModelo->roUltimoQuadro->addContaPlano();
$obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->setMes( $_POST['inMes'] );
$obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
$obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRLRFTCERSModelo->consultar();
$obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->consultarNomes( $rsEntidade );

$inCodEntidade  = $obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->getCodigoEntidade();
$stNomEntidade  = $obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->getNomeEntidade();
$inCodModelo    = $obRLRFTCERSModelo->getCodModelo();
if (Sessao::read('modulo') != 8) {
    $stNomModelo    = $obRLRFTCERSModelo->getNomModelo();
} else {
    $stNomModelo    = $obRLRFTCERSModelo->getNomModeloOrcamento();
}
$inMes          = $obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->getMes();
$inCountQuadros = count( $obRLRFTCERSModelo->getRLRFTCERSQuadro() );

$arMes = array( '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março'    , '04' => 'Abril'  , '05' => 'Maio'    , '06' => 'Junho',
                '07' => 'Julho'  , '08' => 'Agosto'   , '09' => 'Setembro' , '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro' );

$stNomMes = $arMes[$inMes];

$sessao->transf5 = array();
$inCount = 0;
foreach ( $obRLRFTCERSModelo->getRLRFTCERSQuadro() as $obRLRFTCERSQuadro ) {
    $arArray[$inCount]['stExercicio']   = $obRLRFTCERSQuadro->roRLRFTCERSModelo->getExercicio();
    $arArray[$inCount]['inCodModelo']   = $obRLRFTCERSQuadro->roRLRFTCERSModelo->getCodModelo();
    $arArray[$inCount]['inCodQuadro']   = $obRLRFTCERSQuadro->getCodQuadro();
    $arArray[$inCount]['inCodEntidade'] = $inCodEntidade;
    $arArray[$inCount]['inMes']         = $inMes;
    $inCount++;
}

$sessao->transf5 = $arArray;

SistemaLegado::executaFramePrincipal("buscaDado('montaListaEntidadeValor');");

//*****************************************************//
// Define COMPONENTES DO FORMULARIO ABA Identificação
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );
$obForm->setName( 'frm1' );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define Objeto Hidden para Código do modelo
$obHdnCodModelo = new Hidden;
$obHdnCodModelo->setName ( "inCodModelo" );
$obHdnCodModelo->setValue( $inCodModelo );

//Define Objeto Hidden para Código da entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $inCodEntidade );

//Define Objeto Hidden para Código da entidade
$obHdnMes = new Hidden;
$obHdnMes->setName ( "inMes" );
$obHdnMes->setValue( $inMes );

// Define Objeto Label para entidade
$obLblEntidade = new Label;
$obLblEntidade->setId     ( "stEntidade"                         );
$obLblEntidade->setValue  ( $inCodEntidade.' - '.$stNomEntidade  );
$obLblEntidade->setRotulo ( "Entidade"                           );

// Define Objeto Label para modelo
$obLblModelo = new Label;
$obLblModelo->setId     ( "stModelo"                      );
$obLblModelo->setValue  ( $inCodModelo.' - '.$stNomModelo );
$obLblModelo->setRotulo ( "Modelo"                        );

// Define Objeto Label para modelo
$obLblMes = new Label;
$obLblMes->setId     ( "stMes"   );
$obLblMes->setValue  ( $stNomMes );
$obLblMes->setRotulo ( "Mês"     );

//******************************************************//
// Define COMPONENTES DO FORMULARIO ABAS Quadro
//******************************************************//

// Define objeto span para quadros
if ($inCountQuadros) {
    for ($inQuadro = 1; $inQuadro <= $inCountQuadros; $inQuadro++) {
        ${'obSpnQuadro'.$inQuadro} = new Span;
        ${'obSpnQuadro'.$inQuadro}->setId( 'spnQuadro'.$inQuadro );
    }

}

//****************************************//
// Monta FORMULARIO
//****************************************//
//$obFormulario = new FormularioAbas;
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados do filtro" );

$obFormulario->addComponente( $obLblEntidade );
$obFormulario->addComponente( $obLblModelo   );
$obFormulario->addComponente( $obLblMes      );

$obFormulario->show();

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget("oculto" );
$obForm->setName( 'frm' );

$obFormulario = new FormularioAbas;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo("Contas");

$obFormulario->addHidden( $obHdnCtrl        );
$obFormulario->addHidden( $obHdnAcao        );
$obFormulario->addHidden( $obHdnCodEntidade );
$obFormulario->addHidden( $obHdnCodModelo   );
$obFormulario->addHidden( $obHdnMes         );

# Abas quadro
for ($inQuadro = 1; $inQuadro <= $inCountQuadros; $inQuadro++) {
    $obFormulario->addAba("Quadro ".$inQuadro );
    $obFormulario->addSpan( ${'obSpnQuadro'.$inQuadro} );
}

$stLocation = $pgFilt.'?'.Sessao::getId().'&stAcao'.$stAcao;
$obFormulario->Cancelar( $stLocation );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
