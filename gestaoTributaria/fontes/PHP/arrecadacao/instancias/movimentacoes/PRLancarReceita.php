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
  * Página de Processamento para Lançar Receita
  * Data de criação : 20/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: PRLancarReceita.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.4  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRAvaliacaoEconomica.class.php");

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "LancarReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$obErro = new Erro;
$obRARRAvaliacaoEconomica = new RARRAvaliacaoEconomica;

$obAtributos = new MontaAtributos;
$obAtributos->setName( "Atributo_" );
$obAtributos->recuperaVetor( $arChave );

switch ($stAcao) {
    case "incluir":
        $obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
        $obRARRAvaliacaoEconomica->setFaturamento( $_REQUEST['flFaturamento'] );
        $obRARRAvaliacaoEconomica->setComplemento( $_REQUEST['flComplemento'] );
        $obRARRAvaliacaoEconomica->setCompetencia( $_REQUEST['stCompetencia'] );
        $obRARRAvaliacaoEconomica->setDataVencimento( $_REQUEST['dtDataVencimento'] );
        $obRARRAvaliacaoEconomica->obRFuncao->setCodFuncao( $_REQUEST['inCodigoFormula'] );
        // dados de atributos dinamicos
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRARRAvaliacaoEconomica->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $obErro = $obRARRAvaliacaoEconomica->avaliarCadastroEconomico();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Receita lançada:  ".$obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->getInscricaoEconomica(),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}
