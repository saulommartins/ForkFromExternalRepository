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
 * Página que lista Penalidades
 * Data de Criação: 28/07/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: LSManterPenalidade.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once( CAM_GT_FIS_NEGOCIO . "RFISPenalidade.class.php" );
include_once( CAM_GT_FIS_VISAO . "VFISPenalidade.class.php" );

# Define o nome dos arquivos PHP
$stPrograma = "ManterPenalidade";
$pgFilt     = "FL" . $stPrograma . ".php";
$pgList     = "LS" . $stPrograma . ".php";
$pgForm     = "FM" . $stPrograma . ".php";
$pgProc     = "PR" . $stPrograma . ".php";
$pgOcul     = "OC" . $stPrograma . ".php";
$pgJs       = "JS" . $stPrograma . ".js";

# Mantem mesma paginação
if ($_GET["pg"] and $_GET["pos"]) {
    $stLink .= "&pg=" . $_GET["pg"] . "&pos=" . $_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write( 'link', $link );
}

# Quando existir filtro na página FLManterPenalidade.php, a variável
# link deve ser resetada.
$link = Sessao::read( 'link' );

if ( is_array( $link ) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $stKey => $stValor) {
        $link[$stKey] = $stValor;
    }

    Sessao::write( 'link', $link );
}

# Mantem mesmo filtro
$stAcao = $request->get('stAcao');
$stLink .= "&stAcao=" . $stAcao;

$obRegra = new RFISPenalidade();
$obVisao = new VFISPenalidade( $obRegra );

# Filtros da pesquisa
if ($stAcao == "reativar") {
    $rsRecordSet = $obVisao->buscaPenalidadesBaixadas( $_REQUEST );
    $rsRecordSet->addFormatacao( "nom_penalidade", "STRIPSLASHES" );
    $rsRecordSet->addFormatacao( "motivo", "STRIPSLASHES" );
} else {
    $rsRecordSet = $obVisao->buscaPenalidades( $_REQUEST );
    $rsRecordSet->addFormatacao( "nom_penalidade", "STRIPSLASHES" );
}

# Lista de Penalidades.
$obLista = new Lista();
$obLista->setMostraPaginacao( true );
$obLista->setTitulo( "Registros de Penalidade" );
$obLista->setRecordSet( $rsRecordSet );

# Título da primeira coluna (número da Penalidade)
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

# Título da segunda coluna (Tipo)
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

# Título da terceira coluna (Penalidade)
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Penalidade" );
$obLista->ultimoCabecalho->setWidth( 90 );
$obLista->commitCabecalho();

# Título da primeira coluna (número da Penalidade)
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

#
# Dados
#

# Segunda coluna de dados (tipo da Penalidade - descricao)
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo( "[cod_tipo_penalidade] - [descricao]" );
$obLista->commitDado();

# Terceira coluna de dados (código da Penalidade - nome da Penalidade)
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo( "[cod_penalidade] - [nom_penalidade]" );
$obLista->commitDado();

# Quarta coluna (ação)
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodTipoPenalidade", "cod_tipo_penalidade" );
$obLista->ultimaAcao->addCampo( "stNomTipoPenalidade", "descricao" );
$obLista->ultimaAcao->addCampo( "stNomPenalidade", "nom_penalidade" );
$obLista->ultimaAcao->addCampo( "inCodPenalidade", "cod_penalidade" );
$obLista->ultimaAcao->addCampo( "inCodNorma", "cod_norma" );

if ($stAcao == "reativar") {
    $obLista->ultimaAcao->addCampo( "dtBaixa", "data_baixa" );
    $obLista->ultimaAcao->addCampo( "stMotivo", "motivo" );
    $obLista->ultimaAcao->addCampo( "stTimestampInicio", "timestamp_inicio" );
}

$obLista->ultimaAcao->setLink( $pgForm.'?'.Sessao::getID().$stLink );
$obLista->commitAcao();

$obLista->show();

?>
