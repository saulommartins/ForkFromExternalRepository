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
    * Arquivo de Processamento do Formulario Contratos TCEMG
    * Data de Criação   : 04/05/2016

    * @author Analista: Ane Caroline
    * @author Desenvolvedor: Lisiane da Rosa Morais

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: PRManterConfiguracaoRgfRreo.php 65345 2016-05-13 18:07:34Z michel $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO."TTCEALPublicacaoRGF.class.php";
include_once CAM_GPC_TCEAL_MAPEAMENTO."TTCEALPublicacaoRREO.class.php";

$stPrograma = "ManterConfiguracaoRgfRreo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$arValoresRGF = Sessao::read('arValoresRGF');
$arValoresRREO = Sessao::read('arValoresRREO');

Sessao::setTrataExcecao ( true );

$obErro = new Erro();
if (count($arValoresRGF) <= 0) {
    $obErro->setDescricao('Nenhum Veículos de Publicação RGF incluso na lista!');
}

if (count($arValoresRREO) <= 0) {
    $obErro->setDescricao('Nenhum Veículos de Publicação RREO incluso na lista!');
}

if ( !$obErro->ocorreu() ){
    //Inclusao de RREO 
    $obTTCEALPublicacaoRGF = new TTCEALPublicacaoRGF;
    $obTTCEALPublicacaoRGF->setDado('exercicio'    , Sessao::getExercicio());
    $obTTCEALPublicacaoRGF->setDado('cod_entidade' , $request->get('cod_entidade'));
    $obTTCEALPublicacaoRGF->recuperaPorChave($rsRecordSet,'','',$boTransacao);

    if($rsRecordSet->getNumLinhas() > 0)
        $obTTCEALPublicacaoRGF->exclusao($boTransacao);

    foreach ( $arValoresRGF AS $arValores ) {
        $obTTCEALPublicacaoRGF->setDado('exercicio'      , Sessao::getExercicio());
        $obTTCEALPublicacaoRGF->setDado('cod_entidade'   , $request->get('cod_entidade'));
        $obTTCEALPublicacaoRGF->setDado('numcgm'         , $arValores['inVeiculo']);
        $obTTCEALPublicacaoRGF->setDado('dt_publicacao'  , $arValores['dtDataPublicacao']);
        $obTTCEALPublicacaoRGF->setDado('observacao'     , $arValores['stObservacao']);
        $obTTCEALPublicacaoRGF->setDado('num_publicacao' , $arValores['inNumPublicacao']);
        $obTTCEALPublicacaoRGF->setDado('tipo_apuracao'  , $tipoApuracao);

        $obTTCEALPublicacaoRGF->inclusao($boTransacao);
    }

    //Inclusao de RREO
    $obTTCEALPublicacaoRREO = new TTCEALPublicacaoRREO;
    $obTTCEALPublicacaoRREO->setDado('exercicio'    , Sessao::getExercicio());
    $obTTCEALPublicacaoRREO->setDado('cod_entidade' , $request->get('cod_entidade'));
    $obTTCEALPublicacaoRREO->recuperaPorChave($rsRecordSet,'','',$boTransacao);

    if($rsRecordSet->getNumLinhas() > 0)
        $obTTCEALPublicacaoRREO->exclusao($boTransacao);

    foreach ( $arValoresRREO AS $arValores ) {
        $obTTCEALPublicacaoRREO = new TTCEALPublicacaoRREO;
        $obTTCEALPublicacaoRREO->setDado('exercicio'      , Sessao::getExercicio());
        $obTTCEALPublicacaoRREO->setDado('cod_entidade'   , $request->get('cod_entidade'));
        $obTTCEALPublicacaoRREO->setDado('numcgm'         , $arValores['inVeiculo']);
        $obTTCEALPublicacaoRREO->setDado('dt_publicacao'  , $arValores['dtDataPublicacao']);
        $obTTCEALPublicacaoRREO->setDado('observacao'     , $arValores['stObservacao']);
        $obTTCEALPublicacaoRREO->setDado('num_publicacao' , $arValores['inNumPublicacao']);

        $obTTCEALPublicacaoRREO->inclusao($boTransacao);
    }
}

if( !$obErro->ocorreu() ){
    Sessao::write   ( 'arValoresRGF', ''  );
    Sessao::remove  ( 'arValoresRGF'      );
    Sessao::write   ( 'arValoresRREO', '' );
    Sessao::remove  ( 'arValoresRREO'     );
    sistemaLegado::alertaAviso($pgFilt."?stAcao=".'Incluir',$request->get('cod_entidade').'/'.Sessao::getExercicio() ,"incluir","aviso", Sessao::getId(), "../");
}else{
    sistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"n_incluir","erro" );
}

Sessao::encerraExcecao();

?>