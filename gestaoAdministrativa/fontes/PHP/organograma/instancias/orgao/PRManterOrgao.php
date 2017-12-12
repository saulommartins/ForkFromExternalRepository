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
 * Arquivo de instância para manutenção de orgao
 * Data de Criação: 25/07/2005

 * @author Analista: Cassiano
 * @author Desenvolvedor: Cassiano

 * $Id: PRManterOrgao.php 66269 2016-08-04 14:50:31Z michel $

 * Casos de uso: uc-01.05.02

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php";

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrgao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList 	= "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgForm 	= "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgProc 	= "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgOcul     = "OC".$stPrograma.".php";

$obRegra = new ROrganogramaOrgao;

$obErro = new Erro();

$stFiltro = '';
$arDados = Sessao::read('dados');
if ($arDados) {
    $stFiltro = '';
    foreach ($arDados as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$stSigla     = $request->get('stSigla');
$stDescricao = $request->get('stDescricao');

switch ($stAcao) {

    case "incluir":
        $inCodOrgaoSuperior = $request->get('inCodOrgaoSuperior');

        if($request->get('inCodNivel') > 1 && empty($inCodOrgaoSuperior)){
            $obErro->setDescricao('Campo Órgão Superior inválido!()');
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"unica","erro");
            break;
        }

        if (!$obErro->ocorreu()){
            $obRegra->setSigla                                  ( stripslashes($stSigla)                );
            $obRegra->setDescricao                              ( stripslashes($stDescricao)            );
            $obRegra->obROrganograma->setCodOrganograma         ( $request->get('inCodOrganograma')     );
            $obRegra->obRNivel->setCodNivel                     ( $request->get('inCodNivel')           );
            $obRegra->setCodOrgaoSuperior                       ( $request->get('inCodOrgaoSuperior')   );
            $obRegra->setCriacao                                ( $request->get('stDataCriacao')        );
            $obRegra->obRCalendario->setCodCalendar             ( $request->get('inCodCalendario')      );
            $obRegra->obRNorma->setCodNorma                     ( $request->get('inCodNorma')           );
            $obRegra->obRNorma->obRTipoNorma->setCodTipoNorma   ( $request->get('inCodTipoNorma')       );
            $obRegra->obRCgmPF->setNumCGM                       ( $request->get('inNumCGMResponsavel')  );
            $obRegra->obRCgmPJ->setNumCGM                       ( $request->get('inNumCGMOrgao')        );

            $obErro = $obRegra->salvar();
        }

        if (!$obErro->ocorreu())
            SistemaLegado::alertaAviso($pgForm,"Órgão: ".$request->get('stDescricao'),"incluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;

    case "alterar":
        $obRegra->setCodOrgao                               ( $request->get('inCodOrgao')           );
        $obRegra->setSigla                                  ( stripslashes($stSigla)                );
        $obRegra->setDescricao                              ( stripslashes($stDescricao)            );
        $obRegra->setUltimaDescricao                        ( $request->get('stHdnDescricao')       );
        $obRegra->obROrganograma->setCodOrganograma         ( $request->get('inCodOrganograma')     );
        $obRegra->obRNivel->setCodNivel                     ( $request->get('inCodNivel')           );
        $obRegra->setCodOrgaoSuperior                       ( $request->get('inCodOrgaoSuperior')   );
        $obRegra->setCriacao                                ( $request->get('stDataCriacao')        );
        $obRegra->obRCalendario->setCodCalendar             ( $request->get('inCodCalendario')      );
        $obRegra->obRNorma->setCodNorma                     ( $request->get('inCodNorma')           );
        $obRegra->obRNorma->obRTipoNorma->setCodTipoNorma   ( $request->get('inCodTipoNorma')       );
        $obRegra->obRCgmPF->setNumCGM                       ( $request->get('inNumCGMResponsavel')  );
        $obRegra->obRCgmPJ->setNumCGM                       ( $request->get('inNumCGMOrgao')        );

        $obErro = $obRegra->salvar();

        if (!$obErro->ocorreu())
            SistemaLegado::alertaAviso($pgList.$stFiltro,"Órgão: ".$request->get('stDescricao'),"alterar","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    break;

    case "excluir";
        $obRegra->setCodOrgao                       ( $request->get('inCodOrgao')       );
        $obRegra->obROrganograma->setCodOrganograma ( $request->get('inCodOrganograma') );

        $obErro = $obRegra->excluir();

        if (!$obErro->ocorreu())
            sistemaLegado::alertaAviso($pgList.$stFiltro,urlencode( "Órgão: ".$request->get('inCodOrgao') ),"excluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::alertaAviso($pgList.$stFiltro,urlencode( "Órgão: ".$request->get('inCodOrgao')." - ".$obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
    break;
}

?>
