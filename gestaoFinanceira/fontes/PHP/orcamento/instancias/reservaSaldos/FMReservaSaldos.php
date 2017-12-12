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
    * Formulario para Orçamento - Reserva de Saldos
    * Data de Criação   : 06/05/2005

    * @author Analista: Diego Barbosa Victória
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    * $Id: FMReservaSaldos.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReservaSaldos.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ReservaSaldos";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PRManter".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
if ($stAcao == "anular") {
    if (date('Y') > Sessao::getExercicio() && Sessao::read('data_reserva_saldo_GF')) {
        $arDataReserva = explode('-', Sessao::read('data_reserva_saldo_GF'));
        $dtDataAnulacao = $arDataReserva[2].'/'.$arDataReserva[1].'/'.$arDataReserva[0];
    } else {
        $dtDataAnulacao = date('d/m')."/".Sessao::getExercicio();
    }
    if (isset($_REQUEST['stMotivoAnulacao'])) {
        $dtDataAnulacao = $_REQUEST['stMotivoAnulacao'];
    }
}

$stFiltro = '';
$arFiltro = Sessao::read('filtro');
if ( is_array($arFiltro) ) {
    foreach ($arFiltro as $stCampo=>$stValor) {
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg');
    $stFiltro .= '&pos='.Sessao::read('pos');
    $stFiltro .= '&paginando='.Sessao::read('paginando');
}

//DEFINE OBJETOS DAS CLASSES
$obROrcamentoReservaSaldos = new ROrcamentoReservaSaldos;
$obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoEntidade->setExercicio( Sessao::getExercicio()    );
$obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade']  );
$obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoEntidade->consultarNomes( $rsNomes );
$stNomEntidade = $rsNomes->getCampo("entidade");

/*$obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoRecurso->setExercicio( Sessao::getExercicio()    );
$obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso( $_REQUEST['inCodRecurso']  );
$obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoRecurso->consultar($rsRecurso, $boTransacao); */

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl"            );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"            );
$obHdnAcao->setValue ( $_REQUEST["stAcao"] );

$obHdnDtReserva = new Hidden;
$obHdnDtReserva->setName ( "dtDataReserva" );
$obHdnDtReserva->setValue  ( $_REQUEST['dtDataReserva']  );

$obHdnDtValidade = new Hidden;
$obHdnDtValidade->setName ( "dtDataValidade" );
$obHdnDtValidade->setValue  ( $_REQUEST['dtDataValidade']  );

$obHdnCodReserva = new Hidden;
$obHdnCodReserva->setName ( "inCodReserva" );
$obHdnCodReserva->setValue  ( $_REQUEST['inCodReserva']  );

// DEFINE OBJETOS DO FORMULARIO

// Define objeto Label para Nro Reserva
$obLblReserva = new Label;
$obLblReserva->setRotulo( "Número da Reserva" );
$obLblReserva->setValue ( $_REQUEST['inCodReserva'] );

// Define objeto Label para Entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade" );
$obLblEntidade->setValue ( $_REQUEST['inCodEntidade'].' - '.$stNomEntidade );

// Define Objeto Label para Despesa
$obLblDespesa = new Label;
$obLblDespesa->setRotulo ( "Dotação Orçamentária" );
$obLblDespesa->setValue  ( $_REQUEST['inCodDespesa'] );

// Define Objeto Label para Data da Reserva
$obLblDtReserva = new Label;
$obLblDtReserva->setRotulo ( "Data da Reserva" );
$obLblDtReserva->setValue  ( $_REQUEST['dtDataReserva']  );

// Define Objeto Label para Data de Validade
$obLblDtValidade = new Label;
$obLblDtValidade->setRotulo ( "Data de Validade" );
$obLblDtValidade->setValue  ( $_REQUEST['dtDataValidade']  );

include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoRecurso.class.php"             );
$obTOrcamentoRecurso = new TOrcamentoRecurso;

$inCodRecurso = $_REQUEST['inCodRecurso'];

if ($inCodRecurso) {
    $stFiltroQuery .= " WHERE cod_recurso = ".$_REQUEST['inCodRecurso'];
    if (Sessao::getExercicio()) {
        $stFiltroQuery .= " AND exercicio = '".Sessao::getExercicio()."' ";
    }
}

$obErro = $obTOrcamentoRecurso->recuperaTodos( $rsLista, $stFiltroQuery);
if ( !$obErro->ocorreu() ) {
    $stNomRecurso = $rsLista->getCampo( 'nom_recurso' );
}
$stRecurso = $_REQUEST['inCodRecurso'] . ' - ' . $stNomRecurso;

//Define objeto label para Recurso
$obLblRecurso = new Label;
$obLblRecurso->setRotulo( "Recurso"  );
$obLblRecurso->setValue ( $stRecurso );

// include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
// $obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
// $obIMontaRecursoDestinacao->setLabel( true );
// $obIMontaRecursoDestinacao->setCodRecurso( $_REQUEST['inCodRecurso'] );

if ($stAcao == "consultar") {
   // Define Objeto Label para Motivo
    $obLblMotivo = new Label;
    $obLblMotivo->setRotulo    ( "Motivo"      );
    $obLblMotivo->setValue     ( $_REQUEST['stMotivo']);

}

// Define Objeto Label para Valor da Reserva
$obLblValor = new Label;
$obLblValor->setRotulo ( "Valor" );
$obLblValor->setValue  ( $_REQUEST['flValorReserva'] );

if ($stAcao == "anular") {
    // Define Objeto data para Data de Anulação
    $obDtDataAnulacao = new Data;
    $obDtDataAnulacao->setName          ( "dtDataAnulacao"              );
    $obDtDataAnulacao->setRotulo        ( "Data da Anulação"            );
    $obDtDataAnulacao->setValue         ( $dtDataAnulacao   );
    $obDtDataAnulacao->setMaxLength     ( 20                            );
    $obDtDataAnulacao->setSize          ( 10                            );
    $obDtDataAnulacao->setNull          ( false                         );

    //Define objeto TEXTAREA para motivo de anulação
    $obTxtMotivoAnulacao = new TextArea;
    $obTxtMotivoAnulacao->setName             ( "stMotivoAnulacao"            );
    $obTxtMotivoAnulacao->setRotulo           ( "Motivo Anulação"             );
    $obTxtMotivoAnulacao->setValue            ( $_REQUEST['stMotivoAnulacao'] );
    $obTxtMotivoAnulacao->setMaxCaracteres    ( 160                           );
    $obTxtMotivoAnulacao->setNull             ( true                          );

} elseif ($stAcao == "consultar") {
    // Define Objeto Label para Data de Anulação
    $obLblDataAnulacao = new Label;
    $obLblDataAnulacao->setRotulo ( "Data de Anulação" );
    $obLblDataAnulacao->setValue  ( $_REQUEST['dtDataAnulacao']  );

    // Define Objeto Label para Motivo Anulação
    $obLblMotivoAnulacao = new Label;
    $obLblMotivoAnulacao->setRotulo    ( "Motivo Anulação"      );
    $obLblMotivoAnulacao->setValue     ( $_REQUEST['stMotivoAnulacao'] );
}

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm              );
$obFormulario->addHidden     ( $obHdnCtrl           );
$obFormulario->addHidden     ( $obHdnAcao           );
$obFormulario->addHidden     ( $obHdnDtReserva      );
$obFormulario->addHidden     ( $obHdnDtValidade     );
$obFormulario->addHidden     ( $obHdnCodReserva     );
$obFormulario->addTitulo     ( "Dados para Reserva de Saldos" );
$obFormulario->addComponente ( $obLblReserva        );
$obFormulario->addComponente ( $obLblEntidade       );
$obFormulario->addComponente ( $obLblDespesa        );
$obFormulario->addComponente ( $obLblDtReserva      );
$obFormulario->addComponente ( $obLblDtValidade     );
$obFormulario->addComponente ( $obLblRecurso        );
// $obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );

if ($stAcao == "anular") {
    $obFormulario->addComponente ( $obLblValor          );
    $obFormulario->addComponente ( $obDtDataAnulacao    );
    $obFormulario->addComponente ( $obTxtMotivoAnulacao );
    $obFormulario->Cancelar( $stLocation );
} elseif ($stAcao == "consultar") {
    $obFormulario->addComponente ( $obLblMotivo         );
    $obFormulario->addComponente ( $obLblValor          );
    $obFormulario->addComponente ( $obLblDataAnulacao   );
    $obFormulario->addComponente ( $obLblMotivoAnulacao );
    $obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
