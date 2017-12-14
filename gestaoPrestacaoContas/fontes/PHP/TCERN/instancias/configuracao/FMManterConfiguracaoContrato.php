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
include_once(CAM_GPC_TCERN_MAPEAMENTO.'TTCERNContrato.class.php');
include_once(CAM_GA_PROT_COMPONENTES.'IPopUpProcesso.class.php');
include_once(TCGM.'TCGM.class.php');

//Define o nome dos arquivos PHP
$stPrograma = 'ManterConfiguracaoContrato';
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

    $obTTCERNContrato = new TTCERNContrato;
    $obTTCERNContrato->setDado('num_convenio'       , $_REQUEST['inNumConvenio']);
    $obTTCERNContrato->setDado('exercicio'          , $_REQUEST['stExercicio']);
    $obTTCERNContrato->setDado('cod_entidade'       , $_REQUEST['inCodEntidade']);
    $obTTCERNContrato->setDado('num_contrato'       , $_REQUEST['inNumContrato']);
    $obTTCERNContrato->setDado('exercicio_contrato' , $_REQUEST['stExercicioContrato']);
    $obTTCERNContrato->recuperaPorChave($rsContrato);

    $inNumConvenio                = trim($rsContrato->getCampo('num_convenio')).'§'.$rsContrato->getCampo('cod_entidade').'§'.$rsContrato->getCampo( 'exercicio' );
    $inNumContrato                = trim($rsContrato->getCampo( 'num_contrato'));
    $stProcesso                   = $rsContrato->getCampo('cod_processo').'/'.$rsContrato->getCampo('exercicio_processo');
    $inBimestre                   = $rsContrato->getCampo('bimestre');
    $stExercicioContrato          = $rsContrato->getCampo('exercicio_contrato');
    $inCodContaEspecifica         = $rsContrato->getCampo('cod_conta_especifica');
    $dtEntregaRecurso             = $rsContrato->getCampo('dt_entrega_recurso');
    $vlRepasse                    = str_replace('.', ',', $rsContrato->getCampo('valor_repasse'));
    $vlExecutado                  = str_replace('.', ',', $rsContrato->getCampo('valor_executado'));
    $vlReceitaAplicacaoFinanceira = str_replace('.', ',', $rsContrato->getCampo('receita_aplicacao_financeira'));
    $dtRecebimentoSaldo           = $rsContrato->getCampo('dt_recebimento_saldo');
    $dtPrestacaoContas            = $rsContrato->getCampo('dt_prestacao_contas');
}

$obTTCERNConvenio = new TTCERNConvenio();
$obTTCERNConvenio->recuperaConvenioEntidade( $rsConvenio );

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

if ($_REQUEST['stAcao'] == 'manter') {
    $obTxtNumContrato = new TextBox;
    $obTxtNumContrato->setReadOnly(true);
} else {
    $obTxtNumContrato = new TextBox;
    $obTxtNumContrato->setSize(10);
}
$obTxtNumContrato->setName  ( 'inNumContrato'         );
$obTxtNumContrato->setId    ( 'inNumContrato'         );
$obTxtNumContrato->setRotulo( 'Número do Contrato'    );
$obTxtNumContrato->setSize  ( 14                      );
$obTxtNumContrato->setNull  ( false                   );
$obTxtNumContrato->setValue ( $inNumContrato          );

$obCmbConvenio = new Select();
$obCmbConvenio->setRotulo    ( 'Convênio' );
$obCmbConvenio->setTitle     ( 'Selecione o Convênio' );
$obCmbConvenio->setName      ( 'stNumConvenio' );
$obCmbConvenio->setId        ( 'stNumConvenio' );
$obCmbConvenio->addOption    ( '', 'Selecione' );
$obCmbConvenio->setCampoId   ( 'num_convenio' );
$obCmbConvenio->setCampoDesc ( 'convenio' );
$obCmbConvenio->setStyle     ( 'width: 520px' );
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

$obCmbBimestre = new Select();
$obCmbBimestre->setRotulo  ( 'Bimestre'             );
$obCmbBimestre->setTitle   ( 'Selecione o Bimestre' );
$obCmbBimestre->setName    ( 'inBimestre'           );
$obCmbBimestre->setId      ( 'inBimestre'           );
$obCmbBimestre->addOption  ( '1', '1° bimestre'     );
$obCmbBimestre->addOption  ( '2', '2° bimestre'     );
$obCmbBimestre->addOption  ( '3', '3° bimestre'     );
$obCmbBimestre->addOption  ( '4', '4° bimestre'     );
$obCmbBimestre->setStyle   ( 'width: 120px'         );
$obCmbBimestre->setValue   ( $inBimestre            );

$obTxtCodigoContaEspecifica = new TextBox;
$obTxtCodigoContaEspecifica->setName  ( 'inCodContaEspecifica' );
$obTxtCodigoContaEspecifica->setId    ( 'inCodContaEspecifica' );
$obTxtCodigoContaEspecifica->setRotulo( 'Código da conta específica do convênio' );
$obTxtCodigoContaEspecifica->setValue ( $inCodContaEspecifica  );
$obTxtCodigoContaEspecifica->setSize  ( 14                     );
$obTxtCodigoContaEspecifica->setNull  ( false                  );

$obDtEntregaRecurso = new Data;
$obDtEntregaRecurso->setName  ( 'dtEntregaRecurso' );
$obDtEntregaRecurso->setRotulo( 'Data de entrega do recurso' );
$obDtEntregaRecurso->setTitle ( 'Data de entrega do recurso' );
$obDtEntregaRecurso->setValue ( $dtEntregaRecurso  );
$obDtEntregaRecurso->setNull  ( false              );
$obDtEntregaRecurso->setStyle ( 'width: 120px'     );

$obTxtValorRepasse = new Moeda;
$obTxtValorRepasse->setName  ( 'vlRepasse'        );
$obTxtValorRepasse->setId    ( 'vlRepasse'        );
$obTxtValorRepasse->setRotulo( 'Valor do Repasse' );
$obTxtValorRepasse->setValue ( $vlRepasse         );
$obTxtValorRepasse->setSize  ( 14                 );
$obTxtValorRepasse->setNull  ( false              );
$obTxtValorRepasse->setNull  ( false              );

$obTxtReceitaAplicacaoFinanceira = new Moeda;
$obTxtReceitaAplicacaoFinanceira->setName  ( 'vlReceitaAplicacaoFinanceira'    );
$obTxtReceitaAplicacaoFinanceira->setId    ( 'vlReceitaAplicacaoFinanceira'    );
$obTxtReceitaAplicacaoFinanceira->setRotulo( 'Receita da aplicação financeira' );
$obTxtReceitaAplicacaoFinanceira->setValue ( $vlReceitaAplicacaoFinanceira     );
$obTxtReceitaAplicacaoFinanceira->setSize  ( 14                                );
$obTxtReceitaAplicacaoFinanceira->setNull  ( false                             );

$obTxtValorExecutado = new Moeda;
$obTxtValorExecutado->setName  ( 'vlExecutado'     );
$obTxtValorExecutado->setId    ( 'vlExecutado'     );
$obTxtValorExecutado->setRotulo( 'Valor executado' );
$obTxtValorExecutado->setValue ( $vlExecutado      );
$obTxtValorExecutado->setSize  ( 14                );
$obTxtValorExecutado->setNull  ( false             );

$obDtRecebimentoSaldo = new Data;
$obDtRecebimentoSaldo->setName  ( 'dtRecebimentoSaldo'           );
$obDtRecebimentoSaldo->setRotulo( 'Data de recebimento de saldo' );
$obDtRecebimentoSaldo->setTitle ( 'Data de recebimento de saldo' );
$obDtRecebimentoSaldo->setValue ( $dtRecebimentoSaldo            );
$obDtRecebimentoSaldo->setNull  ( false                          );
$obDtRecebimentoSaldo->setStyle ( 'width: 120px'                 );

$obDtPrestacaoContas = new Data;
$obDtPrestacaoContas->setName  ( 'dtPrestacaoContas'           );
$obDtPrestacaoContas->setRotulo( 'Data de prestação de contas' );
$obDtPrestacaoContas->setTitle ( 'Data de prestação de contas' );
$obDtPrestacaoContas->setValue ( $dtPrestacaoContas            );
$obDtPrestacaoContas->setNull  ( false                         );
$obDtPrestacaoContas->setStyle ( 'width: 120px'                );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( 'Dados' );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addComponente( $obTxtNumContrato );
$obFormulario->addComponente( $obCmbConvenio );
$obFormulario->addComponente( $obPopUpProcesso );
$obFormulario->addComponente( $obCmbBimestre );
$obFormulario->addComponente( $obTxtCodigoContaEspecifica );
$obFormulario->addComponente( $obDtEntregaRecurso );
$obFormulario->addComponente( $obTxtValorRepasse );
$obFormulario->addComponente( $obTxtReceitaAplicacaoFinanceira );
$obFormulario->addComponente( $obTxtValorExecutado );
$obFormulario->addComponente( $obDtRecebimentoSaldo );
$obFormulario->addComponente( $obDtPrestacaoContas );

$obOk = new Ok();
$obLimpar = new Limpar();
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
