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
    * Data de Criação: 15/04/2014

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Lisiane Morais

    $Id: PRManterEscola.php 59612 2014-09-02 12:00:51Z gelson $

    
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaEscola.class.php" );

$stPrograma = "ManterEscola";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$obErro     = new Erro;

// verifica se nao existe uma apolice ja cadastrada no banco
$stFiltro = "
                WHERE numcgm = ".$_REQUEST['numCgm']."
            ";
$obFrotaEscola = new TFrotaEscola;           
$obFrotaEscola->recuperaTodos( $rsEscola, $stFiltro );

if ( $rsEscola->getNumLinhas() > 0 ) {
    $obFrotaEscola->setDado('numcgm',$_REQUEST['numCgm']);
    $obFrotaEscola->setDado('ativo',$_REQUEST['boAtivo']);
    $obErro = $obFrotaEscola->alteracao();
    
    if($obErro->ocorreu()){
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");       
    }else 
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Escola atualizada","alterar","aviso", Sessao::getId(), "../");
}else{
    $obFrotaEscola->setDado('numcgm',$_REQUEST['numCgm']);
    $obFrotaEscola->setDado('ativo',$_REQUEST['boAtivo']);
    $obErro = $obFrotaEscola->inclusao();
    if($obErro->ocorreu()){
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");       
    }else {
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Escola cadastrada","incluir","aviso", Sessao::getId(), "../");
    }
}

?>