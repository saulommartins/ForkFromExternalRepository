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
    * Página de oculto do IMA configuração - Bradesco
    * Data de Criação: 28/05/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.24

    $Id: OCConfiguracaoBradesco.php 64131 2015-12-04 21:03:54Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConfiguracaoBradesco";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function preencherDados(Request $request)
{
    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioBradesco.class.php");
    $obTIMAConfiguracaoConvenioBradesco = new TIMAConfiguracaoConvenioBradesco;
    $obTIMAConfiguracaoConvenioBradesco->recuperaRelacionamento($rsDados);
    if ($rsDados->getNumLinhas() > 0 ) {
        $request->set('stNumAgenciaTxt', $rsDados->getCampo("num_agencia"));
        $stJs .= preencheDadosConta($request);
        $stJs .= "f.stCodEmpresa.value = '".$rsDados->getCampo("cod_empresa")."';\n";
        $stJs .= "f.stNumAgencia.value = '".$rsDados->getCampo("num_agencia")."';\n";
        $stJs .= "f.stNumAgenciaTxt.value = '".$rsDados->getCampo("num_agencia")."';\n";
        $stJs .= "f.inTxtContaCorrente.value = '".$rsDados->getCampo("cod_conta_corrente")."';\n";
    }

    return $stJs;
}

function preencherDadosAgencia(Request $request)
{
    include_once(CAM_GT_MON_INSTANCIAS."agenciaBancaria/OCMontaAgencia.php");
    $request->set('stNumBanco', Sessao::read("stNumBanco"));
    $stJs = PreencheAgencia($request);

    return $stJs;
}

function preencheDadosConta(Request $request)
{
    include_once ( CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php" );
    $obTMONAgencia = new TMONAgencia;
    $stFiltro  = " where num_banco = '".Sessao::read("stNumBanco")."'";
    $stFiltro .= "   and num_agencia = '".$request->get("stNumAgenciaTxt")."'";
    $obTMONAgencia->recuperaRelacionamento($rsAgencia, $stFiltro);
    $rsConta = new RecordSet();
    if ($rsAgencia->getCampo('cod_agencia')) {
        include_once ( CAM_GT_MON_MAPEAMENTO."TMONContaCorrente.class.php" );
        $obTMONContaCorrente = new TMONContaCorrente;
        $stFiltro  = " where num_banco = '".Sessao::read("stNumBanco")."'";
        $stFiltro .= " and Ag.cod_agencia =".$rsAgencia->getCampo('cod_agencia');
        $obTMONContaCorrente->recuperaRelacionamento($rsConta, $stFiltro);
    }

    $inCount = 1;
    $stJs .= "limpaSelect(f.inTxtContaCorrente,0);                                     \n";
    $stJs .= "f.inTxtContaCorrente.options[0] = new Option('Selecione','', 'selected');\n";
    while (!$rsConta->eof()) {
        $inId   = $rsConta->getCampo("cod_conta_corrente");
        $stDesc = $rsConta->getCampo("num_conta_corrente");
        $stJs .= "f.inTxtContaCorrente.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
        $rsConta->proximo();
        $inCount++;
    }

    return $stJs;
}
switch ($request->get('stCtrl')) {
    case "preencherDadosAgencia":
        $stJs .= preencherDadosAgencia($request);
        $stJs .= preencherDados($request);
        break;
    case "preencheDadosConta";
        $stJs .= preencheDadosConta($request);
        break;
}
if ($stJs) {
    echo $stJs;
}

?>
