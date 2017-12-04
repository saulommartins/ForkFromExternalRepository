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
    * Página de Processamento para Definir Vencimentos
    * Data de Criação: 23/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar R. Bernardo

    * @ignore

    * $Id: PRManterVencimentos.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.03
*/

/*
$Log$
Revision 1.8  2006/09/15 11:50:32  fabio
corrigidas tags de caso de uso

Revision 1.7  2006/09/15 11:02:23  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalendarioFiscal.class.php");
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"           );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupoVencimento.class.php" );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( 'link' );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterVencimentos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$obErro = new Erro;
$obRARRCalendarioFiscal = new RARRCalendarioFiscal;
$obRARRGrupoVencimento  = new RARRGrupoVencimento( $obRARRCalendarioFiscal );

switch ($stAcao) {
    case "incluir":
        $obRARRGrupoVencimento->roRARRCalendarioFiscal->setAnoExercicio( $_REQUEST['stExercicio'] );
        $obRARRGrupoVencimento->roRARRCalendarioFiscal->setCodigoGrupo( $_REQUEST['inCodigoGrupo'] );
        $obRARRGrupoVencimento->setCodigoVencimento( $_REQUEST['inCodigoVencimento'] );
        $obRARRGrupoVencimento->setDescricao( $_REQUEST['stDescricaoVencimento'] );

        $arVenc = Sessao::read( 'vencimentos' );
        if ( count( $arVenc ) > 0 ) {
            $obRARRGrupoVencimento->setDescontos( $arVenc );
        }

        $arParcel = Sessao::read( 'parcelamentos' );
        if ( count( $arParcel ) > 0 ) {
            $obRARRGrupoVencimento->setParcelas( $arParcel );
        }
        $obErro = $obRARRGrupoVencimento->definirGrupo();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Vencimento incluído: ".$_REQUEST['stDescricaoVencimento'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}
