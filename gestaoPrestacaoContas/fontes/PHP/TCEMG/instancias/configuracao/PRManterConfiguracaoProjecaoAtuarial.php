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
 * @author Analista: Dagiane Vieira
 * @author Desenvolvedor: Michel Teixeira
 *
 * $Id: PRManterConfiguracaoProjecaoAtuarial.php 61820 2015-03-06 16:15:57Z michel $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGProjecaoAtuarial.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoProjecaoAtuarial";
$pgFilt = "FL".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obErro = new Erro;

$arExercicios = explode('_', $_REQUEST['stExercicios']);

if(count($arExercicios)==2){
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

    $inExercicioInicial = $arExercicios[0];
    $inExercicioFinal = $arExercicios[1];

    for ($i=$inExercicioInicial; $i<=$inExercicioFinal; $i++) {
        $obTTCEMGProjecaoAtuarial = new TTCEMGProjecaoAtuarial();
        $obTTCEMGProjecaoAtuarial->setDado('exercicio'          , $i                            );
        $obTTCEMGProjecaoAtuarial->setDado('exercicio_entidade' , Sessao::getExercicio()        );
        $obTTCEMGProjecaoAtuarial->setDado('cod_entidade'       , $_REQUEST['inCodEntidadeRPPS']);
        $obErro = $obTTCEMGProjecaoAtuarial->limpaProjecao($boTransacao);

        if (!$obErro->ocorreu()) {
            $vlPatronal = str_replace(',', '.', str_replace('.', '', $_REQUEST['vlPatronal_'.$i              ]));
            $vlReceita  = str_replace(',', '.', str_replace('.', '', $_REQUEST['vlReceitaPrevidenciaria_'.$i ]));
            $vlDespesa  = str_replace(',', '.', str_replace('.', '', $_REQUEST['vlDespesaPrevidenciaria_'.$i ]));
            $vlRPPS     = str_replace(',', '.', str_replace('.', '', $_REQUEST['vlRPPS_'.$i                  ]));

            if(!$obErro->ocorreu() && ($vlPatronal != '' || $vlReceita != ''|| $vlDespesa != ''|| $vlRPPS != '') ){
                $obTTCEMGProjecaoAtuarial = new TTCEMGProjecaoAtuarial();
                $obTTCEMGProjecaoAtuarial->setDado('exercicio'          , $i                                        );
                $obTTCEMGProjecaoAtuarial->setDado('cod_entidade'       , $_REQUEST['inCodEntidadeRPPS']            );
                $obTTCEMGProjecaoAtuarial->setDado('exercicio_entidade' , Sessao::getExercicio()                    );
                $obTTCEMGProjecaoAtuarial->setDado('vl_patronal'        , $vlPatronal != '' ? $vlPatronal   : 'NULL');
                $obTTCEMGProjecaoAtuarial->setDado('vl_receita'         , $vlReceita != ''  ? $vlReceita    : 'NULL');
                $obTTCEMGProjecaoAtuarial->setDado('vl_despesa'         , $vlDespesa != ''  ? $vlDespesa    : 'NULL');
                $obTTCEMGProjecaoAtuarial->setDado('vl_rpps'            , $vlRPPS   != ''   ? $vlRPPS       : 'NULL');
                $obErro = $obTTCEMGProjecaoAtuarial->inclusao($boTransacao);
            }
        }
    }
}

SistemaLegado::LiberaFrames();

if (!$obErro->ocorreu())
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(), " Configuração salva ", "incluir", "aviso", Sessao::getId(), "../");
else
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

$obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGProjecaoAtuarial);
