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

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: PRManterMarca.php 63194 2015-08-03 20:44:43Z carlos.silva $

    * Casos de uso: uc-03.02.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaMarca.class.php" );

$stPrograma = "ManterMarca";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTFrotaMarca = new TFrotaMarca();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTFrotaMarca );

switch ($stAcao) {
    case 'incluir':
        //verifica se nao existe ja no cadastro o nome da marca
        $obTFrotaMarca->recuperaTodos( $rsMarca, " WHERE nom_marca ILIKE '".$_REQUEST['stMarca']."' ");
        if ( $rsMarca->getNumLinhas() > 0 ) {
            $stMensagem = 'Já existe uma marca com esta descrição';
        }
        
        //verifica se nao existe ja no cadastro o cod_marca da marca
        $obTFrotaMarca->recuperaTodos( $rsMarca, " WHERE cod_marca = ".$_REQUEST['inCodMarca']." ");
        if ( $rsMarca->getNumLinhas() > 0 ) {
            $stMensagem = 'Já existe uma marca com este código';
        }

        if (!$stMensagem) {
            //seta os dados e cadastra no sistema
            $obTFrotaMarca->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
            $obTFrotaMarca->setDado( 'nom_marca', $_REQUEST['stMarca'] );
            $obTFrotaMarca->inclusao();

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,'Marca - '.$_REQUEST['inCodMarca'].' - '.$_REQUEST['stMarca'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;

    case 'alterar':
        //verifica se nao existe ja no cadastro o nome da marca
        $obTFrotaMarca->recuperaTodos( $rsMarca, " WHERE nom_marca ILIKE '".$_REQUEST['stMarca']."' AND cod_marca <> ".$_REQUEST['inCodMarca']." ");
        if ( $rsMarca->getNumLinhas() > 0 ) {
            $stMensagem = 'Já existe uma marca com esta descrição';
        }

        if (!$stMensagem) {
            //seta os dados e cadastra no sistema
            $obTFrotaMarca->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
            $obTFrotaMarca->setDado( 'nom_marca', $_REQUEST['stMarca'] );
            $obTFrotaMarca->alteracao();

            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Marca - '.$_REQUEST['inCodMarca'].' - '.$_REQUEST['stMarca'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;

    CASE 'excluir':
        //seta os dados e exclui da base
        $obTFrotaMarca->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
        $obTFrotaMarca->exclusao();
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Marca - '.$inCodMarca.' - '.$_REQUEST['stNomMarca'],"excluir","aviso", Sessao::getId(), "../");

        break;

}

Sessao::encerraExcecao();
