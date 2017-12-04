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
/*
 * Página de processamento dos Vínculo das Contas Bancárias com a Fonte Pagadora
 * Data de Criação   : 18/02/2009

 * @author Analista      Tonismar Regis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTPB."TTPBRelacaoContaBancariaFontePagadora.class.php");
include_once CAM_GPC_TPB_MAPEAMENTO.'TTPBPlanoConta.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterFontePagadoraContaBancaria";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );
$obMapeamento = new TTPBRelacaoContaBancariaFontePagadora();
Sessao::getTransacao()->setMapeamento( $obMapeamento );
$stAcao = $request->get('stAcao');

switch ($_REQUEST['stAcao']) {
    default:
        $arContasSessao = Sessao::read('arContas');
        $inCodTipo=$arContasSessao[0]['cod_tipo'];

        $obTTPBRelacaoContaBancariaFontePagadora = new TTPBRelacaoContaBancariaFontePagadora();
        $obTTPBRelacaoContaBancariaFontePagadora->setDado('exercicio',Sessao::getExercicio()."::varchar");
        $obTTPBRelacaoContaBancariaFontePagadora->setDado('cod_tipo',$inCodTipo);
        $obTTPBRelacaoContaBancariaFontePagadora->recuperaTodosFiltrado($rsContasBancariasFontePagadora);

        while (!$rsContasBancariasFontePagadora->eof()) {
           $obTTPBRelacaoContaBancariaFontePagadora->setDado('exercicio',$rsContasBancariasFontePagadora->getCampo("exercicio"));
           $obTTPBRelacaoContaBancariaFontePagadora->setDado('cod_tipo',$rsContasBancariasFontePagadora->getCampo("cod_tipo"));
           $obTTPBRelacaoContaBancariaFontePagadora->exclusao();
           $rsContasBancariasFontePagadora->proximo();
        }

        foreach ($arContasSessao as $arContasTmp) {
            foreach ($arContasTmp["Conta"] as $arConta) {
                $obMapeamento->setDado('cod_banco',$arConta["cod_banco"]);
                $obMapeamento->setDado('cod_agencia',$arConta["cod_agencia"]);
                $obMapeamento->setDado('cod_conta_corrente',$arConta["cod_conta_corrente"]);
                $obMapeamento->setDado('cod_tipo',$arContasTmp["cod_tipo"]);
                $obMapeamento->setDado('exercicio',Sessao::getExercicio());
                $obMapeamento->inclusao();
            }
        }
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
    break;
}

Sessao::encerraExcecao();
?>
