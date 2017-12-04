<?php
/**
* Arquivo que passa os filtros dos estádos e cidade para a página de filtros
* Data de Criação: 04/07/2013

* Copyright CNM - Confederação Nacional de Municípios

$Id: $

* @author Analista      : Eduardo Schitz
* @author Desenvolvedor : Franver Sarmento de Moraes

* @package URBEM
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GA_CSE_MAPEAMENTO."TMunicipio.class.php");
include_once(CAM_GA_CSE_MAPEAMENTO."TUf.class.php");
//Define o nome dos arquivos PHP
$stPrograma = "RelatorioCgm";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$js = isset($js) ? $js : null;

switch ($_REQUEST["stCtrl"]) {
    case 'preencheUf':
        $js .= "var f = window.parent.frames['telaPrincipal'];\n";
        $js .= "f.inCodEstadoUf.value=''; \n";
        $js .= "limpaSelect(f.inCodEstadoUf,0); \n";
        $js .= "f.inCodEstadoUf[0] = new Option('Selecione um Estado','', 'selected');\n";
        $stFilto = "";
        if ($_REQUEST["inCodPais"]) {
            $obTMunicipio = new TUf();
            $stFilto = " AND sw_uf.cod_pais = ".$_REQUEST['inCodPais'];

            $obTMunicipio->mostraTodosEstadoCgm($rsUfs, $stFilto);
            $js .= "f.inCodMunicipio.options.value ='';";
            $inContador = 1;
            while ( !$rsUfs->eof() ) {
                $inCodUf = $rsUfs->getCampo( "cod_uf" );
                $stNomUF = $rsUfs->getCampo( "nom_uf" );
                $js .= "f.inCodEstadoUf.options[$inContador] = new Option('".addslashes($stNomUF)."','".$inCodUf."'); \n";
                $inContador++;
                $rsUfs->proximo();
            }
        }

        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inCodEstadoUf.value=''; \n";
        }
        sistemaLegado::executaIFrameOculto($js);
        break;

    case 'preencheMunicipio':
        $js .= "var f = window.parent.frames['telaPrincipal'];\n";
        $js .= "f.inCodMunicipio.value=''; \n";
        $js .= "limpaSelect(f.inCodMunicipio,0); \n";
        $js .= "f.inCodMunicipio[0] = new Option('Selecione um Município','', 'selected');\n";
        $stFilto = "";
        if ($_REQUEST["inCodEstadoUf"]) {
            $obTMunicipio = new TMunicipio();
            if ($_REQUEST['inCodPais']) {
                $stFilto = " AND sw_cgm.cod_pais = ".$_REQUEST['inCodPais'];
            }
            $stFilto .= " AND sw_municipio.cod_uf = ".$_REQUEST['inCodEstadoUf'];

            $obTMunicipio->mostraTodosMunicipiosCgm($rsMunicipios, $stFilto);
            $inContador = 1;
            while ( !$rsMunicipios->eof() ) {
                $inCodMunicipio = $rsMunicipios->getCampo( "cod_municipio" );
                $stNomMunicipio = $rsMunicipios->getCampo( "nom_municipio" );
                $js .= "f.inCodMunicipio.options[$inContador] = new Option('".addslashes($stNomMunicipio)."','".$inCodMunicipio."'); \n";
                $inContador++;
                $rsMunicipios->proximo();
            }
        }

        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inCodMunicipio.value=''; \n";
        }
        sistemaLegado::executaIFrameOculto($js);
        break;
}
