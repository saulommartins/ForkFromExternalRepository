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

    * $Id: PRManterModelo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaModelo.class.php" );

$stPrograma = "ManterModelo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTFrotaModelo = new TFrotaModelo();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTFrotaModelo );

switch ($stAcao) {
    case 'incluir':
        //verifica se nao existe ja no cadastro o nome do modelo para aquela marca
        $obTFrotaModelo->recuperaTodos( $rsModelo, " WHERE nom_modelo ILIKE '".$_REQUEST['stModelo']."' AND cod_marca = ".$_REQUEST['inCodMarca']." " );
        if ( $rsModelo->getNumLinhas() > 0 ) {
            $stMensagem = 'Já existe um modelo com esta descrição para esta marca';
        }

        if (!$stMensagem) {
            //recupera o cod_modelo
            $obTFrotaModelo->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
            $obTFrotaModelo->ProximoCod( $inCodModelo );

            //seta os dados e cadastra no sistema
            $obTFrotaModelo->setDado( 'cod_modelo', $inCodModelo );
            $obTFrotaModelo->setDado( 'nom_modelo', $_REQUEST['stModelo'] );
            $obTFrotaModelo->inclusao();

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,'Modelo - '.$inCodModelo.' - '.$_REQUEST['stModelo'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;

    case 'alterar':
        //verifica se nao existe ja no cadastro o nome do modelo
        $obTFrotaModelo->recuperaTodos( $rsModelo, " WHERE nom_modelo ILIKE '".$_REQUEST['stModelo']."' AND cod_marca = ".$_REQUEST['inCodMarca']." AND cod_modelo <> ".$_REQUEST['inCodModelo']." " );

        if ( $rsModelo->getNumLinhas() > 0 ) {
            $stMensagem = 'Já existe um modelo com esta descrição para esta marca';
        }

        if (!$stMensagem) {
            //seta os dados e altera no sistema
            $obTFrotaModelo->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
            $obTFrotaModelo->setDado( 'cod_modelo', $_REQUEST['inCodModelo'] );
            $obTFrotaModelo->setDado( 'nom_modelo', $_REQUEST['stModelo'] );
            $obTFrotaModelo->alteracao();
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Modelo - '.$_REQUEST['inCodModelo'].' - '.$_REQUEST['stModelo'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
        }

        break;

    CASE 'excluir':
        //seta os dados e exclui da base
        $obTFrotaModelo->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
        $obTFrotaModelo->setDado( 'cod_modelo', $_REQUEST['inCodModelo'] );
        $obTFrotaModelo->exclusao();
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Modelo - '.$inCodModelo.' - '.$_REQUEST['stNomModelo'],"excluir","aviso", Sessao::getId(), "../");

        break;

}

Sessao::encerraExcecao();
