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
    * Página Formulário - Parâmetros do Arquivo
    * Data de Criação   : 30/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 25762 $
    $Name$
    $Autor: $
    $Date: 2007-10-02 15:20:03 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once(CAM_GPC_TCERN_MAPEAMENTO.'TTCERNConvenio.class.php');
include_once(CAM_GPC_TCERN_MAPEAMENTO.'TTCERNContratoAditivo.class.php');
include_once(CAM_GA_PROT_COMPONENTES.'IPopUpProcesso.class.php');
include_once(TCOM."TComprasObjeto.class.php" );
include_once(TCGM.'TCGM.class.php');

//Define o nome dos arquivos PHP
$stPrograma = 'ManterConfiguracaoContratoAditivo';
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
    $stAcao = $request->get('stAcao');

    $obTTCERNContratoAditivo = new TTCERNContratoAditivo;
    $obTTCERNContratoAditivo->setDado('num_contrato_aditivo', $_REQUEST['inNumContratoAditivo']);
    $obTTCERNContratoAditivo->setDado('exercicio_aditivo'   , $_REQUEST['stExercicioAditivo']);
    $obTTCERNContratoAditivo->setDado('num_convenio'        , $_REQUEST['inNumConvenio']);
    $obTTCERNContratoAditivo->setDado('exercicio'           , $_REQUEST['stExercicio']);
    $obTTCERNContratoAditivo->setDado('cod_entidade'        , $_REQUEST['inCodEntidade']);
    $obTTCERNContratoAditivo->recuperaContratoAditivo($rsContratoAditivo);

    $inNumConvenio        = trim($rsContratoAditivo->getCampo('num_convenio')).'§'.$rsContratoAditivo->getCampo('cod_entidade').'§'.$rsContratoAditivo->getCampo( 'exercicio' );
    $stExercicioContrato  = $rsContratoAditivo->getCampo('exercicio_contrato');
    $inNumContratoAditivo = trim($rsContratoAditivo->getCampo('num_contrato_aditivo'));
    $stProcesso           = $rsContratoAditivo->getCampo('cod_processo').'/'.$rsContratoAditivo->getCampo('exercicio_processo');
    $inBimestre           = $rsContratoAditivo->getCampo('bimestre');
    $inCodObjeto          = $rsContratoAditivo->getCampo('cod_objeto');
    $vlAditivo            = str_replace('.', ',', $rsContratoAditivo->getCampo( 'valor_aditivo' ));
    $dtInicioVigencia     = $rsContratoAditivo->getCampo('dt_inicio_vigencia');
    $dtTerminoVigencia    = $rsContratoAditivo->getCampo('dt_termino_vigencia');
    $dtAssinatura         = $rsContratoAditivo->getCampo('dt_assinatura');
    $dtPublicacao         = $rsContratoAditivo->getCampo('dt_publicacao');
}

$obTTCERNConvenio = new TTCERNConvenio();
$obTTCERNConvenio->recuperaConvenioEntidade($rsConvenio);

$obTComprasObjeto = new TComprasObjeto();
$obTComprasObjeto->recuperaTodos( $rsObjeto );

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
$obHdnCtrl->setValue( '' );

$obPopUpProcesso = new IPopUpProcesso($obForm);
$obPopUpProcesso->setRotulo("Processo Administrativo");
$obPopUpProcesso->setValue($stProcesso);
$obPopUpProcesso->obCampoCod->setValue($stProcesso);
$obPopUpProcesso->setValidar(true);
$obPopUpProcesso->setNull(false);

$obCmbConvenio = new Select();
$obCmbConvenio->setRotulo    ( 'Convênio' );
$obCmbConvenio->setTitle     ( 'Selecione o Convênio' );
$obCmbConvenio->setName      ( 'stNumConvenio' );
$obCmbConvenio->setId        ( 'stNumConvenio' );
$obCmbConvenio->addOption    ( '', 'Selecione' );
$obCmbConvenio->setCampoId   ( 'num_convenio' );
$obCmbConvenio->setCampoDesc ( 'convenio' );
$obCmbConvenio->setStyle     ('width: 520px');
$obCmbConvenio->preencheCombo( $rsConvenio );
$obCmbConvenio->setValue     ( $inNumConvenio );
$obCmbConvenio->setNull      ( false );

if ($_REQUEST['stAcao'] == 'manter') {
    $obCmbConvenio->setName      ( 'stNumConvenio_disabled' );
    $obCmbConvenio->setDisabled( true );

    //Como combo está desabilitada salva no hidden
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ( 'stNumConvenio' );
    $obHdnCtrl->setValue( $inNumConvenio );
}

$obTxtNumContratoAditivo = new TextBox;
$obTxtNumContratoAditivo->setName  ( 'inNumContratoAditivo' );
$obTxtNumContratoAditivo->setId    ( 'inNumContratoAditivo' );
$obTxtNumContratoAditivo->setRotulo( 'Número do Aditivo'    );
$obTxtNumContratoAditivo->setSize  ( 14                     );
$obTxtNumContratoAditivo->setNull  ( false                  );
$obTxtNumContratoAditivo->setValue ( $inNumContratoAditivo  );

$obCmbBimestre = new Select();
$obCmbBimestre->setRotulo  ( 'Bimestre'             );
$obCmbBimestre->setTitle   ( 'Selecione o Bimestre' );
$obCmbBimestre->setName    ( 'inBimestre'           );
$obCmbBimestre->setId      ( 'inBimestre'           );
$obCmbBimestre->setValue   ( $inBimestre            );
$obCmbBimestre->addOption  ( '1', '1° bimestre'     );
$obCmbBimestre->addOption  ( '2', '2° bimestre'     );
$obCmbBimestre->addOption  ( '3', '3° bimestre'     );
$obCmbBimestre->addOption  ( '4', '4° bimestre'     );
$obCmbBimestre->setStyle   ( 'width: 119px'         );

$obCmbObjeto = new Select();
$obCmbObjeto->setRotulo( 'Objeto' );
$obCmbObjeto->setTitle( 'Selecione o Objeto' );
$obCmbObjeto->setName( 'inObjeto' );
$obCmbObjeto->setId( 'inObjeto' );
$obCmbObjeto->addOption( '', 'Selecione' );
$obCmbObjeto->setCampoId( 'cod_objeto' );
$obCmbObjeto->setCampoDesc( 'descricao' );
$obCmbObjeto->setStyle('width: 520px');
$obCmbObjeto->preencheCombo( $rsObjeto );
$obCmbObjeto->setValue( $inCodObjeto );
$obCmbObjeto->setNull( false );

$obTxtValorAditivo = new Moeda;
$obTxtValorAditivo->setName  ( 'vlAditivo'        );
$obTxtValorAditivo->setId    ( 'vlAditivo'        );
$obTxtValorAditivo->setRotulo( 'Valor do aditivo' );
$obTxtValorAditivo->setValue ( $vlAditivo         );
$obTxtValorAditivo->setSize  ( 14                 );
$obTxtValorAditivo->setNull  ( false              );

$obDtInicioVigencia = new Data;
$obDtInicioVigencia->setName  ( 'dtInicioVigencia'   );
$obDtInicioVigencia->setRotulo( 'Data de início da vigência' );
$obDtInicioVigencia->setValue ( $dtInicioVigencia );
$obDtInicioVigencia->setTitle ( ''                );
$obDtInicioVigencia->setStyle ( 'width: 119px'    );
$obDtInicioVigencia->setNull  ( false             );

$obDtTerminoVigencia = new Data;
$obDtTerminoVigencia->setName  ( 'dtTerminoVigencia' );
$obDtTerminoVigencia->setRotulo( 'Data de Término da vigência' );
$obDtTerminoVigencia->setValue ( $dtTerminoVigencia );
$obDtTerminoVigencia->setTitle ( ''                 );
$obDtTerminoVigencia->setStyle ( 'width: 119px'     );
$obDtTerminoVigencia->setNull  ( false              );

$obDtAssinatura = new Data;
$obDtAssinatura->setName  ( 'dtAssinatura' );
$obDtAssinatura->setRotulo( 'Data de Assinatura' );
$obDtAssinatura->setValue ( $dtAssinatura  );
$obDtAssinatura->setTitle ( ''             );
$obDtAssinatura->setStyle ( 'width: 119px' );
$obDtAssinatura->setNull  ( false          );

$obDtPublicacao = new Data;
$obDtPublicacao->setName  ( 'dtPublicacao' );
$obDtPublicacao->setRotulo( 'Data de Publicação' );
$obDtPublicacao->setValue ( $dtPublicacao  );
$obDtPublicacao->setTitle ( ''             );
$obDtPublicacao->setStyle ('width: 119px'  );
$obDtPublicacao->setNull  ( false          );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( 'Dados' );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addComponente( $obTxtNumContratoAditivo );
$obFormulario->addComponente( $obCmbConvenio );
$obFormulario->addComponente( $obPopUpProcesso );
$obFormulario->addComponente( $obCmbBimestre );
$obFormulario->addComponente( $obCmbObjeto );
$obFormulario->addComponente( $obTxtValorAditivo );
$obFormulario->addComponente( $obDtInicioVigencia );
$obFormulario->addComponente( $obDtTerminoVigencia );
$obFormulario->addComponente( $obDtAssinatura );
$obFormulario->addComponente( $obDtPublicacao );

$obOk = new Ok();
$obLimpar = new Limpar();
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
