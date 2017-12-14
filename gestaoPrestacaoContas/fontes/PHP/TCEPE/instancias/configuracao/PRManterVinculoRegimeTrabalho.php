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
    * Titulo do arquivo : Arquivo de processamento do vinculo do tipo de regime de trabalho
    * Data de Criação: 17/07/2009

    * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalVinculoRegimeSubdivisao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ManterVinculoRegimeTrabalho';
$pgFilt    = 'FL'.$stPrograma.'.php';
$pgList    = 'LS'.$stPrograma.'.php';
$pgForm    = 'FM'.$stPrograma.'.php';
$pgProc    = 'PR'.$stPrograma.'.php';
$pgOcul    = 'OC'.$stPrograma.'.php';

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao(true);

switch ($stAcao) {
case 'configurar' :
    $obErro = new Erro;

    $obTPessoalVinculoRegimeSubdivisao = new TPessoalVinculoRegimeSubdivisao();
    $obTPessoalVinculoRegimeSubdivisao->setDado('exercicio', Sessao::getExercicio());
    $obErro = $obTPessoalVinculoRegimeSubdivisao->exclusao($boTransacao);

    if (!$obErro->ocorreu()) {

        foreach ($_REQUEST as $stKey => $stValue) {
            if (strpos($stKey,'cmbCargo') !== false && $stValue != '') {

                $arVinculo = explode('_', $stKey);

                if (empty($_REQUEST['cmbVinculo_'.$arVinculo[1].'_'.$arVinculo[1]])) {
                    $obErro->setDescricao('É necessário informar todos os campos na tela');
                    break;
                } else {
                    $inCodTipoVinculo = $_REQUEST['cmbVinculo_'.$arVinculo[1].'_'.$arVinculo[1]];
                }

                $obTPessoalVinculoRegimeSubdivisao->setDado('exercicio'        , Sessao::getExercicio());
                $obTPessoalVinculoRegimeSubdivisao->setDado('cod_sub_divisao'  , $arVinculo[1]);
                $obTPessoalVinculoRegimeSubdivisao->setDado('cod_tipo_regime'  , $stValue);
                $obTPessoalVinculoRegimeSubdivisao->setDado('cod_tipo_vinculo' , $inCodTipoVinculo);
                $obErro = $obTPessoalVinculoRegimeSubdivisao->inclusao($boTransacao);

                if ($obErro->ocorreu()) {
                    break;
                }
            }
        }
    }

    Sessao::encerraExcecao();

    if (!$obErro->ocorreu) {
        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso($obErro->getDescricao() ,"n_incluir","erro");
    }
}

