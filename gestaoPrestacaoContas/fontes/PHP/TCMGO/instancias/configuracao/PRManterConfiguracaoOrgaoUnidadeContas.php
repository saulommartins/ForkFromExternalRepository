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
set_time_limit(0);
/**
    * Página de Formulario de Ajustes Gerais Exportacao - TCE-RS
    * Data de Criação   : 11/07/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-07-19 14:51:27 -0300 (Qua, 19 Jul 2006) $

    * Casos de uso: uc-02.08.15
*/

/*
$Log$
Revision 1.3  2006/07/19 17:51:27  cako
Bug #6013#

Revision 1.2  2006/07/19 17:49:53  cako
Bug #6013#

Revision 1.1  2006/07/17 14:30:48  cako
Bug #6013#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TGO_MAPEAMENTO."TTCMGOConfiguracaoOrgaoUnidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoOrgaoUnidadeContas";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obTTCMGOConfiguracaoOrgaoUnidade = new TTCMGOConfiguracaoOrgaoUnidade();

$obErro = new Erro;

$obTransacao = new Transacao;
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

if ($request->get('inCodExecutivo')) {
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("exercicio", Sessao::getExercicio() );
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("cod_entidade",SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE cod_modulo = 8 AND parametro = 'cod_entidade_prefeitura' AND exercicio = '".Sessao::getExercicio()."'"));
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("cod_poder", 1 );
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("num_orgao", substr($request->get('inCodExecutivo'),0,2) );
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("num_unidade", substr($request->get('inCodExecutivo'),2,2) );
    $obErro = $obTTCMGOConfiguracaoOrgaoUnidade->recuperaPorChave( $rsRecordSet, $boTransacao );

    $resultado = SistemaLegado::pegaValor("SELECT LPAD(num_orgao::VARCHAR,2,'0')||LPAD(num_unidade::VARCHAR,2,'0') AS resultado
                                             FROM orcamento.unidade
                                             WHERE num_orgao = ".substr($request->get('inCodExecutivo'),0,2)."
                                               AND num_unidade = ".substr($request->get('inCodExecutivo'),2,2)."
                                               AND exercicio = '".Sessao::getExercicio()."'"
                                        ,"resultado"
                                        );
    if ($resultado == '') {
        $obErro->setDescricao("Esse órgão e unidade não existem no urbem!");
    } else {
        if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
            $obErro = $obTTCMGOConfiguracaoOrgaoUnidade->alteracao( $boTransacao );
        } else {
            $obErro = $obTTCMGOConfiguracaoOrgaoUnidade->inclusao( $boTransacao );
        }
    }
}

if ($request->get('inCodLegislativo')) {
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("exercicio", Sessao::getExercicio() );
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("cod_entidade",SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE cod_modulo = 8 AND parametro = 'cod_entidade_camara' AND exercicio = '".Sessao::getExercicio()."'"));
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("cod_poder", 2 );
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("num_orgao", substr($request->get('inCodLegislativo'),0,2) );
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("num_unidade", substr($request->get('inCodLegislativo'),2,2) );
    $obErro = $obTTCMGOConfiguracaoOrgaoUnidade->recuperaPorChave( $rsRecordSet, $boTransacao );
    
    $resultado = SistemaLegado::pegaValor("SELECT LPAD(num_orgao::VARCHAR,2,'0')||LPAD(num_unidade::VARCHAR,2,'0') AS resultado
                                             FROM orcamento.unidade
                                             WHERE num_orgao = ".substr($request->get('inCodLegislativo'),0,2)."
                                               AND num_unidade = ".substr($request->get('inCodLegislativo'),2,2)."
                                               AND exercicio = '".Sessao::getExercicio()."'"
                                        ,"resultado"
                                        );
    if ($resultado == '') {
        $obErro->setDescricao("Esse órgão e unidade não existem no urbem!");
    } else {
        if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
            $obErro = $obTTCMGOConfiguracaoOrgaoUnidade->alteracao( $boTransacao );
        } else {
            $obErro = $obTTCMGOConfiguracaoOrgaoUnidade->inclusao( $boTransacao );
        }
    }
}

if ($request->get('inCodRPPS')) {
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("exercicio", Sessao::getExercicio() );
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("cod_entidade",SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE cod_modulo = 8 AND parametro = 'cod_entidade_rpps' AND exercicio = '".Sessao::getExercicio()."'"));
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("cod_poder", 3 );
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("num_orgao", substr($request->get('inCodRPPS'),0,2) );
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("num_unidade", substr($request->get('inCodRPPS'),2,2) );
    $obErro = $obTTCMGOConfiguracaoOrgaoUnidade->recuperaPorChave( $rsRecordSet, $boTransacao );

    $resultado = SistemaLegado::pegaValor("SELECT LPAD(num_orgao::VARCHAR,2,'0')||LPAD(num_unidade::VARCHAR,2,'0') AS resultado
                                             FROM orcamento.unidade
                                             WHERE num_orgao = ".substr($request->get('inCodRPPS'),0,2)."
                                               AND num_unidade = ".substr($request->get('inCodRPPS'),2,2)."
                                               AND exercicio = '".Sessao::getExercicio()."'"
                                        ,"resultado"
                                        );
    if ($resultado == '') {
        $obErro->setDescricao("Esse órgão e unidade não existem no urbem!");
    } else {
        if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
            $obErro = $obTTCMGOConfiguracaoOrgaoUnidade->alteracao( $boTransacao );
        } else {
            $obErro = $obTTCMGOConfiguracaoOrgaoUnidade->inclusao( $boTransacao );
        }
    }
}

if ($request->get('inCodOutros')) {
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("exercicio", Sessao::getExercicio() );
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("cod_entidade",SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE cod_modulo = 8 AND parametro = 'cod_entidade_consorcio' AND exercicio = '".Sessao::getExercicio()."'"));
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("cod_poder", 4 );
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("num_orgao", substr($request->get('inCodOutros'),0,2) );
    $obTTCMGOConfiguracaoOrgaoUnidade->setDado("num_unidade", substr($request->get('inCodOutros'),2,2) );
    $obErro = $obTTCMGOConfiguracaoOrgaoUnidade->recuperaPorChave( $rsRecordSet, $boTransacao );

    $resultado = SistemaLegado::pegaValor("SELECT LPAD(num_orgao::VARCHAR,2,'0')||LPAD(num_unidade::VARCHAR,2,'0') AS resultado
                                             FROM orcamento.unidade
                                             WHERE num_orgao = ".substr($request->get('inCodOutros'),0,2)."
                                               AND num_unidade = ".substr($request->get('inCodOutros'),2,2)."
                                               AND exercicio = '".Sessao::getExercicio()."'"
                                        ,"resultado"
                                        );
    if ($resultado == '') {
        $obErro->setDescricao("Esse órgão e unidade não existem no urbem!");
    } else {
        if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
            $obErro = $obTTCMGOConfiguracaoOrgaoUnidade->alteracao( $boTransacao );
        } else {
            $obErro = $obTTCMGOConfiguracaoOrgaoUnidade->inclusao( $boTransacao );
        }
    }
}

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTCMGOConfiguracaoOrgaoUnidade );

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm,"parâmetros atualizados", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

?>