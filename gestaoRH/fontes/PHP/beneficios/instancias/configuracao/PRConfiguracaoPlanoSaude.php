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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_BEN_MAPEAMENTO."TBeneficioLayoutFornecedor.class.php");

$stPrograma = "ConfiguracaoPlanoSaude";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);

$arLista = Sessao::read('arLista');
$obTBeneficioLayoutFornecedor = new TBeneficioLayoutFornecedor();
$obErro = new Erro;

if (empty($arLista) || (count($arLista) == 0)) {
    $obErro->setDescricao("Você deve inserir ao menos um vínculo do layout com fornecedor!");
    SistemaLegado::LiberaFrames(true,False);
}

if (!$obErro->ocorreu()){
    foreach ($arLista as $value){
        $obTBeneficioLayoutFornecedor->recuperaTodos($rsLayoutFornecedor, " WHERE cgm_fornecedor = ".$value['cgm_fornecedor']." AND cod_layout = ".$value['cod_layout']."");
        
        $obTBeneficioLayoutFornecedor->setDado('cgm_fornecedor', $value['cgm_fornecedor']);
        $obTBeneficioLayoutFornecedor->setDado('cod_layout'    , $value['cod_layout']);
        
        if ($value['excluido'] == 'n'){
            if ($rsLayoutFornecedor->getNumLinhas() < 0){
                $obErro = $obTBeneficioLayoutFornecedor->inclusao();
            }
        } else {
            $obErro = $obTBeneficioLayoutFornecedor->exclusao();
        }
    }
}

Sessao::encerraExcecao();

if (!$obErro->ocorreu()){
    sistemaLegado::alertaAviso($pgForm,"Layout Importado com sucesso!","importar","aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::alertaAviso($pgForm, $obErro->getDescricao(),"n_incluir","erro", Sessao::getId(), "../");
}

?>