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
 * Página de Inclusão de Mapa de Compras
 * Data de Criação   : 19/09/2006

 * @author Analista: Cleisson Barbosa
 * @author Desenvolvedor: Anderson C. Konze

 * @ignore

 * Casos de uso: uc-03.04.05

 $Id: FMManterMapaCompras.php 64284 2016-01-06 18:34:31Z jean $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_COMPONENTES.'IMontaSolicitacao.class.php';
include_once CAM_GP_COM_COMPONENTES.'IPopUpObjeto.class.php';
include_once CAM_GP_LIC_COMPONENTES.'ISelectTipoLicitacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasObjeto.class.php';

$link = Sessao::read('link');
$link = (is_array($link)) ? $link : array();

$stPrograma = "ManterMapaCompras";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php" ."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$link["pg"]."&pos=".$link["pos"];
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once $pgJs;
include_once $pgOcul;

$stAcao = $request->get('stAcao', 'incluir');

# Em breve irei tirar as gambiarras desse fonte.
Sessao::write('solicitacoes'           , array());
Sessao::write('itens'                  , array());
Sessao::write('itens_excluidos'        , array());
Sessao::write('solicitacoes_excluidas' , array());
Sessao::write('solicitacoes_anuladas'  , array());

# a variavel abaixo será usada pra gerar o codigo das solicitações que forem adcionadas na lista
Sessao::write('ultimoCodigo' , 0);

$boReservaRigida = SistemaLegado::pegaConfiguracao('reserva_rigida', '35', Sessao::getExercicio());
$boReservaRigida = ($boReservaRigida == 'true') ? true : false;

$boReservaAutorizacao = SistemaLegado::pegaConfiguracao('reserva_autorizacao', '35', Sessao::getExercicio());
$boReservaAutorizacao = ($boReservaAutorizacao == 'true') ? true : false;

if(!$boReservaRigida && !$boReservaAutorizacao){
    $stMsg = "Obrigatório Configurar o Tipo de Reserva em: Gestão Patrimonial :: Compras :: Configuração :: Alterar Configuração";
    
    $obLblMsgTipoReserva = new Label();
    $obLblMsgTipoReserva->setRotulo ( "Aviso" );
    $obLblMsgTipoReserva->setValue  ( $stMsg );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo     ( 'Configuração Tipo de Reserva'   );
    $obFormulario->addComponente ( $obLblMsgTipoReserva             );
    $obFormulario->show();
    
    exit();
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodClassificacao = new Hidden;
$obHdnCodClassificacao->setName ('HdnCodClassificacao');
$obHdnCodClassificacao->setId   ('HdnCodClassificacao');

$obHdnCentroCusto = new Hidden;
$obHdnCentroCusto->setName  ( 'inCodCentroCusto'    );
$obHdnCentroCusto->setId    ( 'inCodCentroCusto'    );
$obHdnCentroCusto->setValue ( $arItem['cod_centro'] );

// Objetos utilizados para emitir o Mapa de Compras.
$obCheckBoxEmitirMapa = new CheckBox;
$obCheckBoxEmitirMapa->setName  ('boEmitirMapa');
$obCheckBoxEmitirMapa->setId    ('boEmitirMapa');
$obCheckBoxEmitirMapa->setRotulo('Emitir Mapa de Compras');
$obCheckBoxEmitirMapa->setTitle ('Emitir Mapa de Compras');
$obCheckBoxEmitirMapa->setValue ('true');
$obCheckBoxEmitirMapa->obEvento->setOnClick("if (this.checked) { jQuery('#btnOk').attr('disabled', ''); } else { jQuery('#btnOk').attr('disabled', 'disabled'); }");

$obRadioMostraDadoSim = new Radio;
$obRadioMostraDadoSim->setRotulo ('Exibir Valores Monetários');
$obRadioMostraDadoSim->setTitle  ('Marque sim para que o mapa imprima os valores monetários como Vlr. Unitário, Vlr. Última Compra ou marque não para emitir esses campos em branco.');
$obRadioMostraDadoSim->setLabel  ('Sim');
$obRadioMostraDadoSim->setName   ('boMostraDado');
$obRadioMostraDadoSim->setId     ('boMostraDado');
$obRadioMostraDadoSim->setValue  ('true');
$obRadioMostraDadoSim->setChecked(true);

$obRadioMostraDadoNao = new Radio;
$obRadioMostraDadoNao->setLabel  ('Não');
$obRadioMostraDadoNao->setName   ('boMostraDado');
$obRadioMostraDadoNao->setId     ('boMostraDado');
$obRadioMostraDadoNao->setValue  ('false');

$obHdnCheck = new Hidden;
$obHdnCheck->setName ( 'obHdnCheck' );
$obHdnCheck->setId ( 'obHdnCheck' );

$obCheckBoxAnularTodosItens = new CheckBox;
$obCheckBoxAnularTodosItens->setName ( 'obAnularTodosItens' );
$obCheckBoxAnularTodosItens->setId ( 'obAnularTodosItens' );
$obCheckBoxAnularTodosItens->setRotulo ( 'Anular Todos' );
$obCheckBoxAnularTodosItens->setTitle ( 'Anular todos os itens da lista' );
$obCheckBoxAnularTodosItens->obEvento->setOnClick("if (this.checked) { jQuery('#obHdnCheck').val('true'); montaParametrosGET('anularTodosItens','obHdnCheck'); } else { jQuery('#obHdnCheck').val('false'); montaParametrosGET('anularTodosItens','obHdnCheck'); }");


// Fim

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

include_once 'FMManterMapaComprasAbaMapa.php';
include_once 'FMManterMapaComprasAbaItens.php';
include_once 'FMManterMapaComprasAbaTotais.php';

//Define formulário com abas
$obFormulario = new FormularioAbas;
$obFormulario->setAjuda( 'UC-03.04.05' );
$obFormulario->addForm( $obForm );

$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );

# Define a Aba com os dados do Mapa de Compras.
$obFormulario->addAba ( "Mapa" );
$obFormulario->addTitulo ( "Dados do Mapa"  );
$obFormulario->addComponente ( $obLblExercicio );
$obFormulario->addHidden ( $obHdnCentroCusto );
$obFormulario->addHidden ( $obHdnCodClassificacao );
if ($stAcao != 'incluir') {
    $obFormulario->addComponente ( $obTxtCodMapa );
    $obFormulario->addHidden     ( $obHdnCodMapa );
    $obFormulario->addHidden     ( $obHdnExercicioMapa );
}

$obFormulario->addComponente ( $obObjeto );

if ($stAcao == 'anular') {
    $obFormulario->addComponente ( $obTxtObs );
}

$obFormulario->addComponente ( $obISelectTipoLicitacao );

// Componentes para a Impressão do Mapa de Compras.
$obFormulario->addTitulo     ( "Impressão"           );
$obFormulario->addComponente ( $obCheckBoxEmitirMapa );
$obFormulario->addComponenteComposto($obRadioMostraDadoSim, $obRadioMostraDadoNao);

$obFormulario->addTitulo             ( "Dados das Solicitações do Mapa" );

if ($stAcao != 'anular') {
    $obSolicitacao->geraFormulario       ( $obFormulario );
    $obFormulario->defineBarraAba(array($obBtnIncluirForm, $obBtnLimparSolicitacaoForm));
}else{
    $obFormulario->addHidden     ( $obHdnBoRegistroPreco );
}

$obFormulario->addSpan               ( $obSpnSolicitacoes );
//Aba dos Itens
$obFormulario->addAba                ( "Itens" );
$obFormulario->addComponente         ( $obCheckBoxAnularTodosItens );
$obFormulario->addSpan               ( $obSpnItem );
$obFormulario->addSpan               ( $obSpnItens );
$obFormulario->addHidden             ( $obHdnCheck );

//Aba de Totais por item
$obFormulario->addAba                ( "Totais"          );
$obFormulario->addSpan               ( $obSpnTotais      );

$obBtnOk = new Ok('true');
$obBtnOk->setId( 'Ok');

if ($stAcao == 'incluir') {
    $obBtnLimparForm = new Button;
    $obBtnLimparForm->setName               ( "btnLimpar" );
    $obBtnLimparForm->setValue              ( "Limpar" );
    $obBtnLimparForm->setTipo               ( "button" );
    $obBtnLimparForm->obEvento->setOnClick  ( " executaFuncaoAjax('limpaFormulario');" );
    $obFormulario->defineBarra( array( $obBtnOk, $obBtnLimparForm), '', '' );
} else {
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;
    $obBtnCancelar = new Cancelar;
    $obBtnCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");
    $obFormulario->defineBarra( array( $obBtnOk, $obBtnCancelar  ), '', '' );
}

$obFormulario->show();

if ($stAcao != 'incluir') {
   //// Preenchendo a array de solicitaçãoes
   Sessao::write('exercicio_mapa' , $stExercicio);
   montaMapa($inCodMapa, $stExercicio );
   if ($stAcao != 'anular') {
        $obTComprasObjeto = new TComprasObjeto;
        $obTComprasObjeto->setDado( 'cod_objeto' , $inCodObjeto );
        $obTComprasObjeto->consultar();
        $txtObjeto = $obTComprasObjeto->getDado('descricao');
        $txtObjeto = str_replace("\r\n"," ",$txtObjeto);
        $txtObjeto = str_replace("\n"," ",$txtObjeto); 
        $stJs .= "d.getElementById(\"txtObjeto\").innerHTML = \"".$txtObjeto."\";";
        $stJs .= "montaParametrosGET('tipoLicitacao','inCodTipoLicitacao'  ); ";
   } elseif ($stAcao == 'anular') {
        $stJs .= liberaMapaAnulacao( $_REQUEST['cod_mapa'], $_REQUEST['exercicio'] );
   }
}

$stJs .= montaListaSolicitacoes   ( $stTipoCotacao, $stAcao               );
$stJs .= montaListaItens          ( $rsRecordSet, $stTipoCotacao, $stAcao );

sistemaLegado::executaFrameOculto ( $stJs                                 );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
