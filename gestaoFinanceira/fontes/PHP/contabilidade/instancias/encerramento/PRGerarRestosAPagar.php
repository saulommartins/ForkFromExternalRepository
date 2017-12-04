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
    * Página de Processamento para Gerar Restos A Pagar
    * Data de Criação   : 21/12/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-03-05 11:16:32 -0300 (Qua, 05 Mar 2008) $

    * Casos de uso: uc-02.02.31
*/

/*
$Log$
Revision 1.5  2007/01/05 17:18:11  cako
Bug #7931#

Revision 1.4  2006/12/27 21:23:31  cleisson
UC 02.02.31

Revision 1.3  2006/07/05 20:50:57  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php" );
include_once ( CAM_GF_CONT_MAPEAMENTO."FContabilidadeEncerramento.class.php" );
include_once ( CAM_GF_CONT_MAPEAMENTO."FContabilidadeVerificaVinculoRestos.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "GerarRestosAPagar";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$rsContas = $rsSaldo = new recordSet();
$obErro = new Erro;

$obRConfiguracaoConfiguracao = new RConfiguracaoConfiguracao;
$obFContabilidadeEncerramento = new FContabilidadeEncerramento;
$obFContabilidadeVerificaVinculoRestos = new FContabilidadeVerificaVinculoRestos();

$obTransacao = new Transacao;
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );


if (Sessao::getExercicio() < '2013') {
    //Verifica se todas as despesas esta vinculada a contabilidade
    $obFContabilidadeVerificaVinculoRestos->setDado('exercicio', Sessao::getExercicio());
    $obFContabilidadeVerificaVinculoRestos->setDado('cod_entidade', $request->get('inCodEntidade'));
    $obFContabilidadeVerificaVinculoRestos->recuperaTodos($rsVinculo);

    if ($rsVinculo->getNumLinhas() > 0) {
        while (!$rsVinculo->eof()) {
            $stCodEstrutural .= $rsVinculo->getCampo('cod_estrutural') . ', ';
            $rsVinculo->proximo();
        }
    echo '<script>';
    echo "alertPopUp('Atenção','Conta Contabil do Elemento de Despesa não Analitica: " . substr($stCodEstrutural,0,-2) . "','window.location.href=\'" . $pgFilt . "\';')";
    echo '</script>';
    exit;
    }
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::BloqueiaFrames(true,true);
    flush();
   
    $obFContabilidadeEncerramento->setDado('stExercicio', Sessao::getExercicio());
    if (Sessao::getExercicio() >= '2013') {
        $obFContabilidadeEncerramento->setDado('inCodEntidade', $request->get('inCodEntidade'));
    }
    $boDestinacaoRecurso = sistemaLegado::pegaConfiguracao('recurso_destinacao',8,Sessao::getExercicio());
    if ($boDestinacaoRecurso == 'true') {
        $obErro = $obFContabilidadeEncerramento->gerarRestosPagarDestinacaoRecurso($rsEncerramento, $boTransacao);
    } else {
        $obErro = $obFContabilidadeEncerramento->gerarRestosEncerramento($rsEncerramento, $boTransacao);
    }
   
    //Inluindo ou alterando o campo RESTOS A PAGAR nos empenhos
    if ( !$obErro->ocorreu() ) {
        if (Sessao::getExercicio() >= '2013') {
            $obErro = $obFContabilidadeEncerramento->inscreveRestosPagar($boTransacao);
        }
    }

    if ( !$obErro->ocorreu() ) {
        $obRConfiguracaoConfiguracao->setParametro( "virada_GF" );
        $obRConfiguracaoConfiguracao->setValor( "T" );
        $obRConfiguracaoConfiguracao->setCodModulo( 10 );

        $obRConfiguracaoConfiguracao->verificaParametro( $boExiste, $boTransacao );
        if ($boExiste) {
            $obErro = $obRConfiguracaoConfiguracao->alterar( $boTransacao );
        } else {
            $obErro = $obRConfiguracaoConfiguracao->incluir( $boTransacao );
        }

        if ((!$obErro->ocorreu()) && (Sessao::getExercicio() >= '2013')) {
            $obRConfiguracaoEntidade = new RConfiguracaoConfiguracao;
            $obRConfiguracaoEntidade->setParametro( "virada_GF_entidade_".$request->get('inCodEntidade') );
            $obRConfiguracaoEntidade->setValor( "T" );
            $obRConfiguracaoEntidade->setCodModulo( 10 );
            $obRConfiguracaoEntidade->verificaParametro( $boExiste, $boTransacao );

            if ($boExiste) {
                $obErro = $obRConfiguracaoEntidade->alterar( $boTransacao );
            } else {
                $obErro = $obRConfiguracaoEntidade->incluir( $boTransacao );
            }
            
            $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade;
            $obTAdministracaoConfiguracaoEntidade->setDado('exercicio', Sessao::getExercicio());
            $obTAdministracaoConfiguracaoEntidade->setDado('cod_entidade', $request->get('inCodEntidade'));
            $obTAdministracaoConfiguracaoEntidade->setDado('cod_modulo', 10);
            $obTAdministracaoConfiguracaoEntidade->setDado('parametro', 'virada_GF');
            $obTAdministracaoConfiguracaoEntidade->setDado('valor', 'T');
            $obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsConfig, $boTransacao );

            if ($rsConfig->getNumLinhas() > 0) {
                $obErro = $obTAdministracaoConfiguracaoEntidade->alteracao( $boTransacao );
            } else {
                $obErro = $obTAdministracaoConfiguracaoEntidade->inclusao( $boTransacao );
            }
        }
    }
}

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRConfiguracaoConfiguracao->obTConfiguracao );

SistemaLegado::LiberaFrames(true,true);

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgFilt, Sessao::getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}
?>
