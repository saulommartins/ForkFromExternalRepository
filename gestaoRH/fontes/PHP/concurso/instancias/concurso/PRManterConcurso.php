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
* Página de Processamento de Concurso
* Data de Criação: 29/06/2004

* @author Analista: ???
* @author Desenvolvedor: João Rafael Tissot

* @package URBEM
* @subpackage
$Id: PRManterConcurso.php 60979 2014-11-26 19:04:02Z evandro $
$Revision: 30566 $
$Name: $
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.01.02
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GRH_CON_NEGOCIO."RConcursoConcurso.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConcurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRConcurso  = new RConcursoConcurso;
$obErro= new Erro;

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "incluir":
        //seta array de Cargos
        foreach ($_POST['inCodCargosSelecionados'] as $key => $valor) {
            $obRConcurso->addCargo();
            $obRConcurso->obUltimoCargo->setCodCargo( $valor );
            $obRConcurso->commitCargo();
        }
        //seta valores do ConcursoConcurso
        $obRConcurso->setCodEdital          ($request->get('stEdital'));
        $obRConcurso->setRNorma             ($request->get('stNorma'));
        $obRConcurso->setAplicacao          ($request->get('dtAplicacacao'));
        $nuNotaMinima = str_replace         (',' , '.' , $request->get('nuNotaMinima'));
        $obRConcurso->setNotaMinima         ($nuNotaMinima);
        $obRConcurso->setTipoProva          ($request->get('boTipoProva'));
        $obRConcurso->setAvaliaTitulacao    ($request->get('boAvaliaTitulacao'));
        $obRConcurso->setMesesValidade      ($request->get('inMesesValidade'));
        
        $obErro = $obRConcurso->consultarConcurso($rsConcursos, $rsCargos, "", "", $boTransacao);
        if ($rsConcursos->getNumLinhas() > 0) {
            $obErro->setDescricao("Este edital já está vinculado a outro concurso!");
        }

        //monta array de atributos dinamicos
        foreach ($arChave as $key => $value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode( "," , $value );
            }
            $obRConcurso->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        if ( SistemaLegado::comparaDatas($_POST['dtPublicacao'],$_POST['dtAplicacacao']) ) {
            $obErro->setDescricao("A data de aplicação deve ser posterior a data de publicação!");
        }

        if ( !$obErro->ocorreu() ) {
            $obErro =  $obRConcurso->incluirConcurso();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgForm . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Edital ".$request->get('stEdital')."/".Sessao::getExercicio(),"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $obRConcurso->setCodEdital              ( $request->get('stEdital'));
        $obRConcurso->setAplicacao              ( $request->get('dtAplicacacao'));
        $obRConcurso->setCodEditalHomologacao   ( $request->get('stEditalHomologacao'));
        $nuNotaMinima = str_replace( ',' , '.' , $request->get('nuNotaMinima'));
        $obRConcurso->setNotaMinima             ( $nuNotaMinima);
        $obRConcurso->setRNorma                 ( $request->get('stNorma'));
        $obRConcurso->setMesesValidade          ( $request->get('inMesesValidade'));
        $obRConcurso->setTipoProva              ( $request->get('boTipoProva'));
        $obRConcurso->setAvaliaTitulacao        ( $request->get('boAvaliaTitulacao'));

        //seta array de Cargos
        foreach ($_POST['inCodCargosSelecionados'] as $key => $valor) {
            $obRConcurso->addCargo();
            $obRConcurso->obUltimoCargo->setCodCargo( $valor );
            $obRConcurso->commitCargo();
        }

        //monta array de atributos dinamicos
        foreach ($arChave as $key => $value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode( "," , $value );
            }
            $obRConcurso->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        if ( SistemaLegado::comparaDatas($_POST['dtPublicacao'],$_POST['dtAplicacacao']) ) {
            $obErro->setDescricao("A data de aplicação deve ser posterior a data de publicação!");
        }
        if ( $_POST['dtHomologacao'] != '' and SistemaLegado::comparaDatas($_POST['dtPublicacao'],$_POST['dtHomologacao']) ) {
            $obErro->setDescricao("A data de publicação da homologação deve ser maior a data de publicação do edital!");
        }

        $stLink = '&inExercicio='.$request->get('inExercicio');

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRConcurso->alterarConcurso();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Concurso ".$request->get('inCodConcurso'),"alterar","aviso", Sessao::getId().$stLink, "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "prorrogar":
        $obRConcurso->setCodEdital     ( $request->get('stEdital'));
        $obRConcurso->setAplicacao     ( $request->get('dtAplicacao'));
        $nuNotaMinima = str_replace( ',' , '.' , $request->get('nuNotaMinima'));
        $obRConcurso->setNotaMinima    ( $nuNotaMinima                 );
        $obRConcurso->setRNorma        ( $request('stNorma'));
        $obRConcurso->setMesesValidade ( $request->get('inMesesValidade'));
        $obRConcurso->setProrrogacao   ( $request->get('dtProrrogacao'));

        $stLink = '&inExercicio='.$request->get('inExercicio');
        if ( SistemaLegado::comparaDatas($request->get('dtPublicacao'),$request->get('dtAplicacao')) ) {
            $obErro->setDescricao("A data de aplicação deve ser posterior a data de publicação!");
        }
        if ( SistemaLegado::comparaDatas($request->get('dtHomologacao'),$request->get('dtProrrogacao')) ) {
            $obErro->setDescricao("A data de prorrogação deve ser maior que a data de publicação da homologação!");
        }
        // sugere a data de prorrogacao para 2 anos a data de publicacao, se data for maior que esta não é válida.
        $arDate=explode("/",$request->get('dtPublicacao'));
        $data = $arDate[1]."/".$arDate[0]."/".$arDate[2];

        if (SistemaLegado::comparaDatas($request->get('dtProrrogacao'),strftime('%d/%m/%Y',strtotime($data." +2 year")))) {
            $obErro->setDescricao("A data de Prorrogacao não deve ser maior do que 2 anos a data de Publicação que é (".$request->get('dtPublicacao').")");
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRConcurso->prorrogarConcurso();
            SistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Concurso ".$request->get('stEdital'),"prorrogar","aviso", Sessao::getId().$stLink, "../");
        } else {        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_prorrogar","erro");    }
    break;
}
?>
