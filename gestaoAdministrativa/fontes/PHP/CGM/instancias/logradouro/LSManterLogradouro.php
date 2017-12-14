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
    * Página de lista para o cadastro de logradouro
    * Data de Criação   : 08/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
                             Gustavo Passos Tourinho
                             Cassiano de Vasconcelos Ferreira

    * @ignore

    * $Id: LSProcurarLogradouro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.04
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterLogradouro";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$obRCIMLogradouro = new RCIMLogradouro();
$stErro = $request->get('stErro');

if ($request->get('campoNom')) {
    $stLink .= "&campoNom=".$request->get("campoNom");}
if ($request->get('campoNum')) {
    $stLink .= "&campoNum=".$request->get("campoNum");}
if ($request->get('nomForm')) {
    $stLink .= "&nomForm=".$request->get("nomForm");}

//MONTA OS FILTROS
$inCodBairro = ($request->get("inCodigoBairro") != '') ? $request->get("inCodigoBairro") : $request->get("inCodBairro");
if ($inCodBairro != '' ) {
    $obRCIMLogradouro->setBairro( $inCodBairro );
    $stLink .= "&inCodigoBairro=".$inCodBairro;
}
if ($request->get("stCEP")) {
    $obRCIMLogradouro->setCEP( $request->get("stCEP") );
    $stLink .= "&stCEP=".$request->get("stCEP");
}

if ( $request->get("inCodigoLogradouro") ) {
    $obRCIMLogradouro->setCodigoLogradouro( $request->get("inCodigoLogradouro") );
    $stLink .= "&inCodigoLogradouro=".$request->get("inCodigoLogradouro");
}else if ( $request->get("inCodLogradouro") ) {
    $obRCIMLogradouro->setCodigoLogradouro( $request->get("inCodLogradouro") );
    $stLink .= "&inCodigoLogradouro=".$request->get("inCodLogradouro");
    $stLink .= "&inCodLogradouro=".$request->get("inCodLogradouro");
}
if ($request->get("stNomeLogradouro")) {
    $obRCIMLogradouro->setNomeLogradouro( $request->get("stNomeLogradouro") );
    $stLink .= "&stNomeLogradouro=".$request->get("stNomeLogradouro");
}
if ($request->get("inCodigoUF")) {
    $obRCIMLogradouro->setCodigoUF( $request->get("inCodigoUF") );
    $stLink .= "&inCodigoUF=".$request->get("inCodigoUF");
}
if ($request->get("inCodigoMunicipio")) {
    $obRCIMLogradouro->setCodigoMunicipio( $request->get("inCodigoMunicipio") );
    $stLink .= "&inCodigoMunicipio=".$request->get("inCodigoMunicipio");
}
if ($request->get("stCadastro")) {
    $stLink .= "&stCadastro=".$request->get("stCadastro");
}

if ( $request->get("stCEP") ) {
    $stCEP = str_replace("-","",$request->get("stCEP"));
    $obRCIMLogradouro->setCEP( $stCEP );    
    $stLink .= "&stCEP=".$stCEP;
}

$stLink .= "&stAcao=".$request->get('stAcao');

Sessao::write('stLink',$stLink);
$obRCIMLogradouro->listarLogradouros( $rsLista, $boTransacao );

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro( "&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->setTitulo ( "Registros de Logradouro" );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome do Logradouro" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome do Bairro" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Município" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "CEP" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_logradouro" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "tipo_nome" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_bairro" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[sigla_uf] - [nom_municipio]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cep" );
$obLista->commitDado();

switch ($request->get('stAcao')) {
    case 'alterar':
        // Define ACAO ALTERAR
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao  ( "ALTERAR" );
        $obLista->ultimaAcao->addCampo ( "&inCodigoLogradouro", "cod_logradouro" );
        $obLista->ultimaAcao->addCampo ( "&inCodigoTipo"      , "cod_tipo"       );
        $obLista->ultimaAcao->addCampo ( "&stNomeLogradouro"  , "nom_logradouro" );
        $obLista->ultimaAcao->addCampo ( "&inCodigoUF"        , "cod_uf"         );
        $obLista->ultimaAcao->addCampo ( "&inCodigoMunicipio" , "cod_municipio"  );
        $obLista->ultimaAcao->addCampo ( "&stNomeUF"          , "nom_uf"         );
        $obLista->ultimaAcao->addCampo ( "&stNomeMunicipio"   , "nom_municipio"  );
        $obLista->ultimaAcao->setLink  ( $pgForm."?".Sessao::getId().$stLink );
        $obLista->commitAcao();        
    break;
    
    case 'excluir':
        $obLista->addAcao();
        Sessao::write('acao_generica', 'Excluir Logradouro');
        $obLista->ultimaAcao->setAcao  ( "REMOVER" );
        $obLista->ultimaAcao->addCampo ( "&inCodigoLogradouro", "cod_logradouro"                 );
        $obLista->ultimaAcao->addCampo ( "&inCodigoUF"        , "cod_uf"                         );
        $obLista->ultimaAcao->addCampo ( "&inCodigoMunicipio" , "cod_municipio"                  );
        $obLista->ultimaAcao->addCampo ( "&stNomeLogradouro"  , "tipo_nome"." "."nom_logradouro" );
        $obLista->ultimaAcao->addCampo ( "&stDescQuestao"     , "[cod_logradouro] - [tipo_nome]" );
        $obLista->ultimaAcao->addCampo ( "&stNomeLogradouro"  , "nom_logradouro" );
        $obLista->ultimaAcao->setLink  ( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
        $obLista->commitAcao();
    break;

    case 'consultar':
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao  ( "CONSULTAR" );
        $obLista->ultimaAcao->addCampo ( "&inCodigoLogradouro", '[cod_logradouro]' );
        $obLista->ultimaAcao->addCampo ( "&inCodigoTipo"      , "[cod_tipo] - [nom_tipo]" );
        $obLista->ultimaAcao->addCampo ( "&stNomeLogradouro"  , '[tipo_nome]'      );
        $obLista->ultimaAcao->addCampo ( "&inCodigoUF"        , '[cod_uf]'         );
        $obLista->ultimaAcao->addCampo ( "&inCodigoMunicipio" , '[cod_municipio]'  );
        $obLista->ultimaAcao->addCampo ( "&stNomeUF"          , '[nom_uf]'         );
        $obLista->ultimaAcao->addCampo ( "&stNomeMunicipio"   , '[nom_municipio]'  );
        $obLista->ultimaAcao->setLink  ( $pgForm."?".Sessao::getId().$stLink );
        $obLista->commitAcao();
    break;

}

$obLista->show();

$inCodigoUF = $request->get("inCodigoUF");
$inCodigoMunicipio = $request->get("inCodigoMunicipio");

//DEFINICAO DOS COMPONETES
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $request->get('campoNom') );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $request->get('campoNum') );

$obHdnCodUF = new Hidden;
$obHdnCodUF->setName     ( "inCodigoUF"       );
$obHdnCodUF->setValue    ( $inCodigoUF        );

$obHdnCadastro = new Hidden;
$obHdnCadastro->setName  ( "stCadastro" );
$obHdnCadastro->setValue ( $request->get("stCadastro")  );

$obHdnPais = new Hidden;
$obHdnPais->setName  ( "inCodPais" );
$obHdnPais->setValue ( $request->get("inCodPais") );

$obHdnCodMunicipio = new Hidden;
$obHdnCodMunicipio->setName  ( "inCodigoMunicipio" );
$obHdnCodMunicipio->setValue ( $inCodigoMunicipio  );

// DEFINE BOTOES
$obBtnIncluir = new Button;
$obBtnIncluir->setName              ( "btnIncluir"   );
$obBtnIncluir->setValue             ( "Incluir Novo" );
$obBtnIncluir->setTipo              ( "button"       );
$obBtnIncluir->obEvento->setOnClick ( "incluir();"   );
$obBtnIncluir->setDisabled          ( false          );

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$obBtnFechar = new Button;
$obBtnFechar->setName              ( "btnFechar" );
$obBtnFechar->setValue             ( "Fechar"    );
$obBtnFechar->setTipo              ( "button"    );
$obBtnFechar->obEvento->setOnClick ( "fechar();" );
$obBtnFechar->setDisabled          ( false       );

$botoes = array ($obBtnFiltro);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addHidden   ( $obHdnCampoNom );
$obFormulario->addHidden   ( $obHdnCampoNum );
$obFormulario->addHidden   ( $obHdnCodUF        );
$obFormulario->addHidden   ( $obHdnCodMunicipio );
$obFormulario->addHidden   ( $obHdnCadastro     );
$obFormulario->addHidden   ( $obHdnPais );
$obFormulario->defineBarra ( $botoes,'left',''   );
$obFormulario->show();

if ($stErro) {
    sistemaLegado::exibeAviso(str_replace( "\n", "", $stErro ),"n_excluir","erro");
}

?>
