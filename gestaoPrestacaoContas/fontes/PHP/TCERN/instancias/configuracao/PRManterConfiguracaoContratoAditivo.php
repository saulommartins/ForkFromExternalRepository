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
    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNConvenio.class.php");
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNContratoAditivo.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoContratoAditivo";
$pgFilt = "FL".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCtrl   = $_REQUEST['stCtrl'];
$convenio = explode('§', $_REQUEST['stNumConvenio']);

$obTTCERNContratoAditivo = new TTCERNContratoAditivo;
$obTTCERNContratoAditivo->setDado('num_convenio'         , $convenio[0]);
$obTTCERNContratoAditivo->setDado('cod_entidade'         , $convenio[1]);
$obTTCERNContratoAditivo->setDado('exercicio'            , $convenio[2]);
$obTTCERNContratoAditivo->setDado('num_contrato_aditivo' , $_REQUEST['inNumContratoAditivo']);
$obTTCERNContratoAditivo->setDado('exercicio_aditivo'    , Sessao::getExercicio());
$obTTCERNContratoAditivo->recuperaAditivo($rsContratoAditivo);

//if ($rsContratoAditivo->getNumLinhas() > 0 && $_REQUEST['stAcao'] == 'incluir') {
//    SistemaLegado::exibeAviso("Já existe um aditivo cadastrado com este mesmo número","n_erro","erro");
//    die;
//}

if ($rsContratoAditivo->getNumLinhas() > 0 && $_REQUEST['stAcao'] == 'incluir' ) {
    $arContratos=$rsContratoAditivo->getElementos();
    foreach ($arContratos as $arContratoAditivo) {
        if ($arContratoAditivo[num_contrato_aditivo] == $_REQUEST['inNumContratoAditivo']) {
             SistemaLegado::exibeAviso("Já existe um aditivo cadastrado com este mesmo número","n_erro","erro");
             die;
        }
    }
}

$stProcesso = explode ("/",$_REQUEST['stChaveProcesso']);

$obTTCERNContratoAditivo = new TTCERNContratoAditivo;
$obTTCERNContratoAditivo->setDado('num_convenio'        , $convenio[0]);
$obTTCERNContratoAditivo->setDado('cod_entidade'        , $convenio[1]);
$obTTCERNContratoAditivo->setDado('exercicio'           , $convenio[2]);
$obTTCERNContratoAditivo->setDado('num_contrato_aditivo', $_REQUEST['inNumContratoAditivo']);
$obTTCERNContratoAditivo->setDado('exercicio_aditivo'   , Sessao::getExercicio());
$obTTCERNContratoAditivo->setDado('cod_processo'        , $stProcesso[0]);
$obTTCERNContratoAditivo->setDado('exercicio_processo'  , $stProcesso[1]);
$obTTCERNContratoAditivo->setDado('bimestre'            , $_REQUEST['inBimestre']);
$obTTCERNContratoAditivo->setDado('cod_objeto'          , $_REQUEST['inObjeto']);
$obTTCERNContratoAditivo->setDado('valor_aditivo'       , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlAditivo'])));
$obTTCERNContratoAditivo->setDado('dt_inicio_vigencia'  , $_REQUEST['dtInicioVigencia']);
$obTTCERNContratoAditivo->setDado('dt_termino_vigencia' , $_REQUEST['dtTerminoVigencia']);
$obTTCERNContratoAditivo->setDado('dt_assinatura'       , $_REQUEST['dtAssinatura']);
$obTTCERNContratoAditivo->setDado('dt_publicacao'       , $_REQUEST['dtPublicacao']);

if ($_REQUEST['stAcao'] == 'incluir') {
    $obTTCERNContratoAditivo->inclusao();
    SistemaLegado::exibeAviso("Aditivo incluido com sucesso","incluir","incluir_n");
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
} else {
    $obTTCERNContratoAditivo->alteracao();
    SistemaLegado::exibeAviso("Aditivo alterado com sucesso","incluir","n_alterar");
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
}
