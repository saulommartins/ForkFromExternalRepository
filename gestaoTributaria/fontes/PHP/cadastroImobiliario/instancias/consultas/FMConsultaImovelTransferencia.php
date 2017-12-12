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
    * Pagina de Consulta de Transferencia do Imovel
    * Data de Criação   : 15/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    * $Id: FMConsultaImovelTransferencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.6  2006/09/18 10:30:20  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"  );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTransferencia.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma."Transferencia.php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$stFiltro = '';
$arTransf4 = Sessao::read('sessao_transf4');
if ($arTransf4) {
    $stFiltro = '';
    foreach ($arTransf4 as $stCampo => $stValor) {
        if ( is_array($stValor) ) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".@urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo         ( 12 );
$obRCIMConfiguracao->setAnoExercicio         ( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao   ();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );
$arMascaraProcesso = explode( "/" , $stMascaraProcesso );

$obRCIMTransferencia = new RCIMTransferencia;
$obRCIMTransferencia->setInscricaoMunicipal( $_REQUEST["inCodInscricao"] );
$obRCIMTransferencia->setEfetivacao        ( 'f'                         );
$obRCIMTransferencia->listarTransferencia  ( $rsListaTransferencia       );

//$rsListaTransferencia->addStrPad( "cod_processo", strlen( $arMascaraProcesso[0] ), "0" );

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setRecordSet( $rsListaTransferencia );
$obLista->setTitulo("Lista de Transferência");

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Natureza da Transferência" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Processo" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data de Efetivação" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_processo]/[exercicio_proc]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[dt_efetivacao]&nbsp;" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "VISUALIZAR" );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:visualizarTransferencia();" );
$obLista->ultimaAcao->addCampo( "1", "cod_transferencia" );
$obLista->ultimaAcao->addCampo( "2", "cod_processo"      );
$obLista->ultimaAcao->addCampo( "3", "exercicio_proc"    );
$obLista->ultimaAcao->addCampo( "4", "creci"             );
$obLista->ultimaAcao->addCampo( "5", "nom_cgm"           );
$obLista->ultimaAcao->addCampo( "6", "dt_efetivacao"     );
$obLista->ultimaAcao->addCampo( "7", "observacao"        );
$obLista->commitAcao();

$obLista->show();

/****************************************/

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

$obSpnTransferencia = new Span;
$obSpnTransferencia->setId ( "spnTransferencia" );

$obSpnProprietarios = new Span;
$obSpnProprietarios->setId ( "spnProprietarios" );

$obSpnAdquirentes = new Span;
$obSpnAdquirentes->setId   ( "spnAdquirentes"   );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-05.01.18" );
$obFormulario = new Formulario;
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );

$obFormulario->addSpan( $obSpnTransferencia );
$obFormulario->addSpan( $obSpnProprietarios );
$obFormulario->addSpan( $obSpnAdquirentes   );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );

$obFormulario->show();
?>
