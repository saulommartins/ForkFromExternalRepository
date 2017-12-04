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
    * Página de Processamento de Previsão Despesa Orcamento
    * Data de Criação   : 11/08/2004

    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @ignore

    $Revision: 32722 $
    $Name$
    $Autor: $
    $Date: 2007-01-30 09:44:20 -0200 (Ter, 30 Jan 2007) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.6  2007/01/30 11:44:20  luciano
#7317#

Revision 1.5  2006/07/05 20:43:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoDespesa.class.php"  );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoOrcamentaria.class.php"      );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                   );
include_once ( CAM_FW_HTML."MontaOrgaoUnidade.class.php"      );

//Define o nome dos arquivos PHP
$stPrograma = "MetasDespesa";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

$obRPrevisaoDespesa                = new ROrcamentoPrevisaoDespesa;
$obRConfiguracaoOrcamento          = new ROrcamentoConfiguracao;
$obROrcamentoPrevisaoOrcamentaria  = new ROrcamentoPrevisaoOrcamentaria;
$obROrcamentoDespesa               = new ROrcamentoDespesa;
$obMontaOrgaoUnidade               = new MontaOrgaoUnidade;
$obTransacao                       = new Transacao();
$obErro                            = new Erro;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "buscaOrgaoUnidade":
        $obMontaOrgaoUnidade->buscaValoresUnidade();
    break;

    case "preencheUnidade":
        $obMontaOrgaoUnidade->preencheUnidade();
    break;

    case "preencheMascara":
        $obMontaOrgaoUnidade->preencheMascara();
    break;

    case "alterar":
        $obRPrevisaoDespesa->setQtdColunas ( $_POST['inQtdCol'] );
        $obRPrevisaoDespesa->setQtdLinhas  ( $_POST['inQtdLin'] );
        $obRPrevisaoDespesa->setExercicio  ( Sessao::getExercicio() );

        $obRPrevisaoDespesa->obROrcamentoPrevisaoOrcamentaria->setExercicio( $obRPrevisaoDespesa->getExercicio() );
        if ( $obRPrevisaoDespesa->getExercicio() != $obRPrevisaoDespesa->obROrcamentoPrevisaoOrcamentaria->getExercicio() ) {
            $obRPrevisaoDespesa->obROrcamentoPrevisaoOrcamentaria->setExercicio( $obRPrevisaoDespesa->getExercicio() );
            $obRPrevisaoDespesa->obROrcamentoPrevisaoOrcamentaria->salvar($boTransacao);
        }

        $arID = explode(":", $_POST['stCodDespesa']);
        $arValorFuncaoCol = explode(":", $_POST['stFuncaoValorTotal']);

        $arTotal = array();
        for ($inContLinhas = 0; $inContLinhas < $_POST['inQtdLin']; $inContLinhas++) {
            for ($inContColunas = 0; $inContColunas < $_POST['inQtdCol']; $inContColunas++) {
                $inValor = $_POST["inCelula_".$arID[$inContLinhas]."_".$inContColunas."_".$inContLinhas];
               
                    SistemaLegado::executaFrameOculto( " if ( window.parent.frames['telaPrincipal'].document.getElementsByName('inFuncaoTotal_".$arID[$inContLinhas]."_".$inContLinhas."')[0].value != '0,00'
                                                              && window.parent.frames['telaPrincipal'].document.getElementsByName('inCelula_".$arID[$inContLinhas]."_".$inContColunas."_".$inContLinhas."')[0].value == '0,00' ){
                                                                
                                                                window.parent.frames['telaPrincipal'].document.getElementsByName('inCelula_".$arID[$inContLinhas]."_".$inContColunas."_".$inContLinhas."')[0].disabled = false;
                                                                
                                                              }
                                                    " );                    
                                
                $inValor = str_replace( ".", "", $inValor );
                $inValor = str_replace( ",", ".", $inValor );
                $arTotal[ $inContLinhas ] = number_format(($arTotal[ $inContLinhas ] + $inValor),2,'.','');
            }
        }

        $boSalvar = 0;
        for ( $inKey = 0; $inKey < count($arTotal); $inKey++) {
            if ($arTotal[ $inKey ] != '0,00') {
                if ($arTotal[ $inKey ] > $arValorFuncaoCol[ $inKey ] || $arTotal[ $inKey ] < $arValorFuncaoCol[ $inKey ]) {
                    $obErro->setDescricao( "Total da despesa ".$arID[ $inKey ]." diferente do valor da dotação orçamentária." );
                    $boSalvar++;
                    break;
                }
            }
        }

        if ($boSalvar == 0) {
            if ( count($arID) ) {
                for ( $inContLinhas = 0; $inContLinhas < count($arID); $inContLinhas++) {
                    $obRPrevisaoDespesa->setCodigoDespesa   ( $arID[$inContLinhas] );
                    $obErro = $obRPrevisaoDespesa->limparDados($boTransacao);
                }
            }
            $boFlagTransacao = false;
            $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao ); 
            if ( !$obErro->ocorreu() ) {
                for ($inContLinhas = 0; $inContLinhas < $_POST['inQtdLin']; $inContLinhas++) {
                    for ($inContColunas = 0; $inContColunas < $_POST['inQtdCol']; $inContColunas++) {
                        $obRPrevisaoDespesa->setCodigoDespesa   ( $arID[$inContLinhas] );
                        $obRPrevisaoDespesa->setPeriodo         ( $inContColunas + 1 );
                        $inValor = $_POST["inCelula_".$arID[$inContLinhas]."_".$inContColunas."_".$inContLinhas];
                        if ($inValor == "") {
                            $obRPrevisaoDespesa->setValorPrevisto ( 0 );
                        } else {
                            $obRPrevisaoDespesa->setValorPrevisto ( $inValor );
                        }
                        $obErro = $obRPrevisaoDespesa->salvar($boTransacao);
                    }
                }
                $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRPrevisaoDespesa );
            }
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Configuração realizada com sucesso.", "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::LiberaFrames(true,true);
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
}
?>
