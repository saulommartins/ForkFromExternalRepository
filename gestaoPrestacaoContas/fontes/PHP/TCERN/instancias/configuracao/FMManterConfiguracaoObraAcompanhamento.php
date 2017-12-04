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
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php");
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNObraContrato.class.php");
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNObraAcompanhamento.class.php");
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");
include_once(TCGM."TCGM.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoObraAcompanhamento";
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
    $obTTCERNAcompanhamento = new TTCERNObraAcompanhamento;
    $obTTCERNAcompanhamento->setDado('id', $_REQUEST['id']);
    $obTTCERNAcompanhamento->recuperaPorChave($rsObraAcompanhamento);

    $inId                = $rsObraAcompanhamento->getCampo('id');
    $stContrato          = $rsObraAcompanhamento->getCampo('obra_contrato_id');
    $dtEvento            = $rsObraAcompanhamento->getCampo('dt_evento');
    $inNumCGMResponsavel = $rsObraAcompanhamento->getCampo('numcgm_responsavel');
    $stSituacaoObra      = $rsObraAcompanhamento->getCampo('cod_situacao');
    $stJustificativa     = $rsObraAcompanhamento->getCampo('justificativa');

    $obTCGM = new TCGM();
    $obTCGM->setDado('numcgm', $rsObraAcompanhamento->getCampo('numcgm_responsavel'));
    $obTCGM->recuperaPorChave($rsCGM);
    $inNumCGMResponsavel = $rsCGM->getCampo('numcgm');
    $stNomCGMResponsavel = $rsCGM->getCampo('nom_cgm');

}

$obTObraContrato = new TTCERNObraContrato();
$obTObraContrato->recuperaTodos( $rsObraContrato );

$obTObraAcompanhamentoSituacao = new TTCERNObraAcompanhamento();
$obTObraAcompanhamentoSituacao->recuperaSituacao( $rsObraSituacao );

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

$obTxtDtEvento = new Data;
$obTxtDtEvento->setName  ( "dtEvento"       );
$obTxtDtEvento->setRotulo( "Data do evento" );
$obTxtDtEvento->setTitle ( ''               );
$obTxtDtEvento->setValue ( $dtEvento        );
$obTxtDtEvento->setNull  ( false            );

$obCGMResponsavel = new IPopUpCGMVinculado( $obForm           );
$obCGMResponsavel->setTabelaVinculo    ( 'sw_cgm'             );
$obCGMResponsavel->setCampoVinculo     ( 'numcgm'             );
$obCGMResponsavel->setNomeVinculo      ( 'CGM do Responsável' );
$obCGMResponsavel->setRotulo           ( 'CGM do Responsável' );
$obCGMResponsavel->setName             ( 'stCGMResponsavel'   );
$obCGMResponsavel->setId               ( 'stCGMResponsavel'   );
$obCGMResponsavel->setValue            ( $stNomCGMResponsavel );
$obCGMResponsavel->obCampoCod->setName ( 'inCGMResponsavel'   );
$obCGMResponsavel->obCampoCod->setId   ( 'inCGMResponsavel'   );
$obCGMResponsavel->obCampoCod->setValue( $inNumCGMResponsavel );
$obCGMResponsavel->setNull             ( false                );

$obCmbSituacao = new Select();
$obCmbSituacao->setRotulo    ( 'Situação da obra'     );
$obCmbSituacao->setTitle     ( 'Selecione a Situação' );
$obCmbSituacao->setName      ( 'inCodSituacao'        );
$obCmbSituacao->setId        ( 'inCodSituacao'        );
$obCmbSituacao->addOption    ( '', 'Selecione'        );
$obCmbSituacao->setCampoId   ( 'cod_situacao'         );
$obCmbSituacao->setCampoDesc ( 'situacao'             );
$obCmbSituacao->setStyle     ( 'width: 350px'         );
$obCmbSituacao->preencheCombo( $rsObraSituacao        );
$obCmbSituacao->setValue     ( $stSituacaoObra        );
$obCmbSituacao->setNull      ( false                  );

$obTxtJustificativa = new TextArea;
$obTxtJustificativa->setName  ( "stJustificativa" );
$obTxtJustificativa->setRotulo( "Justificativa"   );
$obTxtJustificativa->setTitle ( ''                );
$obTxtJustificativa->setValue ( $stJustificativa  );
$obTxtJustificativa->setNull  ( false             );
$obTxtJustificativa->setMaxCaracteres ( 255       );
$obTxtJustificativa->setStyle ( 'width: 350px'    );

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
$obFormulario->addComponente( $obTxtDtEvento      );
$obFormulario->addComponente( $obCGMResponsavel   );
$obFormulario->addComponente( $obCmbSituacao      );
$obFormulario->addComponente( $obTxtJustificativa );

$obOk = new Ok();
$obLimpar = new Limpar();
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
