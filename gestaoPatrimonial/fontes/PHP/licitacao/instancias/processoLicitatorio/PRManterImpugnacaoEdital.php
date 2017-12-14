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
    * Página de Processamento de impugnação de edital
    * Data de Criação   : 13/11/206

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    * $Id: PRManterImpugnacaoEdital.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.05.27

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoEditalImpugnado.class.php");
include_once(TLIC."TLicitacaoAnulacaoImpugnacaoEdital.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterImpugnacaoEdital";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );
$obTLicitacaoEditalImpugnado = new TLicitacaoEditalImpugnado();
Sessao::getTransacao()->setMapeamento( $obTLicitacaoEditalImpugnado );
$inMascara = explode("/",SistemaLegado::pegaConfiguracao('mascara_processo', 5, Sessao::getExercicio() ));
$inMascara = strlen($inMascara[0]);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "incluir":
    
        $arEdital = explode ( "/", $_REQUEST["stNumeroEdital"] );
        $arItensChave = array();
        $obTLicitacaoEditalImpugnado->setDado("num_edital",$arEdital[0]);
        $obTLicitacaoEditalImpugnado->setDado("exercicio", $arEdital[1]);
        $obTLicitacaoEditalImpugnado->recuperaProcessos( $rsProcessos );
        
        while (!$rsProcessos->eof()) {
            $stKeyDb = trim($arEdital[1]                                  )."-".
                       trim($arEdital[0]                                  )."-".
                       trim($rsProcessos->getCampo('exercicio_processo'  ))."-".
                       trim(str_pad($rsProcessos->getCampo('cod_processo'  ),$inMascara,"0",STR_PAD_LEFT));
            $arItensChave[$stKeyDb] = true;            
            $rsProcessos->proximo();
        }
        
        $arItem = Sessao::read('item');
            
        if ( count( $arItem ) > 0 ) {
            foreach ($arItem as $key => $value) {
                $stKeyNew = trim($arEdital[1]           )."-".
                            trim($arEdital[0]           )."-".
                            trim($value["inExercicio"]  )."-".
                            trim($value["inCodProcesso"]);
                $obTLicitacaoEditalImpugnado->setDado('exercicio'          ,$arEdital[1]);
                $obTLicitacaoEditalImpugnado->setDado('num_edital'         ,$arEdital[0]);
                $obTLicitacaoEditalImpugnado->setDado('exercicio_processo' ,$value["inExercicio"]);
                $obTLicitacaoEditalImpugnado->setDado('cod_processo'       ,$value["inCodProcesso"]);

                if ( !isset( $arItensChave[$stKeyNew] ) ) {
                    $obTLicitacaoEditalImpugnado->inclusao();
                } else {
                    if ($value["stParecerJuridico"]!="Anulado") {
                        $obTLicitacaoEditalImpugnado->alteracao();
                    unset( $arItensChave[$stKeyNew] );
                    }
                }
            }
    
            if ($arItensChave) {
                foreach ($arItensChave as $stChave => $valor) {
                    $arChave = explode('-',$stChave);
                    $obTLicitacaoAnulacaoImpugnacaoEdital = new TLicitacaoAnulacaoImpugnacaoEdital();
                    $obTLicitacaoAnulacaoImpugnacaoEdital->setDado('exercicio'  , $arChave[0] );
                    $obTLicitacaoAnulacaoImpugnacaoEdital->setDado('num_edital', $arChave[1] );
                    $obTLicitacaoAnulacaoImpugnacaoEdital->setDado('exercicio_processo' , $arChave[2]);
                    $obTLicitacaoAnulacaoImpugnacaoEdital->setDado('cod_processo' , $arChave[3] );
                    $obTLicitacaoAnulacaoImpugnacaoEdital->exclusao();
                    
                    $obTLicitacaoEditalImpugnado->setDado('exercicio'  , $arChave[0] );
                    $obTLicitacaoEditalImpugnado->setDado('num_edital', $arChave[1] );
                    $obTLicitacaoEditalImpugnado->setDado('exercicio_processo' , $arChave[2] );
                    $obTLicitacaoEditalImpugnado->setDado('cod_processo' , $arChave[3] );
                    $obTLicitacaoEditalImpugnado->exclusao();
                }
            }
            
            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."",$arEdital[0]."/".$arEdital[1],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso("Não há processos cadastrados para impugnar este edital.","unica","erro");
        }
    break;

    case "anular":
        $arEdital = explode ( "/", $_REQUEST["stNumeroEdital"] );
        $arProcesso = explode ( "/", $_REQUEST["stCodProcesso"] );
        $boErro = false;
        $stMensagem = "";

        if ( trim($_REQUEST["stParecerJuridico"]) == "" ) {
            $boErro = true;
            $stMensagem = "O campo Parecer Jurídico precisa ser preenchido!";
        }
        if ( count($arProcesso) < 2 ) {
            $boErro = true;
            $stMensagem = "O campo Processo precisa ser preenchido!";
        }

        if (!$boErro) {
            $obTLicitacaoAnulacaoImpugnacaoEdital = new TLicitacaoAnulacaoImpugnacaoEdital;
            $obTLicitacaoAnulacaoImpugnacaoEdital->setDado('num_edital'        ,$arEdital[0]                  );
            $obTLicitacaoAnulacaoImpugnacaoEdital->setDado('exercicio'         ,$arEdital[1]                  );
            $obTLicitacaoAnulacaoImpugnacaoEdital->setDado('cod_processo'      ,$arProcesso[0]                );
            $obTLicitacaoAnulacaoImpugnacaoEdital->setDado('exercicio_processo',$arProcesso[1]                );
            $obTLicitacaoAnulacaoImpugnacaoEdital->setDado('parecer_juridico'  ,$_REQUEST["stParecerJuridico"]);
            $obTLicitacaoAnulacaoImpugnacaoEdital->inclusao();
            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,$obTLicitacaoAnulacaoImpugnacaoEdital->getDado("cod_processo")."/".$obTLicitacaoAnulacaoImpugnacaoEdital->getDado("exercicio_processo"),"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso($stMensagem,"unica","erro");
        }
    break;
}
Sessao::encerraExcecao();

?>
