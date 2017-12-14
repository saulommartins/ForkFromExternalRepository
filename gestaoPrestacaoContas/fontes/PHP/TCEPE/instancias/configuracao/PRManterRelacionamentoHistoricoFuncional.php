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
     * 
    * Data de Criação   : 26/09/2014

    * @author Analista:
    * @author Desenvolvedor:  Lisiane Morais
    * @ignore

    $Id: PRManterRelacionamentoHistoricoFuncional.php 60247 2014-10-08 17:06:26Z michel $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalTCEPEConfiguracaoRelacionaHistorico.class.php" );

$stPrograma = "ManterRelacionamentoHistoricoFuncional";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);

$arLista = Sessao::read('arLista');

$obTPessoalTCEPEConfiguracaoRelacionaHistorico = new TPessoalTCEPEConfiguracaoRelacionaHistorico();
$obErro = new Erro;

if (empty($arLista) || (count($arLista) == 0)) {
    SistemaLegado::alertaAviso($pgForm, "Você deve inserir ao menos um relacionamento!");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

foreach ($arLista as $value){
    $obTPessoalTCEPEConfiguracaoRelacionaHistorico->setDado('cod_assentamento'      , $value['inCodAssentamento']);
    $obTPessoalTCEPEConfiguracaoRelacionaHistorico->setDado('timestamp'             , $value['timestampAssentamento']);
    $obTPessoalTCEPEConfiguracaoRelacionaHistorico->setDado('cod_tipo_movimentacao' , $value['inCodMovimentacao']);
    $obTPessoalTCEPEConfiguracaoRelacionaHistorico->setDado('cod_entidade'          , $value['inCodEntidade']);
    $obTPessoalTCEPEConfiguracaoRelacionaHistorico->setDado('exercicio'             , Sessao::getExercicio());
    
    $obTPessoalTCEPEConfiguracaoRelacionaHistorico->recuperaRelacionamento($rsRecordSet, " WHERE cod_assentamento = ".$value['inCodAssentamento']."
                                                                                             AND cod_tipo_movimentacao = ".$value['inCodMovimentacao']."
                                                                                             AND exercicio = '".Sessao::getExercicio()."'");
    
    if ($value['excluido'] == 'n'){
        if ($rsRecordSet->getNumLinhas() < 0){
            $obErro = $obTPessoalTCEPEConfiguracaoRelacionaHistorico->inclusao();
        }
    } else {
        $obErro = $obTPessoalTCEPEConfiguracaoRelacionaHistorico->exclusao();
    }
}

Sessao::encerraExcecao();

//Esvaziando array da lista
Sessao::remove('arLista');

if (!$obErro->ocorreu()){
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
} else {
    SistemaLegado::alertaAviso($pgFilt, $obErro->getDescricao(),"form","erro", Sessao::getId(), "../");
}

?>