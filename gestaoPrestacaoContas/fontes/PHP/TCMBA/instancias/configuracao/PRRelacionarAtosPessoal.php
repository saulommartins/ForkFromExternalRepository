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
    * Data de Criação   : 21/09/2015

    * @author Analista:
    * @author Desenvolvedor:  Jean da Silva
    * @ignore

    $Id: PRManterRelacionamentoHistoricoFuncional.php 60247 2014-10-08 17:06:26Z michel $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TTPessoalTCMBAAssentamentoAtoPessoal.class.php" );

$stPrograma = "RelacionarAtosPessoal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);

$arLista = Sessao::read('arLista');

$obTTPessoalTCMBAAssentamentoAtoPessoal = new TTPessoalTCMBAAssentamentoAtoPessoal();

$obErro = new Erro;

if (empty($arLista) || (count($arLista) == 0)) {
    SistemaLegado::alertaAviso($pgForm, "Você deve inserir ao menos um relacionamento!");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

foreach ($arLista as $value){
    $obTTPessoalTCMBAAssentamentoAtoPessoal->setDado('cod_assentamento'     , $value['inCodAssentamento']);
    $obTTPessoalTCMBAAssentamentoAtoPessoal->setDado('cod_tipo_ato_pessoal' , $value['inCodTipoAto']);
    $obTTPessoalTCMBAAssentamentoAtoPessoal->setDado('exercicio'            , Sessao::getExercicio());
    $obTTPessoalTCMBAAssentamentoAtoPessoal->recuperaTodos ($rsRecordSet, " WHERE cod_assentamento = ".$value['inCodAssentamento']."
                                                                              AND cod_tipo_ato_pessoal = ".$value['inCodTipoAto']."
                                                                              AND exercicio = '".Sessao::getExercicio()."'");
    
    if ($value['excluido'] == 'n'){
        if ($rsRecordSet->getNumLinhas() < 0){
            $obErro = $obTTPessoalTCMBAAssentamentoAtoPessoal->inclusao();
        }
    } else {
        $obErro = $obTTPessoalTCMBAAssentamentoAtoPessoal->exclusao();
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