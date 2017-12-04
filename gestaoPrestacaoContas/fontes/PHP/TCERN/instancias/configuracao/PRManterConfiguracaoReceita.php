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
 * Página de Processamento - Parâmetros do Arquivo
 * Data de Criação   : 17/04/2008

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Girardi dos Santos

 * @ignore

 * $Id: PRManterConfiguracaoReceita.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-06.06.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCERN_MAPEAMENTO."TTRNReceitaTC.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoReceita";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

function validaDados()
{
    foreach ($_POST as $key => $value) {
        if (strstr($key,"stCodigoTc")) {
            $arIdentificador = explode('_',$key);
            $inCod = $arIdentificador[1];
            $stCodEstrutural = $arIdentificador[2];
            $value = trim($value);

            if ($value <> "") {
                $stMsg = "";
                if ($stCodEstrutural[0] != 9 && strlen($value) > 8) {
                    $stMsg = 'O valor da linha '.$key[(strlen($key)-1)].' deve possuir apenas 8 caracteres. ';
                    $stMsg .= 'Para poder possuir 9 caracteres, o código estrutural deve começar com o número 9';
                } elseif ($stCodEstrutural[0] != 9 && strlen($value) < 8) {
                    $stMsg = 'O valor da linha '.$key[(strlen($key)-1)].' deve possuir 8 caracteres.';
                } elseif ($stCodEstrutural[0] == 9 && strlen($value) < 9) {
                    $stMsg = 'O valor da linha '.$key[(strlen($key)-1)].' deve possuir 9 caracteres.';
                }

                if ($stMsg != "") {
                    SistemaLegado::exibeAviso($stMsg,"n_alterar","erro");

                    return false;
                }
            }
        }
    }

    return true;
}

if (validaDados()) {

    $obErro = new Erro();
    $obReceitaTC = new TTRNReceitaTC();
    $obReceitaTC->setDado('exercicio', Sessao::getExercicio());
    $boErro = $obReceitaTC->exclusao();

    if ( !$obErro->ocorreu() ) {

        foreach ($_POST as $key => $value) {
            if (strstr($key,"stCodigoTc")) {
                $arIdentificador = explode('_',$key);
                $inCod = $arIdentificador[1];
                if (trim($value) <> "") {
                    $obReceitaTC->setDado('cod_receita', $inCod);
                    $obReceitaTC->setDado('cod_tc'     , $value);
                    $obReceitaTC->setDado('exercicio', Sessao::getExercicio());

                    $obErro = $obReceitaTC->inclusao();
                    if( $obErro->ocorreu() ) break;
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm."?", " Dados alterados ", "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    }
}

SistemaLegado::LiberaFrames();

?>
