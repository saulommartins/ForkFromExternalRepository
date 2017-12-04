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
* Página de Processamento Candidato
* Data de Criação: 30/06/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.01.03
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GRH_CON_NEGOCIO."RConcursoCandidato.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCandidato";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=".$request->get('stAcao');
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$request->get('stAcao')."&inCodEdital=".$request->get('inCodEdital');
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$request->get('stAcao');
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRCandidato  = new RConcursoCandidato;

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

$stAcao = $request->get("stAcao");

switch ($stAcao) {
    case "incluir":
        $obRCandidato->setNumCGM($request->get('inNumCGM'));
        $obRCandidato->obRConcursoConcurso->setCodEdital($request->get('inCodEdital'));
        $obRCandidato->setCodCargo($request->get('inCodCargo'));

        //monta array de atributos dinamicos
        foreach ($arChave as $key => $value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode( "," , $value );
            }
            $obRCandidato->obRCadastroDinamico->addAtributosDinamicos($inCodAtributo , $value);
        }

        $obErro = $obRCandidato->incluirCandidato();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::exibeAviso("Candidato: ".$obRCandidato->getCodCandidato(),"incluir","aviso");
            $js .= 'f.inCodEdital.value = "'.$request->get('inCodEdital').'";';
            $js .= "f.inNumCGM.value='';";
            $js .= 'd.getElementById("nom_cgm").innerHTML       = "&nbsp;";';
            $js .= 'd.getElementById("stEndereco").innerHTML    = "&nbsp;";';
            $js .= 'd.getElementById("stEstado").innerHTML      = "&nbsp;";';
            $js .= 'd.getElementById("stCidade").innerHTML      = "&nbsp;";';
            $js .= 'd.getElementById("stBairro").innerHTML      = "&nbsp;";';
            $js .= 'd.getElementById("stCep").innerHTML         = "&nbsp;";';
            $js .= 'd.getElementById("stFoneRes").innerHTML     = "&nbsp;";';
            $js .= 'd.getElementById("stFoneCel").innerHTML     = "&nbsp;";';
            $js .= 'd.getElementById("stEmail").innerHTML       = "&nbsp;";';
            SistemaLegado::executaFrameOculto($js);
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $obRCandidato->obRConcursoConcurso->setCodEdital($request->get('inCodEdital'));
        $obRCandidato->setCodCandidato($request->get('inCodCandidato'));
        $obRCandidato->setNumCGM($request->get('inNumCGM'));
        if ($request->get('inHdnTipoProva') == '1') {
               $obRCandidato->setNotaProva(str_replace(",",".",$request->get('stNotaProvaPratica')));
        } else {
               $obRCandidato->setNotaProva(str_replace(",",".",$request->get('stNotaProvaTeoricoPratica')));
        }
        if( $request->get('inHdnProvaTitulacao') == 't' )
            $obRCandidato->setNotaTitulo ( str_replace(",",".",$request->get('stNotaTitulacao')));
        $obErro = $obRCandidato->classificarCandidato();

        if ( !$obErro->ocorreu() ) {
            $obRCandidato->listarCandidatoPorEdital( $rsCandidatos );
            $rsCandidatos->setPrimeiroElemento();

            do {
               $pgProx = $pgForm;
               $pgForm = $pgList;
               if ( $rsCandidatos->getCampo("situacao")  == 'Sem nota' ) {
                    $stFiltro .= "&inCodCandidato=".$rsCandidatos->getCampo('cod_candidato')."&";
                    $pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$request->get('stAcao');
                    break;
               } else {
                    $rsCandidatos->proximo();
               }
            } while ( !$rsCandidatos->eof() );

        }
        if ( !$obErro->ocorreu() ) {
            if ($pgForm == $pgList) {
                $obRCandidato->listarCandidatoPorEdital( $rsCandidatos,"",$stOrder, $boTransacao );
                $rsCandidatos->setPrimeiroElemento();
                $obRCandidato->obRConcursoConcurso->recuperaNotasEdital( $rsNotas );
                $rsNotas->setPrimeiroElemento();
                $inClassificacao = 1;
                $inClassificacaoAux = 1;
                do {
                        while ( !$rsCandidatos->eof() ) {
                            if ( $rsNotas->getCampo("media") == $rsCandidatos->getCampo("media") ) {
                                $obRCandidato = new RConcursoCandidato;
                                $obRCandidato->setClassificacao( $inClassificacao );
                                $obRCandidato->setNotaProva(null);
                                $obRCandidato->setNotaTitulo("");
                                $obRCandidato->setCodCandidato( $rsCandidatos->getCampo("cod_candidato") );
                                $obRCandidato->setNumCGM( $rsCandidatos->getCampo( "numcgm" ) );
                                $obErro = $obRCandidato->classificarCandidato();
                                unset( $obRCandidato );
                                if ( $obErro->ocorreu() ) {
                                     return $obErro;
                                }
                                $inClassificacaoAux++;
                            }

                            $rsCandidatos->proximo();
                        }
                        $inClassificacao = $inClassificacaoAux;
                        $rsCandidatos->setPrimeiroElemento();
                        $rsNotas->proximo();

                } while ( !$rsNotas->eof() );

                SistemaLegado::alertaAviso($pgForm.$stFiltro,"Candidatos Classificados","alterar","aviso", Sessao::getId() );

            } else {

                SistemaLegado::alertaAviso($pgForm.$stFiltro,"Candidato:".$obRCandidato->getCodCandidato(),"alterar","aviso", Sessao::getId() );

            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"classificar","erro");
        }
    break;

    case "reclassifi":
        $obRCandidato->obRConcursoConcurso->setCodEdital($inCodConcurso);
        $obRCandidato->setNumCGM($request->get('inNumCGM'));
        $obRCandidato->setCodCandidato($request->get('inCodCandidato'));

        if ($request->get('boReclassificar') == 'false') {
            $obRCandidato->setReclassificacao( true );
        } else {
            $obRCandidato->setReclassificacao( false );
        }

        $obErro = $obRCandidato->listarCandidatoPorCodigo( $rsCandidato );

        if ( !$obErro->ocorreu() ) {
            if ( $rsCandidato->getCampo("reclassificado") == 'false' ) {
                $obErro = $obRCandidato->reclassificarCandidato();
                if ( !$obErro->ocorreu() ) {
                    SistemaLegado::alertaAviso($pgList,"Candidato: ".$obRCandidato->getCodCandidato(),"alterar","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                }

            } else {
        $obErro = $obRCandidato->reclassificarCandidato();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Removida reclassificação Candidato: ".$obRCandidato->getCodCandidato(),"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
            }
        }

    break;
}
?>