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
    
    * Página de processamento para o cadastro de bairro
    * Data de Criação   : 14/10/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: PRProcurarBairro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"        );

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterBairro" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?";
$pgForm     = "FM".$stPrograma.".php";
$pgFMMLogr  = "../logradouro/FMManterLogradouro.php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

$obRCIMBairro = new RCIMBairro;
$stAcao = $request->get('stAcao');

switch ($stAcao) {
    case "incluir":

        $obRCIMBairro->setNomeBairro      ( $request->get( "stNomeBairro")   );
        $obRCIMBairro->setCodigoUF        ( $request->get( "inCodUF")        );
        $obRCIMBairro->setCodigoMunicipio ( $request->get( "inCodMunicipio") );
        $obErro = $obRCIMBairro->incluirBairro($boTransacao);
        
        if ( !$obErro->ocorreu() ) {
            $link["inCodigoBairro"] = $obRCIMBairro->getCodigoBairro();
            Sessao::write('link', $link);
            sistemaLegado::alertaAviso($pgForm."?stCtrl=".$stCtrl."&stAcao=".$stAcao,"Nome Bairro: ".$request->get("stNomeBairro"),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::LiberaFrames('true','true');
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $inCodBairro = $request->get("hdnCodigoBairro");

        $obRCIMBairro->setCodigoBairro    ( $inCodBairro                  );
        $obRCIMBairro->setNomeBairro      ( $request->get( "stNomeBairro")   );
        $obRCIMBairro->setCodigoUF        ( $request->get( "inCodUF")        );
        $obRCIMBairro->setCodigoMunicipio ( $request->get( "inCodMunicipio") );

        $obErro = $obRCIMBairro->alterarBairro($boTransacao);
        $inCodBairro = $obRCIMBairro->getCodigoBairro();

        if ( !$obErro->ocorreu() ) {
            $pgList .= "&stAcao=".$stAcao."&inCodMunicipio=".$request->get( "inCodMunicipio")."&inCodUF=".$request->get( "inCodUF");
            SistemaLegado::alertaAviso($pgList, "Nome Bairro: ".$request->get("stNomeBairro"),"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::LiberaFrames('true','true');
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir";
        $obRCIMBairro->setCodigoBairro    ( $request->get( "inCodBairro")    );
        $obRCIMBairro->setCodigoUF        ( $request->get( "inCodUF")        );
        $obRCIMBairro->setCodigoMunicipio ( $request->get( "inCodMunicipio") );
        
        $obErro = $obRCIMBairro->excluirBairro( $boTransacao );

        $pgList .= "&stAcao=".$stAcao."&inCodMunicipio=".$request->get( "inCodMunicipio");
        $pgList .= "&inCodUF=".$request->get("inCodUF")."&stNomBairro=".$request->get("stNomBairro")."&inCodBairro=".$request->get("inCodBairro");
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome Bairro: ".$request->get("stDescQuestao"),"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::LiberaFrames('true','true');
            sistemaLegado::alertaAviso($pgList."&stErro=".urlencode($obErro->getDescricao()),"" ,"excluir","aviso", Sessao::getId(), "../");
        }
    break;
}
?>
