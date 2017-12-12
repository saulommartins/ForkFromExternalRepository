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
  * Data de Criação: 08/11/2007

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Henrique Boaventura

  * $Id: FMManterRetirarVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

  * Casos de uso: uc-03.02.08
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_COMPONENTES.'ISelectModeloVeiculo.class.php';
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaUtilizacaoRetorno.class.php';

$stPrograma = "ManterRetirarVeiculo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include $pgJs;

$stAcao = $request->get('stAcao');
$stDataSaida = $_REQUEST['stDataSaida'];

if (!empty($stDataSaida)) {
    $stDataSaida = SistemaLegado::dataToSql($_REQUEST['stDataSaida']);
}

$obTFrotaUtilizacaoRetorno = new TFrotaUtilizacaoRetorno();
$obTFrotaUtilizacaoRetorno->setDado('cod_veiculo' , $_REQUEST['inCodVeiculo']);
$obTFrotaUtilizacaoRetorno->setDado('dt_saida'    , $stDataSaida             );
$obTFrotaUtilizacaoRetorno->setDado('hr_saida'    , $_REQUEST['stHoraSaida'] );

if ($stAcao == 'retornar') {
    $obTFrotaUtilizacaoRetorno->recuperaVeiculoSemRetorno($rsVeiculo);
} else {
    $obTFrotaUtilizacaoRetorno->recuperaRetornoVeiculo($rsVeiculo);
}

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//instancia um textbox para o cod_veiculo
$obInCodVeiculo = new TextBox();
$obInCodVeiculo->setName( 'inCodVeiculo' );
$obInCodVeiculo->setId( 'inCodVeiculo' );
$obInCodVeiculo->setRotulo( 'Código do Veículo' );
$obInCodVeiculo->setValue( $_REQUEST['inCodVeiculo'] );
$obInCodVeiculo->setLabel( true );

$obBscMotorista = new IPopUpCGMVinculado( $obForm );
$obBscMotorista->setTabelaVinculo       ( 'frota.motorista' );
$obBscMotorista->setCampoVinculo        ( 'cgm_motorista' );
$obBscMotorista->setNomeVinculo         ( 'Motorista' );
$obBscMotorista->setRotulo              ( 'Motorista' );
$obBscMotorista->setName                ( 'stNomMotorista');
$obBscMotorista->setId                  ( 'stNomMotorista');
$obBscMotorista->obCampoCod->setName    ( "inCodMotorista"   );
$obBscMotorista->obCampoCod->setId      ( "inCodMotorista"   );
$obBscMotorista->obCampoCod->setNull    ( false              );
$obBscMotorista->setNull                ( false              );
$obBscMotorista->obCampoCod->obEvento->setOnBlur("montaParametrosGET('validaMotorista','inCodMotorista', true);");

if ($stAcao == 'retornar') {
    $obBscMotorista->setValue             ( $rsVeiculo->getCampo('nom_motorista') );
    $obBscMotorista->obCampoCod->setValue ( $rsVeiculo->getCampo('num_motorista') );
} else {
    $obBscMotorista->setFiltro( ' AND NOT EXISTS
                                      (
                                        SELECT 1
                                          FROM frota.motorista
                                         WHERE ativo = false
                                           AND cgm_motorista = cgm.numcgm
                                       ) ' );
}

//instancia um componente data para a saida
$obDtSaida = new Data();
$obDtSaida->setName( 'dtSaida' );
$obDtSaida->setId  ( 'dtSaida' );
$obDtSaida->setRotulo( 'Data de Saída' );
$obDtSaida->setTitle ( 'Informe a data de saída do veículo.' );

if ($stAcao == 'retornar') {
    $obDtSaida->setLabel( true );
    $obDtSaida->setValue( $rsVeiculo->getCampo( 'dt_saida' ));
} else {
    $obDtSaida->setValue( date('d/m/Y') );
    $obDtSaida->setNull( false );
}

//instancia um componente hora para a saida
$obHoraSaida = new Hora();
$obHoraSaida->setName( 'horaSaida' );
$obHoraSaida->setId  ( 'horaSaida' );
$obHoraSaida->setRotulo( 'Hora de Saída' );
$obHoraSaida->setTitle ( 'Informe a hora de saída do veículo.' );

if ($stAcao == 'retornar') {
    $obHoraSaida->setLabel( true );
    $obHoraSaida->setValue( $rsVeiculo->getCampo('hr_saida') );
} else {
    $obHoraSaida->setValue( date('H:i:s') );
    $obHoraSaida->setNull ( false );
}

//instancia um numerico para a quilometragem
$obNumKmInicial = new Numerico();
$obNumKmInicial->setRotulo( 'Km Inicial' );
$obNumKmInicial->setTitle ( 'Informe a quilometragem inicial do veículo.' );
$obNumKmInicial->setName  ( 'inKmInicial' );
$obNumKmInicial->setNull  ( true );
$obNumKmInicial->setDecimais( 1 );
$obNumKmInicial->setNegativo( false );
$obNumKmInicial->obEvento->setOnChange( $obNumKmInicial->obEvento->getOnKeyUp() );
$kmInicial = $rsVeiculo->getCampo('km_inicial');
$obNumKmInicial->setValue( number_format(($kmInicial ? $kmInicial:'0'),1,',','.') );

if ($stAcao == 'retornar') {
    $obNumKmInicial->setLabel( true );
} else {
    $obNumKmInicial->setNull( false );
}

//instancia um textarea para o destino
$obTxtDestino = new TextArea();
$obTxtDestino->setName( 'stDestino' );
$obTxtDestino->setId( 'stDestino' );
$obTxtDestino->setRotulo( 'Destino/Observações' );
$obTxtDestino->setTitle( 'Informe o destino do veículo.' );

if ($stAcao == 'retornar') {
    $obTxtDestino->setLabel( true );
    $obTxtDestino->setValue( $rsVeiculo->getCampo('destino') );
} else {
    $obTxtDestino->setNull( false );
}

if ($stAcao == 'retornar') {
    //instancia um componente data para o retorno
    $obDtRetorno = new Data();
    $obDtRetorno->setName( 'dtRetorno' );
    $obDtRetorno->setId( 'dtRetorno' );
    $obDtRetorno->setValue( date('d/m/Y') );
    $obDtRetorno->setRotulo( 'Data de Retorno' );
    $obDtRetorno->setTitle( 'Informe a data de retorno do veículo.' );
    $obDtRetorno->setNull( false );

    //instancia um componente hora para a retorno
    $obHoraRetorno = new Hora();
    $obHoraRetorno->setName( 'horaRetorno' );
    $obHoraRetorno->setId  ( 'horaRetorno' );
    $obHoraRetorno->setValue( date('H:i:s') );
    $obHoraRetorno->setRotulo( 'Hora de Retorno' );
    $obHoraRetorno->setTitle ( 'Informe a hora de retorno do veículo.' );
    $obHoraRetorno->setNull ( false );

    //instancia um numerico para a quilometragem
    $obNumKmRetorno = new Numerico();
    $obNumKmRetorno->setRotulo( 'Km no Retorno' );
    $obNumKmRetorno->setTitle ( 'Informe a quilometragem no retorno do veículo.' );
    $obNumKmRetorno->setName  ( 'inKmRetorno' );
    $obNumKmRetorno->setNull  ( true );
    $obNumKmRetorno->setDecimais( 1 );
    $obNumKmRetorno->setNegativo( false );
    $obNumKmRetorno->obEvento->setOnChange( $obNumKmInicial->obEvento->getOnKeyUp() );
    $obNumKmRetorno->setValue( number_format($rsVeiculo->getCampo('km_inicial'),1,',','.') );
    $obNumKmRetorno->setNull( false );
    
    //Hidden para Quantidade de Horas Trabalhadas
    $obHdnHoraTrabalhada  = new Hidden;
    $obHdnHoraTrabalhada->setName ( "stHoraTrabalhada" );
    $obHdnHoraTrabalhada->setId   ( 'stHoraTrabalhada' );
    $obHdnHoraTrabalhada->setValue("");
    
    //instancia um componente hora para Quantidade de Horas Trabalhadas
    $obHoraTrabalhada = new Hora();
    $obHoraTrabalhada->setName( 'horaTrabalhada' );
    $obHoraTrabalhada->setId  ( 'horaTrabalhada' );
    $obHoraTrabalhada->setRotulo( 'Quantidade de Horas Trabalhadas' );
    $obHoraTrabalhada->setTitle ( 'Informe a Quantidade de Horas Trabalhadas.' );
    $obHoraTrabalhada->setNull ( false );
    $obHoraTrabalhada->setMaxLength ( 7 );
    $obHoraTrabalhada->setValue('000:00');    
    $obHoraTrabalhada->obEvento->setOnKeyUp("javascript: mascaraHorasTrabalhadas(this, event);");
    $obHoraTrabalhada->obEvento->setOnBlur ("verificaHorasTrabalhadas( this );");
    
    $jsFocusDireita = "javascript: this.selectionStart = this.value.length; this.selectionEnd = this.value.length;";
    $obHoraTrabalhada->obEvento->setOnFocus     ($jsFocusDireita);
    $obHoraTrabalhada->obEvento->setOnClick     ($jsFocusDireita);
    $obHoraTrabalhada->obEvento->setOnDblClick  ($jsFocusDireita);

    //instancia um textbox para observacao
    $obTxtObservacao = new TextArea();
    $obTxtObservacao->setName( 'stObservacao' );
    $obTxtObservacao->setId( 'stObservacao' );
    $obTxtObservacao->setRotulo( 'Observação' );
    $obTxtObservacao->setTitle( 'Informe a observação para o veículo.' );
    $obTxtObservacao->setNull( true );

    //instancia um radio para verificar se houve a virada do odometro
    $obChkVirada = new CheckBox();
    $obChkVirada->setName( 'boViradaOdometro' );
    $obChkVirada->setRotulo( 'Virada do Odômetro' );
    $obChkVirada->setTitle( 'Informe se houve virada de odometro no retorno.' );
    $obChkVirada->setNull( true );
}

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
if($rsVeiculo->getCampo('controlar_horas_trabalhadas')=='t')
    $obFormulario->addHidden    ( $obHdnHoraTrabalhada );

$obFormulario->addTitulo    ( 'Dados da Retirada' );

$obFormulario->addComponente( $obInCodVeiculo );
$obFormulario->addComponente( $obBscMotorista );
$obFormulario->addComponente( $obDtSaida );
$obFormulario->addComponente( $obHoraSaida );
$obFormulario->addComponente( $obNumKmInicial );
$obFormulario->addComponente( $obTxtDestino );

if ($stAcao == 'retornar') {
    $obFormulario->addTitulo    ( 'Dados do Retorno' );
    $obFormulario->addComponente( $obDtRetorno );
    $obFormulario->addComponente( $obHoraRetorno );
    $obFormulario->addComponente( $obNumKmRetorno );
    if($rsVeiculo->getCampo('controlar_horas_trabalhadas')=='t')
        $obFormulario->addComponente( $obHoraTrabalhada );
    $obFormulario->addComponente( $obTxtObservacao );
    $obFormulario->addComponente( $obChkVirada );
}

// Botao de OK
$obBtnOK = new Ok(true);
$obBtnOK->obEvento->setOnClick("validaCampos();");

// Botao de Limpar
$obBtnLimpar = new Limpar;

$arBotoes = array($obBtnOK, $obBtnLimpar);

$obFormulario->defineBarra( $arBotoes );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
