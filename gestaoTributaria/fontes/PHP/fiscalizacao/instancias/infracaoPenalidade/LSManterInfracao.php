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
 * Página que lista Infrações
 * Data de Criacao: 04/08/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: LSManterInfracao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_FIS_NEGOCIO . "RFISInfracao.class.php" );
include_once( CAM_GT_FIS_VISAO . "VFISInfracao.class.php" );

# Define o nome dos arquivos PHP
$stPrograma = "ManterInfracao";
$pgFilt     = "FL" . $stPrograma . ".php";
$pgList     = "LS" . $stPrograma . ".php";
$pgForm     = "FM" . $stPrograma . ".php";
$pgProc     = "PR" . $stPrograma . ".php";
$pgOcul     = "OC" . $stPrograma . ".php";
$pgJs       = "JS" . $stPrograma . ".php";
$stCaminho  = CAM_GT_FIS_INSTANCIAS . "infracaoPenalidade/";

# Mantem mesma paginação
if ($_GET["pg"] and $_GET["pos"]) {
    $stLink .= "&pg=" . $_GET["pg"] . "&pos=" . $_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write( 'link', $link );
}

# Quando existir filtro na página FLManterInfracao.php, a variável
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

$stAcao = $request->get('stAcao');

# Mantem mesmo filtro
$stLink .= "&stAcao=".$stAcao;

$obRegra = new RFISInfracao();
$obVisao = new VFISInfracao( $obRegra );

# Filtro de pesquisa
if ($stAcao == "reativar") {
    $rsRecordSet = $obVisao->searchInfracoesBaixa( $_REQUEST );
} else {
    $rsRecordSet = $obVisao->searchInfracoes( $_REQUEST );
}

# Lista de Infrações.
$obLista = new Lista();
$obLista->setMostraPaginacao( true );
$obLista->setTitulo( "Registros de Infração" );
$obLista->setRecordSet( $rsRecordSet );

# Título da primeira coluna (número da Infração)
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

# Título da segunda coluna (Tipo)
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Fiscalização" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

# Título da terceira coluna (Infração)
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Infração" );
$obLista->ultimoCabecalho->setWidth( 45 );
$obLista->commitCabecalho();

# Título da primeira coluna (número da Infração)
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

#
# Dados
#

# Segunda coluna de dados (tipo de Fiscalização - descrição)
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo( "[cod_tipo] - [descricao]" );
$obLista->commitDado();

# Terceira coluna de dados (código da Infração - nome da Infração)
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo( "[cod_infracao] - [nom_infracao]" );
$obLista->commitDado();

# Quarta coluna (ação)
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodInfracao", "cod_infracao" );
$obLista->ultimaAcao->addCampo( "inTipoFiscalizacao", "cod_tipo" );
$obLista->ultimaAcao->addCampo( "stNomTipoFiscalizacao", "descricao" );
$obLista->ultimaAcao->addCampo( "stNomInfracao", "nom_infracao" );
$obLista->ultimaAcao->addCampo( "stCodTipoDocumento", "cod_tipo_documento" );
$obLista->ultimaAcao->addCampo( "stCodDocumento", "cod_documento" );
if ($stAcao == "reativar") {
    $obLista->ultimaAcao->addCampo( "stMotivo", "motivo" );
    $obLista->ultimaAcao->addCampo( "stDtbaixa", "data_baixa" );
    $obLista->ultimaAcao->addCampo( "stTimestampInicio", "timestamp_inicio" );
}

# Define a ação para cada item da tabela
$obLista->ultimaAcao->setLink( $pgForm.'?'.Sessao::getID().$stLink );
$obLista->commitAcao();
$obLista->show();

?>
