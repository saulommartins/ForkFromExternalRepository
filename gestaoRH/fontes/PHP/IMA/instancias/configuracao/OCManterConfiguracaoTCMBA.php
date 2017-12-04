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
    * Página oculta do formulário FMManterConfiguracaoTCMBA.php
    * Data de Criação: 22/02/2006

    * @author Analista: Dagiane
    * @author Desenvolvedor: Tiago Finger

    * @ignore

    * Casos de uso: uc-04.08.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php"                                );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoTCMBA";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgDown     = "DW".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

function preencherSubDivisao()
{
    $stServidor = $_GET['stServidor'];

    $stJs .= 'limpaSelect(f.inCodSubDivisaoDisponiveis'.$stServidor.",0);                               \n";
    $stJs .= 'limpaSelect(f.inCodSubDivisaoSelecionados'.$stServidor.",0);                              \n";

    //Limpa select subDivisaoSelecionados se não tem regimeSelecionados
    if ( count($_GET['inCodRegimeSelecionados'.$stServidor]) <= 0) {
        $stJs .= 'limpaSelect(f.inCodSubDivisaoSelecionados'.$stServidor.",0);                          \n";
    }

    if ( is_array($_GET['inCodRegimeSelecionados'.$stServidor]) ) {
        foreach ($_GET['inCodRegimeSelecionados'.$stServidor] as $inCodRegime) {
            $stCodRegime .= $inCodRegime.',';
        }
        $stCodRegime = substr($stCodRegime,0,strlen($stCodRegime)-1);
        $obTPessoalSubDivisao = new TPessoalSubDivisao;

        $stFiltro = ' AND psd.cod_regime IN ('.$stCodRegime.')';
        $obTPessoalSubDivisao->recuperaRelacionamento( $rsSubDivisao , $stFiltro, "", $boTransacao );

        $inIndex = 0;
        while ( !$rsSubDivisao->eof() ) {
            $stJs .= 'f.inCodSubDivisaoDisponiveis'.$stServidor.'['.$inIndex.'] = new Option(\''.$rsSubDivisao->getCampo("nom_sub_divisao").'\','.$rsSubDivisao->getCampo("cod_sub_divisao")." ,'');\n";
            $inIndex++;
            $rsSubDivisao->proximo();
        }
    }

    return $stJs;
}

$stJs = '';

switch ($_GET['stCtrl']) {
    case 'preencherSubDivisao':
        $stJs .= preencherSubDivisao();
    break;
}
if ($stJs) {
    print($stJs);
}
?>
