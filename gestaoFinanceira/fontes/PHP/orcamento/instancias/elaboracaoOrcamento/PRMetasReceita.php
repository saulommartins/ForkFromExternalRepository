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
    * Página de Processamento de Previsão Receita Orcamento
    * Data de Criação   : 06/08/2004

    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @ignore

    $Revision: 32754 $
    $Name$
    $Autor: $
    $Date: 2007-01-30 16:45:09 -0200 (Ter, 30 Jan 2007) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.7  2007/01/30 18:45:09  luciano
#7316#

Revision 1.6  2006/07/05 20:43:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoReceita.class.php"   );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoOrcamentaria.class.php"       );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"                    );

//Define o nome dos arquivos PHP
$stPrograma = "MetasReceita";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

ini_set('max_input_vars', '10000');

$obRPrevisaoReceita                 = new ROrcamentoPrevisaoReceita;
$obROrcamentoPrevisaoOrcamentaria   = new ROrcamentoPrevisaoOrcamentaria;
$obRConfiguracaoOrcamento           = new ROrcamentoConfiguracao;
$obROrcamentoReceita                = new ROrcamentoReceita;
$obTransacao                        = new Transacao();
$obErro                             = new Erro;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "alterar":
         //funcao para comparar numeros float
        define("EPSILON", 1e-5); 
        function floatcmp($a, $b) {
            $diff = abs($a-$b);
            if ($diff < EPSILON) {
                return 0;
            }
            return ($a<$b) ? -1 : 1;
        }
        
        $obRPrevisaoReceita->setQtdColunas ( $_POST['inQtdCol'] );
        $obRPrevisaoReceita->setQtdLinhas  ( $_POST['inQtdLin'] );
        $obRPrevisaoReceita->setExercicio  ( Sessao::getExercicio() );

        $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->setExercicio( $obRPrevisaoReceita->getExercicio() );
        $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->consultar( $rsPrevisaoOrcamentaria, $boTransacao );

        if ( $obRPrevisaoReceita->getExercicio() != $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->getExercicio() ) {
            $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->setExercicio( $obRPrevisaoReceita->getExercicio() );
            $obRPrevisaoReceita->obROrcamentoPrevisaoOrcamentaria->salvar($boTransacao);
        }

        $stFiltro = '&stCodReceita='.$_POST['stCodReceita'].'&inCodEntidade='.$_POST['inCodEntidade'];

        $arID = explode(":", $_POST['stCodReceita']);
        $arValorFuncaoCol = explode(":", $_POST['stFuncaoValorTotal']);

        $arTotal = array();
        for ($inContLinhas = 0; $inContLinhas < $_POST['inQtdLin']; $inContLinhas++) {
            for ($inContColunas = 0; $inContColunas < $_POST['inQtdCol']; $inContColunas++) {
                $inValor = $_REQUEST["inCelula_".$arID[$inContLinhas]."_".$inContColunas."_".$inContLinhas];
                $inValor = str_replace( ".", "", $inValor );
                $inValor = str_replace( ",", ".", $inValor );
                $arTotal[ $inContLinhas ] = $arTotal[ $inContLinhas ] + $inValor;
            }
        }

        $boSalvar = 0;
         for ( $inKey = 0; $inKey < count($arTotal); $inKey++) {
            if ($arTotal[ $inKey ] != '0,00') {
                $arValorFuncaoCol[ $inKey ] = (float)$arValorFuncaoCol[ $inKey ];
                
                if(floatcmp($arTotal[ $inKey ], $arValorFuncaoCol[ $inKey ]) == 1){                
                    $obErro->setDescricao( "Total da receita ".$arID[ $inKey ]." não deve ser maior que o valor orçado." );
                    $boSalvar++;
                    SistemaLegado::LiberaFrames(true,false);
                    break;
                }
            }
        }

        if ($boSalvar == 0) {
            if ( count($arID) ) {
                for ( $inContLinhas = 0; $inContLinhas < count($arID); $inContLinhas++) {
                    $obRPrevisaoReceita->setCodigoReceita   ( $arID[$inContLinhas] );
                    $obErro = $obRPrevisaoReceita->limparDados($boTransacao);
                }
            }

            $boFlagTransacao = false;
            $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao ); 
            for ($inContLinhas = 0; $inContLinhas < $_POST['inQtdLin']; $inContLinhas++) {
                for ($inContColunas = 0; $inContColunas < $_POST['inQtdCol']; $inContColunas++) {
                    $obRPrevisaoReceita->setCodigoReceita   ( $arID[$inContLinhas] );
                    $obRPrevisaoReceita->setPeriodo         ( $inContColunas + 1 );
                    $inValor = $_REQUEST["inCelula_".$arID[$inContLinhas]."_".$inContColunas."_".$inContLinhas];                    
                    if ($inValor == "") {
                        $obRPrevisaoReceita->setValorPeriodo ( 0 );
                    } else {
                        $valor = str_replace('.','',$inValor);
                        $valor = str_replace(',','.',$valor);
                        $obRPrevisaoReceita->setValorPeriodo ( $valor );
                    }
                    $obErro = $obRPrevisaoReceita->salvar($boTransacao);
                    if ( $obErro->ocorreu() ) {
                        break 2;
                    }
                }
            }
            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRPrevisaoReceita );
        }
                
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId().$stFiltro, "Configuração realizada com sucesso.", "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

}
?>
