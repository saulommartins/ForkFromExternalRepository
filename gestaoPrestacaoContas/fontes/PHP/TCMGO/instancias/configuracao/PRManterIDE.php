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
    * PÃ¡gina de Processamento
    * Data de CriaÃ§Ã£o   : 25/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Id: PRManterOrgao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TTGO."TCMGOConfiguracaoIDE.class.php";
include_once TTGO."TTGOConfiguracaoEntidade.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterIDE";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );
$obErro = new Erro;

$obTTGOConfiguracaoEntidade = new TTGOConfiguracaoEntidade();
$obTTGOConfiguracaoEntidade->recuperaEntidadePrefeitura($rsEntidades, " AND ent.cod_entidade = ".$request->get('inCodEntidade')."","",$boTransacao);

if ($rsEntidades->getNumLinhas() > 0) {
    $obTCMGOConfiguracaoIDE = new TCMGOConfiguracaoIDE();
    $obTCMGOConfiguracaoIDE->setDado( 'cod_entidade', $request->get('inCodEntidade') );
    $obTCMGOConfiguracaoIDE->setDado( 'exercicio', Sessao::getExercicio() );
    $obTCMGOConfiguracaoIDE->setDado( 'cgm_chefe_governo', $request->get('inCGMChefeGoverno') );
    $obTCMGOConfiguracaoIDE->setDado( 'cgm_contador',$request->get('inCGMContador') );
    $obTCMGOConfiguracaoIDE->setDado( 'cgm_controle_interno', $request->get('inCGMControleInterno') );
    $obTCMGOConfiguracaoIDE->setDado( 'crc_contador', $request->get('inCRCContador') );
    $obTCMGOConfiguracaoIDE->setDado( 'uf_crc_contador',$request->get('inCodUf'));
    $obTCMGOConfiguracaoIDE->recuperaPorChave( $rsConfiguracao );

    if ( $rsConfiguracao->getNumLinhas() > 0 ) {
        $obErro = $obTCMGOConfiguracaoIDE->alteracao();
    } else {
        $obErro = $obTCMGOConfiguracaoIDE->inclusao();
    }
} else {
    $obErro->setDescricao("Configuração somente para a entidade Prefeitura! Se não existe, por favor, configure a entidade Prefeitura.");
}

 if ( !$obErro->ocorreu() ) {
        sistemaLegado::alertaAviso($pgForm."?stAcao=".$request->get('stAcao'), "TCMGO" ,"incluir","aviso", Sessao::getId(), "../");
    } else {
        sistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"n_incluir","erro" );
    }

Sessao::encerraExcecao();
?>
