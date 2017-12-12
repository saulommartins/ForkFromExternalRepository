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
    * Pagina de processamento para Grupos de Credito
    * Data de Criação   : 25/05/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Texeira Stephanou

    * $Id: PRManterTipoSuspensao.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

* Casos de uso: uc-05.03.7
*/

/*
$Log$
Revision 1.6  2006/09/15 11:23:59  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRTipoSuspensao.class.php"   );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma    = "ManterTipoSuspensao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

function alertaAvisoRedirect($location="", $objeto="", $tipo="n_incluir", $chamada="erro", $sessao, $caminho="", $func="")
{
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                SistemaLegado::alertaAviso      ( "'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");
                mudaMenu         ( "'.$func.'"     );
                mudaTelaPrincipal( "'.$location.'" );
           </script>';
}

switch ($stAcao) {
    case "incluir":
        $obErro = new Erro;
        if ($_REQUEST["boEmitir"] == "1") {
            $boEmitir = 'true'    ;
        } else {
            $boEmitir = '0'   ;
        }
        $obRegra = new RARRTipoSuspensao;
        $obRegra->setDescricao  ( $_REQUEST["stDescricao"]  );
        $obRegra->setEmitir     ( $boEmitir                 );
        $obErro = $obRegra->incluirTipoSuspensao(true);
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm."?stAcao=incluir","Código do Tipo de Suspensão: ".$obRegra->getCodigoTipoSuspensao(),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgForm,urlencode($obErro->getDescricao()),"n_incluir","incluir",Sessao::getId(), "../");
        }

    break;
    case "alterar":
        $obErro = new Erro;
        if ($_REQUEST["boEmitir"] == "1") {
            $boEmitir = 'true'    ;
        } else {
            $boEmitir = '0'   ;
        }
        $obRegra = new RARRTipoSuspensao;
        $obRegra->setCodigoTipoSuspensao( $_REQUEST["inCodigoTipoSuspensao"] );
        $obRegra->setDescricao          ( $_REQUEST["stDescricao"]  );
        $obRegra->setEmitir             ( $boEmitir                 );

        $obErro = $obRegra->alterarTipoSuspensao(true);
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=alterar","Código do Tipo de Suspensão: ".$obRegra->getCodigoTipoSuspensao(),"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_alterar","alterar",Sessao::getId(), "../");
        }
    break;

    case "excluir":
        $obErro = new Erro;
        $obRegra = new RARRTipoSuspensao;
        $obRegra->setCodigoTipoSuspensao( $_REQUEST["inCodigoTipoSuspensao"]);
        $obErro = $obRegra->excluirTipoSuspensao(true);
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir","Código do Tipo de Suspensão: ".$obRegra->getCodigoTipoSuspensao(),"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir",urlencode($obErro->getDescricao()),"n_excluir","excluir",Sessao::getId(), "../");
        }

    break;
}
?>
