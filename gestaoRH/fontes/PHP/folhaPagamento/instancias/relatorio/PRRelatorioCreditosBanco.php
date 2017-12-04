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
    * Página de Oculto do Relatório de Cadastro de Estagiários
    * Data de Criação : 19/04/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Alexandre Melo

    * @ignore

    * Casos de uso: uc-04.05.52
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';

//Define o nome dos arquivos PHP
//creditosBanco.rptdesign
$stPrograma = "RelatorioCreditosBanco";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$preview = new PreviewBirt(4,27,6);
$preview->setVersaoBirt( '2.5.0' );
$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/FLRelatorioCreditosBanco.php");
$preview->setTitulo('Relatório de Créditos por Banco');
$preview->setNomeArquivo('creditosBanco');
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("entidade", Sessao::getCodEntidade($boTransacao));

//periodo de movimentação
$inMesFinal =( $_POST["inCodMes"]<10 ) ? "0".$_POST["inCodMes"]:$_POST["inCodMes"];
$dtCompetenciaFinal = $inMesFinal."/".$_POST["inAno"];
$stFiltro = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetenciaFinal."'";
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentaco,$stFiltro);
$periodoMovimentacao = $rsPeriodoMovimentaco->getCampo("cod_periodo_movimentacao");

if (!$rsPeriodoMovimentaco->eof()) {
  $preview->addParametro("inCodPeriodoMovimentacao", $periodoMovimentacao);
  $preview->addParametro("stCompetencia", $dtCompetenciaFinal);
  $preview->addParametro("stPeriodoInicial", $rsPeriodoMovimentaco->getCampo("dt_inicial"));
  $preview->addParametro("stPeriodoFinal", $rsPeriodoMovimentaco->getCampo("dt_final"));
}

//tipo complementar
if ($_POST["inCodComplementar"] != "") {
    $inCodComplementar = $_POST["inCodComplementar"];
    $preview->addParametro("cod_complementar", $inCodComplementar);
} else {
    $inCodComplementar = 0;
    $preview->addParametro("cod_complementar", 0);
}

//lotação - local
if (count($_POST['inCodLotacaoSelecionados']) > 0) {
   $stLotacaoSelecionados = implode(',', $_POST['inCodLotacaoSelecionados']);
}

if (count($_POST['inCodLocalSelecionados']) > 0) {
    $stLocalSelecionados = implode(',', $_POST['inCodLocalSelecionados']);
}

if (count($_POST['inCodBancoSelecionados']) > 0) {
    $stBancoSelecionados = implode(',', $_POST['inCodBancoSelecionados']);
}

if (count($_POST['inCodAgenciaSelecionados']) > 0) {
    $stAgenciaSelecionados = implode(',', $_POST['inCodAgenciaSelecionados']);
}

$InCodConfiguracao = $_POST["inCodConfiguracao"];
$stSituacao =  $_POST["stSituacao"];
$boAgruparLotacao = ($_POST["boTotalLotacao"]) ? "true" : "false";
$boAgruparLocal = ($_POST["boTotalLocal"]) ? "true" : "false";
$boAgruparAgencia = ($_POST["boTotalAgencia"]) ? "true" : "false";
$stEntidade =Sessao::getEntidade();
$entidade = Sessao::getCodEntidade($boTransacao);

$obConexao   = new Conexao;
$rsRecordSet = new RecordSet;
//$stSql ="select * from creditosPorBanco(".$periodoMovimentacao .",".$InCodConfiguracao.",".$inCodComplementar.",'".$stLotacaoSelecionados."','".$stLocalSelecionados."','".$stBancoSelecionados."','".$stAgenciaSelecionados."','".$stSituacao."','".$stEntidade."','".$boAgruparLotacao."','".$boAgruparLocal."','".$boAgruparAgencia."') order by nom_cgm;" ;
//$obConexao->executaSQL( $rsRecordSet, $stSql);
//cria a tabela para adicionar os parametros
$dbCriaTabela = new dataBaseLegado;
$dbCriaTabela->abreBd();
$select =   "SELECT creditosPorBanco()";
$dbCriaTabela->abreSelecao($select);
$stNomeTabela = $dbCriaTabela->pegaCampo("creditosporbanco");

$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('inCodPeriodoMovimentacao' ,'$periodoMovimentacao'   ,'true', 'integer', 1  );";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('InCodConfiguracao'               ,'$InCodConfiguracao'        ,'true', 'integer', 2  );";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('inCodComplementar'             ,'$inCodComplementar'      ,'true', 'integer', 3  );";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stLotacaoSelecionados'        ,'$stLotacaoSelecionados' ,'true', 'varchar', 4  );";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stLocalSelecionados'             ,'$stLocalSelecionados'     ,'true', 'varchar', 5  );";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stBancoSelecionados'           ,'$stBancoSelecionados'    ,'true', 'varchar', 6 );";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stAgenciaSelecionados'        ,'$stAgenciaSelecionados' ,'true', 'varchar', 7 );";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stSituacao'                            ,'$stSituacao'                     ,'true', 'varchar', 8  );";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('stEntidade'                            ,'$stEntidade'                    ,'true', 'varchar', 9  );";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('boAgruparLotacao'                ,'$boAgruparLotacao'        ,'true', 'varchar', 10  );";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('boAgruparLocal'                    ,'$boAgruparLocal'             ,'true', 'varchar', 11  );";
$insert .= "insert into $stNomeTabela (campo, valor, parametrosPL, tipo, ordem) values('boAgruparAgencia'                ,'$boAgruparAgencia'        ,'true', 'varchar', 12  );";

$preview->addParametro( "stNomeTabela"              , $stNomeTabela);
$preview->addParametro( "boAgruparLotacao", ($_POST["boTotalLotacao"]) ? "true" : "false");
$preview->addParametro( "boQuebrarLotacao", ($_POST["boQuebraLotacao"]) ? "true" : "false");
$preview->addParametro( "boAgruparLocal"  , ($_POST["boTotalLocal"]) ? "true" : "false");
$preview->addParametro( "boQuebrarLocal"  , ($_POST["boQuebraLocal"]) ? "true" : "false");
$preview->addParametro( "boAgruparAgencia", ($_POST["boTotalAgencia"]) ? "true" : "false");
$preview->addParametro( "boQuebrarAgencia", ($_POST["boQuebraAgencia"]) ? "true" : "false");
$preview->addParametro( "inCodConfiguracao", $_POST["inCodConfiguracao"]);
$preview->addParametro( "stSituacao", $stSituacao);

if ( !$dbCriaTabela->executaSql($insert) ) {
    $stJs .= "alertaAviso('Erro ao criar tabela temporária','form','aviso','".Sessao::getId()."');";
    SistemaLegado::executaFrameOculto($stJs);
}

$jsOnload = "window.onunload = function sair() {
    executaFuncaoAjax('dropTmpTable','&stNomeTabela=$stNomeTabela',true);
};";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/ajax.php';

$dbCriaTabela->limpaSelecao();
$dbCriaTabela->fechaBd();

//$preview->addParametro( "stSituacao"      , $_POST["stSituacao"]);
$preview->addParametro( "codLocal"        , $stLocalSelecionados);
$preview->addParametro( "codLotacao"      , $stLotacaoSelecionados);
$preview->addParametro( "codBanco"        , $stBancoSelecionados);
$preview->addParametro( "codAgencia"      , $stAgenciaSelecionados);

$preview->preview();
