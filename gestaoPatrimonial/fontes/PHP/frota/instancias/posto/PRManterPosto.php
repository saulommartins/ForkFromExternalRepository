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
    * Data de Criação: 10/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * $Id: PRManterTipoVeiculo.php 32939 2008-09-03 21:14:50Z domluc $

    * Casos de uso: uc-03.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaPosto.class.php" );

$stPrograma = "ManterPosto";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$inCGM  = $_REQUEST['inCGM'];

$obTPosto = new TFrotaPosto();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPosto );

switch ($stAcao) {
    case 'incluir':
        //verifica se nao existe ja no cadastro a descricao do tipo do veiculo
        $obTPosto->recuperaTodos( $rsRecordSet, " WHERE cgm_posto = ".$inCGM." " );

        if ( $rsRecordSet->getNumLinhas() > 0 ) {
            $stMensagem = 'Este posto já foi cadastrado.';
        }

        if (!$stMensagem) {
            //seta os dados e cadastra no sistema
            $obTPosto->setDado( 'cgm_posto', $inCGM );
            $obTPosto->setDado( 'interno', ( $_REQUEST['boInterno'] == 1 ) ? true : false );
            $obTPosto->setDado( 'ativo', true );
            $obTPosto->inclusao();

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,'Posto - '.$inCGM,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;

    case 'alterar':
        //seta os dados e cadastra no sistema
        $obTPosto->setDado( 'cgm_posto', $inCGM );
        $obTPosto->setDado( 'interno',  ( $_REQUEST['boInterno'] == 1 ) ? true : false );
        $obTPosto->setDado( 'ativo',    ( $_REQUEST['boAtivo'] == 1 ) ? true : false );
        $obTPosto->alteracao();

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Posto - '.$inCGM,"alterar","aviso", Sessao::getId(), "../");

        break;

    case 'excluir':
        //seta os dados e exclui da base
        $obTPosto->setDado( 'cgm_posto', $inCGM );

        $obTPosto->recuperaVinculoPosto($rsVinculo);

        if ($rsVinculo->getNumLinhas() < 1) {
            $obTPosto->exclusao();
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Posto - '.$inCGM,"excluir","aviso", Sessao::getId(), "../");
        } else {
            $stMensagem = "Posto ".$inCGM." está sendo utilizado no sistema. Efetue a inativação.";
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,$stMensagem,"n_excluir","erro", Sessao::getId(), "../");
        }

        break;

}

Sessao::encerraExcecao();
