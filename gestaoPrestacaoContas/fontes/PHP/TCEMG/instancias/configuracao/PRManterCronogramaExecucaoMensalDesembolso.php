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
    * Titulo do arquivo : Arquivo de Cronograma de Execucao Mensal de Desembolso 
    * Data de Criação   : 29/02/2016

    * @author Analista      Ane Caroline
    * @author Desenvolvedor Lisiane Morais

    * @package URBEM
    * @subpackage

    * $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGCronogramaExecucaoMensalDesembolso.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterCronogramaExecucaoMensalDesembolso";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');
Sessao::setTrataExcecao( true );

switch ($stAcao) {
    case 'manter' :
            $TTCEMGCronogramaExecucaoMensalDesembolso = new TTCEMGCronogramaExecucaoMensalDesembolso();
            $somatorio = 0.00;
            $obErro = new Erro();
            
            //Soma os totais para fazer a verificacao com saldo inicial   
            foreach ($_REQUEST as $stKey=>$stValue) {
                 $arCodigo = explode('_',$stKey); 
                if ($arCodigo[0] == 'TotalValor') {
                    $vlSomatorio = str_replace(".","",$stValue);
                    $vlSomatorio = str_replace(",",".",$vlSomatorio);
                    $vlSomatorioGrupos = bcadd($vlSomatorioGrupos, $vlSomatorio, 2);
                }
            }
            
            if($vlSomatorioGrupos <= $request->get('hdnVlSaldoTotal')) {
                foreach ($_REQUEST as $stKey=>$stValue) {
                    $arCodigo = explode('_',$stKey); //Formato: mes_cod_grupo
                    if ($arCodigo[0] > 0 ) {
                        $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('cod_grupo'   , $arCodigo[1] );
                        $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('periodo'     , $arCodigo[0] );
                        $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('cod_entidade', $request->get('inCodEntidade') );
                        $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('exercicio'   , Sessao::getExercicio() );
                        $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('num_unidade' , $request->get('inCodUnidade') );
                        $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('num_orgao'   , $request->get('inCodOrgao') );
                        $TTCEMGCronogramaExecucaoMensalDesembolso->setDado('valor'       , $stValue );
                        $TTCEMGCronogramaExecucaoMensalDesembolso->recuperaPorChave($rsRecordSet);
                       
                        if ($rsRecordSet->eof()) {
                            $TTCEMGCronogramaExecucaoMensalDesembolso->inclusao($boTransacao); 
                        } elseif($stValue != NULL) {
                            $TTCEMGCronogramaExecucaoMensalDesembolso->alteracao($boTransacao); 
                        }
                    }
                }
                if (!$obErro->ocorreu()) {
                    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
                }
            }else {
                $obErro->setDescricao("Valores Totais Superior ao Saldo Diponível para esta Unidade.");
                SistemaLegado::alertaAviso("FMManterCronogramaExecucaoMensalDesembolso.php?'.Sessao::getId().&stAcao=$stAcao", $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
            }
    break;
}

Sessao::encerraExcecao();
