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
  * Página que abre o preview do relatório desenvolvido no Birt.
  * Data de criação : 12/08/2008

  $Id: OCGeratermoResponsabilidade.php 64188 2015-12-14 12:52:21Z evandro $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
include_once CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php";

if (Sessao::read('filtroRelatorio')) {
    $filtroRelatorio = Sessao::read('filtroRelatorio');
} else {
    $filtroRelatorio = $_REQUEST;
}

$obTPatrimonioBem = new TPatrimonioBem();
$stFiltro 	  = "WHERE  numcgm = ".$filtroRelatorio['inNumResponsavel']." AND dt_fim IS NULL";
$obTPatrimonioBem->recuperaBemResponsavel ( $rsBemResponsavel, $stFiltro );

$obTCGMPessoaFisica = new TCGMPessoaFisica();
$obTCGMPessoaFisica->setDado( 'numcgm', $filtroRelatorio['inNumResponsavel'] );
$obTCGMPessoaFisica->recuperaPorChave( $rsCGMPessoaFisica );
$cpf = $rsCGMPessoaFisica->getCampo('cpf');

if (isset($cpf)) {
        $cpf1 = substr($rsCGMPessoaFisica->getCampo('cpf'),0,3);
        $cpf2 = substr($rsCGMPessoaFisica->getCampo('cpf'),3,3);
        $cpf3 = substr($rsCGMPessoaFisica->getCampo('cpf'),6,3);
        $cpf4 = substr($rsCGMPessoaFisica->getCampo('cpf'),9,2);
        $cpf = $cpf1.".".$cpf2.".".$cpf3."-".$cpf4;
}

if ($rsBemResponsavel->getNumLinhas() > 0) {

    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
    $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 2 );
    $obTAdministracaoConfiguracao->pegaConfiguracao( $cnpjCNM, 'cnpj' );

    if ($cnpjCNM == '00703157000183') {
        $preview = new PreviewBirt(3,6,19);
        $preview->addParametro( 'data', 'Brasília, '.SistemaLegado::dataExtenso(date('Y-m-d'), false));
        $preview->addParametro( 'cpf', 'CPF: '.$cpf );

    } else {
        //gestaoPatrimonial/fontes/RPT/patrimonio/report/design/termoResponsabilidade.rptdesign        
        $preview = new PreviewBirt(3,6,2);
    }

    $preview->setVersaoBirt( '2.5.0' );
    $preview->setTitulo('Relatório do Birt');
    $preview->setNomeArquivo('termoResponsabilidade');
    if ($filtroRelatorio['setPDF'] == 'true') {
        $preview->setFormato('pdf');
    }
    $preview->addParametro( 'exercicio', Sessao::getExercicio() );
    $preview->addParametro( 'numcgm', $filtroRelatorio['inNumResponsavel'] );
    $preview->addParametro( 'nomecgm', $filtroRelatorio['stNomResponsavel'] );
    $preview->addParametro( 'hidden_valor', $filtroRelatorio['demo_valor']);
    $preview->addParametro( 'lista_bens', $filtroRelatorio['lista_bens']);

    # Organograma
    if ($_REQUEST['inCodOrganogramaAtivo'] != '') {
        if ($_REQUEST['inCodOrganogramaClassificacao'] != '') {

            $boPermissaoHierarquica = SistemaLegado::pegaDado('permissao_hierarquica', 'organograma.organograma', ' WHERE cod_organograma = '.$_REQUEST['inCodOrganogramaAtivo']);
            
            if ($boPermissaoHierarquica == 't'){
                $preview->addParametro( 'cod_orgao', $_REQUEST['hdninCodOrganograma'].'%' );
            } else {
                $preview->addParametro( 'cod_orgao', $_REQUEST['hdninCodOrganograma'] );
            }
        } else {

        }

        $preview->addParametro( 'cod_organograma', $_REQUEST['inCodOrganogramaAtivo'] );
    } else {
        $preview->addParametro( 'cod_organograma', '' );
    }

    # Local
    if ($_REQUEST['inCodLocal'] != '' ) {
        $preview->addParametro( 'cod_local', $_REQUEST['inCodLocal'] );
    } else {
        $preview->addParametro( 'cod_local', '' );
    }


    $preview->preview();

} else {
    $stMensagem = 'Este CGM não está responsável por nenhum bem.';
    SistemaLegado::alertaAviso('termoResponsabilidade.php', urlencode($stMensagem),"aviso","aviso");
}

?>
