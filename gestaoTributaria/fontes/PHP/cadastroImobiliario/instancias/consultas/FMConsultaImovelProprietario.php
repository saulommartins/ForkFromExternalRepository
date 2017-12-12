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
    * Pagina de Consulta dos Proprietarios e Promitentes do Imóvel
    * Data de Criação   : 14/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    * $Id: FMConsultaImovelProprietario.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.12  2006/09/18 10:30:20  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS   );
include_once( $pgOcul );

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

$obRCIMImovel = new RCIMImovel( new RCIMLote );
$obRCIMImovel->setNumeroInscricao( $_REQUEST["inCodInscricao"] );
$obErro = $obRCIMImovel->consultarImovel( $boTransacao, TRUE );

$arProprietariosSessao = array();
Sessao::write('proprietarios', $arProprietariosSessao);

//MONTA LISTA DE PROPRIETÁRIOS
foreach ($obRCIMImovel->arRCIMProprietario as $obRCIMProprietario) {
    $arProprietariosSessao[] = array( "inNumCGM"   => $obRCIMProprietario->getNumeroCGM(),
                                      "stNomeCGM"  => $obRCIMProprietario->obRCGM->getNomCGM(),
                                      "flQuota"    => number_format( $obRCIMProprietario->getCota(),2,",","."),
                                      "ordem"      => $obRCIMProprietario->getOrdem()  );
}
Sessao::write('proprietarios', $arProprietariosSessao);

$rsProprietarios = new RecordSet;
if ( is_array($arProprietariosSessao) ) {
    $rsProprietarios->preenche( $arProprietariosSessao );
}
$stJs = montaListaProprietario( $rsProprietarios  );
//MONTA LISTA DE PROMITENTES

$arPromitentesSessao = Sessao::read('promitentes');

foreach ($obRCIMImovel->arRCIMProprietarioPromitente as $obRCIMProprietarioPromitente) {
    $arPromitentesSessao[] = array( "inNumCGM"  => $obRCIMProprietarioPromitente->getNumeroCGM(),
                                    "stNomeCGM" => $obRCIMProprietarioPromitente->obRCGM->getNomCGM(),
                                    "flQuota"   => number_format( $obRCIMProprietarioPromitente->getCota(),2,",", "." ),
                                    "ordem"     => $obRCIMProprietarioPromitente->getOrdem()  );
}
Sessao::write('promitentes', $arPromitentesSessao);

$rsPromitentes = new RecordSet;
if ( is_array($arPromitentesSessao)) {
    $rsPromitentes->preenche( $arPromitentesSessao );
}
$stJs .= montaListaPromitente( $rsPromitentes  );

SistemaLegado::executaFramePrincipal( $stJs );

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

$obSpnListaProprietario = new Span;
$obSpnListaProprietario->setId( "lsListaProprietarios" );

$obSpnListaPromitentes = new Span;
$obSpnListaPromitentes->setId( "lsListaPromitentes" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-05.01.18" );
$obFormulario->addTitulo( "Dados do Lote" );
$obFormulario->addHidden( $obHdnCtrl      );
$obFormulario->addHidden( $obHdnAcao      );

$obFormulario->addSpan( $obSpnListaProprietario );
$obFormulario->addSpan( $obSpnListaPromitentes  );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

//$obFormulario->Voltar( $stLocation );
$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );
$obFormulario->show();

?>
