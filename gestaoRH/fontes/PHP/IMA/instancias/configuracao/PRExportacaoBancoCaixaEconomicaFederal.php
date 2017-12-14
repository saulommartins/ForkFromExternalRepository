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
    * Página de processamento do IMA Configuração - Caixa Economica Federal
    * Data de Criação: 09/10/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    * Casos de uso: uc-04.08.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioCaixaEconomicaFederal.class.php"                        );
include_once( CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php" 										 );

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoBancoCaixaEconomicaFederal";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get("stAcao");
$obErro = new Erro;
$obTransacao = new Transacao();

switch ($stAcao) {
    case "configurar":

        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if (!$obErro->ocorreu()) {
            $obTIMAConfiguracaoConvenioCaixaEconomicaFederal = new TIMAConfiguracaoConvenioCaixaEconomicaFederal;
            $obErro = $obTIMAConfiguracaoConvenioCaixaEconomicaFederal->recuperaRelacionamento($rsDados,"","",$boTransacao);

            if (!$obErro->ocorreu()) {

                if ($request->get('stNumAgenciaTxt')) {
                    $obTMONAgencia = new TMONAgencia;
                    $stFiltro = " WHERE num_agencia = '".$request->get('stNumAgenciaTxt')."'";
                    $obTMONAgencia->recuperaTodos($rsAgencia, $stFiltro);
                    $cod_agencia = $rsAgencia->getCampo('cod_agencia');
                }

                $obTIMAConfiguracaoConvenioCaixaEconomicaFederal->setDado("cod_convenio_banco", $request->get('stCodConvenio'));
                $obTIMAConfiguracaoConvenioCaixaEconomicaFederal->setDado("cod_banco", Sessao::read('BANCO'));
                $obTIMAConfiguracaoConvenioCaixaEconomicaFederal->setDado("cod_agencia", $cod_agencia );
                $obTIMAConfiguracaoConvenioCaixaEconomicaFederal->setDado("cod_conta_corrente", $request->get('inTxtContaCorrente'));
                $obTIMAConfiguracaoConvenioCaixaEconomicaFederal->setDado("cod_tipo", $request->get('inTipoConvenioLayout'));

                if ($rsDados->getNumLinhas() > 0) {
                    $obTIMAConfiguracaoConvenioCaixaEconomicaFederal->setDado("cod_convenio", $rsDados->getCampo('cod_convenio'));
                    $obErro = $obTIMAConfiguracaoConvenioCaixaEconomicaFederal->alteracao($boTransacao);
                } else {
                    $obErro = $obTIMAConfiguracaoConvenioCaixaEconomicaFederal->inclusao($boTransacao);
                }

                $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao,$obErro,$obTIMAConfiguracaoConvenioCaixaEconomicaFederal);
                if (!$obErro->ocorreu()) {
                    SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=".$stAcao,"Configuração da exportação bancária concluída com sucesso!","incluir","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
                }
            }
       }
    break;
}

?>