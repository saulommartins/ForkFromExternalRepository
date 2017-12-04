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
* Arquivo instância para popup de Dependentes
* Data de Criação: 04/03/2008

* @author Analista: Dagiane Vieria
* @author Desenvolvedor: Alex Cardoso

$Id: LSProcurarCgmServidorDependente.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-04.08.17
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCgmServidorDependente";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript .= " function insereCGM(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " if ( window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."') ) { window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; } \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus(); \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obTPessoalServidor = new TPessoalServidor;
$obMascara = new Mascara;
$stFiltro = "";
$stLink   = "";

//Definição do filtro de acordo com os valores informados no FL
if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}
if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}

if ($_REQUEST["stNomeCgm"]) {
    $stFiltro .= " AND lower(nom_cgm) like lower('".addslashes($_REQUEST["stNomeCgm"])."%')||'%' ";
    $stLink   .= "&stNomeCgm=".$_REQUEST["stNomeCgm"];
}
if ($_REQUEST["stCPF"]) {
    $inCPF = $_REQUEST["stCPF"];
    $obMascara->desmascaraDado( $inCPF );
    $stFiltro .= " AND cpf = '".addslashes($inCPF)."'";
    $stLink   .= "&stCPF=".$_REQUEST["stCPF"];
}

if ($_REQUEST["boFiltrarPensaoJudicial"]) {

    $stFiltroPensaoJudicial = " WHERE cod_servidor IN (
                                        SELECT pensao.cod_servidor
                                          FROM pessoal.pensao,
                                               (SELECT pensao.cod_pensao,
                                                       max(timestamp) as timestamp
                                                  FROM pessoal.pensao
                                              GROUP BY cod_pensao) as max_pensao
                                         WHERE pensao.cod_pensao = max_pensao.cod_pensao AND
                                               pensao.timestamp = max_pensao.timestamp AND
                                               pensao.cod_pensao NOT IN (
                                                 SELECT pensao_excluida.cod_pensao
                                                   FROM pessoal.pensao_excluida,
                                                        (SELECT cod_pensao,
                                                                max(timestamp) as timestamp
                                                           FROM pessoal.pensao_excluida
                                                       GROUP BY cod_pensao) as max_pensao_excluida
                                                  WHERE pensao_excluida.cod_pensao = max_pensao_excluida.cod_pensao AND
                                                        pensao_excluida.timestamp  = max_pensao_excluida.timestamp
                                               )
                                         GROUP BY pensao.cod_servidor
                               )";

    $stLink   .= "&boFiltrarPensaoJudicial=".$_REQUEST["boFiltrarPensaoJudicial"];
}

$stFiltro .= " AND cod_servidor IN (
                   SELECT
                        cod_servidor
                   FROM
                        pessoal.servidor_dependente
                   $stFiltroPensaoJudicial
                   GROUP BY cod_servidor
              )\n";

//Filtro setado na pagina que abre a popup
$stLink .= "&inFiltro=".$_REQUEST['inFiltro'];

//faz busca dos CGM's utilizando o filtro setado
$stLink .= "&stAcao=".$stAcao;
$rsLista = new RecordSet;

$obTPessoalServidor->recuperaServidorPessoaFisica( $rsLista, $stFiltro, " ORDER BY nom_cgm" );
//$obTPessoalServidor->debug();
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereCGM();" );
$obLista->ultimaAcao->addCampo("1","numcgm");
$obLista->ultimaAcao->addCampo("2","nom_cgm");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
