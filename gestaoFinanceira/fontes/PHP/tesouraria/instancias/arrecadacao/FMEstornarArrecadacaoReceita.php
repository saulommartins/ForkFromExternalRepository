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
    * Página de Formulário para Arrecadação Receita
    * Data de Criação   : 29/08/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 25002 $
    $Name$
    $Autor:$
    $Date: 2007-08-22 13:09:02 -0300 (Qua, 22 Ago 2007) $

    * Casos de uso: uc-02.04.04

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CLA_IAPPLETTERMINAL;
include_once CAM_GF_TES_NEGOCIO . 'RTesourariaBoletim.class.php';
include_once CAM_GF_ORC_COMPONENTES . 'ILabelEntidade.class.php';
include_once CAM_GF_ORC_COMPONENTES . 'ILabelReceitaRecurso.class.php';
include_once CAM_GF_CONT_COMPONENTES . 'ILabelContaBanco.class.php';
include_once CAM_GF_TES_COMPONENTES . 'ISaldoCaixa.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once ($pgJs);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$arFiltro = sessao::read('filtro');

$stFiltro = '';
if ($arFiltro) {
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        if ( is_array($stValor) ) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                 if ( is_array($stValor2) ) {
                    foreach ($stValor2 as $stCampo3 => $stValor3) {
                        $stFiltro .= "&".$stCampo3."=".@urlencode( $stValor3 );
                    }
                 } else {
                    $stFiltro .= "&".$stCampo2."=".@urlencode( $stValor2 );
                 }
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( sessao::read('exercicio') );
$obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.sessao::read('exercicio') ) );
$obErro = $obRTesourariaBoletim->buscarCodigoBoletim( $inCodBoletim, $stDtBoletim );
if ( $obErro->ocorreu() ) {
    $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".sessao::read('acao'));
    if( !$inCodBoletim )
        SistemaLegado::alertaAviso($pgFilt."?stAcao=incluir",urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"","erro", sessao::getId(), "../");
    else
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"","erro");
}

$nuValor = str_replace(".","",$_REQUEST['nuValor']);
$nuValor = str_replace(",",".",$nuValor);

$nuValorEstornado = str_replace(".","",$_REQUEST['nuValorEstornado']);
$nuValorEstornado = str_replace(",",".",$nuValorEstornado);
$nuValorEstornar = bcsub($nuValor, $nuValorEstornado, 4);
$nuHdnValorEstornar = number_format($nuValorEstornar, "2", ",", ".");
$nuValorEstornar = number_format($nuValorEstornar, "2", ",", ".");

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodArrecadacao = new Hidden();
$obHdnCodArrecadacao->setName( 'inCodArrecadacao' );
$obHdnCodArrecadacao->setValue( $_REQUEST['inCodArrecadacao'] );

$obHdnExercicio = new Hidden();
$obHdnExercicio->setName( 'stExercicio' );
$obHdnExercicio->setValue( $_REQUEST['stExercicio'] );

$obHdnCodReceita = new Hidden();
$obHdnCodReceita->setName( 'inCodReceita' );
$obHdnCodReceita->setValue( $_REQUEST['inCodReceita'] );

$obHdnCodReceitaDedutora = new Hidden();
$obHdnCodReceitaDedutora->setName( 'inCodReceitaDedutora' );
$obHdnCodReceitaDedutora->setValue( $_REQUEST['inCodReceitaDedutora'] );

$obHdnTimestampArrecadacao = new Hidden();
$obHdnTimestampArrecadacao->setName( 'stTimestampArrecadacao' );
$obHdnTimestampArrecadacao->setValue( $_REQUEST['stTimestampArrecadacao'] );

$obHdnCodEntidade = new Hidden();
$obHdnCodEntidade->setName( 'inCodEntidade' );
$obHdnCodEntidade->setValue( $_REQUEST['inCodEntidade'] );

$obHdnCodPlano = new Hidden();
$obHdnCodPlano->setName( 'inCodPlano' );
$obHdnCodPlano->setValue( $_REQUEST['inCodPlano'] );

$obHdnVlArrecadado = new Hidden();
$obHdnVlArrecadado->setName( 'nuValor' );
$obHdnVlArrecadado->setValue( $_REQUEST['nuValor'] );

$obHdnVlEstornado = new Hidden();
$obHdnVlEstornado->setName( 'nuValorEstornado' );
$obHdnVlEstornado->setValue( $_REQUEST['nuValorEstornado'] );

$obHdnVlEstornar = new Hidden();
$obHdnVlEstornar->setName( 'nuHdnValorEstornar' );
$obHdnVlEstornar->setValue( $nuHdnValorEstornar );

$obHdnVlDeducao = new Hidden();
$obHdnVlDeducao->setName( 'nuValorDeducao' );
$obHdnVlDeducao->setValue( $_REQUEST['nuValorDeducao'] );

if ( $request->get('nuValorDeducao') != '') {
    SistemaLegado::exibeAviso("Arrecadação com Dedução: O estorno não poderá ser parcial.","","aviso");
} elseif ( $request->get('inCodBemAlienacao') != '') {
    SistemaLegado::exibeAviso("Arrecadação com Alienação: O estorno não poderá ser parcial.","","aviso");
}

$obHdnVlDeducaoEstornado = new Hidden();
$obHdnVlDeducaoEstornado->setName( 'nuValorDeducaoEstornado' );
$obHdnVlDeducaoEstornado->setValue( $_REQUEST['nuValorDeducaoEstornado'] );

$obApplet = new IAppletTerminal( $obForm );

// Define Objeto ILabelEntidade para label da entidade
$obILabelEntidade = new ILabelEntidade( $obForm );
$obILabelEntidade->setMostraCodigo( true );
$obILabelEntidade->setCodEntidade( $_REQUEST['inCodEntidade'] );

require_once( CAM_GF_TES_COMPONENTES . 'ISelectBoletim.class.php' );
$obISelectBoletim = new ISelectBoletim;
$obISelectBoletim->obBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade']  );
$obISelectBoletim->obBoletim->setExercicio( $_REQUEST['stExercicio'] );

// Define Objeto ILabelContaBanco para label da conta banco
$obILabelContaBanco = new ILabelContaBanco( $obForm );
$obILabelContaBanco->setMostraCodigo( true );
$obILabelContaBanco->setCodPlano( $_REQUEST['inCodPlano'] );

// Define Objeto ILabelReceitaRecurso para label da receita
$obILabelReceitaRecurso = new ILabelReceitaRecurso( $obForm );
$obILabelReceitaRecurso->setMostraCodigo( true );
$obILabelReceitaRecurso->setCodReceita( $_REQUEST['inCodReceita'] );

// Define Objeto Label para data de arrecadacao
$stDtArrecadacao = SistemaLegado::dataToBr(substr($_REQUEST['stTimestaArrecadacao'],0,10));
$obLblDtArrecadacao = new Label;
$obLblDtArrecadacao->setRotulo  ( "Data Arrecadação" );
$obLblDtArrecadacao->setId      ( "stDtArrecadacaoLbl"  );
$obLblDtArrecadacao->setValue   ( $_REQUEST['stDtBoletim'] );

// Define Objeto Label para valor arrecadado
$obLblVlArrecadado = new Label;
$obLblVlArrecadado->setRotulo  ( "Valor Arrecadado" );
$obLblVlArrecadado->setId      ( "stVlArrecadadoLbl"  );
$obLblVlArrecadado->setValue   ( $_REQUEST['nuValor'] );

if ( $request->get('inCodReceitaDedutora') ) {
    $nuValorDeducao  = str_replace(".","",$_REQUEST['nuValorDeducao']);
    $nuValorDeducao  = str_replace(",",".",$nuValorDeducao);

    $obHdnValorEstornar = new Hidden();
    $obHdnValorEstornar->setName    ( "nuValorEstornar" );
    $obHdnValorEstornar->setValue   ( $nuValorEstornar  );

    $obLblVlEstornar = new Label;
    $obLblVlEstornar->setRotulo  ( "Valor a Estornar" );
    $obLblVlEstornar->setValue   ( $nuValorEstornar   );
    
    $nuValorDeducaoEstornado = str_replace(".","",$_REQUEST['nuValorDeducaoEstornado']);
    $nuValorDeducaoEstornado = str_replace(",",".",$nuValorDeducaoEstornado);

    $nuValorDeducaoEstornar = bcsub($nuValorDeducao, $nuValorDeducaoEstornado, 4);
    $nuHdnValorDeducaoEstornar = number_format($nuValorDeducaoEstornar, "2", ",", ".");
    $nuValorDeducaoEstornar = number_format($nuValorDeducaoEstornar, "2", ",", ".");

    $obHdnVlDeducaoEstornar = new Hidden();
    $obHdnVlDeducaoEstornar->setName( 'nuHdnValorDeducaoEstornar' );
    $obHdnVlDeducaoEstornar->setValue( $nuHdnValorDeducaoEstornar );

    // Define Objeto ILabelReceitaRecurso para label da receita
    $obILabelReceitaRecursoDedutora = new ILabelReceitaRecurso( $obForm );
    $obILabelReceitaRecursoDedutora->setRotulo( "Conta Dedução" );
    $obILabelReceitaRecursoDedutora->setMostraCodigo( true );
    $obILabelReceitaRecursoDedutora->setCodReceita( $_REQUEST['inCodReceitaDedutora'] );

    // Define Objeto Label para valor deducao
    $obLblVlDeducao = new Label;
    $obLblVlDeducao->setRotulo  ( "Valor Dedução" );
    $obLblVlDeducao->setId      ( "stVlDeducaoLbl"  );
    $obLblVlDeducao->setValue   ( $_REQUEST['nuValorDeducao'] );

    // Define Objeto Label para valor deducao estornado
    $obLblVlDeducaoEstornar = new Label;
    $obLblVlDeducaoEstornar->setRotulo  ( "Valor Dedução a Estornar" );
    $obLblVlDeducaoEstornar->setValue   ( $_REQUEST['nuValorDeducao'] );

    $obHdnValorDeducaoEstornar = new Hidden();
    $obHdnValorDeducaoEstornar->setName ( 'nuValorDeducaoEstornar' );
    $obHdnValorDeducaoEstornar->setValue ( $nuValorDeducaoEstornar );

} elseif ( $request->get('inCodBemAlienacao') == '' && $request->get('inCodReceitaDedutora') == '' ) {

    // Define Objeto Label para valor estornado
    $obLblVlEstornado = new Label;
    $obLblVlEstornado->setRotulo  ( "Valor Estornado" );
    $obLblVlEstornado->setId      ( "stVlEstornadoLbl"  );
    $obLblVlEstornado->setValue   ( $_REQUEST['nuValorEstornado'] );

    // Define Obeto Numerico para valor a estornar
    $obTxtValorEstornar = new Numerico();
    $obTxtValorEstornar->setRotulo   ("*Valor a Estornar"        );
    $obTxtValorEstornar->setTitle    ("Digite o Valor a Estornar");
    $obTxtValorEstornar->setName     ("nuValorEstornar"          );
    $obTxtValorEstornar->setId       ("nuValorEstornar"          );
    $obTxtValorEstornar->setValue    ($nuValorEstornar 		 );
    $obTxtValorEstornar->setNull     (false                      );
    $obTxtValorEstornar->setDecimais (2                          );
    $obTxtValorEstornar->setNegativo (false                      );
    $obTxtValorEstornar->setNull     (true                       );
    $obTxtValorEstornar->setSize     (17                         );
    $obTxtValorEstornar->setMaxLength(17                         );
    $obTxtValorEstornar->setMinValue (0.01                       );

} elseif ($request->get('inCodReceitaDedutora') == '' && $request->get('inCodBemAlienacao') != '') {
    $obHdnValorEstornar = new Hidden();
    $obHdnValorEstornar->setName    ( "nuValorEstornar" );
    $obHdnValorEstornar->setValue   ( $nuValorEstornar  );
}

if ( $request->get('inCodBemAlienacao') ) {
    include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
    
    $obTPatrimonioBem = new TPatrimonioBem();
    $obTPatrimonioBem->setDado('cod_bem', $request->get('inCodBemAlienacao'));
    $obTPatrimonioBem->recuperaPorChave($rsBem);
    
    $obHdnCodBemAlienacao = new Hidden();
    $obHdnCodBemAlienacao->setName  ( 'inCodBemAlienacao' );
    $obHdnCodBemAlienacao->setValue ( $request->get('inCodBemAlienacao') );
    
    $obLbDescricaoBem = new Label();
    $obLbDescricaoBem->setRotulo  ( "Bem:" );
    $obLbDescricaoBem->setValue   ( $rsBem->getCampo('cod_bem').' - '.$rsBem->getCampo('descricao') );
    
    $obLbValorAlienacao = new Label();
    $obLbValorAlienacao->setRotulo  ( "Valor Alienação:" );
    $obLbValorAlienacao->setValue   ( $request->get('nuValor') );
}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                    );
$obFormulario->addHidden     ( $obHdnAcao                 );
$obFormulario->addHidden     ( $obHdnCtrl                 );
$obFormulario->addHidden     ( $obHdnCodArrecadacao       );
$obFormulario->addHidden     ( $obHdnExercicio            );
$obFormulario->addHidden     ( $obHdnCodReceita           );
$obFormulario->addHidden     ( $obHdnCodReceitaDedutora   );
$obFormulario->addHidden     ( $obHdnTimestampArrecadacao );
$obFormulario->addHidden     ( $obHdnCodEntidade          );
$obFormulario->addHidden     ( $obHdnCodPlano             );
$obFormulario->addHidden     ( $obHdnVlArrecadado         );
$obFormulario->addHidden     ( $obHdnVlEstornado          );
$obFormulario->addHidden     ( $obHdnVlEstornar           );
$obFormulario->addHidden     ( $obHdnVlDeducao            );
$obFormulario->addHidden     ( $obHdnVlDeducaoEstornado   );
$obFormulario->addHidden     ( $obApplet                  );
$obFormulario->addTitulo     ( "Estornar Arrecadação por Receita" );
$obFormulario->addComponente ( $obILabelEntidade     );
$obFormulario->addComponente ( $obISelectBoletim     );
$obFormulario->addComponente ( $obILabelContaBanco   );
$obFormulario->addComponente ( $obILabelReceitaRecurso );
$obFormulario->addComponente ( $obLblDtArrecadacao   );
$obFormulario->addComponente ( $obLblVlArrecadado    );
if ( $request->get('inCodReceitaDedutora') ) {
    $obFormulario->addComponente ( $obLblVlEstornar );
    $obFormulario->addHidden     ( $obHdnValorEstornar );
    $obFormulario->addComponente ( $obILabelReceitaRecursoDedutora );
    $obFormulario->addComponente ( $obLblVlDeducao            );
    $obFormulario->addComponente ( $obLblVlDeducaoEstornar    );
    $obFormulario->addHidden     ( $obHdnValorDeducaoEstornar    );
} elseif ( $request->get('inCodReceitaDedutora') == '' && $request->get('inCodBemAlienacao') == '' )  {
    $obFormulario->addComponente ( $obLblVlEstornado     );
    $obFormulario->addComponente ( $obTxtValorEstornar   );
}
if ( $request->get('inCodBemAlienacao') ) {
    $obFormulario->addTitulo     ( "Alienação" );
    $obFormulario->addHidden     ( $obHdnValorEstornar   );
    $obFormulario->addHidden     ( $obHdnCodBemAlienacao );
    $obFormulario->addComponente ( $obLbDescricaoBem     );
    $obFormulario->addComponente ( $obLbValorAlienacao   );
}

$obOk = new Ok();
$obOk->obEvento->setOnClick("salvarArrecadacaoEstornada();");

$stLocation = $pgList.'?'.sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obCancelar = new Cancelar();
$obCancelar->obEvento->setOnClick( "Cancelar('".$stLocation."','telaPrincipal');" );
$obFormulario->defineBarra( array( $obOk, $obCancelar ) );

$obFormulario->show();

$ISaldoCaixa = new ISaldoCaixa();
$ISaldoCaixa->inCodEntidade = $_REQUEST['inCodEntidade'];
$jsOnLoad .= $ISaldoCaixa->montaSaldo();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>