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
    * Página de Processamento de Configuração do módulo Tesouraria
    * Data de Criação   : 08/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Cleisson da Silva Barboza
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30984 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.02
*/

/*
$Log$
Revision 1.9  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_TES_NEGOCIO."RTesourariaTerminal.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTerminal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stNow = date( 'Y-m-d H:i:s.ms' );

$stAcao = $request->get('stAcao');

$obRTesourariaTerminal = new RTesourariaTerminal();
$obRTesourariaTerminal->setCodTerminal( $_REQUEST['inNumTerminal'] );
$obRTesourariaTerminal->setCodVerificador( $_REQUEST['stCodVerificador'] );
if ($stAcao=='incluir') {
    $obRTesourariaTerminal->setTimestampTerminal( $stNow );
} else {
    $obRTesourariaTerminal->setTimestampTerminal( $_REQUEST['stTimestampTerminal'] );
}
$inCount=0;
if (!($stAcao=="excluir")) {
    $arUsuario = Sessao::read('arUsuario');
    if ( count( $arUsuario ) ) {
        foreach ($arUsuario as $arTemp) {
            $obRTesourariaTerminal->addUsuarioTerminal();
            $obRTesourariaTerminal->roUltimoUsuario->obRCGM->setNumCGM( $arTemp['numcgm']);
            $obRTesourariaTerminal->roUltimoUsuario->setTimestampUsuario($stNow );
            $boResponsavel = ( $arTemp['responsavel'] == 't' ) ? 'true' : 'false';
            $obRTesourariaTerminal->roUltimoUsuario->setResponsavel( $boResponsavel );
            
            if ($boResponsavel=='true') {
                $inCount++;
            }
        }
        
        if($inCount==0) {
            $stErro = "Deve haver um usuário vinculado como responsável do terminal!";
        }
        
        # Antigo teste onde permitia somente um responsável por terminal.
        #if($inCount>1)
        #    $stErro = "Deve haver somente um usuário vinculado como responsável do terminal!";
    } else {
        $stErro = "Deve haver pelo menos um usuário vinculado ao terminal!";
    }
}
if (!$stErro) {
    switch ($stAcao) {
        case "incluir":
            $obErro = $obRTesourariaTerminal->incluir( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Terminal e Usuários","incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        break;

        case "alterar":
            $obErro = $obRTesourariaTerminal->alterar( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arUsuario = Sessao::read('arUsuario');
                if (($_REQUEST['stSituacao']=='Inativo') and ($_REQUEST['stSituacao']!=$arUsuario['situacao'])) {
                    $obRTesourariaTerminal->setTimestampDesativado( $stNow );
                    $obErro = $obRTesourariaTerminal->desativar( $boTransacao );

                    if ( !$obErro->ocorreu() ) {
                        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=alterar","Terminal e Usuários","alterar","aviso", Sessao::getId(), "../");
                    } else {
                        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
                    }
                } elseif (($_REQUEST['stSituacao']=='Ativo') and ($_REQUEST['stSituacao']!=$arUsuario['situacao'])) {
                    $obErro = $obRTesourariaTerminal->ativar( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=alterar","Terminal e Usuários","alterar","aviso", Sessao::getId(), "../");
                    } else {
                        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
                    }

                } else {
                     SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=alterar","Terminal e Usuários","alterar","aviso", Sessao::getId(), "../");
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            }
        break;

        case "excluir":
            $obRTesourariaTerminal->setTimestampDesativado( $stNow );
            $obErro = $obRTesourariaTerminal->desativar( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=excluir","Terminal e Usuários","alterar","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            }
        break;
    }
} else {
    SistemaLegado::exibeAviso(urlencode($stErro),"n_incluir","erro");
}

?>
