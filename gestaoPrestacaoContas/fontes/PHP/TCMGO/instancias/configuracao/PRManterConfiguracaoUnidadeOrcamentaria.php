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
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOUnidadeResponsavel.class.php" );
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOContadorTerceirizado.class.php" );
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOJuridicoTerceirizado.class.php" );
include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php" );

if ($_REQUEST['inOrgao'] == '') {
    SistemaLegado::exibeAviso("Campo Orgão inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

if ($_REQUEST['inUnidade'] == '') {
    SistemaLegado::exibeAviso("Campo Unidade inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

// GESTOR

if ($_REQUEST['inCGMGestor'] == '') {
    SistemaLegado::exibeAviso("Campo Gestor inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

if ($_REQUEST['dtInicioGestor'] == '') {
    SistemaLegado::exibeAviso("Campo Data início (Gestor) inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

if ($_REQUEST['dtFimGestor'] != '') {
    if (SistemaLegado::comparaDatas($_REQUEST['dtInicioGestor'], $_REQUEST['dtFimGestor'], true)) {
        SistemaLegado::exibeAviso("A Data de término não pode ser maior, nem igual a Data de início (Gestor) inválido!","n_incluir","erro");
        SistemaLegado::LiberaFrames(true,False);
        die;
    }
}

if ($_REQUEST['inTipoResponsavelGestor'] == '') {
    SistemaLegado::exibeAviso("Campo Tipo responsável inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

// CONTADOR

if ($_REQUEST['inCGMContador'] == '') {
    SistemaLegado::exibeAviso("Campo Contador inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

if ($_REQUEST['dtInicioContador'] == '') {
    SistemaLegado::exibeAviso("Campo Data início (Contador) inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

if ($_REQUEST['dtFimContador'] != '') {
    if (SistemaLegado::comparaDatas($_REQUEST['dtInicioContador'], $_REQUEST['dtFimContador'], true)) {
        SistemaLegado::exibeAviso("A Data de término não pode ser maior, nem igual a Data de início (Contador) inválido!","n_incluir","erro");
        SistemaLegado::LiberaFrames(true,False);
        die;
    }
}

// CONTROLE INTERNO

if ($_REQUEST['inCGMControleInterno'] == '') {
    SistemaLegado::exibeAviso("Campo Controle interno inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

if ($_REQUEST['dtInicioControleInterno'] == '') {
    SistemaLegado::exibeAviso("Campo Data início (Controle interno) inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

if ($_REQUEST['dtFimControleInterno'] != '') {
    if (SistemaLegado::comparaDatas($_REQUEST['dtInicioControleInterno'], $_REQUEST['dtFimControleInterno'], true)) {
        SistemaLegado::exibeAviso("A Data de término não pode ser maior, nem igual a Data de início (Controle interno) inválido!","n_incluir","erro");
        SistemaLegado::LiberaFrames(true,False);
        die;
    }
}

// JURÍDICO

if ($_REQUEST['inCGMJuridico'] == '') {
    SistemaLegado::exibeAviso("Campo Jurídico inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

if ($_REQUEST['dtInicioJuridico'] == '') {
    SistemaLegado::exibeAviso("Campo Data início (Jurídico) inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

//if ($_REQUEST['dtFimJuridico'] == '') {
//    SistemaLegado::exibeAviso("Campo Data término (Jurídico) inválido!","n_incluir","erro");
//    SistemaLegado::LiberaFrames(true,False);
//    die;
//}

if ($_REQUEST['dtFimJuridico'] != '') {
    if (SistemaLegado::comparaDatas($_REQUEST['dtInicioJuridico'], $_REQUEST['dtFimJuridico'], true)) {
        SistemaLegado::exibeAviso("A Data de término não pode ser maior, nem igual a Data de início (Jurídico) inválido!","n_incluir","erro");
        SistemaLegado::LiberaFrames(true,False);
        die;
    }
}

if ($_REQUEST['stOABJuridico'] == '') {
    $_REQUEST['stOABJuridico'] = null;
}

if (isset($_REQUEST['inCGMTerceirizadaContador']) && ($_REQUEST['inCGMTerceirizadaContador'] == '')) {
    SistemaLegado::exibeAviso("Campo CGM Terceirizada (Contador) inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

if (isset($_REQUEST['inCGMTerceirizadaJuridico']) && ($_REQUEST['inCGMTerceirizadaJuridico'] == '')) {
    SistemaLegado::exibeAviso("Campo CGM Terceirizada (Jurídico) inválido!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

//valida se a escolaridade do gestor está preenchida
$obTCGMPessoaFisica = new TCGMPessoaFisica;
$obTCGMPessoaFisica->recuperaTodos($rsPessoaFisiscaGestor, ' WHERE numcgm = '.$_REQUEST['inCGMGestor']);
if (!$rsPessoaFisiscaGestor->getCampo('cod_escolaridade')) {
    SistemaLegado::exibeAviso("Escolaridade do Gestor Responsável inválida(Necessário alterar o cadastro CGM informando a escolaridade)!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

//valida se a escolaridade do contador está preenchida
$obTCGMPessoaFisica = new TCGMPessoaFisica;
$obTCGMPessoaFisica->recuperaTodos($rsPessoaFisiscaContador, ' WHERE numcgm = '.$_REQUEST['inCGMContador']);
if (!$rsPessoaFisiscaContador->getCampo('cod_escolaridade')) {
    SistemaLegado::exibeAviso("Escolaridade do Contador Responsável inválida(Necessário alterar o cadastro CGM informando a escolaridade)!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

//valida se a escolaridade do responsavel pelo controle interno está preenchida
$obTCGMPessoaFisica = new TCGMPessoaFisica;
$obTCGMPessoaFisica->recuperaTodos($rsPessoaFisiscaContador, ' WHERE numcgm = '.$_REQUEST['inCGMControleInterno']);
if (!$rsPessoaFisiscaContador->getCampo('cod_escolaridade')) {
    SistemaLegado::exibeAviso("Escolaridade do Responsável pelo Controle Interno inválida(Necessário alterar o cadastro CGM informando a escolaridade)!","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

/*
 * INSERT NO BANCO DE DADOS
 */
$obTTCMGOUnidadeResponsavel = new TTCMGOUnidadeResponsavel();
$obTTCMGOUnidadeResponsavel->setDado('num_unidade' , $_REQUEST['inUnidade']);
$obTTCMGOUnidadeResponsavel->setDado('num_orgao'   , $_REQUEST['inOrgao']);
$obTTCMGOUnidadeResponsavel->setDado('exercicio'   , Sessao::getExercicio());
$obTTCMGOUnidadeResponsavel->setDado('timestamp'   , date('Y-m-d H:i:s'));
$obTTCMGOUnidadeResponsavel->setDado('cgm_gestor'       , $_REQUEST['inCGMGestor']);
$obTTCMGOUnidadeResponsavel->setDado('gestor_dt_inicio' , $_REQUEST['dtInicioGestor']);
$obTTCMGOUnidadeResponsavel->setDado('gestor_dt_fim'    , $_REQUEST['dtFimGestor']);
$obTTCMGOUnidadeResponsavel->setDado('tipo_responsavel' , $_REQUEST['inTipoResponsavelGestor']);
$obTTCMGOUnidadeResponsavel->setDado('gestor_cargo'     , $_REQUEST['stCargoGestor']);
$obTTCMGOUnidadeResponsavel->setDado('cgm_contador'        , $_REQUEST['inCGMContador']);
$obTTCMGOUnidadeResponsavel->setDado('contador_dt_inicio'  , $_REQUEST['dtInicioContador']);
$obTTCMGOUnidadeResponsavel->setDado('contador_dt_fim'     , $_REQUEST['dtFimContador']);
$obTTCMGOUnidadeResponsavel->setDado('contador_crc'        , $_REQUEST['stCRCContador']);
$obTTCMGOUnidadeResponsavel->setDado('uf_crc'              , $_REQUEST['inSiglaUFContador']);
$obTTCMGOUnidadeResponsavel->setDado('cod_provimento_contabil'    , $_REQUEST['inProvimentoContabil']);
$obTTCMGOUnidadeResponsavel->setDado('cgm_controle_interno'       , $_REQUEST['inCGMControleInterno']);
$obTTCMGOUnidadeResponsavel->setDado('controle_interno_dt_inicio' , $_REQUEST['dtInicioControleInterno']);
$obTTCMGOUnidadeResponsavel->setDado('controle_interno_dt_fim'    , $_REQUEST['dtFimControleInterno']);
$obTTCMGOUnidadeResponsavel->setDado('cgm_juridico'            , $_REQUEST['inCGMJuridico']);
$obTTCMGOUnidadeResponsavel->setDado('juridico_dt_inicio'      , $_REQUEST['dtInicioJuridico']);
$obTTCMGOUnidadeResponsavel->setDado('juridico_dt_fim'         , $_REQUEST['dtFimJuridico']);
$obTTCMGOUnidadeResponsavel->setDado('juridico_oab'            , $_REQUEST['stOABJuridico']);
$obTTCMGOUnidadeResponsavel->setDado('uf_oab'                  , $_REQUEST['inSiglaUFJuridico']);
$obTTCMGOUnidadeResponsavel->setDado('cod_provimento_juridico' , $_REQUEST['inProvimentoJuridico']);
$obTTCMGOUnidadeResponsavel->inclusao();

if (isset($_REQUEST['inCGMTerceirizadaContador']) && ($_REQUEST['inCGMTerceirizadaContador'] != '')) {
    $obTTCMGOContadorTerceirizado = new TTCMGOContadorTerceirizado();
    $obTTCMGOContadorTerceirizado->setDado('num_unidade' , $obTTCMGOUnidadeResponsavel->getDado('num_unidade'));
    $obTTCMGOContadorTerceirizado->setDado('num_orgao'   , $obTTCMGOUnidadeResponsavel->getDado('num_orgao'));
    $obTTCMGOContadorTerceirizado->setDado('exercicio'   , $obTTCMGOUnidadeResponsavel->getDado('exercicio'));
    $obTTCMGOContadorTerceirizado->setDado('numcgm'      , $_REQUEST['inCGMTerceirizadaContador']);
    $obTTCMGOContadorTerceirizado->setDado('timestamp'   , $obTTCMGOUnidadeResponsavel->getDado('timestamp'));
    $obTTCMGOContadorTerceirizado->inclusao();
}

if (isset($_REQUEST['inCGMTerceirizadaJuridico']) && ($_REQUEST['inCGMTerceirizadaJuridico'] != '')) {
    $obTTCMGOJuridicoTerceirizado = new TTCMGOJuridicoTerceirizado();
    $obTTCMGOJuridicoTerceirizado->setDado('num_unidade' , $obTTCMGOUnidadeResponsavel->getDado('num_unidade'));
    $obTTCMGOJuridicoTerceirizado->setDado('num_orgao'   , $obTTCMGOUnidadeResponsavel->getDado('num_orgao'));
    $obTTCMGOJuridicoTerceirizado->setDado('exercicio'   , $obTTCMGOUnidadeResponsavel->getDado('exercicio'));
    $obTTCMGOJuridicoTerceirizado->setDado('numcgm'      , $_REQUEST['inCGMTerceirizadaJuridico']);
    $obTTCMGOJuridicoTerceirizado->setDado('timestamp'   , $obTTCMGOUnidadeResponsavel->getDado('timestamp'));
    $obTTCMGOJuridicoTerceirizado->inclusao();
}

SistemaLegado::exibeAviso("Configuração salva","incluir","incluir_n");
