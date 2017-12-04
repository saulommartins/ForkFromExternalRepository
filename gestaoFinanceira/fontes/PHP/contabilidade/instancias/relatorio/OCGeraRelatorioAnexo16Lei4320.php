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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioAnexo16Lei4320";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgGera     = "OCGeraRelatorio".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

//Define Birt
$preview = new PreviewBirt(2,9,21);
$preview->setTitulo('Demonstrativo da Dívida Fundada Interna/Externa');
$preview->setVersaoBirt('2.5.0');

$obTOrcamentoEntidade = new TOrcamentoEntidade;
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio()  );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

if (count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
    if (preg_match( "/prefeitura.*/i", $rsEntidade->getCampo('nom_cgm')) || $boConfirmaFundo > 0) {
        $preview->addParametro( 'poder' , 'Executivo' );
    } else {
        $preview->addParametro( 'poder' , 'Legislativo' );
    }
} else {
    while (!$rsEntidade->eof()) {
        if (preg_match( "/prefeitura.*/i", $rsEntidade->getCampo('nom_cgm')) ) {
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
            $preview->addParametro( 'poder' , 'Executivo' );
            break;
        }
        $rsEntidade->proximo();
    }
}

# Recupera o nome do município.
$stSqlNomeMunicipio = "
SELECT nom_municipio 
  FROM sw_municipio 
 WHERE cod_municipio = (SELECT valor from administracao.configuracao WHERE cod_modulo = 2 AND exercicio = '".Sessao::getExercicio()."' AND parametro = 'cod_municipio')::INTEGER
   AND cod_uf = (SELECT valor from administracao.configuracao WHERE cod_modulo = 2 AND exercicio = '".Sessao::getExercicio()."' AND parametro = 'cod_uf')::INTEGER ";

$obConexao   = new Conexao;
$obErro      = new Erro;
$obRecordSet = new RecordSet;

$obErro = $obConexao->executaSQL( $rsRecordSet, $stSqlNomeMunicipio, $boTransacao );

$stCodEntidades = implode(',', $_POST['inCodEntidade']);

# SQL que define a regra do ticket #24184
$stSqlAux = " cod_entidade IN ( ".$stCodEntidades." ) AND cod_estrutural ILIKE '2.2.1.%' OR cod_estrutural ILIKE '2.2.2.%' ";

$preview->addParametro("exercicio"	  , Sessao::getExercicio() );
$preview->addParametro("cod_entidade" , $stCodEntidades );
$preview->addParametro("dt_inicial"   , '01/01/'.Sessao::getExercicio() );
$preview->addParametro("dt_final"     , '31/12/'.Sessao::getExercicio() );
$preview->addParametro("sqlAux"       , $stSqlAux );
$preview->addParametro("municipio"    , $rsRecordSet->getCampo('nom_municipio') );
$preview->addAssinaturas(Sessao::read('assinaturas'));

$preview->preview();

?>
