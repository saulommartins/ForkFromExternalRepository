<?php

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO . "RContabilidadeFundo.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterFundo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$obRContabilidadeFundo = new RContabilidadeFundo;

$obRContabilidadeFundo->setCodEntidade($_POST['inCodEntidade']);
$obRContabilidadeFundo->setCodOrgao($_POST['inCodOrgao']);

if (isset($_POST['inCodUnidade'])) {
	$obRContabilidadeFundo->setCodUnidade($_POST['inCodUnidade']);
}

$obRContabilidadeFundo->setCodFundo($_POST['inCodFundo']);
$obRContabilidadeFundo->setCnpj($_POST['stCnpjFundo']);
$obRContabilidadeFundo->setDescricao($_POST['stDescricaoFundo']);

$obRContabilidadeFundo->setPlano($_POST['inPlano']);
$obRContabilidadeFundo->setContabilidadeCentralizada($_POST['inTipoContabilidade']);

$obRContabilidadeFundo->setSituacao(1);
$obErro = $obRContabilidadeFundo->salvar();


if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm, "Novo fundo cadastrado com sucesso!", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::alertaAviso($pgForm, "Ocorreu um erro ao cadastrar o fundo!", "aviso", Sessao::getId(), "../");
}

?>
