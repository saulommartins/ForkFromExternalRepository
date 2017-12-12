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
    * Página de Processamento para Cancelar Abertura de Restos a Pagar
    * Data de Criação   : 20/01/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Processamento

    * @ignore

    $Id: PRCancelarAberturaRestosAPagar.php 62406 2015-05-05 14:43:16Z franver $

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeCancelarAberturaRestosAPagar.class.php"               );
include_once ( CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "CancelarAberturaRestosAPagar";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$rsLote = new recordSet();
$obErro = new Erro;

$obRContabilidadeCancelarAberturaRestosAPagar = new RContabilidadeCancelarAberturaRestosAPagar;

$stAcao = $request->get('stAcao');

switch ($stAcao) {
    case "excluir":
        $obTransacao      = new Transacao;
        $boFlagTransacao = false;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRContabilidadeCancelarAberturaRestosAPagar->consultarLote($rsLote, $boTransacao);
            if ( !$obErro->ocorreu() ) {
                for ($i=0; $i<$rsLote->inNumLinhas;$i++) {
                    $obRContabilidadeCancelarAberturaRestosAPagar->setExercicio     ( $rsLote->arElementos[$i]['exercicio']     );
                    $obRContabilidadeCancelarAberturaRestosAPagar->setCodEntidade   ( $rsLote->arElementos[$i]['cod_entidade']  );
                    $obRContabilidadeCancelarAberturaRestosAPagar->setCodLote       ( $rsLote->arElementos[$i]['cod_lote']      );
                    $obRContabilidadeCancelarAberturaRestosAPagar->setTipo          ( $rsLote->arElementos[$i]['tipo']          );

                    #CADEIA CANCELAR ABERTURA DE RESTOS A PAGAR
                    //excluir conta_credito
                    $obErro = $obRContabilidadeCancelarAberturaRestosAPagar->excluirLancamentoContaCredito($boTransacao);
                    if (!$obErro->ocorreu()) {

                        //excluir conta_debito
                        $obErro = $obRContabilidadeCancelarAberturaRestosAPagar->excluirLancamentoContaDebito($boTransacao);
                        if (!$obErro->ocorreu()) {

                            //excluir valor_lancamento
                            $obErro = $obRContabilidadeCancelarAberturaRestosAPagar->excluirValorLancamento($boTransacao);
                            if (!$obErro->ocorreu()) {

                                //excluir lancamento
                                $obErro = $obRContabilidadeCancelarAberturaRestosAPagar->excluirLancamento($boTransacao);
                                if (!$obErro->ocorreu()) {

                                    //excluir lote
                                    $obErro = $obRContabilidadeCancelarAberturaRestosAPagar->excluirLote($boTransacao);
                                    if ($obErro->ocorreu()) {
                                        break;
                                    }
                                } else {
                                    break;
                                }
                            } else {
                                break;
                            }
                        } else {
                            break;
                        }
                    } else {
                        break;
                    }
                }
                #FIM DA CADEIA
            }
            if (!$obErro->ocorreu()) {
                $obRConfiguracao = new RConfiguracaoConfiguracao;
                $obRConfiguracao->setParametro('abertura_RP');
                $obRConfiguracao->setExercicio( Sessao::getExercicio());
                $obRConfiguracao->setCodModulo( 9 );
                $obRConfiguracao->setValor( 'F' );
                $obErro = $obRConfiguracao->alterar($boTransacao);
            }
            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRContabilidadeCancelarAberturaRestosAPagar->obTContabilidadeLote );
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgFilt."?stAcao=incluir","Cancelar Geração de Abertura do Exercício - Restos a Pagar realizado.","pagar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        SistemaLegado::LiberaFrames(true,false);
    break;
}
?>
