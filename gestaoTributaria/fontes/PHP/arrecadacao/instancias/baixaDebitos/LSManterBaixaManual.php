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
    * Pagina de Lista de Carne de Pagamento
    * Data de Criação   : 31/01/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    $Id: LSManterBaixaManual.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.24  2007/04/03 21:46:38  dibueno
*** empty log message ***

Revision 1.23  2007/03/12 19:30:21  cercato
adicionada opcao para baixa da carne da divida.

Revision 1.22  2007/03/05 17:28:51  cercato
retirando aspa simples do nomecgm para nao trancar na lista.

Revision 1.21  2007/02/16 15:08:05  dibueno
Bug #8432#

Revision 1.20  2007/02/12 18:08:31  cercato
correcao para passar a numeracao do carne correta.

Revision 1.19  2007/02/07 17:42:29  rodrigo
#8318#

Revision 1.18  2006/11/17 11:49:11  cercato
bug #7357#

Revision 1.17  2006/09/15 11:50:21  fabio
corrigidas tags de caso de uso

Revision 1.16  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php"                                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBaixaManual";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

require_once( $pgJs );

$pgProx = $pgForm   = "FM".$stPrograma.".php";

$arFiltro2 = Sessao::read( 'filtro2' );
//USADO QUANDO EXISTIR FILTRO
if ( is_array($arFiltro2) ) {
    $_REQUEST = $arFiltro2;
} else {
    $arFiltro2 = array();
    foreach ($_REQUEST as $key => $valor) {
        $arFiltro2[$key] = $valor;
    }

    Sessao::write( 'filtro2', $arFiltro2 );
}

// instancia regra de lancamento
$obRARRCarne = new RARRCarne ();

$obRARRCarne->setNumeracao                  ($_REQUEST["stNumeracao"]           );
$obRARRCarne->setNumeracaoMigrada           ($_REQUEST["stNumeracaoMigrada"]    );
$obRARRCarne->setCodContribuinteInicial     ($_REQUEST["inCodContribuinte"]     );
$obRARRCarne->setInscricaoImobiliariaInicial($_REQUEST["inInscricaoImobiliaria"]);
$obRARRCarne->setInscricaoEconomicaInicial  ($_REQUEST["inInscricaoEconomica"] );

$arNumCobranca = explode( "/", $_REQUEST["inNrParcelamento"] );
$obRARRCarne->setNumCobranca                ( $arNumCobranca[0]  );
if ($arNumCobranca[1] != "") {
    $obRARRCarne->setExercicioCobranca          ( $arNumCobranca[1]  );
}

$arInscricao = explode( "/", $_REQUEST["inCodInscricao"] );
if (is_array($arInscricao)) {
    $obRARRCarne->setInscricaoDivida ( $arInscricao[0] );
}

$arDados = explode( "/", $_REQUEST["inCodGrupo"] );
if (is_array($arInscricao)) {
    $obRARRCarne->setGrupo ( $arDados[0] );
}

$obRARRCarne->setCredito ( $_REQUEST["inCodCredito"] );
Sessao::write( 'consultadivida', false );
if ($_REQUEST["stCreditosRef"] == "da") {
    $obRARRCarne->stTipo = "da";
    Sessao::write( 'consultadivida', true );
} elseif ($_REQUEST["stCreditosRef"] == "ii" AND $_REQUEST["inInscricaoImobiliaria"]) {
    $obRARRCarne->stTipo = "imobiliaria";
} elseif ($_REQUEST["stCreditosRef"] == "ie" AND $_REQUEST["inInscricaoEconomica"]) {
    $obRARRCarne->stTipo = "economica";
} else {
    $obRARRCarne->stTipo = "cgm";
}
$obRARRCarne->setExercicio( $_REQUEST["inExercicio"] );
$obRARRCarne->obTARRCarne->setDado( 'exercicio', $_REQUEST["inExercicio"] );
$obRARRCarne->listarCarnesBaixa( $rsLista );

//passa filtro pra sessao
Sessao::write( 'filtro', "&inInscricaoImobiliaria=".$_REQUEST["inInscricaoImobiliaria"]."&inInscricaoEconomica=".$_REQUEST["inInscricaoEconomica"]."&inCodContribuinte=".$_REQUEST["inCodContribuinte"]."" );
Sessao::write( 'stRefCred',$_REQUEST["stCreditosRef"] );

// ** INICIO TESTE
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

while ( !$rsLista->eof() ) {
    $stNomCGM = $rsLista->getCampo( "nom_cgm" );
    $stNomCGM = str_replace( "'", " ", $stNomCGM );
    $rsLista->setCampo( "nom_cgm", $stNomCGM );
    $rsLista->setCampo( "valor_br", number_format( $rsLista->getCampo( 'valor' ), 2, ',', '.' ) );
    $rsLista->proximo();
}

$rsLista->setPrimeiroElemento();
$table = new Table();
$table->setRecordset( $rsLista );
$table->setSummary('Registros de carnês de pagamento');

//$table->setConditional( true , "#efefef" );

$table->Head->addCabecalho( 'Numeração' , 15 );
$table->Head->addCabecalho( 'Numeração Migrada' , 12 );
$table->Head->addCabecalho( 'Contribuinte' , 30 );
$table->Head->addCabecalho( 'Inscrição' , 8 );
$table->Head->addCabecalho( 'Origem' , 20 );
$table->Head->addCabecalho( 'Parcela' , 5 );
$table->Head->addCabecalho( '&nbsp;',5);

$stTitleLanc = "<b>Valor : </b><i>R$ [valor_br]</i><br>";

$table->Body->addCampo( '[numeracao]/[exercicio]', "E", $stTitleLanc );
$table->Body->addCampo( '[numeracao_migrada]'    , "C", $stTitleLanc );
$table->Body->addCampo( '[numcgm]-[nom_cgm]'     , "C", $stTitleLanc );
$table->Body->addCampo( '[inscricao]'            , "C", $stTitleLanc );
$table->Body->addCampo( '[origem]/[exercicio]'   , "E", $stTitleLanc );
$table->Body->addCampo( '[info_parcela]'         , "C", $stTitleLanc );

$table->Body->addAcao('alterar', 'consultarManual( %s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s );' , array( 'cod_lancamento','numeracao','numeracao_migrada','exercicio','cod_parcela','database','nom_cgm','numcgm','cod_convenio','valor','inscricao','vencimento','valida','nr_parcela' ) );

$table->montaHTML();
echo $table->getHtml();

// ** FINAL TESTE

?>
