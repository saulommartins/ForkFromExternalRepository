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
  * Página de Lista da Consulta de Escrituração
  * Data de criação : 13/12/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Márson Luís Oliveira de Paula

    * $Id: LSConsultarEscrituracao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.19
**/

/*
$Log$
Revision 1.2  2007/02/22 12:21:43  cassiano
Consulta escrituração

Revision 1.1  2007/01/02 12:27:58  marson
Inclusão Consulta de Escrituração de Receita.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoFaturamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarEscrituracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId();
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read( "link" );
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write( "link", $link );
//MONTAGEM DO FILTRO
$stFiltro = '';
//    [stEscrituracao] => servico
if ($_REQUEST['inInscricaoEconomica']) {
    $stFiltro .= " \n CADASTRO_ECONOMICO.inscricao_economica = '".$_REQUEST['inInscricaoEconomica']."' AND ";
}

if ($_REQUEST['inCGM']) {
    $stFiltro .= " ( CADASTRO_ECONOMICO_EMPRESA_DIREITO.numcgm =  ".$_REQUEST['inCGM']."  \n";
    $stFiltro .= " OR CADASTRO_ECONOMICO_EMPRESA_FATO.numcgm = ".$_REQUEST['inCGM']."  \n";
    $stFiltro .= " OR CADASTRO_ECONOMICO_AUTONOMO.numcgm = ".$_REQUEST['inCGM'].") AND ";
}

//$stFiltro .= " ( COALESCE(eceml.cod_modalidade, eam.cod_modalidade) IS NOT NULL) AND ";

if ( $stFiltro )
    $stFiltro = " AND ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

$stOrder .= " ORDER BY                                     \n";
$stOrder .= "     CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA,  \n";
$stOrder .= "     CADASTRO_ECONOMICO_FATURAMENTO.TIMESTAMP \n";

$obTARRCadastroEconomicoFaturamento = new TARRCadastroEconomicoFaturamento;
$obTARRCadastroEconomicoFaturamento->recuperaRelacionamento( $rsLista, $stFiltro,$stOrder );

$table = new TableTree();
$table->setRecordset( $rsLista );

// Defina o arquivo que conterá os métodos para exibir dados relacionados
$table->setArquivo( CAM_GT_ARR_INSTANCIAS.'consultas/'.$pgOcul);
// parametros do recordSet
$table->setParametros( array( 'inscricao_economica', 'numcgm','timestamp','competencia') );
// parametros adicionais
$table->setComplementoParametros( "stCtrl=listaModalidade");

// Defina o título da tabela
$table->setSummary('Registros');

$table->Head->addCabecalho( 'Inscrição Econômica' , 20  );
$table->Head->addCabecalho( 'Contribuinte' , 40  );
$table->Head->addCabecalho( 'Competência' , 20  );

// parãmetros para exibição: ( coluna, alinhamento )
$table->Body->addCampo( 'inscricao_economica', 'C' );
$table->Body->addCampo( '[numcgm] - [nom_cgm]', 'E' );
$table->Body->addCampo( 'competencia', 'D' );

$table->montaHTML();

echo $table->getHtml();
