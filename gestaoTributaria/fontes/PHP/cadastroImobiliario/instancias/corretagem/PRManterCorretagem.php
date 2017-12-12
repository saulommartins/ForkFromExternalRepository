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
    * Página de processamento para o cadastro de corretagem
    * Data de Criação   : 25/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: PRManterCorretagem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.13
*/

/*
$Log$
Revision 1.4  2006/09/18 10:30:25  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php"  );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretor.class.php"    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImobiliaria.class.php" );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterCorretagem";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgFormInc  = "FM".$stPrograma."Inclusao.php";
$pgFormAlt  = "FM".$stPrograma."Alteracao.php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );
switch ($stAcao) {
    case "incluir":
        if ($_REQUEST["boTipoCorretagem"] == "imobiliaria") {
            $obRCIMImobiliaria = new RCIMImobiliaria( new RCIMCorretor );
            $obRCIMImobiliaria->setRegistroCreci                 ( trim($_REQUEST["stRegistroCreci"])    );
            $obRCIMImobiliaria->obRCGMPessoaJuridica->setNumCGM  ( $_REQUEST["inNumCGM"]           );
            $obRCIMImobiliaria->obRCIMCorretor->setRegistroCreci ( $_REQUEST["stCreciResponsavel"] );
            $obErro = $obRCIMImobiliaria->incluirImobiliaria();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgFormInc."?stAcao=incluir&boTipoCorretagem=imobiliaria","Imobiliária - Registro CRECI: ".$_REQUEST["stRegistroCreci"],"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } elseif ($_REQUEST["boTipoCorretagem"] == "corretor") {
            $obRCIMCorretor = new RCIMCorretor;
            $obRCIMCorretor->setRegistroCreci             ( trim($_REQUEST["stRegistroCreci"]) );
            $obRCIMCorretor->obRCGMPessoaFisica->setNumCGM( $_REQUEST["inNumCGM"]        );
            $obErro = $obRCIMCorretor->incluirCorretor();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgFormInc."?stAcao=incluir&boTipoCorretagem=corretor","Corretor - Registro CRECI: ".$_REQUEST["stRegistroCreci"],"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }
    break;
    case "alterar":
        $obRCIMImobiliaria = new RCIMImobiliaria( new RCIMCorretor );
        $obRCIMImobiliaria->setRegistroCreci                 ( $_REQUEST["stRegistroCreci"]    );
        $obRCIMImobiliaria->obRCGMPessoaJuridica->setNumCGM  ( $_REQUEST["inNumCGM"]           );
        $obRCIMImobiliaria->obRCIMCorretor->setRegistroCreci ( $_REQUEST["stCreciResponsavel"] );
        $obErro = $obRCIMImobiliaria->alterarImobiliaria();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=alterar&boTipoCorretagem=imobiliaria","Imobiliária - Registro CRECI: ".$_REQUEST["stRegistroCreci"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir":
        if ($_REQUEST["boTipoCorretagem"] == "imobiliaria") {
            $obRCIMImobiliaria = new RCIMImobiliaria( new RCIMCorretor );
            $obRCIMImobiliaria->setRegistroCreci                 ( $_REQUEST["stRegistroCreci"]    );
            $obErro = $obRCIMImobiliaria->excluirImobiliaria();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgList."?stAcao=excluir&boTipoCorretagem=imobiliaria","Imobiliária - Registro CRECI: ".$_REQUEST["stRegistroCreci"],"excluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($pgList."?stAcao=excluir&boTipoCorretagem=imobiliaria",urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
//                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
            }
        } elseif ($_REQUEST["boTipoCorretagem"] == "corretor") {
            $obRCIMCorretor = new RCIMCorretor;
            $obRCIMCorretor->setRegistroCreci             ( $_REQUEST["stRegistroCreci"] );
            $obErro = $obRCIMCorretor->excluirCorretor();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgList."?stAcao=excluir&boTipoCorretagem=corretor","Corretor - Registro CRECI: ".$_REQUEST["stRegistroCreci"],"excluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::alertaAviso($pgList."?stAcao=excluir&boTipoCorretagem=corretor",urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
            }
        }
    break;
}
?>
