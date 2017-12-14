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
    * Titulo do arquivo : Arquivo de processamento do vinculo do tipo de cargo
    * Data de Criação   : 14/07/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalDeParaTipoCargo.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ManterTipoCargo';
$pgFilt    = 'FL'.$stPrograma.'.php';
$pgList    = 'LS'.$stPrograma.'.php';
$pgForm    = 'FM'.$stPrograma.'.php';
$pgProc    = 'PR'.$stPrograma.'.php';
$pgOcul    = 'OC'.$stPrograma.'.php';

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao( true );

switch ($stAcao) {
    case 'configurar' :
        $obTPessoalDeParaTipoCargo = new TPessoalDeParaTipoCargo();
        $obTPessoalDeParaTipoCargo->recuperaTodos($rsDeParaTipoCargo);

        while (!$rsDeParaTipoCargo->EOF()) {
            $obTPessoalDeParaTipoCargo->setDado('cod_sub_divisao', $rsDeParaTipoCargo->getCampo('cod_sub_divisao'));
            $obTPessoalDeParaTipoCargo->setDado('cod_tipo_cargo_tce', $rsDeParaTipoCargo->getCampo('cod_tipo_cargo_tce'));
            $obTPessoalDeParaTipoCargo->exclusao();
            $rsDeParaTipoCargo->proximo();
        }

        foreach ($_REQUEST as $stKey => $stValue) {
            if (strpos($stKey,'cmbCargo') !== false AND $stValue != '') {
                $arRetencao = explode('_',$stKey);

                $obTPessoalDeParaTipoCargo->setDado('cod_tipo_cargo_tce',$stValue);
                $obTPessoalDeParaTipoCargo->setDado('cod_sub_divisao',$arRetencao[1]);
                $obErro = $obTPessoalDeParaTipoCargo->inclusao();
            }
        }
        if (!$obErro->ocorreu) {
            SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso($obErro->getDescricao() ,"n_incluir","erro");
        }
}

Sessao::encerraExcecao();
