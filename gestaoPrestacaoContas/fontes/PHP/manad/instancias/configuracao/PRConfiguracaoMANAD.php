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
set_time_limit(0);

/**
    * PÃ¡gina de Formulario de Ajustes Gerais Exportacao - MANAD
    * Data de CriaÃ§Ã£o: 19/11/2012
    *
    * @author Analista: Gelson GonÃ§alves
    * @author Desenvolvedor: Matheus Figueredo
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_MANAD_MAPEAMENTO."TExportacaoMANADConfiguracao.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ConfiguracaoMANAD";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTExportacao = new TExportacaoMANADConfiguracao();
$obTExportacao->setDado("exercicio", Sessao::getExercicio());

$obErro = new Erro;
$obTransacao = new Transacao;
$obTransacao->begin();
$boTransacao = $obTransacao->getTransacao();

# Codigo do Municipio
$obTExportacao->setDado("parametro", "manad_cod_mun");
$obTExportacao->setDado("cod_modulo", 59);
$obTExportacao->setDado("valor", $_POST["inCodMun"]);
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obErro = $obTExportacao->alteracao( $boTransacao );
} else {
    $obErro = $obTExportacao->inclusao( $boTransacao );
}

# Codigo da Finalidade
$obTExportacao->setDado("parametro", "manad_cod_fin");
$obTExportacao->setDado("cod_modulo", 59);
$obTExportacao->setDado("valor", $_POST["inCodFin"]);
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obErro = $obTExportacao->alteracao( $boTransacao );
} else {
    $obErro = $obTExportacao->inclusao( $boTransacao );
}

# Codigo do contador responsavel
$obTExportacao->setDado("parametro", "manad_numcgm_contador_responsavel");
$obTExportacao->setDado("cod_modulo", 59);
$obTExportacao->setDado("valor", $_POST["inCodResponsavelContabilidade"]);
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obErro = $obTExportacao->alteracao( $boTransacao );
} else {
    $obErro = $obTExportacao->inclusao( $boTransacao );
}

# Codigo do Documento do INSS
$obTExportacao->setDado("parametro", "manad_documento_inss_fornecedor");
$obTExportacao->setDado("cod_modulo", 59);
$obTExportacao->setDado("valor", $_POST["inDocINSS"]);
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obErro = $obTExportacao->alteracao( $boTransacao );
} else {
    $obErro = $obTExportacao->inclusao( $boTransacao );
}

# Codigo do Orgao Prefeitura
$obTExportacao->setDado("parametro", "manad_orgao_prefeitura");
$obTExportacao->setDado("cod_modulo", 59);
$obTExportacao->setDado("valor", $_POST["inCodOrgaoExecutivo"]);
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obErro = $obTExportacao->alteracao( $boTransacao );
} else {
    $obErro = $obTExportacao->inclusao( $boTransacao );
}

# Codigo da Unidade Prefeitura
$obTExportacao->setDado("parametro", "manad_unidade_prefeitura");
$obTExportacao->setDado("cod_modulo", 59);
$obTExportacao->setDado("valor", $_POST["inCodUnidadeExecutivo"]);
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obErro = $obTExportacao->alteracao( $boTransacao );
} else {
    $obErro = $obTExportacao->inclusao( $boTransacao );
}

# Codigo do Orgao Camara
$obTExportacao->setDado("parametro", "manad_orgao_camara");
$obTExportacao->setDado("cod_modulo", 59);
$obTExportacao->setDado("valor", $_POST["inCodOrgaoLegislativo"]);
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obErro = $obTExportacao->alteracao( $boTransacao );
} else {
    $obErro = $obTExportacao->inclusao( $boTransacao );
}

# Codigo da Unidade Camara
$obTExportacao->setDado("parametro", "manad_unidade_camara");
$obTExportacao->setDado("cod_modulo", 59);
$obTExportacao->setDado("valor", $_POST["inCodUnidadeLegislativo"]);
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obErro = $obTExportacao->alteracao( $boTransacao );
} else {
    $obErro = $obTExportacao->inclusao( $boTransacao );
}

# Codigo do Orgao RPPS
$obTExportacao->setDado("parametro", "manad_orgao_rpps");
$obTExportacao->setDado("cod_modulo", 59);
$obTExportacao->setDado("valor", $_POST["inCodOrgaoRPPS"]);
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obErro = $obTExportacao->alteracao( $boTransacao );
} else {
    $obErro = $obTExportacao->inclusao( $boTransacao );
}

# Codigo da Unidade RPPS
$obTExportacao->setDado("parametro", "manad_unidade_rpps");
$obTExportacao->setDado("cod_modulo", 59);
$obTExportacao->setDado("valor", $_POST["inCodUnidadeRPPS"]);
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obErro = $obTExportacao->alteracao( $boTransacao );
} else {
    $obErro = $obTExportacao->inclusao( $boTransacao );
}

# Codigo do Orgao Outros
$obTExportacao->setDado("parametro", "manad_orgao_outros");
$obTExportacao->setDado("cod_modulo", 59);
$obTExportacao->setDado("valor", $_POST["inCodOrgaoOutros"]);
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obErro = $obTExportacao->alteracao( $boTransacao );
} else {
    $obErro = $obTExportacao->inclusao( $boTransacao );
}

# Codigo da Unidade Outros
$obTExportacao->setDado("parametro", "manad_unidade_outros");
$obTExportacao->setDado("cod_modulo", 59);
$obTExportacao->setDado("valor", $_POST["inCodUnidadeOutros"]);
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
    $obErro = $obTExportacao->alteracao( $boTransacao );
} else {
    $obErro = $obTExportacao->inclusao( $boTransacao );
}

if (!$obErro->ocorreu()) {
    $obErro = $obTransacao->commitAndClose();
} else {
    $obTransacao->rollbackAndClose();
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm,"Configuração atualizada", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

?>
