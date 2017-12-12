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
 * Pagina de Lista de Imoveis para Consulta de Imóveis
 * Data de Criação   : 10/06/2005

 * @author Analista: Fabio Bertoldi
 * @author Desenvolvedor: Marcelo Boezzio Paulino

 * @ignore

 * $Id: LSConsultaImovel.php 64894 2016-04-12 18:12:52Z evandro $

 * Casos de uso: uc-05.01.18
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMLote.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaImovel";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgLote   = "FM".$stPrograma."Lote.php";
$pgProp   = "FM".$stPrograma."Proprietario.php";
$pgCond   = "FM".$stPrograma."Condominio.php";
$pgTransf = "FM".$stPrograma."Transferencia.php";
$pgOculCons = "";

include_once 'JSConsultaImovel.js';

$stCaminho = CAM_GT_CIM_INSTANCIAS."consultas/";

$obRegra = new RCIMImovel( new RCIMLote );

Sessao::remove('proprietario');
Sessao::remove('promitentes' );
Sessao::remove('Adquirentes' );

$arFiltroSessao = Sessao::read('filtro');

if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltroSessao[$stCampo] = $stValor;
    }
    $boPg  = isset($_GET['pg']) ? $_GET['pg'] : 0;
    Sessao::write('pg', $boPg);
    $boPos = isset($_GET['pos']) ? $_GET['pos'] : 0;
    Sessao::write('pos', $boPos);
    Sessao::write('paginando', true);

    Sessao::write('filtro', $arFiltroSessao);
} else {
    Sessao::write('pg' , $request->get('pg'));
    Sessao::write('pos', $request->get('pos'));

    $request->set('inInscricaoImobiliaria', $arFiltroSessao['inInscricaoImobiliaria'] );
    $request->set('inCodLote'             , $arFiltroSessao['inCodLote'] );
    $request->set('stChaveLocalizacao'    , $arFiltroSessao['stChaveLocalizacao']) ;
    $request->set('inNumLogradouro'       , $arFiltroSessao['inNumLogradouro'] );
    $request->set('inNumero'              , $arFiltroSessao['inNumero'] );
    $request->set('stComplemento'         , $arFiltroSessao['stComplemento'] );
    $request->set('inCodCondominio'       , $arFiltroSessao['inCodCondominio'] );
    $request->set('inCodBairro'           , $arFiltroSessao['inCodBairro'] );
    $request->set('inNumCGM'              , $arFiltroSessao['inNumCGM'] );
    $request->set('stCreci'               , $arFiltroSessao['stCreci'] );
    $request->set('stAcao'                , $arFiltroSessao['stAcao'] );
}

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "consultar";
    $pgProx = $pgForm;
}

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraLote = $obRCIMConfiguracao->getMascaraLote();

$arAtributosFiltro = array();

foreach ($arFiltroSessao as $valor => $key) {
    if (preg_match("/Atributo_/",$valor)) {
        if ($key) {
            $arDados = explode( "_", $valor );
            $stValor = implode(",",$key);
            $arAtributosFiltro[] = array(
                "cod_cadastro" => $arDados[2],
                "cod_atributo" => $arDados[1],
                "valor" => $stValor
            );
        }
    }
}

//SETA ELEMENTOS PARA O FILTRO
$obRegra->setAtributosDinamicosConsultaImob                  ( $arAtributosFiltro );
$obRegra->setNumeroInscricao                                 ( $request->get('inInscricaoImobiliaria') );
$obRegra->roRCIMLote->setNumeroLote                          ( ltrim($request->get('inCodLote'), '0')  );
$obRegra->roRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $request->get('stChaveLocalizacao')     );
$obRegra->obRCIMCondominio->setCodigoCondominio              ( $request->get('inCodCondominio')        );
$obRegra->addImovelCorrespondencia();
$obRegra->obRCIMLogradouro->setCodigoLogradouro              ( $request->get('inNumLogradouro')        );
$obRegra->setNumeroImovel                                    ( (int) $request->get('inNumero')         );
$obRegra->setComplementoImovel                               ( $request->get('stComplemento')          );
$obRegra->obRCIMBairro->setCodigoBairro                      ( $request->get('inCodBairro')            );
$obRegra->addProprietario();
$obRegra->roUltimoProprietario->setNumeroCGM                 ( $request->get('inNumCGM')               );
$obRegra->obRCIMImobiliaria->setRegistroCreci                ( $request->get('stCreci')                );
$obRegra->listarConsultaCadastroImobilario( $rsLista ,"", $request->get("stOrder"));

$rsLista->setprimeiroElemento();

//MONTA LISTA DE IMOVEIS
$obLista = new Lista;
$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição Imobiliária");
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Endereço");
$obLista->ultimoCabecalho->setWidth( 37 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Localização");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Lote");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 18 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao_municipal" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "endereco" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "localizacao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor_lote" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

// Define ACOES
$stLink = "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
Sessao::write('stLink', $stLink);

$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( "lote" );
$obLista->ultimaAcao->addCampo ( "&inCodInscricao"   , "inscricao_municipal" );
$obLista->ultimaAcao->addCampo ( "&inCodLote"        , "cod_lote"            );
$obLista->ultimaAcao->addCampo ( "&stTipoLote"       , "tipo_lote"           );
$obLista->ultimaAcao->addCampo ( "&inCodLocalizacao" , "cod_localizacao"     );
$obLista->ultimaAcao->setLink  ( $pgLote."?".Sessao::getId().$stLink."&stAcao=lote" );
$obLista->commitAcao();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( "imovel" );
$obLista->ultimaAcao->addCampo ( "&inCodInscricao", "inscricao_municipal" );
$obLista->ultimaAcao->addCampo ( "&inCodLote"     , "cod_lote"            );
$obLista->ultimaAcao->addCampo ( "&stCreci"       , "creci"               );
$obLista->ultimaAcao->setLink  ( $pgForm."?".Sessao::getId().$stLink."&stAcao=imovel" );
$obLista->commitAcao();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( "proprietario" );
$obLista->ultimaAcao->addCampo ( "&inCodInscricao", "inscricao_municipal" );
$obLista->ultimaAcao->setLink  ( $pgProp."?".Sessao::getId().$stLink."&stAcao=proprietario" );
$obLista->commitAcao();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( "condominio" );
$obLista->ultimaAcao->addCampo ( "&inCodInscricao"  , "inscricao_municipal" );
$obLista->ultimaAcao->addCampo ( "&inCodCondominio" , "cod_condominio"      );
$obLista->ultimaAcao->setLink  ( $pgCond."?".Sessao::getId().$stLink."&stAcao=condominio" );
$obLista->commitAcao();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( "transf" );
$obLista->ultimaAcao->addCampo ( "&inCodInscricao", "inscricao_municipal" );
$obLista->ultimaAcao->setLink  ( $pgTransf."?".Sessao::getId().$stLink."&stAcao=transf" );
$obLista->commitAcao();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( "relatorio" );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink  ( "javascript:relatorio();" );
$obLista->ultimaAcao->addCampo ( "1", "[inscricao_municipal]" );
$obLista->commitAcao();

$obLista->show();

//DEFINE COMPONENTES DO FORMULARIO PARA EXECUTAR ACAO DO RELATORIO
$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( "relatorio" );

$obHdnInscricao = new Hidden;
$obHdnInscricao->setName  ( "inCodInscricao" );
$obHdnInscricao->setValue ( $rsLista->getCampo('inscricao_municipal') );

$obHdnLote = new Hidden;
$obHdnLote->setName  ( "inCodLote" );
$obHdnLote->setValue ( $rsLista->getCampo('cod_lote') );

$obHdnTipoLote = new Hidden;
$obHdnTipoLote->setName  ( "stTipoLote" );
$obHdnTipoLote->setValue ( $rsLista->getCampo('tipo_lote') );

$obHdnLocalizacao = new Hidden;
$obHdnLocalizacao->setName  ( "inCodLocalizacao" );
$obHdnLocalizacao->setValue ( $rsLista->getCampo('cod_localizacao') );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_CIM_INSTANCIAS."consultas/OCCadastroImobiliario.php" );

//MONTA FORMULARIO PARA EXECUTAR ACAO DO RELATORIO
$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.18" );
$obFormulario->addForm  ( $obForm       );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addHidden( $obHdnCtrl    );

$obFormulario->addHidden( $obHdnInscricao   );
$obFormulario->addHidden( $obHdnLote        );
$obFormulario->addHidden( $obHdnTipoLote    );
$obFormulario->addHidden( $obHdnLocalizacao );

$obFormulario->show();

?>
