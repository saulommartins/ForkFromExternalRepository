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
include_once(CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php');
include_once(CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php');
include_once(CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php');
include_once(CAM_GPC_TCERN_MAPEAMENTO.'TTCERNObra.class.php');
include_once(CAM_GPC_TCERN_MAPEAMENTO.'TTCERNObraContrato.class.php');
include_once(TCGM.'TCGM.class.php');

//Define o nome dos arquivos PHP
$stPrograma = 'ManterConfiguracaoObraContrato';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = 'incluir';
}

if ($_REQUEST['stAcao'] == 'manter') {
    $obTTCERNObraContrato = new TTCERNObraContrato;
    $obTTCERNObraContrato->setDado('num_contrato', $_REQUEST['stContrato']);
    $obTTCERNObraContrato->recuperaPorChave($rsObraContrato);

    $inNumObra            = trim($rsObraContrato->getCampo('num_obra')).'§'.$rsObraContrato->getCampo('cod_entidade').'§'.$rsObraContrato->getCampo( 'exercicio' );
    $stContrato           = $rsObraContrato->getCampo('num_contrato');
    $stServico            = $rsObraContrato->getCampo('servico');
    $stProcessoLicitacao  = $rsObraContrato->getCampo('processo_licitacao');
    $vlContrato           = number_format($rsObraContrato->getCampo('valor_contrato'), '2', ',', '.');
    $vlExecutadoExercicio = number_format($rsObraContrato->getCampo('valor_executado_exercicio'), '2', ',', '.');
    $vlAExecutar          = number_format($rsObraContrato->getCampo('valor_a_exercutar'), '2', ',', '.');
    $dtInicioContrato     = $rsObraContrato->getCampo('dt_inicio_contrato');
    $dtTerminoContrato    = $rsObraContrato->getCampo('dt_termino_contrato');
    $inART                = $rsObraContrato->getCampo('num_art');
    $vlISS                = number_format($rsObraContrato->getCampo('valor_iss'), '2', ',', '.');
    $inDCMS               = $rsObraContrato->getCampo('num_dcms');
    $vlINSS               = number_format($rsObraContrato->getCampo('valor_inss'), '2', ',', '.');

    $obTCGM = new TCGM();
    $obTCGM->setDado('numcgm', $rsObraContrato->getCampo('numcgm'));
    $obTCGM->recuperaPorChave($rsCGM);
    $inNumCGM = $rsCGM->getCampo('numcgm');
    $stNomCGM = $rsCGM->getCampo('nom_cgm');

    $obTCGM->setDado('numcgm', $rsObraContrato->getCampo('numcgm_fiscal'));
    $obTCGM->recuperaPorChave($rsCGM);
    $inNumCGMFiscal = $rsCGM->getCampo('numcgm');
    $stNomCGMFiscal = $rsCGM->getCampo('nom_cgm');
}

$obTTCERNObra = new TTCERNObra();
$obTTCERNObra->recuperaObraEntidade( $rsObra );

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( 'oculto' );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( 'stAcao' );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( ''       );

$obCmbObra = new Select();
$obCmbObra->setRotulo    ( 'Obra'             );
$obCmbObra->setTitle     ( 'Selecione a Obra' );
$obCmbObra->setName      ( 'stNumObra'        );
$obCmbObra->setId        ( 'stNumObra'        );
$obCmbObra->addOption    ( '', 'Selecione'    );
$obCmbObra->setCampoId   ( 'num_obra'         );
$obCmbObra->setCampoDesc ( 'obra'             );
$obCmbObra->setStyle     ( 'width: 520px'     );
$obCmbObra->preencheCombo( $rsObra            );
$obCmbObra->setValue     ( $inNumObra         );
$obCmbObra->setNull      ( false              );

$obTxtNumContrato = new TextBox;
$obTxtNumContrato->setName  ( 'stContrato'   );
$obTxtNumContrato->setRotulo( 'Contrato'     );
$obTxtNumContrato->setTitle ( ''             );
$obTxtNumContrato->setValue ( $stContrato    );
$obTxtNumContrato->setNull  ( false          );
$obTxtNumContrato->setMaxLength( 50          );
$obTxtNumContrato->setStyle ( 'width: 350px' );
if ($_REQUEST['stAcao'] == 'manter') {
    $obTxtNumContrato->setReadOnly(true);
}

$obTxtServico = new TextArea;
$obTxtServico->setName  ( 'stServico'   );
$obTxtServico->setRotulo( 'Serviço' );
$obTxtServico->setTitle ( ''             );
$obTxtServico->setValue ( $stServico     );
$obTxtServico->setNull  ( false          );
$obTxtServico->setMaxCaracteres (255     );
$obTxtServico->setStyle ( 'width: 350px' );

$obTxtProcessoLicitacao = new TextBox;
$obTxtProcessoLicitacao->setName  ( 'stProcessoLicitacao'   );
$obTxtProcessoLicitacao->setRotulo( 'Número do processo de licitação' );
$obTxtProcessoLicitacao->setTitle ( ''             );
$obTxtProcessoLicitacao->setValue ( $stProcessoLicitacao );
$obTxtProcessoLicitacao->setNull  ( false          );
$obTxtProcessoLicitacao->setMaxLength ( 10 );
$obTxtProcessoLicitacao->setStyle ( 'width: 350px' );

$obCGM = new IPopUpCGMVinculado( $obForm );
$obCGM->setTabelaVinculo    ( 'sw_cgm_pessoa_juridica' );
$obCGM->setCampoVinculo     ( 'numcgm'           );
$obCGM->setNomeVinculo      ( 'CGM do Empresa' );
$obCGM->setRotulo           ( 'CGM do Empresa' );
$obCGM->setName             ( 'stCGM' );
$obCGM->setId               ( 'stCGM' );
$obCGM->setValue            ( $stNomCGM );
$obCGM->obCampoCod->setName ( 'inCGM' );
$obCGM->obCampoCod->setId   ( 'inCGM' );
$obCGM->obCampoCod->setValue( $inNumCGM );
$obCGM->setNull             ( false    );

$obTxtValorContrato = new Moeda;
$obTxtValorContrato->setName  ( 'vlContrato'        );
$obTxtValorContrato->setId    ( 'vlContrato'        );
$obTxtValorContrato->setRotulo( 'Valor do Contrato' );
$obTxtValorContrato->setValue ( $vlContrato         );
$obTxtValorContrato->setSize  ( 14                  );
$obTxtValorContrato->setNull  ( false               );

$obTxtValorExecutadoExercicio = new Moeda;
$obTxtValorExecutadoExercicio->setName  ( 'vlExecutadoExercicio'         );
$obTxtValorExecutadoExercicio->setId    ( 'vlExecutadoExercicio'         );
$obTxtValorExecutadoExercicio->setRotulo( 'Valor executado no exercício' );
$obTxtValorExecutadoExercicio->setValue ( $vlExecutadoExercicio          );
$obTxtValorExecutadoExercicio->setSize  ( 14                             );
$obTxtValorExecutadoExercicio->setNull  ( false                          );

$obTxtValorAExercutar = new Moeda;
$obTxtValorAExercutar->setName  ( 'vlAExecutar'      );
$obTxtValorAExercutar->setId    ( 'vlAExecutar'      );
$obTxtValorAExercutar->setRotulo( 'Valor a executar' );
$obTxtValorAExercutar->setValue ( $vlAExecutar       );
$obTxtValorAExercutar->setSize  ( 14                 );
$obTxtValorAExercutar->setSize  ( false              );

$obDtInicioContrato = new Data;
$obDtInicioContrato->setName  ( 'dtInicioContrato'   );
$obDtInicioContrato->setRotulo( 'Data de início do contrato'   );
$obDtInicioContrato->setTitle ( ''          );
$obDtInicioContrato->setValue ( $dtInicioContrato );
$obDtInicioContrato->setNull  ( false       );

$obDtTerminoContrato = new Data;
$obDtTerminoContrato->setName  ( 'dtTerminoContrato'    );
$obDtTerminoContrato->setRotulo( 'Data de Término da contrato' );
$obDtTerminoContrato->setTitle ( ''                     );
$obDtTerminoContrato->setValue ( $dtTerminoContrato     );
$obDtTerminoContrato->setNull  ( false                  );

$obTxtART = new Inteiro;
$obTxtART->setName  ( 'inART'   );
$obTxtART->setRotulo( 'Número da ART' );
$obTxtART->setTitle ( ''              );
$obTxtART->setValue ( $inART          );
$obTxtART->setNull  ( false           );
$obTxtART->setMaxLength( 50           );
$obTxtART->setStyle ( 'width: 120px'  );

$obTxtValorISS = new Moeda;
$obTxtValorISS->setName  ( 'vlISS'        );
$obTxtValorISS->setRotulo( 'Valor do ISS' );
$obTxtValorISS->setTitle ( ''             );
$obTxtValorISS->setValue ( $vlISS         );
$obTxtValorISS->setNull  ( false          );
$obTxtValorISS->setStyle ( 'width: 120px' );

$obTxtNumDCMS = new Inteiro;
$obTxtNumDCMS->setName  ( 'inDCMS'       );
$obTxtNumDCMS->setRotulo( 'Número DCMS'  );
$obTxtNumDCMS->setTitle ( ''             );
$obTxtNumDCMS->setValue ( $inDCMS        );
$obTxtNumDCMS->setNull  ( false          );
$obTxtNumDCMS->setStyle ( 'width: 120px' );

$obTxtValorINSS = new Moeda;
$obTxtValorINSS->setName  ( 'vlINSS'        );
$obTxtValorINSS->setRotulo( 'Valor do INSS' );
$obTxtValorINSS->setTitle ( ''              );
$obTxtValorINSS->setValue ( $vlINSS         );
$obTxtValorINSS->setNull  ( false           );
$obTxtValorINSS->setStyle ( 'width: 120px'  );

$obCGMFiscal = new IPopUpCGMVinculado( $obForm );
$obCGMFiscal->setTabelaVinculo    ( 'sw_cgm'        );
$obCGMFiscal->setCampoVinculo     ( 'numcgm'        );
$obCGMFiscal->setNomeVinculo      ( 'CGM do Fiscal' );
$obCGMFiscal->setRotulo           ( 'CGM do Fiscal' );
$obCGMFiscal->setName             ( 'stCGMFiscal'   );
$obCGMFiscal->setId               ( 'stCGMFiscal'   );
$obCGMFiscal->setValue            ( $stNomCGMFiscal );
$obCGMFiscal->obCampoCod->setName ( 'inCGMFiscal'   );
$obCGMFiscal->obCampoCod->setId   ( 'inCGMFiscal'   );
$obCGMFiscal->obCampoCod->setValue( $inNumCGMFiscal );
$obCGMFiscal->setNull             ( false           );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( 'Dados' );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addComponente( $obCmbObra );
$obFormulario->addComponente( $obTxtNumContrato );
$obFormulario->addComponente( $obTxtServico );
$obFormulario->addComponente( $obTxtProcessoLicitacao );
$obFormulario->addComponente( $obCGM );
$obFormulario->addComponente( $obTxtValorContrato );
$obFormulario->addComponente( $obTxtValorExecutadoExercicio );
$obFormulario->addComponente( $obTxtValorAExercutar );
$obFormulario->addComponente( $obDtInicioContrato );
$obFormulario->addComponente( $obDtTerminoContrato );
$obFormulario->addComponente( $obTxtART );
$obFormulario->addComponente( $obTxtValorISS );
$obFormulario->addComponente( $obTxtNumDCMS );
$obFormulario->addComponente( $obTxtValorINSS );
$obFormulario->addComponente( $obCGMFiscal );

$obOk = new Ok();
$obLimpar = new Limpar();
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
