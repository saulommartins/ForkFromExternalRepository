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
    * Página de processamento para o cadastro de bairro
    * Data de Criação   : 14/10/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: PRProcurarBairro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.05
*/

/*
$Log$
Revision 1.6  2007/07/27 19:39:20  cercato
Bug#9777#

Revision 1.5  2006/09/15 15:03:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"        );
//include_once    ( CLA_TRANSACAO."Transacao.class.php" );

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarBairro" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?".$stLink;
$pgForm     = "FMManterBairro.php";
$pgFMMLogr  = "../logradouro/FMManterLogradouro.php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

$obRCIMBairro = new RCIMBairro;

$stAcao = $request->get('stAcao');
switch ($stAcao) {
    case "incluir":
        $obRCIMBairro->setNomeBairro      ( $_REQUEST[ "stNomeBairro"   ] );
        $obRCIMBairro->setCodigoUF        ( $_REQUEST[ "inCodUF"        ] );
        $obRCIMBairro->setCodigoMunicipio ( $_REQUEST[ "inCodMunicipio" ] );

        if ($_REQUEST["boAliquotaAtivo"]) {
            $obRCIMBairro->setAliquotaVigencia( $_REQUEST["dtVigenciaAliquota"] );
            $obRCIMBairro->setAliquotaCodNorma( $_REQUEST["inCodigoFundamentacaoAliquota"] );
            $obRCIMBairro->setAliquotaTerritorial( $_REQUEST["flAliquotaTerritorial"] );
            $obRCIMBairro->setAliquotaPredial( $_REQUEST["flAliquotaPredial"] );
        }

        if ($_REQUEST["boM2Ativo"]) {
            $obRCIMBairro->setMDVigencia( $_REQUEST["dtVigenciaMD"] );
            $obRCIMBairro->setMDCodNorma( $_REQUEST["inCodigoFundamentacao"] );
            $obRCIMBairro->setMDTerritorial( $_REQUEST["flValorTerritorial"] );
            $obRCIMBairro->setMDPredial( $_REQUEST["flValorPredial"] );
        }

        $tacao = Sessao::read('acao');
        $tmodulo = Sessao::read('modulo');

        Sessao::write('acao'  ,  "784");
        Sessao::write('modulo', "0");

        $obErro = $obRCIMBairro->incluirBairro();

        Sessao::write('acao'  ,  $stacao  );
        Sessao::write('modulo',  $stmodulo);

        if ( !$obErro->ocorreu() ) {
            $link["inCodigoBairro"] = $obRCIMBairro->getCodigoBairro();
            Sessao::write('link', $link);
            sistemaLegado::alertaAvisoPopUp($pgList."&stCtrl=".$stCtrl."&".$stReqLogr,"Nome Bairro: ".$_REQUEST["stNomeBairro"],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $inCodBairro = $_REQUEST["hdnCodigoBairro"];

        $obRCIMBairro->setCodigoBairro    ( $inCodBairro                  );
        $obRCIMBairro->setNomeBairro      ( $_REQUEST[ "stNomeBairro"   ] );
        $obRCIMBairro->setCodigoUF        ( $_REQUEST[ "inCodUF"        ] );
        $obRCIMBairro->setCodigoMunicipio ( $_REQUEST[ "inCodMunicipio" ] );

        if ($_REQUEST["boAliquotaAtivo"]) {
            $obRCIMBairro->setAliquotaVigencia( $_REQUEST["dtVigenciaAliquota"] );
            $obRCIMBairro->setAliquotaCodNorma( $_REQUEST["inCodigoFundamentacaoAliquota"] );
            $obRCIMBairro->setAliquotaTerritorial( $_REQUEST["flAliquotaTerritorial"] );
            $obRCIMBairro->setAliquotaPredial( $_REQUEST["flAliquotaPredial"] );
        }

        if ($_REQUEST["boM2Ativo"]) {
            $obRCIMBairro->setMDVigencia( $_REQUEST["dtVigenciaMD"] );
            $obRCIMBairro->setMDCodNorma( $_REQUEST["inCodigoFundamentacao"] );
            $obRCIMBairro->setMDTerritorial( $_REQUEST["flValorTerritorial"] );
            $obRCIMBairro->setMDPredial( $_REQUEST["flValorPredial"] );
        }

        $tacao = Sessao::read('acao');
        $tmodulo = Sessao::read('modulo');

        Sessao::write('acao'  ,  "784");
        Sessao::write('modulo',  "0"  );

        $obErro = $obRCIMBairro->alterarBairro();
        $inCodBairro = $obRCIMBairro->getCodigoBairro();

        Sessao::write('acao'  , $stacao);
        Sessao::write('modulo', $stmodulo);

        if ( !$obErro->ocorreu() ) {
             sistemaLegado::alertaAvisoPopUp($pgList."&inCodMunicipio=".$_REQUEST[ "inCodMunicipio" ]."&inCodUF=".$_REQUEST[ "inCodUF" ], "Nome Bairro: ".$_REQUEST["stNomeBairro"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "remover";
        $obRCIMBairro->setCodigoBairro    ( $_REQUEST[ "inCodBairro"    ] );
        $obRCIMBairro->setCodigoUF        ( $_REQUEST[ "inCodUF"        ] );
        $obRCIMBairro->setCodigoMunicipio ( $_REQUEST[ "inCodMunicipio" ] );

        $stacao = Sessao::read('acao');
        $stmodulo = Sessao::read('modulo');

        Sessao::write('acao',  "784");
        Sessao::write('modulo', "0");

        $obErro = $obRCIMBairro->excluirBairro();

        Sessao::write('acao'  , $stacao);
        Sessao::write('modulo', $stmodulo);

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAvisoPopUp($pgList,"Nome Bairro: ".$_REQUEST["stNomeBairro"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAvisoPopUp($pgList."&stErro=".urlencode($obErro->getDescricao()),"" ,"excluir","aviso", Sessao::getId(), "../");
//           alertaAvisoPopUp($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;
}
?>
